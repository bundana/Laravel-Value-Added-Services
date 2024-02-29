<?php

namespace Bundana\Services\Requests;

use Illuminate\Support\Facades\Http;

use Bundana\Services\Messaging\Validator;

class SMSHttps extends Validator
{

    public const REQUEST_VERSION = '' ?: 'v1';
    public const API_URL = 'https://apps.mnotify.net/smsapi';
    public const API_V2_URL = 'https://api.mnotify.com/api/';

    public function SMSVersion1()
    {


    }

    public function SMSBalance($version = 'v1')
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

}
