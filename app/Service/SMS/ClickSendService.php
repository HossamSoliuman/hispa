<?php

namespace App\Service\SMS;

use ClickSend\Api\SMSApi;
use ClickSend\Configuration;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ClickSendService
{
    protected $apiInstance;

    public function __construct()
    {
        // Initialize the Guzzle HTTP client
        $client = new Client;

        // Create the ClickSend configuration
        /** @phpstan-ignore-next-line */
        $config = Configuration::getDefaultConfiguration()
            ->setUsername(env('CLICKSEND_USERNAME'))
            ->setPassword(env('CLICKSEND_API_KEY'));

        // Initialize the SMSApi with Guzzle client and configuration
        /** @phpstan-ignore-next-line */
        $this->apiInstance = new SMSApi($client, $config);
    }

    public function sendMessage($receiver_number, $message_text)
    {
        // Create the SMS message
        /** @phpstan-ignore-next-line */
        $message = new SmsMessage;
        $message->setSource('php');
        $message->setBody($message_text);
        $message->setTo($receiver_number);

        // Create a message collection
        /** @phpstan-ignore-next-line */
        $sms_messages = new SmsMessageCollection;
        $sms_messages->setMessages([$message]);

        try {
            // Send the SMS message via the API
            $result = $this->apiInstance->smsSendPost($sms_messages);

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to send SMS via ClickSend: '.$e->getMessage());

            return false;
        }
    }
}
