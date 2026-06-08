<?php

namespace App\Repository;

use App\Interfaces\CRUD;
use App\Models\Boat;
use App\Models\BoatType;
use App\Models\Category;
use App\Models\Payroll;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class BoatRepository implements CRUD
{
    public function getList($request)
    {
        if ($request['guard'] == 'owner') {
            $boats = Boat::where('owner_id', auth()->user()->id)->get();
            $categories = Category::where('type', 'maintenance')
                ->whereNotNull('parent_id')
                ->get();

            return view('owner.boats.index', compact('boats', 'categories'));
        } else {
            $boat_types = BoatType::Active()
                ->orderBy(App::getLocale() === 'ar' ? 'name_ar' : 'name_en')
                ->get();

            return view('admin.boats.index', compact('boat_types'));
        }
    }

    public function getDetail($id)
    {
        $boat = Boat::findOrFail($id);

        // Check if user is admin or owner of the boat
        if (auth('admin')->check()) {
            // Admin can view any boat
        } elseif (auth()->check()) {
            // Owner can only view their own boats
            if (auth()->user()->id != $boat->owner_id) {
                return abort(403, 'ليس لديك صلاحيه لعرض هذه البيانات');
            }
        } else {
            return abort(403, 'ليس لديك صلاحيه لعرض هذه البيانات');
        }

        $crewStats = $this->getCrewStatusCount($boat);
        $payrolls = Payroll::where('boat_id', $boat->id)->sum('crew_total');
        $totalCatch = 0; // trip_details removed
        $revenues = $boat->trips->flatMap->sales->sum('total_price');
        $expenses = $boat->expenses()->sum('final_price');
        $lastMaintenance = $boat->maintenances()->latest()->first();

        $lastTrip = $boat->trips()->latest()->first();
        $lastTripData = null;
        if ($lastTrip) {
            $lastTripData = [
                'id' => $lastTrip->id,
                'number' => $lastTrip->number,
                'start_date' => $lastTrip->start_date,
                'end_date' => $lastTrip->end_date,
                'totalCatch' => 0, // trip_details removed
                'revenues' => $lastTrip->sales()->sum('total_price'),
            ];
        }

        // Expenses Categories Chart
        $expensesCategories = $this->getExpensesCategoriesChart($boat);

        $categories = \App\Models\Category::where('type', 'maintenance')
            ->whereNotNull('parent_id')
            ->get();

        $regions = \App\Models\Region::Active()->select('id', 'name')->get();

        return compact(
            'boat',
            'crewStats',
            'payrolls',
            'totalCatch',
            'revenues',
            'expenses',
            'lastTripData',
            'lastMaintenance',
            'expensesCategories',
            'categories',
            'regions'
        );
    }

    public function saveData($request)
    {
        if ($request['guard'] == 'admin') {
            $request->validate(['owner_id' => 'required|integer|exists:users,id']);
        }
        DB::beginTransaction();

        try {
            $boat = new Boat;
            $boat->owner_id = $request->owner_id ?? auth()->user()->id;
            $boat->name_ar = $request['name_ar'];
            $boat->name_en = $request['name_en'] ?? null;
            $boat->number = $request['number'];
            $boat->boat_type_id = $request['boat_type_id'];
            $boat->status = $request['status'];
            $boat->length = $request['length'];
            $boat->width = $request['width'];
            $boat->color = $request['color'];
            $boat->type = $request['type'] ?? null;
            $boat->license_number = $request['license_number'];
            $boat->license_region_id = $request['license_region_id'];
            $boat->license_date = $request['license_date'];
            $boat->license_date_expire = $request['license_date_expire'];
            $boat->body_number = $request['body_number'] ?? null;
            $boat->body_type = $request['body_type'] ?? null;
            $boat->callsign_number = $request['callsign_number'] ?? null;
            $boat->serial_number = $request['serial_number'] ?? null;
            $boat->engine_status = $request['engine_status'];
            $boat->engine_type = $request['engine_type'];
            $boat->engine_power = $request['engine_power'] ?? null;
            $boat->crew_number = $request['crew_number'];
            $boat->payload = $request['payload'];
            $boat->region_id = $request['region_id'];
            $boat->governorate_id = $request['governorate_id'];
            $boat->port_id = $request['port_id'];

            $boat->save();

            DB::commit();
            session()->flash('success', trans('api.boat_added'));

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['id' => $boat->id, 'message' => trans('api.boat_added')]);
            }

            if ($request->filled('redirect_to')) {
                return redirect($request->redirect_to)->with('success', trans('api.boat_added'));
            }

            if ($request['guard'] == 'owner') {
                return redirect()->route('owner.boats.index');
            } else {
                return redirect()->route('admin.boats.index');
            }
        } catch (\Exception $e) {
            DB::rollback();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => trans('api.error_saving'), 'error' => $e->getMessage()], 500);
            }

            // Optionally log error: \Log::error($e);
            return back()->withErrors(['error' => trans('api.error_saving').$e->getMessage()])->withInput();
        }
    }

    public function updateData($request, $id)
    {

        if ($request['guard'] == 'admin') {
            $request->validate(['owner_id' => 'required|integer|exists:users,id']);
        }
        DB::beginTransaction();

        try {
            $boat = Boat::findOrFail($id);
            $boat->owner_id = $request->owner_id ?? auth()->user()->id;
            $boat->name_ar = $request->name_ar;
            $boat->name_en = $request->name_en;
            $boat->number = $request->number;
            $boat->boat_type_id = $request->boat_type_id;
            $boat->status = $request->status;
            $boat->length = $request->length;
            $boat->width = $request->width;
            $boat->color = $request->color;
            $boat->type = $request->type;
            $boat->license_number = $request->license_number;
            $boat->license_region_id = $request->license_region_id;
            $boat->license_date = $request->license_date;
            $boat->license_date_expire = $request->license_date_expire;
            $boat->body_number = $request->body_number;
            $boat->body_type = $request->body_type;
            $boat->callsign_number = $request->callsign_number;
            $boat->serial_number = $request->serial_number;
            $boat->engine_status = $request->engine_status;
            $boat->engine_type = $request->engine_type;
            $boat->engine_power = $request->engine_power;
            $boat->crew_number = $request->crew_number;
            $boat->payload = $request->payload;
            $boat->region_id = $request->region_id;
            $boat->governorate_id = $request->governorate_id;
            $boat->port_id = $request->port_id;

            $boat->save();

            DB::commit();

            session()->flash('success', trans('api.boat_updated'));

            if ($request['guard'] == 'owner') {
                return redirect()->route('owner.boats.index');
            } else {
                return redirect()->route('admin.boats.index');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => trans('api.error_updating').$e->getMessage()])->withInput();
        }
    }

    public function deleteData($id)
    {
        try {

            $boat = Boat::find($id);

            if (! $boat) {
                return response()->json(['message' => 'not found page !!!'], 404);
            }
            $boat->delete();

            DB::commit();
            session()->flash('success', trans('api.boat_deleted'));

            return response()->json(['message' => trans('api.boat_deleted')], 200);
        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return response()->json(['message' => trans('api.error_deleting').$ex->getMessage()], 403);
            }
        }
    }

    public function getExpensesCategoriesChart($boat)
    {
        $expensesCategories = $boat->expenses()
            ->selectRaw('category_id, SUM(final_price) as total')
            ->whereHas('category', function ($query) {
                $query->whereIn('type', ['maintenance', 'operating']);
            })
            ->with('category')
            ->groupBy('category_id')
            ->get()
            ->groupBy(function ($item) {
                return $item->category->type;
            })
            ->map(function ($group) {
                return $group->sum('total');
            })
            ->mapWithKeys(function ($total, $type) {
                return [
                    $type === 'maintenance' ? 'الصيانة' : 'المشتريات' => $total,
                ];
            });
        $payrolls = Payroll::where('boat_id', $boat->id)->sum('crew_total');
        $baseline = collect([
            'الصيانة' => 0,
            'المشتريات' => 0,
            'الرواتب' => 0,
        ]);

        $expensesCategories = $baseline->merge($expensesCategories);
        $expensesCategories->put('الرواتب', $payrolls);

        return $expensesCategories;
    }

    private function getCrewStatusCount(Boat $boat)
    {
        $activeCaptains = $boat->captain()->where('status', 1)->count();
        $inactiveCaptains = $boat->captain()->where('status', 0)->count();

        $activeCrews = $boat->crews()->where('status', 1)->count();
        $inactiveCrews = $boat->crews()->where('status', 0)->count();

        return [
            'active' => $activeCaptains + $activeCrews,
            'inactive' => $inactiveCaptains + $inactiveCrews,
            'total' => ($activeCaptains + $activeCrews) + ($inactiveCaptains + $inactiveCrews),
        ];
    }
}
