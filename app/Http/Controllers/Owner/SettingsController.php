<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BoatType;
use App\Models\Category;
use App\Models\Fish;
use App\Models\Governorate;
use App\Models\Port;
use App\Models\Region;
use App\Models\User;

class SettingsController extends Controller
{
    public function index()
    {
        $data = Fish::OrderByDesc('id')->get();
        $regions = Region::Active()->get();
        $parents = Category::whereNull('parent_id')->get();
        $governorates = Governorate::OrderByDesc('id')->get();
        $ports = Port::Active()->get();
        $boatTypes = BoatType::orderByDesc('id')->get();
        $categories = Category::where('type', 'maintenance')->whereNotNull('parent_id')->get();
        $captains = User::Active()->CaptainRole()
            ->where('owner_id', auth()->id())
            ->select('id', 'name')
            ->get();

        return view('owner.settings.index', compact('data', 'regions', 'governorates', 'boatTypes', 'ports', 'parents', 'captains', 'categories'));
    }
}
