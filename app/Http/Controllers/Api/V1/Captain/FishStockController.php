<?php

namespace App\Http\Controllers\Api\V1\Captain;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FishStockRequest;
use App\Repository\Api\FishStockRepository;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class FishStockController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * Display a listing of the resource.
     */
    private $rep;

    public function __construct()
    {
        $this->rep = new FishStockRepository;
    }

    public function index(Request $request)
    {

        return $this->rep->getList($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FishStockRequest $request)
    {
        return $this->rep->saveData($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FishStockRequest $request, $id)
    {
        return $this->rep->updateData($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->rep->deleteData($id);
    }
}
