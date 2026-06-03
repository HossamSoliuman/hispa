<?php

namespace App\Repository\Api;

use App\Http\Resources\SaleResource;
use App\Interfaces\CRUD;
use App\Models\Customer;
use App\Models\FishStock;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Trip;
use App\Models\User;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Support\Facades\DB;

class SaleRepository implements CRUD
{
    use RespondsWithHttpStatus;

    public function getList($request)
    {
        $user = $request->user();
        $role = $user->role;
        $trip_id = $request->trip_id;

        if ($role != 'owner') {
            return $this->failure(trans('api.unauthorized_list'), [], 403);
        }

        // قراءة الفلاتر
        $customerName = $request->customer_name;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $totalMin = $request->total_min;
        $totalMax = $request->total_max;

        // بناء الاستعلام الأساسي
        $query = Sale::where('seller_type', $role)
            ->where('seller_id', $user->id)
            ->with('details');

        if ($trip_id) {
            $query->where('trip_id', $trip_id);
        }

        if ($customerName) {
            $query->where('customer_name', 'like', '%'.$customerName.'%');
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($totalMin) {
            $query->where('total_price', '>=', $totalMin);
        }

        if ($totalMax) {
            $query->where('total_price', '<=', $totalMax);
        }

        $sales = $query->latest()->paginate(10);
        $saleIds = $sales->pluck('id');

        $summary = SaleDetail::whereIn('sale_id', $saleIds)
            ->selectRaw('
            COUNT(id) as count_all,
            SUM(total_price) as total_all,
            SUM(weight) as weight_all
        ')
            ->first();

        $trip = Trip::find($trip_id);

        $data = [
            'summary' => [
                'trip_number' => $trip->number ?? null,
                'details_count' => $summary->count_all,
                'total_price_all' => (float) ($summary->total_all ?? 0),
                'total_weight_all' => (float) ($summary->weight_all ?? 0),
            ],
            'sales' => paginationResult(SaleResource::collection($sales)),
        ];

        return $this->success(trans('api.list_sale_success'), $data, 200);
    }

    public function updateStaus($request)
    {
        try {
            $user = $request->user();
            $role = $user->role;

            $sale_id = $request->sale_id;

            $sale = Sale::where('id', $sale_id)
                ->where('seller_type', $role)
                ->where('seller_id', $user->id)
                ->first();

            if (! $sale) {
                return $this->failure(trans('api.sale_not_found'), [], 404);
            }

            if ($sale->status == 2) {
                return $this->failure(trans('api.sale_already_completed'), [], 400);
            }
            // تحقق من وجود تفاصيل داخل الفاتورة
            if ($sale->details()->count() == 0) {
                return $this->failure(trans('api.empty_sale'), [], 400);
            }

            $sale->status = 2; // 2 = تم الإنهاء
            $sale->save();
            Trip::markCompleteIfAllStockConsumed($sale->trip_id);

            return $this->success(trans('api.sale_completed'), new SaleResource($sale), 200);

        } catch (\Throwable $e) {
            return $this->failure($e->getMessage(), [], 500);
        }

    }

    public function getDetail($id)
    {
        $user = request()->user();
        $role = $user->role;

        if (! in_array($role, ['owner', 'dalal'])) {
            return $this->failure(trans('api.unauthorized_view'), [], 403);
        }

        $sale = Sale::with('details')->find($id);

        if (! $sale) {
            return $this->failure(trans('api.sale_not_found'), [], 404);
        }

        // التحقق من الصلاحية
        if (
            ($role == 'owner' && $sale->seller_type == 'owner' && $sale->seller_id != $user->id)
        ) {
            return $this->failure(trans('api.unauthorized_view'), [], 403);
        }

        // لو بدك تتأكد كمان أن الـ owner يملك الرحلة فعلياً
        if ($role == 'owner') {
            $trip = Trip::find($sale->trip_id);
            if (! $trip || $trip->owner_id != $user->id) {
                return $this->failure(trans('api.unauthorized_view'), [], 403);
            }
        }

        return $this->success(trans('api.detail_fetched'), new SaleResource($sale), 200);
    }

    public function saveData($request)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $role = $user->role;

            if ($role != 'owner') {
                return $this->failure(trans('api.unauthorized_create'), [], 403);
            }
            $trip = Trip::find($request->trip_id);

            if (! $trip) {
                return $this->failure(trans('api.trip_not_found'), [], 404);
            }

            if ($trip->status == 8) {
                return $this->failure(trans('api.invalid_trip_status_add'), [
                    'status' => 8,
                    'status_text' => trans('api.status_completed'),
                ], 400);
            }

            $customer = Customer::find($request->customer_id);
            if (! $customer) {
                return $this->failure(trans('api.customer_not_found'), [], 404);
            }
            $payment_method = PaymentMethod::find($request->payment_method_id);

            if (! $payment_method) {
                return $this->failure(trans('api.payment_method_not_found'), [], 404);
            }
            // البحث عن الفاتورة الحالية أو إنشاؤها إذا لم تكن موجودة
            $sale = Sale::firstOrCreate([
                'seller_type' => $role,
                'seller_id' => $user->id,
                'status' => 1,
                'trip_id' => $request->trip_id,
                'customer_id' => $request->customer_id,
            ], [
                'number' => generateInvoiceNumber(),
                'customer_name' => $customer->name ?? $request->customer_name,
                'payment_method_id' => $request->payment_method_id,
                'payment_method' => $payment_method->name ?? $request->payment_method,
                'commission_rate' => $request->commission_rate ?? 0,
                'labor_rate' => $request->labor_rate ?? 0,
                'sale_datetime' => now(),
                'notes' => $request->notes,
            ]);
            if ($sale->status == 2) {
                return $this->failure(trans('api.invalid_sale_status_add'), [
                    'status' => 2,
                    'status_text' => trans('api.status_completed'),
                ], 400);
            }
            $fish_id = $request->fish_id;
            $fish_name = $request->fish_name;
            //            $quantity = $request->quantity;
            $weight = $request->weight;
            $price_per_kilo = $request->price_per_kilo;
            $total_price = $weight * $price_per_kilo;

            $stock = FishStock::where('trip_id', $request->trip_id)
                ->where('fish_id', $fish_id)
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                throw new \Exception(trans('api.stock_not_found_for_fish', ['fish' => $fish_name]));
            }
            // ✅ تحقق من أن نفس السمك لم تُضف مسبقاً في نفس الفاتورة
            $existingDetail = SaleDetail::where('sale_id', $sale->id)
                ->where('fish_id', $fish_id)
                ->first();

            if ($existingDetail) {
                throw new \Exception(trans('api.duplicate_fish', ['fish' => $fish_name]));
            }

            $previousSales = SaleDetail::whereHas('sale', function ($q) use ($request, $user, $role) {
                $q->where('trip_id', $request->trip_id)
                    ->where('seller_type', $role)
                    ->where('seller_id', $user->id);
            })
                ->where('fish_id', $fish_id)
                ->selectRaw('SUM(weight) as total_weight')
                ->first();

            $alreadySoldWeight = $previousSales->total_weight ?? 0;
            $totalRequestedWeight = $alreadySoldWeight + $weight;

            if ($totalRequestedWeight > $stock->weight) {
                throw new \Exception(trans('api.requested_exceeds_stock', ['fish' => $fish_name]));
            }

            // خصم من المخزون
            //            $stock->quantity -= $quantity;
            $stock->weight -= $weight;
            $stock->save();

            // إضافة صنف البيع
            SaleDetail::create([
                'sale_id' => $sale->id,
                'fish_id' => $fish_id,
                'fish_name' => $fish_name,
                //                'quantity' => $quantity,
                'weight' => $weight,
                'price_per_kilo' => $price_per_kilo,
                'total_price' => $total_price,
            ]);

            // تحديث إجمالي الفاتورة
            $sale->total_price += $total_price;

            $commission = ($sale->total_price * $sale->commission_rate) / 100;
            $labor = ($sale->total_price * $sale->labor_rate) / 100;
            $net = $sale->total_price - $commission - $labor;

            $sale->commission_amount = $commission;
            $sale->labor_amount = $labor;
            $sale->net_owner_amount = $net;
            $sale->save();

            DB::commit();

            return $this->success(trans('apحسبة_added'), new SaleResource($sale), 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failure($e->getMessage(), [], 500);
        }
    }

