<?php

namespace App\Observers;

use App\Models\FishStock;
use App\Models\FishStockHistory;
use App\Models\TripDetail;

class FishStockObserver
{
    /**
     * Handle the FishStock "created" event.
     */
    public function created(FishStock $stock): void
    {
        $user = auth()->user();

        FishStockHistory::create([
            'fish_stock_id' => $stock->id,
            'fish_id' => $stock->fish_id,
            'operation_type' => 'add',
            'changed_weight' => $stock->weight,
            'after_weight' => $stock->weight,
            'notes' => $stock->trip->name.'اضافة للمخزون من رحلة ',
            'done_by' => $stock->added_by ?? $stock->corrected_by,
        ]);

        TripDetail::create([
            'trip_id' => $stock->trip->id,
            'fish_id' => $stock->fish_id,
            'fish_name' => $stock->fish?->scientific_name ?? 'test',
            'weight' => $stock->weight,
            'quantity' => $stock->quantity,
            'quantity_captain' => $stock->quantity,
            'weight_captain' => $stock->weight,
            //            'quantity_counter' => $stock->quantity_counter,
            //            'weight_counter' => $stock->weight_counter,
            'added_by' => $user->id,
            'notes' => $stock->notes,

        ]);

    }

    /**
     * Handle the FishStock "updated" event.
     */
    public function updated(FishStock $stock): void
    {
        $originalWeight = $stock->getOriginal('weight');
        $newWeight = $stock->weight;
        $diff = abs($newWeight - $originalWeight);

        if (round($diff, 3) !== 0.0) {
            FishStockHistory::create([
                'fish_stock_id' => $stock->id,
                'fish_id' => $stock->fish_id,
                'operation_type' => 'update',
                'changed_weight' => $diff,
                'before_weight' => $originalWeight,
                'after_weight' => $newWeight,
                'notes' => 'تعديل على الكمية',
                'done_by' => $stock->added_by ?? $stock->corrected_by ?? auth()->id(),
            ]);
        }

        $user = auth()->user();
        if (! $user) {
            return;
        }

        $role = $user->role;

        // جلب سجل التفاصيل الموجود
        $tripDetail = TripDetail::where('trip_id', $stock->trip_id)
            ->where('fish_id', $stock->fish_id)
            ->first();

        if ($tripDetail) {
            $data = [
                'fish_name' => $stock->fish?->scientific_name ?? 'test',
                'weight' => $stock->weight,
                'quantity' => $stock->quantity,
                'notes' => $stock->notes,
            ];

            if ($role === 'captain') {
                $data['quantity_captain'] = $stock->quantity;
                $data['weight_captain'] = $stock->weight;
                $data['added_by'] = $user->id;
            } elseif ($role === 'counter') {
                $data['quantity_counter'] = $stock->quantity_counter;
                $data['weight_counter'] = $stock->weight_counter;
                $data['corrected_by'] = $user->id;
            }

            $tripDetail->update($data);
        }
    }

    /**
     * Handle the FishStock "deleted" event.
     */
    public function deleted(FishStock $stock): void
    {
        FishStockHistory::create([
            'fish_stock_id' => $stock->id,
            'fish_id' => $stock->fish_id,
            'operation_type' => 'delete',
            'changed_weight' => -$stock->weight,
            'before_weight' => $stock->weight,
            'after_weight' => 0,
            'notes' => 'حذف السجل',
            'done_by' => $stock->added_by ?? $stock->corrected_by,
        ]);

        $user = auth()->user();
        if (! $user) {
            return;
        }

        $role = $user->role;

        // جلب سجل TripDetail المرتبط بنفس الرحلة ونوع السمك
        $tripDetail = TripDetail::where('trip_id', $stock->trip_id)
            ->where('fish_id', $stock->fish_id)
            ->first();

        if ($tripDetail) {
            $data = [];

            if ($role === 'captain') {
                $data['quantity_captain'] = 0;
                $data['weight_captain'] = 0;
            }

            if ($role === 'counter') {
                $data['quantity_counter'] = 0;
                $data['weight_counter'] = 0;
            }

            // إذا أردت حذف الوزن الأساسي والكمية أيضاً:
            $data['weight'] = 0;
            $data['quantity'] = 0;
            $data['notes'] = 'تم إلغاء الصنف بواسطة '.$user->name;

            $tripDetail->update($data);
        }
    }

    /**
     * Handle the FishStock "restored" event.
     */
    public function restored(FishStock $fishStock): void
    {
        //
    }

    /**
     * Handle the FishStock "force deleted" event.
     */
    public function forceDeleted(FishStock $fishStock): void
    {
        //
    }
}
