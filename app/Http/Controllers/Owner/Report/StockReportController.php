<?php

namespace App\Http\Controllers\Owner\Report;

use App\DataTable\Owner\Report\StockReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\Fish;
use App\Models\FishStock;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new StockReportDataTable;

    }

    public function index()
    {
        $fish = Fish::Active()->select('scientific_name as name', 'id')->get();

        return view('owner.report.stock', compact('fish'));
    }

    public function getStockData(Request $request)
    {
        return $this->datatable->getData($request);

    }

    public function print(Request $request)
    {
        $owner_id = auth()->user()->id;

        // Build query for fish stocks
        $query = FishStock::with(['fish', 'trip', 'addedBy', 'correctedBy'])
            ->whereHas('trip', function ($q) use ($owner_id) {
                $q->where('owner_id', $owner_id);
            });

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Fish type filter
        if ($request->filled('fish_type')) {
            $query->where('fish_id', $request->fish_type);
        }

        $stocks = $query->orderBy('created_at', 'desc')->get();

        // Transform data for display
        $stocks = $stocks->map(function ($stock) {
            return (object) [
                'name' => optional($stock->fish)->scientific_name ?? '---',
                'weight_captain' => $stock->weight_captain,
                'weight_counter' => $stock->weight_counter,
                'total_weight' => $stock->weight,
                'weight_difference' => abs($stock->weight_captain - $stock->weight_counter),
                'added_by' => optional($stock->addedBy)->name ?? '---',
                'correct_by' => optional($stock->correctedBy)->name ?? '---',
                'date' => $stock->created_at,
            ];
        });

        // Calculate totals
        $totalFishCount = $stocks->pluck('name')->unique()->count();
        $totalWeight = $stocks->sum('total_weight');

        // Get company settings
        $settings = $this->getCompanySettings();

        // Get filter values for display
        $from = $request->start_date ?? null;
        $to = $request->end_date ?? null;
        $fishName = null;
        if ($request->filled('fish_type')) {
            $fishName = Fish::find($request->fish_type)->scientific_name ?? null;
        }

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
     * Get company settings for report header
     */
    private function getCompanySettings()
    {
        return ownerCompanySettings([
            'qr_code' => $this->generateQRCodeImage(),
        ]);
    }

    /**
     * Generate QR code image for the report
     */
    private function generateQRCodeImage()
    {
        $companyName = currentCompany()?->name ?: 'حسبة';

        $qrData = "Company: {$companyName}";

        return app(\App\Service\Owner\ReportQrService::class)->dataUri($qrData)
            ?? 'data:image/svg+xml;base64,'.base64_encode('<svg width="200" height="200"><rect fill="#f0f0f0" width="200" height="200"/></svg>');
    }
}
