<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BoatType;
use App\Models\Category;
use App\Models\Fish;
use App\Models\Governorate;
use App\Models\Port;
use App\Models\Region;

class SettingsController extends Controller
{
    public function index()
    {
        $data = Fish::OrderByDesc('id')->get();
        $regions = Region::Active()->get();
        $parents = Category::whereNull('parent_id')->get();
        $governorates = Governorate::OrderByDesc('id')->get();
        $ports = Port::Active()->get();
        $boatTypes = BoatType::Active()->get();

        return view('owner.settings.index', compact('data', 'regions', 'governorates', 'boatTypes', 'ports', 'parents'));
    }
}
