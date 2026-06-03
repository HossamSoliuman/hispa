<?php

namespace App\Http\Controllers\Api\V1\Dalal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DalalSaleRequest;
use App\Http\Requests\Api\UpdateSaleRequest;
use App\Repository\Api\DalalSaleRepository;
use App\Traits\DalalStockStatusChecker;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class DalalSaleController extends Controller
{
    use DalalStockStatusChecker,RespondsWithHttpStatus;

    /**
     * Display a listing of the resource.
     */
    private $rep;

    public function __construct()
    {
        $this->rep = new DalalSaleRepository;
    }

    public function index(Request $request)
    {
        return $this->rep->getList($request);
    }

    public function finish_sales(Request $request)
    {
        return $this->rep->updateStaus($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DalalSaleRequest $request)
    {
        return $this->rep->saveData($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->rep->getDetail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleRequest $request, $id)
    {
        return $this->rep->updateData($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroySalesDetails(Request $request, string $id)
    {
        return $this->rep->deleteDetailData($id);
    }

    public function destroy(Request $request, string $id)
    {
        return $this->rep->deleteData($id);
    }
}
