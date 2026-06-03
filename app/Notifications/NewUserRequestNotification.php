<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewUserRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $data;

    protected $channels;

    protected $sms;

    public function __construct($data, $channels = [])
    {
        $this->data = $data;
        $this->channels = $channels;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        // Dynamically return channels without including 'sms'
        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    //    public function toMail($notifiable)
    //    {
    //
    //    }

    /**
     * Get the array representation of the notification (for database).
     */
    public function toDatabase($notifiable)
    {
        return $this->getData();
    }

    /**
     * Helper method to format data for database notifications.
     */
    public function getData()
    {
        return [
            'title' => $this->data['title'],
            'body' => $this->data['body'],
            'url' => $this->data['url'] ?? null,
            'sender_name' => $this->data['sender_name'],
            'channels' => $this->channels,
        ];
    }

    public function toFirebase($notifiable)
    {
        return [
            'title' => $this->data['title'],
            'body' => $this->data['body'],
            'url' => $this->data['url'] ?? null,
            'sender_name' => $this->data['sender_name'],
            'fcm_token' => $notifiable->fcm_token,
        ];
    }
}
