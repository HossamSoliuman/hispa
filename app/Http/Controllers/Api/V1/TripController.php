<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use App\Repository\Api\TripRepository;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class TripController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * Display a listing of the resource.
     */
    private $rep;

    public function __construct()
    {
        $this->rep = new TripRepository;
    }

    public function index()
    {
        $request = null;

        return $this->rep->getList($request);
    }

    public function TripsAvailableCounter(Request $request)
    {
        $user = $request->user();

        if ($user->role != 'counter') {
            return $this->failure('مسموح فقط للعدادين', [], 403);
        }

        $query = Trip::where('status', 4)
            ->whereNull('counter_id')
            ->where(function ($q) use ($user) {
                $q->where('region_id', $user->region_id)
                    ->orWhere('governorate_id', $user->governorate_id);
                //                    ->orWhere('city_id', $user->city_id);
            })
            ->orderByDesc('created_at');

        $trips = $query->paginate(10);

        return $this->success('رحلات متاحة للعداد', TripResource::collection($trips));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, $id)
    {
        return $this->rep->updateData($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}
}
