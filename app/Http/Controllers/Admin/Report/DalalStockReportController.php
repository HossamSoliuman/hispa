<?php

namespace App\Http\Controllers\Admin\Report;

use App\DataTable\Report\DalalStockReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\DalalStock;
use App\Models\User;
use Illuminate\Http\Request;

class DalalStockReportController extends Controller
{
    private DalalStockReportDataTable $datatable;

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

    public function print(Request $request)
    {
        $query = DalalStock::with(['dalal', 'details.fish']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $dalalId = $request->filled('dalal_id') ? $request->dalal_id : ($request->dalal_id_filter ?? null);

        if (! empty($dalalId)) {
            $query->where('dalal_id', $dalalId);
        }

        $stocks = $query->orderBy('created_at', 'desc')->get()
            ->map(function (DalalStock $stock): object {
                $fishNames = $stock->details
                    ->pluck('fish_name')
                    ->filter()
                    ->unique()
                    ->values();

                return (object) [
                    'dalal_name' => optional($stock->dalal)->name ?? '---',
                    'fish_name' => $fishNames->isEmpty() ? '---' : $fishNames->implode(', '),
                    'total_weight' => $stock->total_weight ?? $stock->details->sum('weight'),
                    'date' => $stock->created_at,
                ];
            });

        $totalFishCount = $stocks->pluck('fish_name')->unique()->count();
        $totalWeight = $stocks->sum('total_weight');
        $totalDalalCount = $stocks->pluck('dalal_name')->unique()->count();

        $settings = $this->getCompanySettings();

        $from = $request->start_date ?? null;
        $to = $request->end_date ?? null;
        $dalalName = ! empty($dalalId) ? User::find($dalalId)?->name : null;

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
     * Get platform company settings for the report header.
     *
     * @return array<string, mixed>
     */
    private function getCompanySettings(): array
    {
        return adminReportCompanySettings([
            'qr_code' => null,
        ]);
    }
}
