<?php

namespace App\Http\Controllers\Admin\Report;

use App\DataTable\Report\StockReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\CatchDetail;
use App\Models\Fish;
use App\Models\FishStock;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
     * Print or export the stock report (HTML or PDF)
     * يعتمد على catch_details إذا لم يكن جدول fish_stocks موجوداً.
     */
    public function print(Request $request)
    {
        $fromDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $toDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        if (Schema::hasTable('fish_stocks')) {
            $stocks = $this->getFishStocksForPrint($request, $fromDate, $toDate);
            $statistics = $this->fishStocksStatistics($stocks);
        } else {
            $stocks = $this->getCatchDetailsForPrint($request, $fromDate, $toDate);
            $statistics = $this->catchDetailsStatistics($stocks);
        }

        $settings = [
            'title' => config('app.name'),
            'company_name' => config('app.name'),
            'logo' => public_path('default-logo.png'),
            'watermark' => public_path('default-logo.png'),
            'cr_number' => '',
            'phone' => config('app.phone', ''),
            'email' => config('app.email', ''),
            'address' => config('app.address', ''),
        ];

        if ($request->query('pdf') && class_exists(Dompdf::class)) {
            $html = view('dalal.reports.print.stock-component', compact('stocks', 'statistics', 'settings', 'fromDate', 'toDate'))->render();
            $options = new Options;
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="stock-report.pdf"',
            ]);
        }

        return view('dalal.reports.print.stock-component', compact('stocks', 'statistics', 'settings', 'fromDate', 'toDate'));
    }

    private function getFishStocksForPrint(Request $request, Carbon $fromDate, Carbon $toDate)
    {
        $query = FishStock::with(['fish', 'addedBy', 'correctedBy'])->whereBetween('created_at', [$fromDate, $toDate]);
        if ($request->filled('fish_type')) {
            $query->where('fish_id', $request->fish_type);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    private function getCatchDetailsForPrint(Request $request, Carbon $fromDate, Carbon $toDate)
    {
        $query = CatchDetail::with(['fish', 'catch.trip'])
            ->whereBetween('catch_details.created_at', [$fromDate, $toDate]);
        if ($request->filled('fish_type')) {
            $query->where('catch_details.fish_id', $request->fish_type);
        }

        return $query->orderBy('catch_details.created_at', 'desc')->get();
    }

    private function fishStocksStatistics($stocks): array
    {
        return [
            'total_fish_count' => $stocks->count(),
            'total_added_qty' => $stocks->sum('quantity'),
            'total_corrected_qty' => $stocks->sum('quantity_counter'),
            'total_stocks' => $stocks->count(),
            'total_weight' => $stocks->sum('weight'),
            'grand_total' => 0,
        ];
    }

    private function catchDetailsStatistics($stocks): array
    {
        return [
            'total_fish_count' => $stocks->count(),
            'total_added_qty' => 0,
            'total_corrected_qty' => 0,
            'total_stocks' => $stocks->count(),
            'total_weight' => $stocks->sum('weight'),
            'grand_total' => 0,
        ];
    }
}
