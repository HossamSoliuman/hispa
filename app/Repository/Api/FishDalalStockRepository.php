<?php

namespace App\Repository\Api;

use App\Http\Resources\DalalStockResource;
use App\Interfaces\CRUD;
use App\Models\DalalStock;
use App\Models\DalalStockDetail;
use App\Models\FishStock;
use App\Models\Trip;
use App\Traits\DalalStockStatusChecker;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Support\Facades\DB;

class FishDalalStockRepository implements CRUD
{
    use DalalStockStatusChecker, RespondsWithHttpStatus;

    public function getList($request)
    {

        $user = request()->user();
        $user_id = $user->id;
        $query = DalalStock::where('owner_id', $user_id)->with(['dalal', 'owner', 'trip', 'details']);

        // فلترة حسب رقم الرحلة
        if ($request->filled('trip_id')) {
            $query->where('trip_id', $request->trip_id);
        }

        // فلترة حسب الدلال
        if ($request->filled('dalal_id')) {
            $query->where('dalal_id', $request->dalal_id);
        }

        $stocks = $query->latest()->paginate(20);

        return $this->success(__('api.list_success'), paginationResult(DalalStockResource::collection($stocks)), 200);
    }

    public function updateStaus($request)
    {

        $user = $request->user();

        $dalalStockId = $request->input('dalal_stock_id');
        $dalalStock = DalalStock::with('details')->find($dalalStockId);

        if (! $dalalStock) {
            return $this->failure(__('api.stock_not_found'), [], 404);
        }

        $currentStatus = $dalalStock->status;
        $newStatus = 1;

        // ✅ إذا كان يريد تغيير الحالة إلى 1
        if ($currentStatus != 1 && $newStatus == 1) {

            // ✅ افحص الكميات من العلاقة مع details
            $hasQuantities = $dalalStock->details->contains(function ($detail) {
                return $detail->weight > 0;
            });

            if (! $hasQuantities) {
                return $this->failure(__('api.cannot_set_status'), [], 403);
            }
        }

        $dalalStock->status = $newStatus;
        $dalalStock->save();

        Trip::checkTripCompletion($dalalStock->trip_id);

        return $this->success(__('api.status_updated'), new DalalStockResource($dalalStock), 200);
    }

    public function getDetail($id)
    {
        $dalalStock = DalalStock::with('details.fish')->find($id);

        if (! $dalalStock) {

            return $this->failure(__('api.stock_not_found'), [], 404);

        }

        return $this->success(__('api.detail_fetched'), new DalalStockResource($dalalStock), 200);
    }

    public function saveData($request)
    {
        $user = $request->user();
        $user_id = $user->id;

        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // تحقق من وجود مخزون كافٍ للصيّاد
            $fishStock = FishStock::where('trip_id', $validated['trip_id'])
                ->where('fish_id', $validated['fish_id'])
                ->lockForUpdate()
                ->first();

            if (! $fishStock || $fishStock->weight < $validated['weight']) {
                return $this->failure(__('api.insufficient_stock'), [], 422);
            }

            // إنشاء أو جلب رأس مخزون الدلال
            $dalalStock = DalalStock::firstOrCreate([
                'owner_id' => $user_id,
                'dalal_id' => $validated['dalal_id'],
                'trip_id' => $validated['trip_id'],
            ]);
            $result = $this->checkDalalStockStatus($dalalStock->id);
            if (! $result['allowed']) {
                return $result['response'];
            }

            // تحقق من عدم تكرار السمك لنفس الدلال والرحلة
            $existingDetail = DalalStockDetail::where('dalal_stock_id', $dalalStock->id)
                ->where('fish_id', $validated['fish_id'])
                ->first();

            if ($existingDetail) {
                return $this->failure(__('api.item_exists'), [], 422);
            }

            // إنشاء الصنف في تفاصيل مخزون الدلال
            $detail = DalalStockDetail::create([
                'dalal_stock_id' => $dalalStock->id,
                'fish_id' => $validated['fish_id'],
                'fish_name' => $validated['fish_name'] ?? null,
                'weight' => $validated['weight'],
                //                'quantity' => $validated['quantity'],
            ]);

            // خصم الوزن من مخزون الصيّاد
            $fishStock->weight -= $validated['weight'];
            $fishStock->save();

            $totalWeight = $dalalStock->details()->sum('weight');
            if ($totalWeight == 0 && $dalalStock->status != 2) {
                $dalalStock->status = 2;
                $dalalStock->save();
            }
            DB::commit();

            return $this->success(__('api.item_added'), $detail, 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failure(__('api.error_saving'), [], 500);
        }
    }

