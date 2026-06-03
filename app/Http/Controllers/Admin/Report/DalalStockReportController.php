<?php

namespace App\Http\Controllers\Admin\Report;

use App\DataTable\Report\DalalStockReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\DalalStock;
use App\Models\Fish;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class DalalStockReportController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new DalalStockReportDataTable;
        $this->middleware('permission:read_dalal_stock_report', ['only' => ['index', 'show']]);

    }

    public function index()
    {
        $dalals = User::DalalRole()->get();

        return view('admin.report.dalal-stock', compact('dalals'));
    }

    public function getStockData(Request $request)
    {
        return $this->datatable->getData($request);

    }

    /**
     * Render printable dalal stock report for admin
     * Accepts start_date, end_date and dalal_id or dalal_id_filter
     */
    public function print(Request $request)
    {
        // Build query for dalal stocks (admin can see all)
        // Use details.fish since DalalStock doesn't have a direct 'fish' relation
        $query = DalalStock::with(['dalal', 'details.fish']);

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Dalal filter (accept either name used by AJAX or the print form)
        $dalalId = $request->filled('dalal_id') ? $request->dalal_id : ($request->dalal_id_filter ?? null);
        if (! empty($dalalId)) {
            // DalalStock stores the dalal relation in 'dalal_id'
            $query->where('dalal_id', $dalalId);
        }

        $stocks = $query->orderBy('created_at', 'desc')->get();

        // Transform data for the printable view
        $stocks = $stocks->map(function ($stock) {
            // collect fish names from details (could be multiple species)
            $fishNames = $stock->details->pluck('fish_name')->filter()->unique()->values();
            $fishLabel = $fishNames->isEmpty() ? '---' : $fishNames->implode(', ');

            $totalWeight = $stock->total_weight ?? $stock->details->sum('weight');

            return (object) [
                'dalal_name' => optional($stock->dalal)->name ?? '---',
                'fish_name' => $fishLabel,
                'total_weight' => $totalWeight,
                'date' => $stock->created_at,
            ];
        });

        // Calculate totals
        $totalFishCount = $stocks->pluck('fish_name')->unique()->count();
        $totalWeight = $stocks->sum('total_weight');
        $totalDalalCount = $stocks->pluck('dalal_name')->unique()->count();

        // Get company settings
        $settings = $this->getCompanySettings();

        // Filter display values
        $from = $request->start_date ?? null;
        $to = $request->end_date ?? null;
        $dalalName = null;
        if (! empty($dalalId)) {
            $dalalName = User::find($dalalId)->name ?? null;
        }

        return view('admin.report.dalal_stock_print', compact(
            'stocks',
            'totalFishCount',
            'totalWeight',
            'totalDalalCount',
            'settings',
            'from',
            'to',
            'dalalName'
        ));
    }

    /**
     * Get company settings for report header
     */
    private function getCompanySettings()
    {
        $companyName = Setting::where('key', 'site_name')->value('value') ?? 'حسبة';

        return [
            'name' => $companyName,
            'company_name' => $companyName,
            'address' => Setting::where('key', 'address')->value('value') ?? '',
            'phone' => Setting::where('key', 'phone')->value('value') ?? '',
            'email' => Setting::where('key', 'email')->value('value') ?? '',
            'logo' => Setting::where('key', 'logo')->value('value') ?? '',
            'qr_code' => null,
        ];
    }
}
