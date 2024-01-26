<?php

namespace Bundana\Services\Messaging;

interface SMSProviderInterface
{
    const API_URL = '';
    public static function to($phone); 
    public function message($message);

    public function send();
}
