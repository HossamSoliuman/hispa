<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CompanyRequest;
use App\Models\BoatType;
use App\Models\Category;
use App\Models\Fish;
use App\Models\Governorate;
use App\Models\Port;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

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

        $company = currentCompany();

        return view('owner.settings.index', compact('data', 'regions', 'governorates', 'boatTypes', 'ports', 'parents', 'captains', 'categories', 'company'));
    }

    /**
     * Store the per-owner company profile (name, registration numbers, contact
     * details and logo) used across the panel and printable reports.
     */
    public function updateCompany(CompanyRequest $request): RedirectResponse
    {
        $company = currentCompany();

        $data = $request->safe()->except('logo');

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                deleteFile($company->logo);
            }

            $data['logo'] = UploadFile($request->file('logo'), 'uploads/companies');
        }

        $company->update($data);

        return redirect()
            ->route('owner.settings.index', ['tab' => 'company'])
            ->with('success', __('owner.generated.logo_updated'));
    }
}
