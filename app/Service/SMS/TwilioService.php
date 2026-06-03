<?php

namespace App\Service\SMS;

use Twilio\Rest\Client;

class TwilioService
{
    public function sendMessage($receiver_number, $message_text)
    {
        $receiverNumber = $receiver_number;
        $message = $message_text;

        try {

            $account_sid = getenv('TWILIO_SID');
            $auth_token = getenv('TWILIO_TOKEN');
            $twilio_number = getenv('TWILIO_FROM');
            /** @phpstan-ignore-next-line */
            $client = new Client($account_sid, $auth_token);

            $client->messages->create($receiverNumber, [
                'from' => $twilio_number,
                'body' => $message,
            ]);

            dd('SMS Sent Successfully.'.$client);

        } catch (\Exception $e) {
            dd('Error: '.$e->getMessage());
        }
    }
}
