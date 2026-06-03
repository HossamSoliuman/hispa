<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RegionResource;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Port;
use App\Models\Region;
use App\Traits\RespondsWithHttpStatus;

class LocationController extends Controller
{
    use RespondsWithHttpStatus;

    public function getLocation()
    {
        $regions = Region::Active()->select('id', 'name')->get();

        return $this->success(trans('site.getData'), RegionResource::collection($regions), 200);

    }

    public function getRegions()
    {
        $regions = Region::Active()->select('id', 'name')->get();

        return $this->success(trans('site.getData'), $regions, 200);
    }

    public function getGovernorates($region_id)
    {
        $governorates = Governorate::Active()->where('region_id', $region_id)->select('id', 'name')->get();

        return $this->success(trans('site.getData'), $governorates, 200);
    }
    //    public function getCities($governorate_id)
    //    {
    //        $cities = City::Active()->where('governorate_id',$governorate_id)->select('id', 'name')->get();
    //
    //        return $this->success(trans('site.getData'),$cities,200);
    //    }

    public function getPorts($governorate_id)
    {
        $ports = Port::Active()->where('port_id', $governorate_id)->select('id', 'name')->get();

        return $this->success(trans('site.getData'), $ports, 200);
    }
}
