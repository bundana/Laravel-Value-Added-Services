<?php
namespace Bundana\Services\Payment\Paystack;

class Paystack
{
 protected $testSecretKey;
 protected $testPublicKey;
 protected $liveSecretKey;
 protected $livePublicKey;

 const API_BASE_URL = "https://api.paystack.co";

 public static function testPayment($data)
 {
  $url = self::API_BASE_URL . "/transaction/initialize";

  $fields = [
   'email' => $data['email'],
   'amount' => $data['amount'],
   'currency' => isset($data['currency']) ? $data['currency'] : 'your_integration_currency',
   'reference' => $data['reference'],
   'callback_url' => isset($data['callback_url']) ? $data['callback_url'] : null,
   'plan' => isset($data['plan']) ? $data['plan'] : null,
   'invoice_limit' => isset($data['invoice_limit']) ? $data['invoice_limit'] : null,
   'metadata' => isset($data['metadata']) ? $data['metadata'] : null,
   'channels' => isset($data['channels']) ? $data['channels'] : null,
   'split_code' => isset($data['split_code']) ? $data['split_code'] : null,
   'subaccount' => isset($data['subaccount']) ? $data['subaccount'] : null,
   'transaction_charge' => isset($data['transaction_charge']) ? $data['transaction_charge'] : null,
   'bearer' => isset($data['bearer']) ? $data['bearer'] : 'account',
  ];

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
   'Authorization: Bearer YOUR_API_KEY', // Replace with your actual API key
   'Content-Type: application/json',
  ]);

  $response = curl_exec($ch);
  curl_close($ch);

  // Handle the API response as needed
  $decoded_response = json_decode($response, true);
 }

 private function apiRequest($data)
 {
  $ch = curl_init($data);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
   'Authorization: Bearer YOUR_API_KEY', // Replace with your actual API key
   'Content-Type: application/json',
  ]);

  $response = curl_exec($ch);
  curl_close($ch);

  // Handle the API response as needed
  $decoded_response = json_decode($response, true);
 }
}