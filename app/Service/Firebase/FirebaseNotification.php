<?php

namespace App\Service\Firebase;

use Illuminate\Support\Facades\Log;

class FirebaseNotification
{
    public function sendNotificationFirebase($fcm_token, $title, $body)
    {
        try {
            $serviceAccountPath = storage_path('app/public/hawat-app-service.json');
            $credentials = json_decode(file_get_contents($serviceAccountPath), true);

            $client = new \Google_Client;
            $client->setAuthConfig($serviceAccountPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $tokenData = $client->fetchAccessTokenWithAssertion();
            if (isset($tokenData['error'])) {
                throw new \Exception('Error fetching access token: '.$tokenData['error_description']);
            }

            $token = $tokenData['access_token'];

            $data = [
                'message' => [
                    'token' => $fcm_token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ],
            ];

            $dataString = json_encode($data);

            $headers = [
                'Authorization: Bearer '.$token,
                'Content-Type: application/json',
            ];

            $url = 'https://fcm.googleapis.com/v1/projects/'.$credentials['project_id'].'/messages:send';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                curl_close($ch);
                throw new \Exception('CURL error: '.$error_msg);
            }

            curl_close($ch);

            $responseDecoded = json_decode($response, true);

            if (isset($responseDecoded['error'])) {
                throw new \Exception('Firebase Notification failed: '.$responseDecoded['error']['message']);
            }

            Log::info('Firebase Notification sent successfully: '.$response);

            // ✅ RETURN SUCCESS RESPONSE
            return $responseDecoded;
        } catch (\Exception $e) {
            Log::error('Failed to send Firebase notification: '.$e->getMessage());

            // ✅ RETURN ERROR MESSAGE
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
