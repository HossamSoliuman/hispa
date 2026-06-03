<?php

namespace App\Http\Controllers\Admin;

use App\DataTable\Owner\StockDataTable;
use App\Http\Controllers\Controller;
use App\Models\Fish;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function __construct(
        private readonly StockDataTable $stockDataTable
    ) {
        $this->middleware('auth:admin');
    }

    /**
     * Display stocks index (admin layout – all fish stock aggregated).
     */
    public function index(): View
    {
        return view('admin.stocks.index');
    }

    /**
     * Display stock detail by fish id (admin layout).
     */
    public function show(string $id): View
    {
        $fish = Fish::find($id);

        return view('admin.stocks.show', [
            'id' => $id,
            'fish' => $fish,
        ]);
    }

    /**
     * DataTable AJAX: aggregated stock by fish.
     */
    public function getStockData(Request $request)
    {
        return $this->stockDataTable->getData($request);
    }

    /**
     * DataTable AJAX: stock detail rows for a fish.
     */
    public function getShowDetailStockData(Request $request, string $id)
    {
        return $this->stockDataTable->getShowData($request, $id);
    }
}