    public function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $role = $user->role;

            if ($role != 'owner') {
                return $this->failure(trans('api.unauthorized_update_sale'), [], 403);
            }

            $detail = SaleDetail::find($id);
            if (! $detail) {
                return $this->failure(trans('apحسبة_not_found'), [], 404);
            }

            $sale = $detail->sale;
            $trip = Trip::find($sale->trip_id);

            if (! $trip) {
                return $this->failure(trans('api.trip_not_found'), [], 404);
            }

            if ($trip->status == 8) {
                return $this->failure(trans('api.invalid_trip_status_update'), [
                    'status' => 8,
                    'status_text' => trans('api.status_completed'),
                ], 400);
            }

            if ($trip->owner_id != $user->id || $sale->seller_type != $role || $sale->seller_id != $user->id) {
                return $this->failure(trans('api.unauthorized_update_sale'), [], 403);
            }

            // 🐟 بيانات لا يُسمح بتعديلها
            $fish_id = $detail->fish_id;
            $fish_name = $detail->fish_name;

            // البيانات الجديدة
            //            $new_quantity = $request->quantity;
            $new_weight = $request->weight;
            $new_price_per_kilo = $request->price_per_kilo;
            $new_total_price = $new_weight * $new_price_per_kilo;

            // استرجاع المخزون وتأمينه
            $stock = FishStock::where('trip_id', $sale->trip_id)
                ->where('fish_id', $fish_id)
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                throw new \Exception(trans('api.stock_not_found_for_fish', ['fish' => $fish_name]));
            }

            $old_weight = $detail->weight;
            //            $old_quantity = $detail->quantity;

            $weight_diff = $new_weight - $old_weight;
            //            $quantity_diff = $new_quantity - $old_quantity;

            // تحقق إذا كان الوزن الزائد متاح
            if ($weight_diff > 0 && $stock->weight < $weight_diff) {
                throw new \Exception(trans('api.insufficient_stock', ['fish' => $fish_name, 'weight' => $new_weight]));
            }

            // تحقق من عدم تجاوز المخزون المباع مسبقًا
            $previousSales = SaleDetail::whereHas('sale', function ($q) use ($sale) {
                $q->where('trip_id', $sale->trip_id)
                    ->where('seller_type', $sale->seller_type)
                    ->where('seller_id', $sale->seller_id);
            })
                ->where('fish_id', $fish_id)
                ->where('id', '!=', $detail->id)
                ->selectRaw('SUM(weight) as total_weight')
                ->first();

            $alreadySoldWeight = $previousSales->total_weight ?? 0;
            $totalRequestedWeight = $alreadySoldWeight + $new_weight;

            if ($totalRequestedWeight > $stock->weight + $old_weight) {
                throw new \Exception(trans('api.exceeds_stock', ['fish' => $fish_name, 'weight' => $new_weight]));
            }

            // تحديث المخزون
            $stock->weight -= $weight_diff;
            //            $stock->quantity -= $quantity_diff;
            $stock->save();

            // تحديث بيانات البيع
            $detail->update([
                //                'quantity' => $new_quantity,
                'weight' => $new_weight,
                'price_per_kilo' => $new_price_per_kilo,
                'total_price' => $new_total_price,
            ]);

            // تحديث الفاتورة
            $sale->total_price = $sale->details()->sum('total_price');
            $sale->commission_amount = ($sale->total_price * $sale->commission_rate) / 100;
            $sale->labor_amount = ($sale->total_price * $sale->labor_rate) / 100;
            $sale->net_owner_amount = $sale->total_price - $sale->commission_amount - $sale->labor_amount;
            $sale->save();

            DB::commit();

            return $this->success(trans('api.item_updated'), new SaleResource($sale), 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failure($e->getMessage(), [], 500);
        }
    }

    public function deleteData($id)
    {
        $user = request()->user();

        $saleId = $id;

        if (! $saleId) {
            return $this->failure(trans('api.invoice_id_required'), [], 400);
        }

        // جلب الفاتورة
        $sale = Sale::find($saleId);
        // تحقق أن الفاتورة تخص الدلال الحالي ولم يتم إغلاقها
        if ($sale->seller_id != $user->id || $sale->status == 2) {
            return $this->failure(trans('api.cannot_delete_completed_invoice'), [], 403);
        }
        if (! $sale) {
            return $this->failure(trans('api.invoice_not_found_or_forbidden'), [], 404);
        }

        // تحقق من أن صاحب الفاتورة هو نفس الدلال (seller_type = 'dalal' و seller_id = current user)
        if ($sale->seller_type != 'owner' || $sale->seller_id != $user->id) {
            return $this->failure(trans('api.unauthorized_delete'), [], 403);
        }

        // تحقق إذا الفاتورة فارغة (لا تحتوي على تفاصيل)
        $detailsCount = $sale->details()->count();

        if ($detailsCount > 0) {
            return $this->failure(trans('api.invoice_has_items'), [], 400);
        }

        // حذف الفاتورة
        $sale->delete();

        return $this->success(trans('api.invoice_deleted'), [], 200);
    }

    public function deleteDetailData($id)
    {
        try {
            DB::beginTransaction();

            $user = request()->user();

            $role = $user->role;

            $detail = SaleDetail::find($id);
            if (! $detail) {
                return $this->failure(trans('apحسبة_not_found'), [], 404);
            }

            $sale = $detail->sale;
            $trip = Trip::findOrFail($sale->trip_id);

            if ($sale->seller_id != $user->id || $sale->status == 2) {
                return $this->failure(trans('api.cannot_delete_completed_invoice'), [], 403);
            }

            if ($trip->status == 8) {
                return $this->failure(trans('api.trip_completed_delete'), [], 400);
            }
            // تحقق من الصلاحية
            if (($role == 'owner' && $trip->owner_id != $user->id)) {
                return $this->failure(trans('api.unauthorized_delete'), [], 403);
            }

            // استرجاع الكمية والوزن إلى المخزون
            $stock = FishStock::where('trip_id', $sale->trip_id)
                ->where('fish_id', $detail->fish_id)
                ->lockForUpdate()
                ->first();

            if ($stock) {
                //                $stock->quantity += $detail->quantity;
                $stock->weight += $detail->weight;
                $stock->save();
            }

            // حذف الصنف
            $detail->delete();

            // تحديث الفاتورة بعد الحذف
            $sale->total_price = $sale->details()->sum('total_price');
            $sale->commission_amount = ($sale->total_price * $sale->commission_rate) / 100;
            $sale->labor_amount = ($sale->total_price * $sale->labor_rate) / 100;
            $sale->net_owner_amount = $sale->total_price - $sale->commission_amount - $sale->labor_amount;
            $sale->save();

            DB::commit();

            return $this->success(trans('api.item_deleted_updated_stock'), [], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failure($e->getMessage(), [], 500);
        }
    }
}
