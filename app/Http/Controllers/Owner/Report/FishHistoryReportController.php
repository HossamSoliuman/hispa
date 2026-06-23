<?php

namespace App\Http\Controllers\Owner\Report;

use App\DataTable\Owner\Report\FishHistoryReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\Fish;
use App\Models\FishStockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FishHistoryReportController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new FishHistoryReportDataTable;

    }

    public function index()
    {
        $fish = Fish::Active()->select('scientific_name as name', 'id')->get();

        return view('owner.report.fish_history', compact('fish'));
    }

    public function getFishHistoryData(Request $request)
    {
        return $this->datatable->getData($request);

    }

    public function print(Request $request)
    {
        $ownerId = Auth::guard('owner')->id() ?? Auth::id();

        $query = FishStockHistory::with(['fish', 'stock.trip.owner'])
            ->whereHas('stock.trip', function ($builder) use ($ownerId) {
                $builder->where('owner_id', $ownerId);
            })
            ->select('fish_stock_histories.*')
            ->selectRaw('(
                SELECT SUM(fsh2.changed_weight)
                FROM fish_stock_histories fsh2
                JOIN fish_stocks fs2 ON fs2.id = fsh2.fish_stock_id
                JOIN trips t2 ON t2.id = fs2.trip_id
                WHERE fsh2.fish_id = fish_stock_histories.fish_id
                  AND t2.owner_id = ?
                  AND fsh2.created_at <= fish_stock_histories.created_at
            ) as remaining_weight', [$ownerId]);

        if ($request->filled('start_date')) {
            $query->whereDate('fish_stock_histories.created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('fish_stock_histories.created_at', '<=', $request->end_date);
        }

        if ($request->filled('fish_id')) {
            $query->where('fish_stock_histories.fish_id', $request->fish_id);
        }

        $records = $query->orderBy('fish_stock_histories.created_at', 'desc')->get();

        $totalRecords = $records->count();
        $totalFishTypes = $records->pluck('fish_id')->filter()->unique()->count();
        $totalCatch = $records->filter(fn ($record) => $record->changed_weight > 0)->sum('changed_weight');
        $totalWeight = $this->calculateTotalWeight($ownerId, $request);

        $settings = $this->getCompanySettings();

        $from = $request->start_date ?? null;
        $to = $request->end_date ?? null;
        $fishName = null;

        if ($request->filled('fish_id')) {
            $fishName = Fish::find($request->fish_id)->scientific_name ?? null;
        }

        $filename = 'fish-history-'.($from ?? 'all').'-to-'.($to ?? 'all').'.pdf';

        return pdf_report(view('owner.report.fish_history_print', compact(
            'records',
            'totalRecords',
            'totalFishTypes',
            'totalCatch',
            'totalWeight',
            'settings',
            'from',
            'to',
            'fishName'
        )), [], $filename);
    }

    private function calculateTotalWeight(int $ownerId, Request $request): float
    {
        $query = FishStockHistory::join('fish_stocks as fs', 'fs.id', '=', 'fish_stock_histories.fish_stock_id')
            ->join('trips as t', 't.id', '=', 'fs.trip_id')
            ->where('t.owner_id', $ownerId);

        if ($request->filled('fish_id')) {
            $query->where('fish_stock_histories.fish_id', $request->fish_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('fish_stock_histories.created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('fish_stock_histories.created_at', '<=', $request->end_date);
        }

        return (float) $query->sum('fish_stock_histories.changed_weight');
    }

    private function getCompanySettings(): array
    {
        $companyName = currentCompany()?->name ?: 'حسبة';

        return ownerCompanySettings([
            'qr_code' => $this->generateQRCodeImage($companyName),
        ]);
    }

    private function generateQRCodeImage(string $companyName): string
    {
        $qrData = "Company: {$companyName}";

        return app(\App\Service\Owner\ReportQrService::class)->dataUri($qrData)
            ?? 'data:image/svg+xml;base64,'.base64_encode('<svg width="200" height="200"><rect fill="#f0f0f0" width="200" height="200"/></svg>');
    }
}
