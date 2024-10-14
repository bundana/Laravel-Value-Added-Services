namespace Bundana\Services\Messaging;

use Bundana\Services\Requests\SMSHttps;
use Illuminate\Support\Facades\Log;

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
    private $version = 'v1'; // Default to v1
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
     * @param string $message The message to be sent.
     * @return $this The Mnotify instance.
     */
    public function message($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Sets API version (v1 or v2).
     *
     * @param string $version The API version to use.
     * @return $this The Mnotify instance.
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Set new API keys dynamically.
     *
     * @param array $newKeys The API keys to use.
     * @return $this The Mnotify instance.
     */
    public function newKeys($newKeys)
    {
        $this->newKeys = $newKeys;
        return $this;
    }

    /**
     * Sends the notification through the Mnotify API.
     *
     * @return bool|string The API response or error message.
     */
    public function send($scheduleDate = null)
    {
        $this->schedule_date = $scheduleDate;
        return $this->smsApiRequest();
    }

    /**
     * Send the SMS via Mnotify API.
     *
     * @return bool|string The API response or error message.
     */
    private function smsApiRequest()
    {
        $this->setApiKeys();
        $this->buildQueryParams();

        $https = new SMSHttps();
        $response = $https->MnotifySMSRequest($this->query_params, $this->version);
        $response = json_decode($response, true);

        if (isset($response['status']) && $response['status'] != 'success') {
            Log::error("Mnotify Error: " . json_encode($response));
            return json_encode(['success' => false, 'message' => SMSHttps::MnotifySMSErrorMessage($response['code'])]);
        }

        return json_encode(['success' => true, 'message' => SMSHttps::MnotifySMSErrorMessage($response['code']), 'code' => $response['code']]);
    }

    /**
     * Build the query parameters for the API request.
     */
    private function buildQueryParams()
    {
        $this->query_params = [
            'key' => $this->api_key,
            'recipient' => [$this->phone],
            'message' => $this->message,
            'sender' => $this->sender_key,
            'is_schedule' => !is_null($this->schedule_date),
            'schedule_date' => $this->schedule_date ?? '',
        ];
    }

    /**
     * Set API keys depending on version and environment.
     */
    private function setApiKeys()
    {
        if ($this->version === 'v2') {
            $this->api_key = env('MNOTIFY_API_KEY_V2', $this->newKeys['apiKey'] ?? '');
            $this->sender_key = env('MNOTIFY_SENDER_ID_V2', $this->newKeys['sender_id'] ?? '');
        } else {
            $this->api_key = env('MNOTIFY_API_KEY', $this->newKeys['apiKey'] ?? '');
            $this->sender_key = env('MNOTIFY_SENDER_ID', $this->newKeys['sender_id'] ?? '');
        }
    }

    /**
     * Send bulk SMS to multiple contacts.
     *
     * @param array $contactsAndMessages An array of contacts and their respective messages.
     * @return array The responses from sending the messages.
     */
    public static function sendBulk($contactsAndMessages, $version = 'v1')
    {
        $instance = new static();
        $instance->setVersion($version);
        $responses = [];

        foreach ($contactsAndMessages as $contact => $message) {
            $responses[$contact] = $instance->to($contact)->message($message)->send();
        }

        return $responses;
    }

    /**
     * Register a Sender ID.
     *
     * @param string $id The sender ID to register.
     * @param string $purpose The purpose of the registration.
     * @return string The API response.
     */
    public static function registerSenderID($id, $purpose)
    {
        $https = new SMSHttps();
        $response = $https->MnotifyReqisterSenderID($id, $purpose);
        $response = json_decode($response, true);

        if (isset($response['status']) && $response['status'] !== 'success') {
            Log::error("Mnotify Sender ID Error: " . json_encode($response));
            return json_encode(['success' => false, 'message' => $response['message']]);
        }

        return json_encode(['success' => true, 'message' => $response['message'], 'data' => $response['summary']]);
    }

    /**
     * Check SMS balance for the Mnotify service.
     *
     * @param string|null $version API version to use.
     * @return string The response including SMS balance or error message.
     */
    public static function SMSBalance($version = null)
    {
        $https = new SMSHttps();
        $response = json_decode($https->MnotifySMSBalanceRequest($version), true);

        if (isset($response['status']) && $response['status'] !== 'success') {
            Log::error("Mnotify Balance Error: " . json_encode($response));
            return json_encode(['success' => false, 'message' => $response['message']]);
        }

        return isset($response['sms_balance']) 
            ? json_encode(['success' => true, 'balance' => $response['sms_balance']])
            : json_encode(['success' => true, 'balance' => $response['balance'], 'bonus' => $response['bonus']]);
    }
}
