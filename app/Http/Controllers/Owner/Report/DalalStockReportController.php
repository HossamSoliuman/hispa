<?php

namespace App\Http\Controllers\Owner\Report;

use App\DataTable\Owner\Report\DalalStockReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\DalalStock;
use App\Models\User;
use Illuminate\Http\Request;

class DalalStockReportController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new DalalStockReportDataTable;

    }

    public function index()
    {
        $dalals = User::DalalRole()->get();

        return view('owner.report.dalal-stock', compact('dalals'));
    }

    public function getStockData(Request $request)
    {
        return $this->datatable->getData($request);

    }

    public function print(Request $request)
    {
        $owner_id = auth()->user()->id;

        // Build query for dalal stocks
        $query = DalalStock::with(['dalal', 'fish'])
            ->whereHas('dalal.owner', function ($q) use ($owner_id) {
                $q->where('id', $owner_id);
            });

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Dalal filter
        if ($request->filled('dalal_id_filter')) {
            $query->where('user_id', $request->dalal_id_filter);
        }

        $stocks = $query->orderBy('created_at', 'desc')->get();

        // Transform data
        $stocks = $stocks->map(function ($stock) {
            return (object) [
                'dalal_name' => optional($stock->dalal)->name ?? '---',
                'fish_name' => optional($stock->fish)->scientific_name ?? '---',
                'total_weight' => $stock->weight,
                'date' => $stock->created_at,
            ];
        });

        // Calculate totals
        $totalFishCount = $stocks->pluck('fish_name')->unique()->count();
        $totalWeight = $stocks->sum('total_weight');
        $totalDalalCount = $stocks->pluck('dalal_name')->unique()->count();

        // Get company settings
        $settings = $this->getCompanySettings();

        // Get filter values
        $from = $request->start_date ?? null;
        $to = $request->end_date ?? null;
        $dalalName = null;
        if ($request->filled('dalal_id_filter')) {
            $dalalName = User::find($request->dalal_id_filter)->name ?? null;
        }

        $filename = 'dalal-stock-'.($from ?? 'all').'-to-'.($to ?? 'all').'.pdf';

        return pdf_report(view('owner.report.dalal_stock_print', compact(
            'stocks',
            'totalFishCount',
            'totalWeight',
            'totalDalalCount',
            'settings',
            'from',
            'to',
            'dalalName'
        )), [], $filename);
    }

    private function getCompanySettings()
    {
        return ownerCompanySettings([
            'qr_code' => $this->generateQRCodeImage(),
        ]);
    }

    private function generateQRCodeImage()
    {
        $companyName = currentCompany()?->name ?: 'حسبة';

        $qrData = "Company: {$companyName}";

        return app(\App\Service\Owner\ReportQrService::class)->dataUri($qrData)
            ?? 'data:image/svg+xml;base64,'.base64_encode('<svg width="200" height="200"><rect fill="#f0f0f0" width="200" height="200"/></svg>');
    }
}
