<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Port;

class LocationController extends Controller
{
    public function getGovernorates($region_id)
    {
        $governorates = Governorate::where('region_id', $region_id)->get();

        return response()->json($governorates);
    }

    //    public function getCities($governorate_id)
    //    {
    //        $cities = City::where('governorate_id', $governorate_id)->get();
    //        return response()->json($cities);
    //    }
    public function getPorts($governorate_id)
    {
        $ports = Port::where('governorate_id', $governorate_id)->get(['id', 'name']);

        return response()->json($ports);
    }
}
