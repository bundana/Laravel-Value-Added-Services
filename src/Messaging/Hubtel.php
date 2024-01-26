<?php

namespace Bundana\Services\Messaging;

/**
 * This class represents the Mnotify messaging service.
 * It provides methods to send SMS messages using the Mnotify API.
 */
class Hubtel extends Validator implements SMSProviderInterface
{
 private $phone;
 private $message;
 private $newKeys = null;
 private $api_key = null;
 private $sender_key = null;
 private $schedule_date = null;
 private $query_params;
 private $newV2Keys = null;

 const API_URL = 'https://apps.mnotify.net/smsapi';
 const API_V2_URL = 'https://api.mnotify.com/api/';

 /**
  * Creates a new instance of Mnotify and sets the phone number.
  *
  * @param string $phone The phone number to send the notification to.
  * @return Mnotify The Mnotify instance with the phone number set.
  */
 public static function to($phone)
 {
  $instance = new static();
  $instance->phone = $instance->validatePhone($phone);
  return $instance;
 }

 /**
  * Sets the message for the Mnotify instance.
  *
  * @param string $message The message to be set.
  * @return $this The Mnotify instance.
  */
 public function message($message)
 {
  $this->message = $message;
  return $this;
 }


 public function newKeys($newKeys)
 {
  $this->mnotifyNewKeys($newKeys);
  $this->newKeys = $newKeys;
  return $this;
  //
 }
 /**
  * Sends an API request to Mnotify service to send a message.
  *
  * @return void
  */
 private function smsApiRequest()
 {
  if (isset($this->newKeys)) {
   $this->api_key = $this->newKeys['apiKey'];
   $this->sender_key = $this->newKeys['sender_id'];
  } else {
   $this->checkMnotifyKeysfromEnvironment();
   $this->api_key = env('MNOTIFY_API_KEY', '');
   $this->sender_key = env('MNOTIFY_SENDER_ID', '');
  }
  // Build the API request URL
  $query_params = [
   'key' => $this->api_key,
   'to' => $this->phone,
   'msg' => $this->message,
   'sender_id' => $this->sender_key,
  ];
  $request_url = self::API_URL . '?' . http_build_query($query_params);

  // Make the API request using cURL
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $request_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $sms_response = curl_exec($ch);
  $sms_response = json_decode($sms_response, true);
  curl_close($ch);
  // dd($sms_response);
  if (isset($sms_response['status']) && $sms_response['status'] == 'success') {
   return json_encode(['success' => true, 'message' => $this->errorMessage($sms_response['code']), 'code' => $sms_response['code']]);
  } elseif (isset($sms_response['status']) && $sms_response['status'] == 'error') {
   return json_encode(['success' => false, 'message' => $sms_response['message']]);
  }
  curl_close($ch);
 }

 /**
  * Sends the notification.
  *
  * @return bool Returns true if the notification is successfully sent, false otherwise.
  */
 public function send($date = null)
 {
  $this->schedule_date = $date;
  // For now, return true as a placeholder for a successful send
  return $this->smsApiRequest();
 }

 /**
  * Sends bulk SMS messages to multiple contacts.
  *
  * @param array $contactsAndMessages An associative array where the keys are the contacts and the values are the messages.
  * @return array An associative array where the keys are the contacts and the values are the responses from sending the messages.
  */
 public static function sendBulk($contactsAndMessages)
 {
  $instance = new static();
  $instance->mnotifyBulkSMSListValidator($contactsAndMessages);
  $responses = [];

  foreach ($contactsAndMessages as $contact => $message) {
   $responses[$contact] = $instance->to($contact)->message($message)->send();
  }

  return $responses;
 }
 /**
  * Get the error message for a specific error code.
  *
  * @param int $errorCode The error code.
  * @return string The corresponding error message.
  */
 private function errorMessage($errorCode)
 {
  $errorMessages = [
   400 => 'Bad Request',
   401 => 'Unauthorized',
   402 => 'Forbidden',
   403 => 'Not Found',
   404 => 'Method Not Allowed',
   405 => 'Too Many Requests.',
   406 => 'Internal Server Error.',
   407 => 'Service Unavailable.',
   1000 => 'Message sent successfully',
   1000 => 'Message submited successful',
   1002 => 'SMS sending failed',
   1003 => 'insufficient balance',
   1004 => 'invalid API key',
   1005 => 'invalid Phone Number',
   1006 => 'invalid Sender ID. Sender ID must not be more than 11 Characters. Characters include white space.',
   1007 => 'Message scheduled for later delivery',
   1008 => 'Empty Message',
   1009 => 'Empty from date and to date',
   1010 => 'No mesages has been sent on the specified dates using the specified api key',
   1011 => 'Numeric Sender IDs are not allowed',
   1012 => 'Sender ID is not registered. Please contact our support team via senderids@mnotify.com or call 0541509394 for assistance',
   2000 => 'Messages/Voice call sent successfully',
   2001 => 'Message/Voice call has been successfully scheduled',
   4000 => 'Numeric Sender IDs are not allowed',
   4001 => 'The recipient field, sender field, and message field must not be empty',
   4002 => 'Make sure schedule date is not empty when is_schedule field is true',
   4003 => 'The sender must not be greater than 11 in length',
   4004 => 'Credit is not enough to send a message to recipients',
   4005 => 'The scheduled time should be more than an hour from now',
   4006 => 'Either message field or message_id field should be empty',
   4007 => 'Message ID provided does not exist',
   4008 => 'Either some of the groups have no contacts or the group does not exist',
   4009 => 'Either voice file or voice_id field should be empty (only one should not be empty)',
   4010 => 'File size should not be greater than 5MB',
   4011 => 'An error occurred while uploading the file. Try again',
   4012 => 'The bit rate of the voice is too high to be sent to a phone',
   4013 => 'Voice ID does not exist',
   4014 => 'Sender ID is not registered. Please contact our support team via senderids@mnotify.com or call 0200896265 for assistance',
   4015 => 'Sender Name and Purpose must not be empty',
  ];

  return $errorMessages[$errorCode] ?? 'Unknown error';
 }


