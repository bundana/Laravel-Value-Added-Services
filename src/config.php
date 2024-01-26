<?php

return [
 'api_key' => env('MNOTIFY_API_KEY', ''),
 'api_api_secret' => env('MNOTIFY_API_KEY_SECRET', ''),
 'api_sender_id' => env('MNOTIFY_SENDER_ID', ''),

 // Hubtel Payment Gateway Credentials
 'hubtel_client_id' => env('HUBTEL_BO_USERNAME', ''),
 'hubtel_client_secret' => env('HUBTEL_BO_PASSWORD', ''),
 'hubtel_merchant_account_number' => env('HUBTEL_MERCHANT_BO_ACCOUNT_NUMBER', ''),

];
