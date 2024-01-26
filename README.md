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
    Bundana\Services\Messaging\BundanaServiceProvider::class,
],
```

After installation, add the service provider to your `config/app.php` file:

  ```php  'providers' => [
        // ...
        Bundana\Services\Messaging\BundanaServiceProvider::class,
    ],
```
Publish the package configuration file:

```sh
 php artisan vendor:publish --provider="Bundana\Services\Messaging\BundanaServiceProvider" --tag="config"
```
This will create a `config/bundana-config.php` file where you can set your Mnotify API keys.
Now that we have published a few new files to our application we need to reload them with the following command:

```sh
$ composer dump-autoload
```
## Configuration

You'll need to configure the package with your Mnotify API keys. Open the `config/bundana-config.php` file and update the following values:

-   `api_key`: Your Mnotify API key.
-   `api_api_secret`: Your Mnotify API secret.
-   `api_sender_id`: Your Mnotify sender ID.

Additionally, if you plan to use the Hubtel Payment Gateway, provide the required credentials.
## Usage

### Sending SMS

To send an SMS, you can use the `Mnotify` class. Here's an example:

  ```php
use  Bundana\Services\Messaging\Mnotify;
Mnotify::to('recipient_phone_number')
->message('Your SMS message goes here')
->send();
```
### Sending Bulk SMS

To send bulk SMS, use the `sendBulk` method:
```php
    use Bundana\Services\Messaging\Mnotify;
    
    $contactsAndMessages = [
        'recipient1' => 'message1',
        'recipient2' => 'message2',
    ];

    $responses = Mnotify::sendBulk($contactsAndMessages);
  ```

### Checking SMS Balance

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
