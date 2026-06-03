<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FishDalalStockRequest;
use App\Repository\Api\FishDalalStockRepository;
use App\Traits\DalalStockStatusChecker;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class FishDalalStockController extends Controller
{
    use DalalStockStatusChecker, RespondsWithHttpStatus;

    /**
     * Display a listing of the resource.
     */
    private $rep;

    public function __construct()
    {
        $this->rep = new FishDalalStockRepository;
    }

    public function index(Request $request)
    {

        return $this->rep->getList($request);

    }

    public function update_status_stock(Request $request)
    {
        return $this->rep->updateStaus($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FishDalalStockRequest $request)
    {
        return $this->rep->saveData($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        return $this->rep->getDetail($id);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FishDalalStockRequest $request, $id)
    {

        return $this->rep->updateData($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyStockDetail(Request $request, $id)
    {
        return $this->rep->deleteDataDetail($id);
    }

    public function destroy($id)
    {
        return $this->rep->deleteData($id);
    }
}
