# Bundana Laravel Value Added Services Package

The Bundana Laravel Value Added Services package is a Laravel service for sending SMS using Hubtel and Mnotify and Accepting messages using the Paystack, Hubtel API.

## Installation

To install the package, you can use Composer. Run the following command in your terminal:

```bash
composer require bundana/services
```

After installation, add the service provider to your config/app.php file:
```php
'providers' => [
    // ...
 \Bundana\Services\BundanaServiceProvider::class,
```
 
Publish the package configuration file:

```sh
 php artisan vendor:publish --provider="Bundana\Services\Messaging\BundanaServiceProvider" --tag="config"
```
This will create a `bundana-config.php` in your `config` folder of your laravel project file where you can set your Mnotify API keys.
Now that we have published a few new files to our application we need to reload them with the following command:

```sh
$ composer dump-autoload
```
## Configuration

You'll need to configure the package with your Mnotify, Hubtel and Paystack API keys. Open the `.env` file and add the following values:

-   `MNOTIFY_API_KEY=`:Your Mnotify API key.
-   `MNOTIFY_SENDER_ID= `: Your Mnotify sender ID.
-   `MNOTIFY_API_KEY_V2=`: Your Mnotify V2 API key.
-   `MNOTIFY_SENDER_ID_V2=`: Your Mnotify sender ID 
# Add Hubtel credentials if using the Hubtel Payment Gateway
-   `HUBTEL_API_KEY=your_Hubtel_API_key`
-   `HUBTEL_API_SECRET=your_Hubtel_API_secret`

# Add Paystack credentials if using Paystack
-   `PAYSTACK_PUBLIC_KEY=your_Paystack_public_key`
-   `PAYSTACK_SECRET_KEY=your_Paystack_secret_key`
Additionally, if you plan to use the Hubtel Payment Gateway, provide the required credentials.
## Usage

### Sending SMS via Mnotify

To send an SMS, you can use the `Mnotify` class. Here's an example:

  ```php
use Bundana\Services\Messaging\Mnotify;

Mnotify::to('recipient_phone_number')
    ->message('Your SMS message goes here')
    ->send();

```

To send an SMS using a new sender ID and API key:

  ```php
Mnotify::to('recipient_phone_number')
    ->message('Your SMS message goes here')
    ->newKeys(['apiKey' => 'ss', 'sender_id' =>'ss'])
    ->send();

```

### Sending Bulk SMS via Mnotify

To send bulk SMS, use the `sendBulk` method:
```php
    use Bundana\Services\Messaging\Mnotify;
    
    $contactsAndMessages = [
        'recipient1' => 'message1',
        'recipient2' => 'message2',
    ];

    $responses = Mnotify::sendBulk($contactsAndMessages);
  ```

### Checking Mnotify SMS Balance
To check the SMS balance:
```php
    use Bundana\Services\Messaging\Mnotify;

    // For version 1
    $response = Mnotify::checkSMSBalance();

    // For version 2
    $response = Mnotify::checkSMSBalance('v2');

```
## License

This package is open-sourced software licensed under the [MIT license](https://chat.openai.com/c/LICENSE).
