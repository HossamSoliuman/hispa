<?php

namespace App\Http\Controllers\Api\V1\Dalal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FishStockRequest;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use App\Repository\Api\DalalStockRepository;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class DalalStockController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * Display a listing of the resource.
     */
    private $rep;

    public function __construct()
    {
        $this->rep = new DalalStockRepository;
    }

    public function index(Request $request)
    {
        return $this->rep->getList($request);
    }

    public function getDalals()
    {
        $users = User::DalalRole()->Active()->get();

        return $this->success('تم جلب تفاصيل الدلالين بنجاح', ProfileResource::collection($users), 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FishStockRequest $request) {}

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
    public function update(FishStockRequest $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}
}
