<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\DalalStockDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DalalStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $datatable;

    public function __construct()
    {
        $this->datatable = new DalalStockDataTable;

    }

    public function showDalalBoatStock()
    {
        return view('owner.dalal-stock.index');

    }

    public function getDalalStockBoatData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    public function showBoat($id)
    {
        return view('owner.dalal-stock.show-trip', compact('id'));
    }

    public function getBoatTripData(Request $request, $boatId)
    {
        return $this->datatable->showBoatData($request, $boatId);
    }

    public function showTrip($id)
    {
        return view('owner.dalal-stock.show-stock', compact('id'));
    }

    public function getTripDalalData(Request $request, $tripId)
    {
        return $this->datatable->showTripData($request, $tripId);

    }

    public function showDalal($id)
    {
        return view('owner.dalal-stock.show-transaction', compact('id'));
    }

    public function getDalalTransactionData(Request $request, $dalal_id)
    {
        return $this->datatable->showDalalTransaction($request, $dalal_id);
    }

    public function getSaleDetails($sale_id)
    {
        $details = DB::table('sale_details')
            ->where('sale_id', $sale_id)
            ->select(
                'dalal_stock_detail_id',
                'fish_name',
                'weight',
                'quantity',
                'price_per_kilo',
                DB::raw('weight * price_per_kilo as total_price')
            )
            ->get();

        return response()->json($details);
    }

    public function getRemainingStock($dalal_stock_detail_id)
    {
        $totalWeight = DB::table('dalal_stock_details')
            ->where('id', $dalal_stock_detail_id)
            ->value('weight');

        //        $soldWeight = DB::table('sale_details')
        //            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
        //            ->where('sale_details.dalal_stock_detail_id', $dalal_stock_detail_id)
        //            ->where('sales.seller_type', 'dalal')
        //            ->sum('sale_details.weight');

        return response()->json([
            'remaining_weight' => max($totalWeight, 0),
        ]);
    }
}
