<?php

namespace Bundana\Services\Requests;

use Illuminate\Support\Facades\Http;

use Bundana\Services\Messaging\Validator;

class SMSHttps extends Validator
{

    public const API_URL = 'https://apps.mnotify.net/smsapi';
    public const API_V2_URL = 'https://api.mnotify.com/api/';

    public function MnotifySMSRequest($query_params = null, $version = null)
    {
        // Build the endpoint URL
        $endPoint = self::API_URL . '?' . http_build_query($query_params);

        // Use version 2 if specified, otherwise use version 1
        if (isset($version) && $version == 'v2') {
            $this->checkMnotifyVersion2KeysfromEnvironment();
            $apiKey = isset($query_params['key']) ? $query_params['key'] : env('MNOTIFY_API_KEY_V2');
            $endPoint = self::API_V2_URL . "sms/quick?key=" . $apiKey;
        }

        // Send the HTTP request and get the response
        $response = Http::post($endPoint, $query_params ?: []);

        // Return the response body
        return $response->body();
    }


    public function MnotifySMSBalanceRequest($version = 'v1')
    {
        $endPoint = self::API_URL . "/balance";
        $apiKey = env('MNOTIFY_API_KEY');
        $url = $endPoint . '?key=' . $apiKey;

        //use version 2 if set else use version 1
        if (isset($version) && $version == 'v2') {
            $this->checkMnotifyVersion2KeysfromEnvironment();
            $endPoint = self::API_V2_URL . "balance/sms";
            $apiKey = env('MNOTIFY_API_KEY_V2');
            $url = $endPoint . '?key=' . $apiKey;
        }

        $response = Http::get($url);
        return $response->body();
    }

    public function MnotifyReqisterSenderID($id, $purpose)
    {
        //validate
        $this->checkMnotifyVersion2KeysfromEnvironment();

        $validator = new Validator();
        $validator->mnotifySenderIDvalidator($id, $purpose);
        // Build the API request URL
        $query_params = [
            'sender_name' => $id,
            'purpose' => 'For Sending SMS Newsletters'
        ];
        $apiKey = env('MNOTIFY_API_KEY_V2', '');
        // Build the endpoint URL
        $endPoint = self::API_V2_URL . "senderid/register?key=" . $apiKey;

        // Send the HTTP request and get the response
        $response = Http::post($endPoint, $query_params ?: []);

        // Return the response body
        return $response->body();
    }

    /**
     * Get the error message for a specific error code.
     *
     * @param int $errorCode The error code.
     * @return string The corresponding error message.
     */
    public static function MnotifySMSErrorMessage($errorCode)
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
}
