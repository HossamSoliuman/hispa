<?php

namespace App\Service\SMS;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TweetSMSService
{
    private $apiKey;

    private $user;

    private $pass;

    private $sender;

    // Constructor to initialize the class with required values
    public function __construct()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $this->apiKey = $settings['api_key_sms'] ?? 'ed728ae2dc1af45a9b4e196d8ee33160';
        $this->user = $settings['user_sms'] ?? 'TEST';
        $this->pass = $settings['pass_sms'] ?? '123456';
        $this->sender = $settings['sender_name'] ?? 'TweetTEST';
    }

    // Function to send SMS
    public function sendSms($to, $message, $sender = null)
    {

        // Define the URL for sending SMS
        $url = 'https://www.tweetsms.ps/api.php/office/sendsms';

        // Send a POST request to the API with the api_key
        $response = Http::post($url, [
            'api_key' => $this->apiKey, // Assuming $this->api_key contains your API key
            'sender' => $this->sender,
            'message' => $message,
            'to' => $to,
            'groups' => '',
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            return $this->handleSmsResponse($response->body());
            //            Log::info("Message step 2");
        } else {
            return 'Failed to send SMS. Status Code: '.$response->status();
        }
    }

    // Function to handle and parse the API response

    // Function to handle and parse the API response

    public function getSmsBalance2()
    {
        // Define the URL for getting the balance
        $url = 'https://www.tweetsms.ps/api.php';

        // Send the request to the API with the necessary query parameters
        $response = Http::get($url, [
            'comm' => 'chk_balance',
            'user' => $this->sender,
            'pass' => $this->pass,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Parse and return the balance from the response

            return $this->handleBalanceResponse($response->body());
        } else {
            // Return an error message if the request failed
            return 'Failed to retrieve balance. Status Code: '.$response->status();
        }
    }

    public function getSmsBalance()
    {
        // Define the new URL for getting the balance
        $url = 'https://www.tweetsms.ps/api.php/office/chk_balance';

        // Send a POST request to the API with the api_key
        $response = Http::post($url, [
            'api_key' => $this->apiKey, // Assuming $this->api_key contains your API key
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Parse and return the balance from the response
            return $this->handleBalanceResponse($response->body());

        } else {
            // Return an error message if the request failed
            return 'Failed to retrieve balance. Status Code: '.$response->status();
        }
    }

    // Function to handle and parse the API response
    public function handleBalanceResponse($responseBody)
    {
        // Decode the JSON response into an associative array
        $response = json_decode($responseBody, true);

        // Check if the response contains the 'code' field
        if (isset($response['code'])) {
            $code = $response['code'];
            $balance = isset($response['balance']) ? $response['balance'] : null;

            // Switch based on the response code
            switch ($code) {
                case 999:
                    return " الرصيد = {$balance} رسالة.";
                case -100:
                    return 'خطأ: المعلمات مفقودة.';
                case -110:
                    return 'خطأ: اسم المستخدم أو كلمة المرور غير صحيحة.';
                default:
                    return "خطأ غير معروف: الرمز {$code}.";
            }
        } else {
            return 'تنسيق الاستجابة غير صالح.';
        }
    }

    // Function to handle and parse the API response
    private function handleSmsResponse($responseBody)
    {
        $response = json_decode($responseBody, true);
        if (isset($response['code'])) {
            $code = $response['code'];
            switch ($code) {
                case '999':
                    return 'SMS sent successfully!';
                case '-100':
                    return 'Error: Missing parameters.';
                case '-110':
                    return 'Error: Invalid username or password.';
                case '-111':
                    return 'Error: Account not activated.';
                case '-112':
                    return 'Error: Blocked user.';
                case '-115':
                    return 'Error: Invalid sender.';
                case '-116':
                    return 'Error: Invalid sender name.';
                case '-114':
                    return 'Error: Site sending case is stopped.';
                case '-120':
                    return 'Error: No numbers found.';
                case '-124':
                    return 'Error: You have no enough credit to send that message.';
                case '-126':
                    return 'Error: Cannot send right now (may be you are sending from another source).';
                default:
                    return "Unknown error: {$response}";
            }
        }
    }
}
