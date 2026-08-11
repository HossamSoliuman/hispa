<?php

namespace App\Http\Controllers\Admin\Report;

use App\DataTable\Report\StockReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\CatchDetail;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StockReportController extends Controller
{
    private StockReportDataTable $datatable;

    public function __construct()
    {
        $this->datatable = new StockReportDataTable;
        $this->middleware('permission:read_stock_report', ['only' => ['index', 'show']]);
    }

    public function index(): View
    {
        $fish = Fish::Active()->get();

        return view('admin.report.stock', compact('fish'));
    }

    /**
     * Server-side DataTables payload; null when the request is not an AJAX call.
     */
    public function getStockData(Request $request): ?JsonResponse
    {
        return $this->datatable->getData($request);
    }

    /**
     * Render the printable stock report (catch weights across all owners) using the
     * shared, standard-compliant view (x-report-layout). Admin sees every owner's
     * stock, so the owner-scoped filter applied in the owner report is omitted here.
     */
    public function print(Request $request): Response
    {
        $stocks = $this->stocksForPrint($request);

        $totalFishCount = $stocks->pluck('name')->unique()->count();
        $totalWeight = formatWeightByUnit($stocks->map(fn (object $stock) => (object) [
            'weight' => $stock->total_weight,
            'unit' => $stock->unit,
        ]));

        $settings = $this->getCompanySettings();

        $from = $request->start_date ?? null;
        $to = $request->end_date ?? null;
        $fishName = $request->filled('fish_type')
            ? optional(Fish::find($request->fish_type))->name
            : null;

        $filename = 'stock-report-'.($from ?? 'all').'-to-'.($to ?? 'all').'.pdf';

        return pdf_report(view('owner.report.stock_print', array_merge(compact(
            'stocks',
            'totalFishCount',
            'totalWeight',
            'settings',
            'from',
            'to',
            'fishName'
        ), [
            'reportScope' => 'platform',
            'showOwnerColumn' => true,
        ])), [], $filename);
    }

    /**
     * Build printable stock rows from landed catch details. These records retain
     * the original weight and selected unit after their remaining stock has been
     * reduced by sales.
     *
     * @return Collection<int, object>
     */
    private function stocksForPrint(Request $request): Collection
    {
        if (Schema::hasTable('catch_details')) {
            return $this->stocksFromCatchDetails($request);
        }
        if (Schema::hasTable('fish_quantity_stocks')) {
            return $this->stocksFromFishStocks($request);
        }

        return collect();
    }

    /**
     * @return Collection<int, object>
     */
    private function stocksFromFishStocks(Request $request): Collection
    {
        $query = FishQuantityStock::with(['fish', 'unit', 'trip.owner', 'trip.boat.captain']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('fish_type')) {
            $query->where('fish_id', $request->fish_type);
        }

        return $query->orderBy('created_at', 'desc')->get()->map(function (FishQuantityStock $stock): object {
            $unit = $stock->unit?->exists === true ? $stock->unit : null;

            return (object) [
                'name' => optional($stock->fish)->name ?? '---',
                'owner_name' => $stock->trip?->owner?->name ?? '---',
                'weight_captain' => $stock->quantity,
                'weight_counter' => null,
                'total_weight' => $stock->quantity,
                'weight_difference' => null,
                'unit' => $unit,
                'unit_display' => $this->localizedUnitName($unit),
                'added_by' => $stock->trip?->captain?->name ?? $stock->trip?->boat?->captain?->name ?? '---',
                'correct_by' => '---',
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
            ->with(['fish', 'unit', 'catch.trip.owner', 'catch.trip.captain', 'catch.trip.boat.captain'])
            ->orderByDesc('created_at');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('fish_type')) {
            $query->where('catch_details.fish_id', $request->fish_type);
        }

        return $query->get()->map(function (CatchDetail $row): object {
            $unit = $row->unit?->exists === true ? $row->unit : null;

            return (object) [
                'name' => $row->fish?->name ?? '---',
                'owner_name' => $row->catch?->trip?->owner?->name ?? '---',
                'weight_captain' => $row->weight,
                'weight_counter' => null,
                'total_weight' => $row->weight,
                'weight_difference' => null,
                'unit' => $unit,
                'unit_display' => $this->localizedUnitName($unit),
                'added_by' => $row->catch?->trip?->captain?->name ?? $row->catch?->trip?->boat?->captain?->name ?? '---',
                'correct_by' => '---',
                'date' => $row->created_at,
            ];
        });
    }

    private function localizedUnitName(?Unit $unit): ?string
    {
        if ($unit === null) {
            return null;
        }

        $localizedColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        $name = $unit->getRawOriginal($localizedColumn)
            ?? $unit->getRawOriginal('name_en')
            ?? $unit->getRawOriginal('name_ar');

        return is_string($name) && $name !== '' ? $name : null;
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
