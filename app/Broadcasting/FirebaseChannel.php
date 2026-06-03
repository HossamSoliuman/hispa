<?php

namespace App\Broadcasting;

use App\Service\Firebase\FirebaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class FirebaseChannel
{
    private $firebase;

    public function __construct()
    {
        $this->firebase = new FirebaseNotification;
    }

    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toFirebase')) {
            $firebaseData = $notification->toFirebase($notifiable);

            $fcmToken = (string) ($firebaseData['fcm_token'] ?? '');
            $title = (string) ($firebaseData['title'] ?? '');
            $body = (string) ($firebaseData['body'] ?? '');

            if (empty($fcmToken)) {
                Log::warning('FCM token is missing. Notification not sent.');

                return;
            }

            $response = $this->firebase->sendNotificationFirebase($fcmToken, $title, $body);

            if ($response) {
                Log::info('Firebase Notification Sent: '.json_encode($response));
            } else {
                Log::error('Firebase Notification Failed to send.');
            }
        }
    }
}
