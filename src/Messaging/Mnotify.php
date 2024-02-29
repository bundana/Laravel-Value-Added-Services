<?php

namespace Bundana\Services\Messaging;

use Bundana\Services\Requests\SMSHttps;

/**
 * This class represents the Mnotify messaging service.
 * It provides methods to send SMS messages using the Mnotify API.
 */
class Mnotify extends Validator implements SMSProviderInterface
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
    public function smsApiRequest($version = 'v1')
    {
        if (isset($this->newKeys) && !isset($version) && $version != 'v2') {
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

        if ($version == 'v2') {
            $this->checkMnotifyVersion2KeysfromEnvironment();
            $this->api_key = env('MNOTIFY_API_KEY_V2', '');
            $this->sender_key = env('MNOTIFY_SENDER_ID_V2', '');
            // Build the API request URL
            $query_params = [
                'key' => $this->api_key,
                'recipient' => [$this->phone],
                'message' => $this->message,
                'sender' => $this->sender_key,
                'is_schedule' => false,
                'schedule_date' => ''
            ];
        }

        $https = new SMSHttps();
        $https = $https->MnotifySMSRequest($query_params, $version);
        $response = json_decode($https, true);

        if (isset($response['status']) && $response['status'] != 'success') {
            return json_encode(['success' => false, 'message' => SMSHttps::MnotifySMSErrorMessage($response['code'])]);
        }

        return json_encode(['success' => true, 'message' => SMSHttps::MnotifySMSErrorMessage($response['code']), 'code' => $response['code']]);

    }

    /**
     * Sends the notification.
     *
     * @return bool Returns true if the notification is successfully sent, false otherwise.
     */
    public function send($version = 'v1', $date = null)
    {
        $this->schedule_date = $date;
        // For now, return true as a placeholder for a successful send
        return $this->smsApiRequest($version);
    }

    /**
     * Sends bulk SMS messages to multiple contacts.
     *
     * @param array $contactsAndMessages An associative array where the keys are the contacts and the values are the messages.
     * @return array An associative array where the keys are the contacts and the values are the responses from sending the messages.
     */
    public static function sendBulk($contactsAndMessages, $version = 'v1')
    {
        $instance = new static();
        $instance->mnotifyBulkSMSListValidator($contactsAndMessages);
        $responses = [];

        foreach ($contactsAndMessages as $contact => $message) {
            $responses[$contact] = $instance->to($contact)->message($message)->send($version);
        }
        return $responses;
    }


    /**
     * Register Sender ID
     *
     * @param string $id The sender ID to be registered.
     * @param string $purpose The purpose for registering the sender ID.
     * @return string The response from the API request.
     */
    public static function registerSenderID($id, $purpose)
    {
        $https = new SMSHttps();
        $response = $https->MnotifyReqisterSenderID($id, $purpose);
        $response = json_decode($response, true);
        if (isset($response['status']) && $response['status'] !== 'success') {
            return json_encode(['success' => false, 'message' => $response['message']]);
        }

        return json_encode(['success' => true, 'message' => $response['message'], 'data' => $response['summary']]);

    }

    /**
     * Checks the SMS balance for the Mnotify service.
     *
     * @param string|null $version The version of the API to use. If set to 'v2', it uses version 2, otherwise it uses version 1.
     * @return string The JSON-encoded response containing the success status and the SMS balance or error message.
     */
    public static function SMSBalance($version = null)
    {
        $https = new SMSHttps();
        $https = $https->MnotifySMSBalanceRequest($version);
        $response = json_decode($https, true);

        if (isset($response['status']) && $response['status'] !== 'success') {
            return json_encode(['success' => false, 'message' => $response['message']]);
        }

        if (isset($response['status']) && $response['status'] == 'success' && isset($response['sms_balance'])) {
            return json_encode(['success' => true, 'balance' => $response['sms_balance']]);
        } elseif (isset($response['status']) && $response['status'] == 'success' && isset($response['balance'])) {
            return json_encode(['success' => true, 'balance' => $response['balance'], 'bonus' => $response['bonus']]);
        }

    }
}
