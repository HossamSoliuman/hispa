<?php

namespace App\Http\Controllers\Admin\Report;

use App\DataTable\Report\StockReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\CatchDetail;
use App\Models\Fish;
use App\Models\FishStock;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class StockReportController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new StockReportDataTable;
        $this->middleware('permission:read_stock_report', ['only' => ['index', 'show']]);
    }

    public function index()
    {
        $fish = Fish::Active()->get();

        return view('admin.report.stock', compact('fish'));
    }

    public function getStockData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    /**
     * Render the printable stock report (catch weights across all owners) using the
     * shared, standard-compliant view (x-report-layout). Admin sees every owner's
     * stock, so the owner-scoped filter applied in the owner report is omitted here.
     */
    public function print(Request $request)
    {
        $stocks = $this->stocksForPrint($request);

        $totalFishCount = $stocks->pluck('name')->unique()->count();
        $totalWeight = $stocks->sum('total_weight');

        $settings = $this->getCompanySettings();

        $from = $request->start_date ?? null;
        $to = $request->end_date ?? null;
        $fishName = $request->filled('fish_type')
            ? optional(Fish::find($request->fish_type))->name
            : null;

        $filename = 'stock-report-'.($from ?? 'all').'-to-'.($to ?? 'all').'.pdf';

        return pdf_report(view('owner.report.stock_print', compact(
            'stocks',
            'totalFishCount',
            'totalWeight',
            'settings',
            'from',
            'to',
            'fishName'
        )), [], $filename);
    }

    /**
     * Build the printable stock rows. Mirrors StockReportDataTable: prefer the
     * fish_stocks reconciliation table and fall back to a catch_details
     * aggregation when it is absent, so the printed report matches the on-screen
     * table and never queries a missing table.
     *
     * @return Collection<int, object>
     */
    private function stocksForPrint(Request $request): Collection
    {
        if (Schema::hasTable('fish_stocks')) {
            return $this->stocksFromFishStocks($request);
        }
        if (Schema::hasTable('catch_details')) {
            return $this->stocksFromCatchDetails($request);
        }

        return collect();
    }

    /**
     * @return Collection<int, object>
     */
    private function stocksFromFishStocks(Request $request): Collection
    {
        $query = FishStock::with(['fish', 'trip', 'addedBy', 'correctedBy']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('fish_type')) {
            $query->where('fish_id', $request->fish_type);
        }

        return $query->orderBy('created_at', 'desc')->get()->map(function ($stock) {
            return (object) [
                'name' => optional($stock->fish)->name ?? '---',
                'weight_captain' => $stock->weight_captain,
                'weight_counter' => $stock->weight_counter,
                'total_weight' => $stock->weight,
                'weight_difference' => abs(($stock->weight_captain ?? 0) - ($stock->weight_counter ?? 0)),
                'added_by' => optional($stock->addedBy)->name ?? '---',
                'correct_by' => optional($stock->correctedBy)->name ?? '---',
                'date' => $stock->created_at,
            ];
        });
    }

    /**
     * @return Collection<int, object>
     */
    private function stocksFromCatchDetails(Request $request): Collection
    {
        $query = CatchDetail::query()
            ->selectRaw(
                'catch_details.fish_id,
                fish.name_ar as fish_name_ar,
                fish.name_en as fish_name_en,
                MAX(addedUser.name) as added_by_name,
                MAX(catch_details.created_at) as created_at,
                SUM(catch_details.weight) as total_weight'
            )
            ->join('fish', 'catch_details.fish_id', '=', 'fish.id')
            ->leftJoin('catch_models', 'catch_details.catch_id', '=', 'catch_models.id')
            ->leftJoin('users as addedUser', 'catch_models.owner_id', '=', 'addedUser.id')
            ->groupBy('catch_details.fish_id', 'fish.name_ar', 'fish.name_en')
            ->orderByDesc('total_weight');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('catch_details.created_at', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('fish_type')) {
            $query->where('catch_details.fish_id', $request->fish_type);
        }

        return $query->get()->map(function ($row) {
            return (object) [
                'name' => (app()->getLocale() === 'en' ? $row->fish_name_en : $row->fish_name_ar) ?: '---',
                'weight_captain' => $row->total_weight,
                'weight_counter' => $row->total_weight,
                'total_weight' => $row->total_weight,
                'weight_difference' => 0,
                'added_by' => $row->added_by_name ?? '---',
                'correct_by' => '---',
                'date' => $row->created_at,
            ];
        });
    }

    /**
     * Get platform company settings for the report header (admin oversees all owners,
     * so these come from the global Setting table rather than a per-owner company).
     *
     * @return array<string, mixed>
     */
    private function getCompanySettings(): array
    {
        return adminReportCompanySettings();
    }
}
