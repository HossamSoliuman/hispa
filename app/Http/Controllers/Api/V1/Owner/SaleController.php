<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SaleRequest;
use App\Http\Requests\Api\UpdateSaleRequest;
use App\Repository\Api\SaleRepository;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * Display a listing of the resource.
     */
    private $rep;

    public function __construct()
    {
        $this->rep = new SaleRepository;
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
    public function store(SaleRequest $request)
    {
        return $this->rep->saveData($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
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
    public function destroy(Request $request, string $id)
    {
        return $this->rep->deleteData($id);
    }

    public function destroySalesDetails(Request $request, string $id)
    {
        return $this->rep->deleteDetailData($id);

    }
}
