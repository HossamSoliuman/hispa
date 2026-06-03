<?php

namespace App\Repository\Api;

use App\Http\Resources\DalalSaleResource;
use App\Interfaces\CRUD;
use App\Models\Customer;
use App\Models\DalalStockDetail;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DalalSaleRepository implements CRUD
{
    use RespondsWithHttpStatus;

    public function getList($request)
    {
        $user = $request->user();

        if ($user->role != 'dalal') {
            return $this->failure(trans('api.unauthorized_view'), [], 403);
        }

        $dalalStockId = $request->dalal_stock_id;
        $stockDetailIds = [];

        if ($dalalStockId) {
            $stockDetailIds = DalalStockDetail::where('dalal_stock_id', $dalalStockId)
                ->pluck('id')
                ->toArray();

            if (empty($stockDetailIds)) {
                $emptyPaginator = new LengthAwarePaginator([], 0, 10, 1, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]);

                return $this->success(trans('api.no_sales_for_stock'), paginationResult($emptyPaginator), 200);
            }
        }

        $query = Sale::with([
            'details' => function ($q) use ($stockDetailIds) {
                if (! empty($stockDetailIds)) {
                    $q->whereIn('dalal_stock_detail_id', $stockDetailIds);
                }
                $q->with('fish:id,scientific_name');
            },
            'customer:id,name',
        ])
            ->where('seller_type', 'dalal')
            ->where('seller_id', $user->id);

        if (! empty($stockDetailIds)) {
            $query->whereHas('details', function ($q) use ($stockDetailIds) {
                $q->whereIn('dalal_stock_detail_id', $stockDetailIds);
            });
        }

        // فلتر اسم العميل
        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->customer_name.'%');
            });
        }

        // فلتر تاريخ البداية
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // فلتر تاريخ النهاية
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // فلتر الحد الأدنى للإجمالي
        if ($request->filled('total_min')) {
            $query->where('total_price', '>=', $request->total_min);
        }

        // فلتر الحد الأقصى للإجمالي
        if ($request->filled('total_max')) {
            $query->where('total_price', '<=', $request->total_max);
        }

        $sales = $query->latest()->paginate(10);

        return $this->success(trans('api.invoices_fetched'), paginationResult(DalalSaleResource::collection($sales)), 200);
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
                ->with('details') // لتحميل التفاصيل
                ->first();

            if (! $sale) {
                return $this->failure(trans('api.invoice_not_found_or_forbidden'), [], 404);
            }

            if ($sale->status == 2) {
                return $this->failure(trans('api.invoice_already_closed'), [], 400);
            }

            if ($sale->details->isEmpty()) {
                return $this->failure(trans('api.invoice_no_items'), [], 400);
            }

            // استنتاج dalal_stock_id من أول detail
            $firstDetail = $sale->details->first();
            $dalalStockDetailId = $firstDetail->dalal_stock_detail_id ?? null;

            if (! $dalalStockDetailId) {
                return $this->failure(trans('api.invoice_details_not_linked_to_dalal_stock'), [], 400);
            }

            $dalalStockDetail = DalalStockDetail::with('dalalStock')->find($dalalStockDetailId);

            if (! $dalalStockDetail || ! $dalalStockDetail->dalalStock) {
                return $this->failure(trans('api.dalal_stock_not_found'), [], 404);
            }

            $dalalStock = $dalalStockDetail->dalalStock;

            // التحقق من حالة المخزون وتحديثها إذا لزم
            $totalWeight = $dalalStock->details()->sum('weight');

            if ($totalWeight == 0 && $dalalStock->status != 2) {
                $dalalStock->status = 2;
                $dalalStock->save();
            }

            // تحديث حالة البيع
            $sale->status = 2;
            $sale->save();

            return $this->success(trans('api.invoice_closed_successfully'), new DalalSaleResource($sale), 200);

        } catch (\Throwable $e) {
            return $this->failure($e->getMessage(), [], 500);
        }

    }

    public function getDetail($id)
    {
        $user = \request()->user();

        if ($user->role != 'dalal') {
            return $this->failure(trans('api.unauthorized_view'), [], 403);
        }

        $sale = Sale::with([
            'details.fish:id,scientific_name',
            'customer:id,name',
        ])
            ->where('id', $id)
            ->where('seller_type', 'dalal')
            ->where('seller_id', $user->id)
            ->first();

        if (! $sale) {
            return $this->failure(trans('api.invoice_not_found_or_not_yours'), [], 404);
        }

        $details = $sale->details->map(function ($detail) {
            return [
                'id' => $detail->id,
                'fish_id' => $detail->fish_id,
                'fish_name' => $detail->fish->name ?? trans('api.unknown'),
                //                'quantity' => $detail->quantity,
                'weight' => $detail->weight,
                'price_per_kilo' => $detail->price_per_kilo,
                'total_price' => $detail->total_price,
                'dalal_stock_detail_id' => $detail->dalal_stock_detail_id ?? null,
            ];
        });

        $data = [
            'id' => $sale->id,
            'number' => $sale->number,
            'customer' => $sale->customer->name ?? $sale->customer_name,
            'status' => $sale->status,
            'total_price' => $sale->total_price,
            'commission_amount' => $sale->commission_amount,
            'labor_amount' => $sale->labor_amount,
            'net_owner_amount' => $sale->net_owner_amount,
            'remaining_total' => $sale->remaining_total,
            'created_at' => $sale->created_at->toDateTimeString(),
            'details_count' => $details->count(),
            'total_weight' => $details->sum('weight'),
            //            'total_quantity' => $details->sum('quantity'),
            'details' => $details,
        ];

        return $this->success(trans('api.details_fetched'), $data, 200);
    }

    public function saveData($request)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $role = $user->role;

            $commission_setting = $user->commissionSetting;
            if (! $commission_setting) {
                return $this->failure(trans('api.no_commission_setting'), [], 403);
            }

            if ($role != 'dalal') {
                return $this->failure(trans('api.unauthorized_create'), [], 403);
            }

            $customer = Customer::find($request->customer_id);
            if (! $customer) {
                return $this->failure(trans('api.customer_not_found'), [], 404);
            }

            $payment_method = PaymentMethod::find($request->payment_method_id);
            if (! $payment_method) {
                return $this->failure(trans('api.payment_method_not_found'), [], 404);
            }

            // جلب أو إنشاء الفاتورة الحالية
            $sale = Sale::firstOrCreate([
                'seller_type' => $role,
                'seller_id' => $user->id,
                'status' => 1,
                'trip_id' => null,
                'customer_id' => $request->customer_id,
            ], [
                'number' => generateInvoiceNumber(),
                'customer_name' => $customer->name ?? $request->customer_name,
                'payment_method_id' => $request->payment_method_id,
                'commission_setting_id' => $commission_setting->id,
                'payment_method' => $payment_method->name,
                'commission_rate' => $commission_setting->commission_rate ?? 0,
                'labor_rate' => $commission_setting->labor_rate ?? 0,
                'sale_datetime' => now(),
                'notes' => $request->notes,
            ]);

            if ($sale->status == 2) {
                return $this->failure(trans('api.cannot_add_completed_invoice'), [
                    'status' => 2,
                    'status_text' => trans('api.status_completed'),
                ], 400);
            }

            // بيانات الصنف المطلوب
            $fish_id = $request->fish_id;
            $fish_name = $request->fish_name;
            //            $quantity = $request->quantity;
            $weight = $request->weight;
            $price_per_kilo = $request->price_per_kilo;
            $total_price = $weight * $price_per_kilo;
            $dalalStockId = $request->dalal_stock_id;

            // جلب المخزون الصحيح من التفاصيل بناءً على dalal_stock_id
            $stockDetail = DalalStockDetail::where('fish_id', $fish_id)
                ->where('dalal_stock_id', $dalalStockId)
                ->lockForUpdate()
                ->first();

            if (! $stockDetail) {
                throw new \Exception(trans('api.stock_not_found_for_fish', ['fish' => $fish_name]));
            }

            // تحقق من عدم تكرار السمك في نفس الفاتورة
            $existingDetail = SaleDetail::where('sale_id', $sale->id)
                ->where('fish_id', $fish_id)
                ->first();

            if ($existingDetail) {
                throw new \Exception(trans('apحسبة_already_added', ['fish' => $fish_name]));
            }

            // التحقق من الكمية والوزن المتاحين في نفس السجل فقط
            if ($weight > $stockDetail->weight) {
                throw new \Exception(trans('api.requested_exceeds_stock', ['fish' => $fish_name]));
            }

            // خصم الكمية والوزن من المخزون
            //            $stockDetail->quantity -= $quantity;
            $stockDetail->weight -= $weight;
            $stockDetail->save();

            // إضافة الصنف للفاتورة
            SaleDetail::create([
                'sale_id' => $sale->id,
                'fish_id' => $fish_id,
                'fish_name' => $fish_name,
                //                'quantity' => $quantity,
                'weight' => $weight,
                'price_per_kilo' => $price_per_kilo,
                'total_price' => $total_price,
                'dalal_stock_detail_id' => $stockDetail->id,
            ]);

            // تحديث الفاتورة
            $sale->total_price += $total_price;

            $commission = ($sale->total_price * $sale->commission_rate) / 100;
            $labor = ($sale->total_price * $sale->labor_rate) / 100;
            $net = $sale->total_price - $commission - $labor;

            $sale->commission_amount = round($commission, 2);
            $sale->labor_amount = round($labor, 2);
            $sale->net_owner_amount = round($net, 2);
            $sale->remaining_total = round($sale->total_price - $net, 2);

            $sale->save();

            DB::commit();

            return $this->success(trans('api.item_added'), new DalalSaleResource($sale), 200);

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

            $saleDetail = SaleDetail::with('sale', 'dalalStockDetail.dalalStock')
                ->where('id', $id)
                ->firstOrFail();

            $sale = $saleDetail->sale;

            if ($user->id != $sale->seller_id || $role != 'dalal') {
                return $this->failure(trans('api.unauthorized_update_sale'), [], 403);
            }

            if ($sale->status == 2) {
                return $this->failure(trans('api.cannot_update_completed_invoice'), [], 400);
            }

            $stockDetail = $saleDetail->dalalStockDetail;

            if (! $stockDetail) {
                throw new \Exception(trans('api.stock_detail_missing'));
            }

            // استرجاع القيم القديمة
            $oldWeight = $saleDetail->weight;
            //            $oldQuantity = $saleDetail->quantity;
            $oldTotal = $saleDetail->total_price;

            // استرجاع الجديدة
            $newWeight = $request->weight;
            //            $newQuantity = $request->quantity;
            $newPricePerKilo = $request->price_per_kilo;
            $newTotal = $newWeight * $newPricePerKilo;

            // إرجاع القديم إلى المخزون
            $stockDetail->weight += $oldWeight;
            //            $stockDetail->quantity += $oldQuantity;

            // التحقق من توفر الجديد
            if ($newWeight > $stockDetail->weight) {
                throw new \Exception(trans('api.new_requested_exceeds_stock'));
            }

            // خصم الجديد
            $stockDetail->weight -= $newWeight;
            //            $stockDetail->quantity -= $newQuantity;
            $stockDetail->save();

            // تحديث التفاصيل
            $saleDetail->update([
                'weight' => $newWeight,
                //                'quantity' => $newQuantity,
                'price_per_kilo' => $newPricePerKilo,
                'total_price' => $newTotal,
            ]);

            // تحديث الفاتورة
            $sale->total_price = $sale->total_price - $oldTotal + $newTotal;

            $commission = ($sale->total_price * $sale->commission_rate) / 100;
            $labor = ($sale->total_price * $sale->labor_rate) / 100;
            $net = $sale->total_price - $commission - $labor;

            $sale->commission_amount = round($commission, 2);
            $sale->labor_amount = round($labor, 2);
            $sale->net_owner_amount = round($net, 2);
            $sale->remaining_total = round($sale->total_price - $net, 2);

            $sale->save();

            DB::commit();

            return $this->success(trans('api.item_updated'), new DalalSaleResource($sale), 200);

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
            return $this->failure(trans('api.invoice_not_found'), [], 404);
        }

        // تحقق من أن صاحب الفاتورة هو نفس الدلال (seller_type = 'dalal' و seller_id = current user)
        if ($sale->seller_type != 'dalal' || $sale->seller_id != $user->id) {
            return $this->failure(trans('api.not_authorized_delete_invoice'), [], 403);
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

            if ($user->role != 'dalal') {
                return $this->failure(trans('api.unauthorized_delete_data'), [], 403);
            }

            $detail = SaleDetail::with('sale')
                ->where('id', $id)
                ->first();

            if (! $detail) {
                return $this->failure(trans('api.item_not_found'), [], 404);
            }

            $sale = $detail->sale;
            if ($sale->seller_type != 'dalal' || $sale->seller_id != $user->id) {
                return $this->failure(trans('api.not_authorized_delete_invoice'), [], 403);
            }
            // تحقق أن الفاتورة تخص الدلال الحالي ولم يتم إغلاقها
            if ($sale->seller_id != $user->id || $sale->status == 2) {
                return $this->failure(trans('api.cannot_delete_completed_invoice'), [], 403);
            }

            // استرجاع المخزون (dalal_stock_detail)
            if ($detail->dalal_stock_detail_id) {
                $stockDetail = DalalStockDetail::find($detail->dalal_stock_detail_id);
                if ($stockDetail) {
                    //                    $stockDetail->quantity += $detail->quantity;
                    $stockDetail->weight += $detail->weight;
                    $stockDetail->save();
                }
            }

            // خصم السعر من الفاتورة وتحديث العمولة
            $sale->total_price -= $detail->total_price;

            $sale->commission_amount = ($sale->total_price * $sale->commission_rate) / 100;
            $sale->labor_amount = ($sale->total_price * $sale->labor_rate) / 100;
            $sale->net_owner_amount = $sale->total_price - $sale->commission_amount - $sale->labor_amount;
            $sale->save();

            // حذف التفصيلة
            $detail->delete();

            DB::commit();

            return $this->success(trans('api.item_deleted_updated_stock'), [], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failure($e->getMessage(), [], 500);
        }
    }
}