 /**
  * Register Sender ID
  *
  * @param string $id The sender ID to be registered.
  * @param string $purpose The purpose for registering the sender ID.
  * @return string The response from the API request.
  */
 public function registerSenderID($id, $purpose)
 {
  //validate 
  $instance = new static();
  $instance->mnotifySenderIDvalidator($id, $purpose);
  // Build the API request URL
  $this->query_params = [
   'sender_name' => $id,
   'purpose' => 'For Sending SMS Newsletters'
  ];
  $this->apiVersionTwoRequest($this->query_params, 'senderid/register');
 }

 private function apiVersionTwoRequest($query_params = null, $endpoint)
 {
  $this->query_params = $query_params;
  $this->checkMnotifyVersion2KeysfromEnvironment();
  $endPoint = self::API_V2_URL . $endpoint;
  $apiKey = env('MNOTIFY_API_KEY_V2');
  $url = $endPoint . '?key=' . $apiKey;
  // Make the API request using cURL
  $ch = curl_init();
  $headers = array();
  $headers[] = "Content-Type: application/json";
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($query_params));
  $response = curl_exec($ch);
  $response = json_decode($response, TRUE);
  curl_close($ch);
  if (isset($response['status']) && $response['status'] == 'success') {
   return json_encode(['success' => true, 'message' => $this->errorMessage($response['code']), 'code' => $response['code']]);
  } elseif (isset($response['status']) && $response['status'] == 'error') {
   return json_encode(['success' => false, 'message' => $response['message']]);
  }
 }

 /**
  * Checks the SMS balance for the Mnotify service.
  *
  * @param string|null $version The version of the API to use. If set to 'v2', it uses version 2, otherwise it uses version 1.
  * @return string The JSON-encoded response containing the success status and the SMS balance or error message.
  */
 public function checkSMSBalance($version = null)
 {
  $endPoint = '';
  $apiKey = '';
  $url = '';
  //use version 2 if set else use version 1
  if (isset($version) && $version == 'v2') {
   $this->checkMnotifyVersion2KeysfromEnvironment();
   $endPoint = self::API_V2_URL . "balance/sms";
   $apiKey = env('MNOTIFY_API_KEY_V2');
   $url = $endPoint . '?key=' . $apiKey;
  } else {
   $this->checkMnotifyKeysfromEnvironment();
   $endPoint = self::API_URL . "/balance";
   $apiKey = env('MNOTIFY_API_KEY');
   $url = $endPoint . '?key=' . $apiKey;
  }

  // Make the API request using cURL
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
  $response = curl_exec($ch);
  $response = json_decode($response, TRUE);
  curl_close($ch);
  // dd($response);
  if (isset($version) && $version == 'v2') {
   if (isset($response['status']) && $response['status'] == 'success') {
    return json_encode(['success' => true, 'balance' => $response['balance'], 'bonus' => $response['bonus']]);
   } elseif (isset($response['status']) && $response['status'] == 'error') {
    return json_encode(['success' => false, 'message' => $response['message']]);
   }
  } else {
   if (isset($response['status']) && $response['status'] == 'success') {
    return json_encode(['success' => true, 'balance' => $response['sms_balance']]);
   } elseif (isset($response['status']) && $response['status'] == 'error') {
    return json_encode(['success' => false, 'message' => $response['message']]);
   }
  }


 }
}