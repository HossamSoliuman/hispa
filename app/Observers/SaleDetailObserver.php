<?php

namespace App\Observers;

use App\Models\FishStockHistory;
use App\Models\SaleDetail;

class SaleDetailObserver
{
    /**
     * Handle the SaleDetail "created" event.
     */
    public function created(SaleDetail $detail): void
    {
        $sale = $detail->sale;
        FishStockHistory::create([
            'fish_stock_id' => null,
            'fish_id' => $detail->fish_id,
            'operation_type' => 'sale',
            'changed_weight' => -$detail->weight,
            'notes' => 'بيع كمية من الصنف',
            'done_by' => $sale->seller_id,
        ]);
    }

    /**
     * Handle the SaleDetail "updated" event.
     */
    public function updated(SaleDetail $detail): void
    {
        $sale = $detail->sale;

        $oldQty = $detail->getOriginal('weight');
        $newQty = $detail->weight;
        $diff = abs($newQty - $oldQty);

        if ($diff !== 0) {
            FishStockHistory::create([
                'fish_stock_id' => null,
                'fish_id' => $detail->fish_id,
                'operation_type' => 'sale_update',
                'changed_weight' => -$diff,
                'before_weight' => $oldQty,
                'after_weight' => $newQty,
                'notes' => 'تعديل على كمية البيع',
                'done_by' => $sale->seller_id,
            ]);
        }
    }

    /**
     * Handle the SaleDetail "deleted" event.
     */
    public function deleted(SaleDetail $detail): void
    {
        $sale = $detail->sale;

        FishStockHistory::create([
            'fish_stock_id' => null,
            'fish_id' => $detail->fish_id,
            'operation_type' => 'sale_delete',
            'changed_weight' => $detail->weight,
            'notes' => 'حذف عملية بيع واسترجاع الكمية',
            'done_by' => $sale->seller_id,
        ]);
    }

    /**
     * Handle the SaleDetail "restored" event.
     */
    public function restored(SaleDetail $saleDetail): void
    {
        //
    }

    /**
     * Handle the SaleDetail "force deleted" event.
     */
    public function forceDeleted(SaleDetail $saleDetail): void
    {
        //
    }
}
