<?php

namespace App\Repository\Api;

use App\Http\Resources\FishStockResource;
use App\Interfaces\CRUD;
use App\Models\FishStock;
use App\Models\Trip;
use App\Traits\RespondsWithHttpStatus;

class FishStockRepository implements CRUD
{
    use RespondsWithHttpStatus;

    public function getList($request)
    {

        $query = FishStock::query();

        // يمكنك إضافة فلتر حسب الرحلة إن أردت
        if ($request->has('trip_id')) {
            $query->where('trip_id', $request->trip_id);
        }

        $stocks = $query->with(['fish', 'trip', 'addedBy']) // علاقات مفيدة للعرض
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $this->success(
            trans('site.getData'),
            FishStockResource::collection($stocks)
        );
    }

    public function getDetail($id) {}

    public function saveData($request)
    {
        $user = request()->user();

        $trip = Trip::find($request->trip_id);

        if (! $trip || $trip->captain_id != $user->id) {
            return $this->failure(trans('api.unauthorized_trip'), [], 403);
        }

        if (in_array($trip->status, [1, 3, 4])) {
            return $this->failure(trans('api.invalid_trip_status_add'), [], 422);
        }

        $exists = FishStock::where('trip_id', $request->trip_id)
            ->where('fish_id', $request->fish_id)
            ->where('added_by', $user->id)
            ->exists();

        if ($exists) {
            return $this->failure(trans('api.duplicate_fish'), [], 422);
        }

        $validated = $request->validated();
        $validated['added_by'] = $user->id;
        //        $validated['quantity_captain'] = $validated['quantity'];
        $validated['weight_captain'] = $validated['weight'];

        $fishStock = FishStock::create($validated);

        return $this->success(trans('apحسبة_added'), $fishStock, 200);
    }

    public function updateData($request, $id)
    {

        $user = $request->user();
        $role = $user->role;

        $fishStock = FishStock::find($id);

        if (! $fishStock) {
            return $this->failure(trans('apحسبة_not_found'), [], 404);
        }

        $trip = Trip::find($fishStock->trip_id);

        if (! $trip) {
            return $this->failure(trans('api.trip_not_found'), [], 404);
        }

        // تأكد من حالة الرحلة تسمح بالتعديل (عمومًا لا يسمح في الحالات 1,3,4)
        if (in_array($trip->status, [1, 3, 4])) {
            return $this->failure(trans('api.invalid_trip_status_update'), [], 422);
        }

        $validated = $request->validated();

        if ($role == 'captain') {
            if ($fishStock->added_by != $user->id || $trip->captain_id != $user->id) {
                return $this->failure(trans('api.unauthorized_update'), [], 403);
            }

            $fishStock->update([
                'fish_id' => $validated['fish_id'],
                'fish_name' => $validated['fish_name'],
                //                'quantity' => $validated['quantity'],
                'weight' => $validated['weight'],
                //                'quantity_captain' => $validated['quantity'],
                'weight_captain' => $validated['weight'],
                'notes' => $validated['notes'] ?? null,
            ]);
        } elseif ($role === 'counter') {
            // السماح فقط إذا كانت حالة الرحلة تسمح للعداد بالتعديل (مثلاً الحالة 5 فقط)
            if ($trip->counter_id != $user->id) {
                return $this->failure(trans('api.unauthorized_update'), [], 403);
            }

            if ($trip->status != 5) {
                return $this->failure(trans('api.invalid_counter_status'), [], 422);
            }

            $fishStock->update([
                'fish_id' => $validated['fish_id'],
                'fish_name' => $validated['fish_name'],
                //                'quantity' => $validated['quantity'],
                'weight' => $validated['weight'],
                //                'quantity_counter' => $validated['quantity'],
                'weight_counter' => $validated['weight'],
                'corrected_by' => $user->id,
                'notes_by_counter' => $validated['notes_by_counter'] ?? trans('api.no_notes'),
            ]);
        } else {
            return $this->failure(trans('api.no_permission_role'), [], 403);
        }

        return $this->success(trans('apحسبة_updated'), $fishStock, 200);
    }

    public function deleteData($id)
    {
        $user = request()->user();

        $fishStock = FishStock::find($id);

        if (! $fishStock) {
            return $this->failure(trans('apحسبة_not_found'), [], 404);
        }

        if ($fishStock->added_by != $user->id) {
            return $this->failure(trans('api.unauthorized_delete'), [], 403);
        }

        $fishStock->delete();

        return $this->success(trans('apحسبة_deleted'), [], 200);
    }
}
