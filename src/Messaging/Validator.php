<?php
namespace Bundana\Services\Messaging;

class Validator
{
 private $phone;

 protected function validatePhone($phone)
 {
  // Check if the phone number is numeric
  if (!is_numeric($phone)) {
   throw new \InvalidArgumentException('Invalid phone number provided. e.g., 233542345921 or 0542345921');
  }

  // Remove the country code "233" if it exists
  $phoneWithoutCountryCode = preg_replace('/^233/', '', $phone);

  // Regular expression pattern for Ghanaian phone numbers
  $pattern = '/^(020|024|054|055|056|057|059|027)[0-9]{7}$/';

  // Check if the phone number matches the pattern
  if (preg_match($pattern, $phoneWithoutCountryCode)) {
   return $phoneWithoutCountryCode;
  } else {
   // Throw an exception if the phone number doesn't match the pattern
   throw new \InvalidArgumentException('Invalid Ghanaian phone number provided. Must start with acceptable format: 020, 024, 054, 055, 056, 057, 059, 027');

  }
 }


 protected function mnotifyNewKeys($keys)
 {
  if (!is_array($keys) || empty($keys)) {
   throw new \InvalidArgumentException('Invalid keys provided. Please provide an array of key-value pairs, e.g., ["apiKey" => "value1", "sender_id" => "value2"]');
  }

  foreach ($keys as $key => $value) {
   if (!isset($value) || trim($value) === '') {
    throw new \InvalidArgumentException('Invalid key-value pair provided. Each key must have a non-empty value, e.g., ["apiKey" => "value1", "sender_id" => "value2"]');
   }
  }

  return $keys;
 }



 protected function checkMnotifyKeysfromEnvironment()
 {
  if (!env('MNOTIFY_API_KEY') || !env('MNOTIFY_SENDER_ID')) {
   throw new \InvalidArgumentException('Mnotify API key and/or sender ID not set in environment variables. Please set them in your .env file, e.g., MNOTIFY_API_KEY=your_api_key_here and MNOTIFY_SENDER_ID=your_sender_id_here');
  }
 }

 protected function checkMnotifyVersion2KeysfromEnvironment()
 {
  if (!env('MNOTIFY_API_KEY_V2') || !env('MNOTIFY_SENDER_ID_V2')) {
   throw new \InvalidArgumentException('Mnotify version 2 API key and/or sender ID not set in environment variables. Please set them in your .env file, e.g., MNOTIFY_API_KEY=your_api_key_here and MNOTIFY_SENDER_ID=your_sender_id_here');
  }
 }

   /**
  * Sends bulk messages to multiple contacts using WhatsApp.
  *
  * Example usage:
  * ```
  * $contactsAndMessages = [
  *     'recipient1' => 'message1',
  *     'recipient2' => 'message2', 
  * ];
  *
  * $responses = WhatsApp::sendBulk($contactsAndMessages);
  * ```
  *
  * @param array $contactsAndMessages An associative array where the keys are the contacts and the values are the messages.
  * @return array An associative array where the keys are the contacts and the values are the responses from sending the messages.
  */
 protected function mnotifyBulkSMSListValidator($list){
  if (!is_array($list) || empty($list)) {
   throw new \InvalidArgumentException('Invalid list provided. Please provide an array of key-value pairs, e.g., ["recipient1" => "message1", "recipient2" => "message2"]');
  }

  foreach ($list as $key => $value) {
   if (!isset($value) || trim($value) === '') {
    throw new \InvalidArgumentException('Invalid key-value pair provided. Each key must have a non-empty value, e.g., ["recipient1" => "message1", "recipient2" => "message2"]');
   }
  }

  return $list;
 }

 protected function mnotifySenderIDvalidator($id, $purpose){
  // purpose and name is required
  if (!isset($id) || !isset($purpose) || trim($id) === '' || trim($purpose) === '') {
   throw new \InvalidArgumentException('Sender name and purpose is required eg. Sender name: Bundana,  purpose : reason for registration');
  }
  // Sender ID to be registered. Must be at most 11 characters
  if (strlen($id) > 11) {
   throw new \InvalidArgumentException('Invalid sender ID provided. Must be at most 11 characters');
  }
 }
}
