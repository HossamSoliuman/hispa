<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\SalesDataTable;
use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\SalesRequest;
use App\Models\Boat;
use App\Models\CatchDetail;
use App\Models\CatchModel;
use App\Models\Customer;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Trip;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $datatable;

    public function __construct()
    {
        $this->datatable = new SalesDataTable;

    }

    public function index(Request $request)
    {
        $type = $request->get('type');
        $fish = Fish::Active()->get();
        $boats = Boat::Active()->where('owner_id', auth()->id())->get();
        $trips = Trip::where('owner_id', auth()->id())->get();

        return view('owner.sales.index', compact('type', 'fish', 'boats', 'trips'));
    }

    public function getSalesData(Request $request)
    {

        return $this->datatable->getData($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getShowDetailSalesData(Request $request, $fish_id)
    {
        return $this->datatable->getShowData($request, $fish_id);
    }

    public function show($id)
    {
        $sales = Sale::where('seller_type', 'owner')
            ->where('seller_id', auth()->id())
            ->with(['customer', 'paymentMethod', 'details', 'details.fish', 'details.unit'])
            ->findOrFail($id);

        return view('owner.sales.show', compact('sales'));
    }

    /**
     * Print the currently filtered sales listing as a single PDF report.
     * Mirrors the filters applied on the sales management listing.
     */
    public function printReport(Request $request): \Illuminate\Http\Response
    {
        $sales = Sale::where('seller_type', 'owner')
            ->where('seller_id', auth()->id())
            ->with(['customer', 'paymentMethod', 'details', 'details.unit'])
            ->when($request->filled('from_date'), fn ($q) => $q->whereDate('sale_datetime', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn ($q) => $q->whereDate('sale_datetime', '<=', $request->to_date))
            ->when($request->filled('fish_id'), fn ($q) => $q->whereHas('details', fn ($d) => $d->where('fish_id', $request->fish_id)))
            ->orderByDesc('sale_datetime')
            ->get();

        $totalWeight = $sales->sum(fn (Sale $sale) => $sale->details->sum('weight'));
        $totalRevenue = $sales->sum('total_price');
        $netOwnerAmount = $sales->sum('net_owner_amount');

        $statistics = [
            'total_sales' => $sales->count(),
            'total_weight' => $totalWeight,
            'total_revenue' => $totalRevenue,
            'net_owner_amount' => $netOwnerAmount,
            'avg_price_per_kg' => $totalWeight > 0 ? $totalRevenue / $totalWeight : 0,
        ];

        $settings = $this->reportSettings();

        $filters = [
            'from_date' => $request->filled('from_date') ? $request->from_date : null,
            'to_date' => $request->filled('to_date') ? $request->to_date : null,
            'fish_id' => $request->filled('fish_id') ? $request->fish_id : null,
        ];

        $filename = 'sales-report-'.($filters['from_date'] ?? 'all').'-to-'.($filters['to_date'] ?? 'all').'.pdf';
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return pdf_report(view('owner.reports.print.sales-report', compact(
            'sales',
            'statistics',
            'settings',
            'filters'
        )), [], $filename, $disposition);
    }

    /**
     * Print a single sale invoice as a PDF document.
     */
    public function printInvoice(Request $request, $id): \Illuminate\Http\Response
    {
        $sale = Sale::where('seller_type', 'owner')
            ->where('seller_id', auth()->id())
            ->with(['customer', 'paymentMethod', 'details', 'details.fish', 'details.unit'])
            ->findOrFail($id);

        $settings = $this->reportSettings();

        $filename = 'sale-'.trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower((string) $sale->number)), '-').'.pdf';
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return pdf_report(view('owner.reports.print.sale-invoice', compact('sale', 'settings')), [], $filename, $disposition);
    }

    /**
     * Build the company settings array shared by the sales PDF reports.
     *
     * @return array<string, mixed>
     */
    private function reportSettings(): array
    {
        $companyName = currentCompany()?->name ?: 'حسبة';

        return ownerCompanySettings([
            'qr_code' => app(\App\Service\Owner\ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);
    }

    public function create()
    {
        $customers = Customer::Active()->get();
        $trips = Trip::where('owner_id', auth()->user()->id)->get();
        $fish = Fish::Active()->get();
        $paymentMethods = PaymentMethod::active()->get();

        return view('owner.sales.create', compact('fish', 'customers', 'paymentMethods', 'trips'));
    }

    public function store(SalesRequest $request)
    {
        try {
            DB::beginTransaction();

            $customer = Customer::find($request->customer_id);
            $trip = Trip::where('owner_id', auth()->id())->findOrFail($request->trip_id);
            $catch = CatchModel::where('trip_id', $request->trip_id)->first();

            $sale = Sale::create([
                'number' => $request->customer_id.'_'.time(),
                'seller_type' => 'owner',
                'seller_id' => auth()->id(),
                'customer_id' => $request->customer_id,
                'customer_name' => $customer?->name,
                'total_price' => 0,
                'payment_method_id' => $request->payment_method_id,
                'payment_status' => $request->payment_status,
                'status' => $request->payment_status == 'paid' ? 2 : 1,
                'sale_datetime' => $request->sale_datetime,
                'catch_id' => $catch->id ?? 0,
                'trip_id' => $request->trip_id,
                'boat_id' => $trip->boat_id ?? 0,
            ]);

            $defaultUnitId = Unit::defaultId();
            $totalPrice = 0;
            $soldRows = 0;

            foreach ($request->fish_id as $index => $fishId) {
                $weight = (float) ($request->weight[$index] ?? 0);
                $price = (float) ($request->price_per_kilo[$index] ?? 0);
                $unitId = $request->unit_id[$index] ?? $defaultUnitId;

                if ($weight <= 0) {
                    continue;
                }

                if ($price <= 0) {
                    throw new \Exception('يجب إدخال سعر الكيلو للأصناف المباعة');
                }

                $fishStock = FishQuantityStock::where('fish_id', $fishId)
                    ->where('unit_id', $unitId)
                    ->where('catch_id', ($catch->id ?? 0))
                    ->where('trip_id', $request->trip_id)
                    ->first();
                if (! $fishStock || $fishStock->quantity < $weight) {
                    throw new \Exception('الكمية المطلوبة أكبر من المخزون المتوفر');
                }
                $fishStock->decrement('quantity', $weight);

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'fish_id' => $fishId,
                    'unit_id' => $unitId,
                    'fish_name' => optional(Fish::find($fishId))->scientific_name,
                    'weight' => $weight,
                    'price_per_kilo' => $price,
                    'total_price' => ($price * $weight),
                ]);

                $this->stampCatchDetailPrice($catch, $fishId, $unitId, $price);

                $totalPrice += ($price * $weight);
                $soldRows++;
            }

            if ($soldRows === 0) {
                throw new \Exception('يجب إدخال وزن صنف واحد على الأقل');
            }

            $paidAmount = match ($request->payment_status) {
                'paid' => $totalPrice,
                'partially_paid' => (float) $request->paid_amount,
                default => 0,
            };

            $commissionRate = (float) ($request->commission_rate ?? 0);
            $laborRate = (float) ($request->labor_rate ?? 0);
            $commissionAmount = round($totalPrice * $commissionRate / 100, 2);
            $laborAmount = round($totalPrice * $laborRate / 100, 2);
            $netOwnerAmount = round($totalPrice - $commissionAmount - $laborAmount, 2);

            $sale->update([
                'total_price' => $totalPrice,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'labor_rate' => $laborRate,
                'labor_amount' => $laborAmount,
                'net_owner_amount' => $netOwnerAmount,
                'remaining_total' => ($totalPrice - $paidAmount),
            ]);

            $this->markTripSoldIfCatchDepleted($trip, $catch);

            DB::commit();

            return redirect()
                ->route('owner.sales.index')
                ->with('success', 'تم إضافة فاتورة البيع بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $sale = Sale::where('seller_type', 'owner')
            ->where('seller_id', auth()->id())
            ->with(['details.fish', 'details.unit', 'customer'])
            ->findOrFail($id);

        $customers = Customer::Active()->get();
        $paymentMethods = PaymentMethod::active()->get();

        $rows = $sale->details->map(function (SaleDetail $detail) use ($sale) {
            $stock = FishQuantityStock::where('fish_id', $detail->fish_id)
                ->where('unit_id', $detail->unit_id)
                ->where('catch_id', $sale->catch_id ?? 0)
                ->where('trip_id', $sale->trip_id)
                ->first();

            return [
                'fish_id' => $detail->fish_id,
                'fish_name' => $detail->fish->name ?? $detail->fish_name,
                'unit_id' => $detail->unit_id,
                'unit_name' => $detail->unit->name ?? '',
                'weight' => (float) $detail->weight,
                'price' => (float) $detail->price_per_kilo,
                'available' => (float) ($stock->quantity ?? 0) + (float) $detail->weight,
            ];
        });

        return view('owner.sales.edit', compact('sale', 'customers', 'paymentMethods', 'rows'));
    }

    public function update(SalesRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $sale = Sale::where('seller_type', 'owner')
                ->where('seller_id', auth()->id())
                ->with('details')
                ->findOrFail($id);

            $trip = Trip::where('owner_id', auth()->id())->findOrFail($sale->trip_id);
            $catch = CatchModel::find($sale->catch_id);
            $customer = Customer::find($request->customer_id);

            foreach ($sale->details as $detail) {
                $this->restoreStock($sale, $detail->fish_id, $detail->unit_id, (float) $detail->weight);
            }

            SaleDetail::where('sale_id', $sale->id)->delete();

            $sale->update([
                'customer_id' => $request->customer_id,
                'customer_name' => $customer?->name,
                'payment_method_id' => $request->payment_method_id,
                'payment_status' => $request->payment_status,
                'status' => $request->payment_status == 'paid' ? 2 : 1,
                'sale_datetime' => $request->sale_datetime,
            ]);

            $totalPrice = 0;
            $soldRows = 0;

            foreach ($request->fish_id as $index => $fishId) {
                $weight = (float) ($request->weight[$index] ?? 0);
                $price = (float) ($request->price_per_kilo[$index] ?? 0);
                $unitId = $this->normalizeUnitId($request->unit_id[$index] ?? null);

                if ($weight <= 0) {
                    continue;
                }

                if ($price <= 0) {
                    throw new \Exception('يجب إدخال سعر الكيلو للأصناف المباعة');
                }

                $fishStock = FishQuantityStock::firstOrCreate(
                    [
                        'fish_id' => $fishId,
                        'unit_id' => $unitId,
                        'catch_id' => $sale->catch_id ?? 0,
                        'trip_id' => $sale->trip_id,
                        'boat_id' => $sale->boat_id,
                    ],
                    ['quantity' => 0]
                );

                if ($fishStock->quantity < $weight) {
                    throw new \Exception('الكمية المطلوبة أكبر من المخزون المتوفر');
                }

                $fishStock->decrement('quantity', $weight);

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'fish_id' => $fishId,
                    'unit_id' => $unitId,
                    'fish_name' => optional(Fish::find($fishId))->scientific_name,
                    'weight' => $weight,
                    'price_per_kilo' => $price,
                    'total_price' => ($price * $weight),
                ]);

                $this->stampCatchDetailPrice($catch, $fishId, $unitId, $price);

                $totalPrice += ($price * $weight);
                $soldRows++;
            }

            if ($soldRows === 0) {
                throw new \Exception('يجب إدخال وزن صنف واحد على الأقل');
            }

            $paidAmount = match ($request->payment_status) {
                'paid' => $totalPrice,
                'partially_paid' => (float) $request->paid_amount,
                default => 0,
            };

            $commissionRate = (float) ($request->commission_rate ?? 0);
            $laborRate = (float) ($request->labor_rate ?? 0);
            $commissionAmount = round($totalPrice * $commissionRate / 100, 2);
            $laborAmount = round($totalPrice * $laborRate / 100, 2);
            $netOwnerAmount = round($totalPrice - $commissionAmount - $laborAmount, 2);

            $sale->update([
                'total_price' => $totalPrice,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'labor_rate' => $laborRate,
                'labor_amount' => $laborAmount,
                'net_owner_amount' => $netOwnerAmount,
                'remaining_total' => ($totalPrice - $paidAmount),
            ]);

            $this->markTripSoldIfCatchDepleted($trip, $catch);

            DB::commit();

            return redirect()
                ->route('owner.sales.index')
                ->with('success', 'تم تعديل فاتورة البيع بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mirror the selling price onto the catch details so the catch/trip
     * reports show the realised price instead of zero. The catch detail's
     * total reflects the full caught weight valued at the latest sale price.
     */
    /**
     * Normalise an incoming unit identifier. Legacy rows store a null unit, and
     * the edit form echoes that back as an empty string, so coalesce blanks to
     * null to keep stock lookups matching the stored records.
     */
    private function normalizeUnitId(mixed $unitId): ?int
    {
        return filled($unitId) ? (int) $unitId : null;
    }

    /**
     * Return a previously sold quantity back to its stock record before the
     * sale details are recalculated on update.
     */
    private function restoreStock(Sale $sale, int $fishId, ?int $unitId, float $weight): void
    {
        if ($weight <= 0) {
            return;
        }

        FishQuantityStock::firstOrCreate(
            [
                'fish_id' => $fishId,
                'unit_id' => $unitId,
                'catch_id' => $sale->catch_id ?? 0,
                'trip_id' => $sale->trip_id,
                'boat_id' => $sale->boat_id,
            ],
            ['quantity' => 0]
        )->increment('quantity', $weight);
    }

    private function stampCatchDetailPrice(?CatchModel $catch, int $fishId, ?int $unitId, float $price): void
    {
        if (! $catch) {
            return;
        }

        CatchDetail::where('catch_id', $catch->id)
            ->where('fish_id', $fishId)
            ->where('unit_id', $unitId)
            ->get()
            ->each(function (CatchDetail $detail) use ($price): void {
                $detail->update([
                    'price_per_kg' => $price,
                    'total_price' => $price * $detail->weight,
                ]);
            });
    }

    private function markTripSoldIfCatchDepleted(?Trip $trip, ?CatchModel $catch): void
    {
        if (! $trip || ! in_array($trip->status, [TripStatus::Counted, TripStatus::ReadyToSell], true)) {
            return;
        }

        $hasRemainingStock = FishQuantityStock::where('trip_id', $trip->id)
            ->where('catch_id', $catch->id ?? 0)
            ->where('quantity', '>', 0)
            ->exists();

        if (! $hasRemainingStock) {
            $trip->update(['status' => TripStatus::Sold]);
        }
    }

    public function catchDetails($tripId)
    {
        Trip::where('owner_id', auth()->id())->findOrFail($tripId);

        $catch = CatchModel::where('trip_id', $tripId)->with('details', 'details.fish')->first();
        if ($catch) {
            $fishQuntity = FishQuantityStock::with('fish', 'unit')
                ->where('catch_id', $catch->id)
                ->where('trip_id', $tripId)
                ->where('quantity', '>', 0)
                ->get();

            return response()->json($fishQuntity);
        }

        return response()->json([]);
    }
}
