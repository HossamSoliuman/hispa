<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Boat;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\Payroll;
use App\Models\Sale;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function fishQuntity(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $ownerId = Auth::guard('owner')->id() ?? Auth::id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        // Fetch boats for this owner
        $boats = Boat::where('owner_id', $ownerId)->get();
        $trips = Trip::where('owner_id', $ownerId)->get();
        $fishs = Fish::active()->get();

        $boatId = $request->input('boat_id');
        $tripId = $request->input('trip_id');
        $fishId = $request->input('fish_id');

        $stock = FishQuantityStock::whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);

        if ($boatId) {
            $stock = $stock->where('boat_id', $boatId);
        }

        if ($tripId) {
            $stock = $stock->where('trip_id', $tripId);
        }

        if ($fishId) {
            $stock = $stock->where('fish_id', $fishId);
        }

        $stocks = $stock->get();

        return view('owner.reports.fish_quntity', compact('stocks', 'from', 'to', 'boatId', 'boats', 'fishId', 'fishs', 'tripId', 'trips'));
    }

    public function assetDepreciation(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $ownerId = Auth::guard('owner')->id() ?? Auth::id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        $asset_type = $request->input('asset_type');
        $depreciation_method = $request->input('depreciation_method');
        $status = $request->input('status');

        $assets = Asset::with('latestDepreciation')->whereHas('latestDepreciation', function ($q) use ($from, $to) {
            $q->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        });

        if ($asset_type) {
            $assets = $assets->where('asset_type', $asset_type);
        }

        if ($depreciation_method) {
            $assets = $assets->where('depreciation_method', $depreciation_method);
        }

        if ($status) {
            $assets = $assets->where('status', $status);
        }

        $assets = $assets->get();

        return view('owner.reports.asset_depreciation', compact('assets', 'from', 'to', 'asset_type', 'depreciation_method', 'status'));
    }

    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $ownerId = Auth::guard('owner')->id() ?? Auth::id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        // Fetch boats for this owner
        $boats = Boat::where('owner_id', $ownerId)->get();

        $boatId = $request->input('boat_id');

        $salesQ = Sale::query()
            ->where('seller_type', 'owner')
            ->where('seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [$from, $to]);

        //        $expQ = Expense::query()
        //            ->where('owner_id', $ownerId)
        //            ->whereBetween('date', [$from, $to]);

        $tripCategoryIds = Category::whereIn('parent_id', [8, 24])
            ->pluck('id')
            ->toArray();
        $generalCategoryIds = Category::whereIn('parent_id', [1, 17])
            ->pluck('id')
            ->toArray();

        $tripExpQ = Expense::query()
            ->where('owner_id', $ownerId)
            ->whereIn('category_id', $tripCategoryIds)
            ->whereBetween('date', [$from, $to]);

        $generalExpQ = Expense::query()
            ->where('owner_id', $ownerId)
            ->whereIn('category_id', $generalCategoryIds)
            ->whereBetween('date', [$from, $to]);

        $payQ = Payroll::query()
            ->where('owner_id', $ownerId)
            ->where('status', 'closed')
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('period_from', [$from, $to])
                    ->orWhereBetween('period_to', [$from, $to])
                    ->orWhere(function ($q2) use ($from, $to) {
                        // يغطي كامل الفترة
                        $q2->where('period_from', '<=', $from)
                            ->where('period_to', '>=', $to);
                    });
            });

        if ($boatId) {
            $tripExpQ->where('boat_id', $boatId);
            $generalExpQ->where('boat_id', $boatId);
            $payQ->where('boat_id', $boatId);

        }

        $sales = (float) $salesQ->sum('total_price');
        $return = 0;
        $netSales = ($sales - $return);
        $trip_expenses = (float) $tripExpQ->sum('final_price');
        $general_expenses = (float) $generalExpQ->sum('final_price');
        $total_profit = ($netSales - $trip_expenses);
        $net = ($total_profit - $general_expenses);
        //        $expenses = (float) $expQ->sum('final_price');
        //        $payrolls = (float) $payQ->sum('crew_total');
        //        $net = $sales - ($expenses + $payrolls);

        return view('owner.report.profit_loss_new', compact(
            'from', 'to', 'boatId', 'boats', 'sales', 'total_profit', 'net', 'netSales', 'general_expenses', 'trip_expenses'
        ));
    }

    public function print(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $ownerId = Auth::guard('owner')->id() ?? Auth::id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        // Fetch boats for this owner
        $boats = Boat::where('owner_id', $ownerId)->get();

        $boatId = $request->input('boat_id');

        $salesQ = Sale::query()
            ->where('seller_type', 'owner')
            ->where('seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [$from, $to]);

        $expQ = Expense::query()
            ->where('owner_id', $ownerId)
            ->whereBetween('date', [$from, $to]);

        $payQ = Payroll::query()
            ->where('owner_id', $ownerId)
            ->where('status', 'closed')
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('period_from', [$from, $to])
                    ->orWhereBetween('period_to', [$from, $to])
                    ->orWhere(function ($q2) use ($from, $to) {
                        $q2->where('period_from', '<=', $from)
                            ->where('period_to', '>=', $to);
                    });
            });

        if ($boatId) {
            $expQ->where('boat_id', $boatId);
            $payQ->where('boat_id', $boatId);
        }

        $sales = (float) $salesQ->sum('total_price');
        $expenses = (float) $expQ->sum('final_price');
        $payrolls = (float) $payQ->sum('crew_total');
        $net = $sales - ($expenses + $payrolls);

        $settings = $this->getCompanySettings();

        return view('owner.report.profit_loss_print', compact(
            'from', 'to', 'boatId', 'boats', 'sales', 'expenses', 'payrolls', 'net', 'settings'
        ));
    }

    private function getCompanySettings(): array
    {
        $owner = Auth::guard('owner')->user();
        $companyName = $owner->name ?? 'N/A';
        $vat = $owner->vat_number ?? '';
        $qrPayload = "Company: {$companyName}\nVAT: {$vat}";

        return [
            'name' => $companyName,
            'company_name' => $companyName,
            'phone' => $owner->phone ?? 'N/A',
            'email' => $owner->email ?? 'N/A',
            'address' => $owner->address ?? 'N/A',
            'logo' => $owner->logo ?? null,
            'qr_code' => $this->generateQRCodeImage($qrPayload),
        ];
    }

    private function generateQRCodeImage(string $data): ?string
    {
        try {
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='.urlencode($data);
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'ignore_errors' => true,
                ],
            ]);

            $imageData = @file_get_contents($qrUrl, false, $context);

            if ($imageData !== false && ! empty($imageData)) {
                return 'data:image/png;base64,'.base64_encode($imageData);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return null;
    }
}
