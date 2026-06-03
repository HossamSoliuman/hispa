<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\StockDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new StockDataTable;

    }

    public function index()
    {
        return view('owner.stock.index');
    }

    public function getStockData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    public function getShowDetailStockData(Request $request, $fish_id)
    {
        return $this->datatable->getShowData($request, $fish_id);
    }

    public function show($id)
    {
        return view('owner.stock.show', compact('id'));
    }
}
