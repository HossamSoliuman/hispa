<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\Contact;
use App\Notifications\ContactNotification;
use Illuminate\Support\Facades\Notification;

class ContactObserver
{
    /**
     * Handle the Contact "created" event.
     */
    public function created(Contact $contact): void
    {
        $channels = ['firebase', 'database'];

        $data = [
            'contact_id' => $contact->id,
            'sender_name' => $contact->name ?? 'النظام',
            'title' => [
                'ar' => 'طلب اتصال جديد',
                'en' => 'New contact request',
            ],
            'body' => [
                'ar' => "تم تقديم طلب اتصال جديد من قبل {$contact->name}. الرجاء مراجعة تفاصيل الطلب والرد عليه.",
                'en' => "A new contact request has been submitted by {$contact->name}. Please review the details and respond.",
            ],
        ];

        $admins = Admin::whereJsonContains('roles_name', 'owner')->get();

        Notification::send($admins, new ContactNotification($data, $channels));
    }

    /**
     * Handle the Contact "updated" event.
     */
    public function updated(Contact $contact): void
    {
        $data = [
            'contact_id' => $contact->id,
            'sender_name' => auth()->user()->name ?? 'الإدارة',
            'title' => [
                'ar' => 'تم الرد على طلبك',
                'en' => 'Your request has been replied to',
            ],
            'body' => [
                'ar' => 'تم الرد على طلب الاتصال الخاص بك من قبل الإدارة، يرجى المراجعة.',
                'en' => 'Your contact request has been replied to by the management. Please review.',
            ],
        ];

        $channels = ['firebase', 'database'];

        $contact->user->notify(new ContactNotification($data, $channels));
    }

    /**
     * Handle the Contact "deleted" event.
     */
    public function deleted(Contact $contact): void
    {
        //
    }

    /**
     * Handle the Contact "restored" event.
     */
    public function restored(Contact $contact): void
    {
        //
    }

    /**
     * Handle the Contact "force deleted" event.
     */
    public function forceDeleted(Contact $contact): void
    {
        //
    }
}