    public function updateData($request, $id)
    {
        $user = $request->user();
        $role = $user->role;
        $user_id = $user->id;

        if ($role !== 'owner') {
            return $this->failure(__('api.not_owner'), [], 403);
        }

        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $detail = DalalStockDetail::find($id);

            if (! $detail) {
                return $this->failure(__('api.detail_not_found'), [], 404);
            }

            $dalalStock = $detail->dalalStock;
            $result = $this->checkDalalStockStatus($dalalStock->id);
            if (! $result['allowed']) {
                return $result['response'];
            }
            if (! $dalalStock) {
                return $this->failure(__('api.stock_not_found'), [], 404);
            }

            if ($dalalStock->owner_id != $user_id) {
                return $this->failure(__('api.not_owner'), [], 403);
            }

            $fishStock = FishStock::where('trip_id', $dalalStock->trip_id)
                ->where('fish_id', $detail->fish_id)
                ->lockForUpdate()
                ->first();

            if (! $fishStock) {
                return $this->failure(__('apحسبةstock_not_found'), [], 422);
            }

            $weightDiff = $validated['weight'] - $detail->weight;

            if ($weightDiff > 0 && $fishStock->weight < $weightDiff) {
                return $this->failure(__('api.insufficient_stock_update'), [], 422);
            }

            $detail->fish_name = $validated['fish_name'] ?? $detail->fish_name;
            $detail->weight = $validated['weight'];
            //            $detail->quantity = $validated['quantity'];
            $detail->save();

            $fishStock->weight -= $weightDiff;
            $fishStock->save();

            DB::commit();

            return $this->success(__('api.item_updated'), $detail, 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failure(__('api.error_updating'), [], 500);
        }
    }

    public function deleteDataDetail($id)
    {

        $user = request()->user();

        if ($user->role != 'owner') {
            return $this->failure(__('api.not_owner'), [], 403);
        }

        $detail = DalalStockDetail::find($id);
        if (! $detail) {
            return $this->failure(__('api.detail_not_found'), [], 404);
        }

        $dalalStock = DalalStock::find($detail->dalal_stock_id);
        if (! $dalalStock) {
            return $this->failure(__('api.stock_not_found'), [], 404);
        }

        if ($dalalStock->owner_id != $user->id) {
            return $this->failure(__('api.not_owner'), [], 403);
        }

        $result = $this->checkDalalStockStatus($dalalStock->id);
        if (! $result['allowed']) {
            return $result['response'];
        }

        DB::beginTransaction();

        try {
            // Restore weight back to fish stock
            $fishStock = FishStock::where('trip_id', $dalalStock->trip_id)
                ->where('fish_id', $detail->fish_id)
                ->lockForUpdate()
                ->first();

            if (! $fishStock) {
                $fishStock = FishStock::create([
                    'owner_id' => $user->id,
                    'trip_id' => $dalalStock->trip_id,
                    'fish_id' => $detail->fish_id,
                    'weight' => 0,
                ]);
            }

            $fishStock->increment('weight', $detail->weight);

            $detail->delete();

            DB::commit();

            return $this->success(__('api.item_deleted'), [], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failure(__('api.error_deleting'), [], 500);
        }
    }

    public function deleteData($id)
    {
        $user = request()->user();
        $role = $user->role;
        $user_id = $user->id;

        DB::beginTransaction();

        try {
            $dalalStock = DalalStock::with('details:id,dalal_stock_id,fish_id,weight')
                ->find($id);

            if (! $dalalStock) {
                return $this->failure(__('api.stock_not_found'), [], 404);
            }

            $result = $this->checkDalalStockStatus($dalalStock->id);
            if (! $result['allowed']) {
                return $result['response'];
            }

            if ($dalalStock->owner_id != $user_id) {
                return $this->failure(__('api.not_owner'), [], 403);
            }

            // تجميع الأوزان حسب السمك
            $fishWeights = [];
            foreach ($dalalStock->details as $detail) {
                if (! isset($fishWeights[$detail->fish_id])) {
                    $fishWeights[$detail->fish_id] = 0;
                }
                $fishWeights[$detail->fish_id] += $detail->weight;
            }

            // تحديث مخزون السمك دفعة واحدة
            foreach ($fishWeights as $fishId => $weight) {
                $fishStock = FishStock::where('trip_id', $dalalStock->trip_id)
                    ->where('fish_id', $fishId)
                    ->lockForUpdate()
                    ->first();

                if ($fishStock) {
                    $fishStock->weight += $weight;
                    $fishStock->save();
                } else {
                    FishStock::create([
                        'owner_id' => $user_id,
                        'trip_id' => $dalalStock->trip_id,
                        'fish_id' => $fishId,
                        'weight' => $weight,
                    ]);
                }
            }

            // حذف التفاصيل دفعة واحدة
            DalalStockDetail::where('dalal_stock_id', $dalalStock->id)->delete();

            // تحقق هل تبقى تفاصيل
            $hasDetails = DalalStockDetail::where('dalal_stock_id', $dalalStock->id)->exists();

            if (! $hasDetails) {
                $dalalStock->delete();
            }

            DB::commit();

            return $this->success(__('api.item_deleted'), [], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failure(__('api.error_deleting'), [], 500);
        }
    }
}
