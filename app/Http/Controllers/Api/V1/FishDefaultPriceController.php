<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FishDefaultPriceRequest;
use App\Http\Requests\Api\UpdateFishDefaultPriceRequest;
use App\Http\Resources\FishPriceDefaultResource;
use App\Models\TripFishPrice;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class FishDefaultPriceController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->role;

        $query = TripFishPrice::query();

        if ($role == 'owner') {
            $query->where('user_id', $user->id);
        } elseif ($role == 'dalal') {
            $query->where('role', 'dalal')->where('user_id', $user->id);
        } else {
            return $this->failure('غير مصرح لك', [], 403);
        }

        $prices = $query->get();

        return $this->success('تم جلب الأسعار', FishPriceDefaultResource::collection($prices), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FishDefaultPriceRequest $request)
    {
        try {
            $user = $request->user();
            $role = $user->role;

            if (! in_array($role, ['owner', 'dalal'])) {
                return $this->failure('غير مصرح لك بإنشاء عملية بيع', [], 403);
            }

            if ($role == 'owner') {
                return $this->failure('غير مسموح لك بتحديد السعر لهذه الرحلة', [], 403);
            }

            // تحقق من عدم وجود سعر مكرر لنفس السمك والرحلة من نفس المستخدم
            $exists = TripFishPrice::where('trip_id', $request->trip_id)
                ->where('fish_id', $request->fish_id)
                ->where('user_id', $user->id)
                ->exists();

            if ($exists) {
                return $this->failure('تم تحديد سعر لهذه السمك مسبقًا في هذه الرحلة', [], 409);
            }

            $data = [
                'trip_id' => $request->trip_id,
                'fish_id' => $request->fish_id,
                'price_per_kilo' => $request->price_per_kilo,
                'user_id' => $user->id,
                'role' => $role,
            ];

            $trip_fish_price = TripFishPrice::create($data);

            return $this->success(trans('site.save'), $trip_fish_price, 201);

        } catch (\Throwable $e) {
            return $this->failure('حدث خطأ أثناء الحفظ: '.$e->getMessage(), [], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $fish_id)
    {
        $user = \request()->user();
        $role = $user->role;

        if (! $fish_id) {
            return $this->failure('رقم السمك مطلوب', [], 422);
        }

        $query = TripFishPrice::query()->where('fish_id', $fish_id);

        if ($role == 'owner') {
            $query->where('user_id', $user->id);
        } elseif ($role == 'dalal') {
            $query->where('role', 'dalal')->where('user_id', $user->id);
        } else {
            return $this->failure('غير مصرح لك', [], 403);
        }

        $prices = $query->get();

        return $this->success('تم جلب الأسعار', FishPriceDefaultResource::collection($prices), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFishDefaultPriceRequest $request, string $id)
    {
        try {
            $user = $request->user();
            $role = $user->role;

            if (! in_array($role, ['owner', 'dalal'])) {
                return $this->failure('غير مصرح لك بتعديل السعر', [], 403);
            }

            // جلب السجل
            $price = TripFishPrice::find($id);

            if (! $price) {
                return $this->failure('السعر غير موجود', [], 404);
            }

            // التحقق من الصلاحية
            if ($price->user_id != $user->id || $price->role != $role) {
                return $this->failure('غير مصرح لك بتعديل هذا السعر', [], 403);
            }

            // التحقق من البيانات المدخلة

            $price->price_per_kilo = $request->price_per_kilo;
            $price->save();

            return $this->success(trans('site.updated_successfully'), $price, 200);

        } catch (\Throwable $e) {
            return $this->failure($e->getMessage(), [], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = request()->user();
            $role = $user->role;

            if (! in_array($role, ['owner', 'dalal'])) {
                return $this->failure('غير مصرح لك بحذف السعر', [], 403);
            }

            $price = TripFishPrice::find($id);

            if (! $price) {
                return $this->failure('السعر غير موجود', [], 404);
            }

            // التحقق أن المستخدم هو صاحب هذا السعر
            if ($price->user_id != $user->id || $price->role != $role) {
                return $this->failure('غير مصرح لك بحذف هذا السعر', [], 403);
            }

            $price->delete();

            return $this->success('تم حذف السعر بنجاح', [], 200);

        } catch (\Throwable $e) {
            return $this->failure('حدث خطأ أثناء الحذف: '.$e->getMessage(), [], 500);
        }
    }
}
