<?php

namespace App\Observers;

use App\Enums\TripStatus;
use App\Models\Admin;
use App\Models\Trip;
use App\Models\User;
use App\Notifications\TripNotification;

class TripObserver
{
    public function created(Trip $trip): void
    {
        if ($trip->captain) {
            $data = [
                'trip_id' => $trip->id,
                'sender_name' => auth()->user()?->name ?? 'النظام',
                'title' => [
                    'ar' => 'رحلة جديدة بانتظارك',
                    'en' => 'New trip waiting for you',
                ],
                'body' => [
                    'ar' => 'تمت إضافة رحلة جديدة. يرجى المراجعة.',
                    'en' => 'A new trip has been added. Please review.',
                ],
            ];

            $trip->captain->notify(new TripNotification($data, ['database']));
        }
    }

    public function updated(Trip $trip): void
    {
        $channels = ['database'];

        if ($trip->captain) {
            if ($trip->status === TripStatus::New || $trip->status === TripStatus::InProgress) {
                $data = [
                    'trip_id' => $trip->id,
                    'sender_name' => auth()->user()->name ?? 'النظام',
                    'title' => [
                        'ar' => 'رحلة جديدة بانتظارك',
                        'en' => 'New trip waiting for you',
                    ],
                    'body' => [
                        'ar' => "تمت إضافة الرحلة رقم #{$trip->number}. يرجى مراجعتها والبدء بالإجراءات.",
                        'en' => "Trip #{$trip->number} has been added. Please review and start procedures.",
                    ],
                ];
                $trip->captain->notify(new TripNotification($data, $channels));
            }

            if ($trip->status === TripStatus::InProgress) {
                $data = [
                    'trip_id' => $trip->id,
                    'sender_name' => $trip->captain->name ?? null,
                    'title' => [
                        'ar' => 'الرحلة قيد التنفيذ',
                        'en' => 'Trip in progress',
                    ],
                    'body' => [
                        'ar' => "بدأت الرحلة رقم #{$trip->number} من قِبل الكابتن.",
                        'en' => "Trip #{$trip->number} has started from the captain.",
                    ],
                ];
                $trip->captain->notify(new TripNotification($data, $channels));
            }

            if ($trip->status === TripStatus::Cancelled) {
                $data = [
                    'trip_id' => $trip->id,
                    'sender_name' => $trip->captain->name ?? null,
                    'title' => [
                        'ar' => 'تم إلغاء الرحلة',
                        'en' => 'Trip canceled',
                    ],
                    'body' => [
                        'ar' => "تم إلغاء الرحلة رقم #{$trip->number}.",
                        'en' => "Trip #{$trip->number} has been canceled.",
                    ],
                ];
                $trip->captain->notify(new TripNotification($data, $channels));
            }

            if ($trip->status === TripStatus::Finished) {
                $data = [
                    'trip_id' => $trip->id,
                    'sender_name' => $trip->captain->name ?? null,
                    'title' => [
                        'ar' => 'الرحلة مكتملة',
                        'en' => 'Trip completed',
                    ],
                    'body' => [
                        'ar' => "تم إكمال الرحلة رقم #{$trip->number} بنجاح.",
                        'en' => "Trip #{$trip->number} has been completed successfully.",
                    ],
                ];
                $trip->captain->notify(new TripNotification($data, $channels));
            }
        }

        if ($trip->status === TripStatus::Finished && $trip->counter_id === null) {
            $nearbyCounters = User::counterRole()
                ->where(function ($q) use ($trip) {
                    $q->where('region_id', $trip->region_id)
                        ->orWhere('governorate_id', $trip->governorate_id);
                })->get();

            foreach ($nearbyCounters as $counter) {
                $data = [
                    'trip_id' => $trip->id,
                    'sender_name' => auth()->user()->name ?? 'النظام',
                    'title' => [
                        'ar' => 'رحلة بانتظار العد',
                        'en' => 'Trip waiting for counting',
                    ],
                    'body' => [
                        'ar' => "الرحلة رقم #{$trip->number} جاهزة للعد وتقع ضمن منطقتك. يمكنك قبولها الآن.",
                        'en' => "Trip #{$trip->number} is ready for counting and is located in your area. You can accept it now.",
                    ],
                ];
                $counter->notify(new TripNotification($data, $channels));
            }
        }

        if ($trip->counter) {
            if ($trip->status === TripStatus::Counting) {
                $data = [
                    'trip_id' => $trip->id,
                    'sender_name' => $trip->counter->name ?? null,
                    'title' => [
                        'ar' => 'الرحلة قيد العد',
                        'en' => 'Trip in progress',
                    ],
                    'body' => [
                        'ar' => "بدأ عدّ الرحلة رقم #{$trip->number}. يرجى المتابعة واستكمال الإجراءات.",
                        'en' => "Trip #{$trip->number} has started. Please follow and complete the procedures.",
                    ],
                ];
                $trip->counter->notify(new TripNotification($data, $channels));
            }

            if ($trip->status === TripStatus::Counted) {
                $data = [
                    'trip_id' => $trip->id,
                    'sender_name' => $trip->counter->name ?? null,
                    'title' => [
                        'ar' => 'تم اكتمال العد',
                        'en' => 'Trip completed',
                    ],
                    'body' => [
                        'ar' => "اكتمل عدّ الرحلة رقم #{$trip->number} بنجاح.",
                        'en' => "Trip #{$trip->number} has been completed successfully.",
                    ],
                ];
                $trip->counter->notify(new TripNotification($data, $channels));
            }
        }

        if ($trip->owner) {
            if ($trip->status === TripStatus::ReadyToSell) {
                $data = [
                    'trip_id' => $trip->id,
                    'sender_name' => $trip->captain->name ?? null,
                    'title' => [
                        'ar' => 'الرحلة جاهزة للبيع',
                        'en' => 'Trip ready for sale',
                    ],
                    'body' => [
                        'ar' => "الرحلة رقم #{$trip->number} أصبحت جاهزة للبيع. يرجى المتابعة لاتخاذ اللازم.",
                        'en' => "Trip #{$trip->number} has become ready for sale. Please follow and take necessary actions.",
                    ],
                ];
                $trip->owner->notify(new TripNotification($data, $channels));
            }
        }

        if ($trip->status === TripStatus::Sold) {
            $admins = Admin::whereJsonContains('roles_name', 'owner')->get();
            foreach ($admins as $admin) {
                $data = [
                    'trip_id' => $trip->id,
                    'sender_name' => auth()->user()->name ?? 'النظام',
                    'title' => [
                        'ar' => 'تم إتمام الرحلة',
                        'en' => 'Trip completed',
                    ],
                    'body' => [
                        'ar' => "اكتملت جميع مراحل الرحلة رقم #{$trip->number} بنجاح.",
                        'en' => "All stages of trip #{$trip->number} have been completed successfully.",
                    ],
                ];
                $admin->notify(new TripNotification($data, $channels));
            }

            if ($trip->owner) {
                $data = [
                    'trip_id' => $trip->id,
                    'sender_name' => auth()->user()->name ?? 'النظام',
                    'title' => [
                        'ar' => 'تم إتمام الرحلة',
                        'en' => 'Trip completed',
                    ],
                    'body' => [
                        'ar' => "تم الانتهاء من جميع مراحل الرحلة رقم #{$trip->number} بنجاح. شكرًا لتعاونكم.",
                        'en' => "All stages of trip #{$trip->number} have been completed successfully. Thank you for your cooperation.",
                    ],
                ];
                $trip->owner->notify(new TripNotification($data, $channels));
            }
        }
    }

    public function deleted(Trip $trip): void {}

    public function restored(Trip $trip): void {}

    public function forceDeleted(Trip $trip): void {}
}
