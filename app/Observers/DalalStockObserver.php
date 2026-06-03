<?php

namespace App\Observers;

use App\Models\DalalStock;
use App\Notifications\DalalStockNotification;

class DalalStockObserver
{
    /**
     * Handle the DalalStock "created" event.
     */
    public function created(DalalStock $dalalStock): void
    {
        if ($dalalStock->owner) {
            if ($dalalStock->status == 1) {

                $channels = ['firebase', 'database'];

                // إشعار للصيّاد
                $data_owner = [
                    'dalal_stock_id' => $dalalStock->id,
                    'sender_name' => $dalalStock->owner->name ?? 'النظام',
                    'title' => [
                        'ar' => 'تمت إضافة المخزون إلى الدلال',
                        'en' => 'Stock added to the dealer',
                    ],
                    'body' => [
                        'ar' => "تمت إضافة المخزون إلى الدلال {$dalalStock->dalal->name} بنجاح.",
                        'en' => "Stock has been added to the dealer {$dalalStock->dalal->name} successfully.",
                    ],

                ];

                // إشعار للدلال
                $data_dalal = [
                    'dalal_stock_id' => $dalalStock->id,
                    'sender_name' => auth()->user()->name,
                    'title' => [
                        'ar' => 'مخزون جديد بانتظارك',
                        'en' => 'New stock waiting for you',
                    ],
                    'body' => [
                        'ar' => "تمت إضافة مخزون جديد من قِبل {$dalalStock->owner->name}. يرجى المراجعة.",
                        'en' => "A new stock has been added by {$dalalStock->owner->name}. Please review.",
                    ],
                ];

                $dalalStock->owner->notify(new DalalStockNotification($data_owner, $channels));
                $dalalStock->dalal->notify(new DalalStockNotification($data_dalal, $channels));
            }
        }

    }

    /**
     * Handle the DalalStock "updated" event.
     */
    public function updated(DalalStock $dalalStock): void
    {
        if ($dalalStock->owner) {
            if ($dalalStock->status == 1) {

                $channels = ['firebase', 'database'];

                // إشعار للصيّاد
                $data_owner = [
                    'dalal_stock_id' => $dalalStock->id,
                    'sender_name' => $dalalStock->owner->name ?? 'النظام',
                    'title' => [
                        'ar' => 'تمت إضافة المخزون إلى الدلال',
                        'en' => 'Stock added to the dealer',
                    ],
                    'body' => [
                        'ar' => "تمت إضافة المخزون إلى الدلال {$dalalStock->dalal->name} بنجاح.",
                        'en' => "Stock has been added to the dealer {$dalalStock->dalal->name} successfully.",
                    ],
                ];

                // إشعار للدلال
                $data_dalal = [
                    'dalal_stock_id' => $dalalStock->id,
                    'sender_name' => auth()->user()->name,
                    'title' => [
                        'ar' => 'مخزون جديد بانتظارك',
                        'en' => 'New stock waiting for you',
                    ],
                    'body' => [
                        'ar' => "تمت إضافة مخزون جديد من قِبل {$dalalStock->owner->name}. يرجى المراجعة.",
                        'en' => "A new stock has been added by {$dalalStock->owner->name}. Please review.",
                    ],
                ];

                $dalalStock->owner->notify(new DalalStockNotification($data_owner, $channels));
                $dalalStock->dalal->notify(new DalalStockNotification($data_dalal, $channels));
            }
        }
    }

    /**
     * Handle the DalalStock "deleted" event.
     */
    public function deleted(DalalStock $dalalStock): void
    {
        //
    }

    /**
     * Handle the DalalStock "restored" event.
     */
    public function restored(DalalStock $dalalStock): void
    {
        //
    }

    /**
     * Handle the DalalStock "force deleted" event.
     */
    public function forceDeleted(DalalStock $dalalStock): void
    {
        //
    }
}
