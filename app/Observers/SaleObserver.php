<?php

namespace App\Observers;

use App\Models\Sale;
use App\Notifications\SaleNotification;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        $channels = ['firebase', 'database'];

        if ($sale->status == 2) {

            $data = [
                'sale_id' => $sale->id,
                'sender_name' => $sale->seller->name ?? 'النظام',
                'title' => [
                    'ar' => 'اكتملت عملية البيع',
                    'en' => 'Sale completed',
                ],
                'body' => [
                    'ar' => "تم إتمام عملية البيع رقم #{$sale->number} بنجاح. شكرًا لجهودك.",
                    'en' => "Sale #{$sale->number} has been completed successfully. Thank you for your efforts.",
                ],

            ];

            $sale->seller->notify(new SaleNotification($data, $channels));
        }

    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        $channels = ['firebase', 'database'];

        if ($sale->status == 2) {

            $data = [
                'sale_id' => $sale->id,
                'sender_name' => $sale->seller->name ?? 'النظام',
                'title' => [
                    'ar' => 'اكتملت عملية البيع',
                    'en' => 'Sale completed',
                ],
                'body' => [
                    'ar' => "تم إتمام عملية البيع رقم #{$sale->number} بنجاح. شكرًا لجهودك.",
                    'en' => "Sale #{$sale->number} has been completed successfully. Thank you for your efforts.",
                ],
            ];

            $sale->seller->notify(new SaleNotification($data, $channels));
        }
    }

    /**
     * Handle the Sale "deleted" event.
     */
    public function deleted(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "restored" event.
     */
    public function restored(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "force deleted" event.
     */
    public function forceDeleted(Sale $sale): void
    {
        //
    }
}
