# Mnotify SMS API Integration

This package provides an easy-to-use interface for integrating the Mnotify SMS API into your PHP applications. The package allows you to send single and bulk SMS, check SMS balance, and register sender IDs with the Mnotify API.

## **Installation**

To use this package, make sure you have the following environment variables set in your `.env` file:

```env
MNOTIFY_API_KEY=your_api_key
MNOTIFY_SENDER_ID=your_sender_id
MNOTIFY_API_KEY_V2=your_api_key_v2
MNOTIFY_SENDER_ID_V2=your_sender_id_v2
```

### **Basic Usage**

### 1. **Sending a Single SMS**

To send a single SMS, you can use the `Mnotify::to()` method to specify the recipient’s phone number, followed by the `message()` method to set the SMS content. Finally, call `send()` to dispatch the message.

```php
use Bundana\Services\Messaging\Mnotify;

// Example of sending a single SMS
$response = Mnotify::to('1234567890') // recipient phone number
    ->message('Hello, this is a test message!') // message content
    ->send(); // send the message

// The $response will contain the success status and details of the send operation
echo $response;
```

### 2. **Checking SMS Balance**

To check your SMS balance, use the `SMSBalance()` method. This can be done for both API version 1 (default) and version 2.

```php
use Bundana\Services\Messaging\Mnotify;

// Example of checking balance
$response = Mnotify::SMSBalance(); // by default, checks using version 1

// If you need to check balance using version 2
$response_v2 = Mnotify::SMSBalance('v2');

// The $response will contain the SMS balance or an error message
echo $response;
```

### 3. **Sending Bulk SMS**

To send bulk SMS to multiple recipients, use the `sendBulk()` method. This method takes an associative array where the keys are phone numbers and the values are the corresponding messages to send.

```php
use Bundana\Services\Messaging\Mnotify;

// Example of sending bulk SMS
$contactsAndMessages = [
    '1234567890' => 'Message to recipient 1',
    '0987654321' => 'Message to recipient 2',
    '5555555555' => 'Message to recipient 3',
];

$response = Mnotify::sendBulk($contactsAndMessages); // sending using version 1 by default

// To send using version 2, pass the 'v2' as a second argument
$response_v2 = Mnotify::sendBulk($contactsAndMessages, 'v2');

// The $response will contain the success status and details of the send operation
print_r($response);
```

### 4. **Registering a Sender ID**

To register a sender ID with Mnotify, use the `registerSenderID()` method. This method takes two parameters: the sender ID and the purpose for registering the sender ID.

```php
use Bundana\Services\Messaging\Mnotify;

// Example of registering a sender ID
$senderId = 'MySenderID';
$purpose = 'Transactional Messages';

$response = Mnotify::registerSenderID($senderId, $purpose);

// The $response will contain the success status and details of the registration
echo $response;
```

### **Additional Features**

- **Set Custom API Keys**: You can dynamically set API keys using the `newKeys()` method if you need to override the environment variables.
  
  Example:
  
  ```php
  $customKeys = [
      'apiKey' => 'custom_api_key',
      'sender_id' => 'custom_sender_id',
  ];

  $response = Mnotify::to('1234567890')
      ->message('Hello, custom API keys!')
      ->newKeys($customKeys)
      ->send();
  ```

- **Scheduled SMS**: To schedule SMS for future delivery, you can pass a `schedule_date` to the `send()` method.
  
  Example:
  
  ```php
  $response = Mnotify::to('1234567890')
      ->message('This message will be sent later!')
      ->send(null, '2024-10-31 10:00:00');
  ```

### **Error Handling**

Each method returns a JSON-encoded response. If an error occurs, the response will include an error message and a status of `false`. 

Example error response:

```json
{
    "success": false,
    "message": "Invalid recipient number."
}
```

### **API Versioning**

By default, the package uses Mnotify API version 1 (`v1`). If you need to use API version 2, pass `'v2'` as a parameter when sending messages or checking the balance.

### **License**

This package is open-source and licensed under the MIT License.

---

This `README.md` file provides comprehensive documentation for using the Mnotify class, covering the key functionalities such as sending SMS, checking balance, bulk SMS, and registering sender IDs. You can further extend this document with additional details if required.
