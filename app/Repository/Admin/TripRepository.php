<?php

namespace App\Repository\Admin;

use App\Enums\TripStatus;
use App\Interfaces\CRUD;
use App\Models\Port;
use App\Models\Region;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TripRepository implements CRUD
{
    public function getList($request)
    {
        $tripsCount = Trip::count();

        // حساب عدد الرحلات حسب الحالة
        $statusCounts = Trip::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $tripStatusCards = array_map(fn (TripStatus $s) => [
            'status' => $s->value,
            'label' => $s->label(),
            'count' => $statusCounts[(string) $s->value] ?? 0,
        ], TripStatus::cases());
        $salesStats = (object) [
            'trips_count' => $tripsCount,
            'trip_items_count' => $totalItemsCount ?? 0,
            'total_trip_weight' => $totalWeight ?? 0,

        ];

        return view('admin.trips.index', compact('salesStats', 'tripStatusCards'));
    }

    public function getDetail($id)
    {
        $regions = Region::Active()->select('id', 'name')->get();
        $owners = User::Active()->OwnerRole()->select('id', 'name')->get();
        $data = Trip::with(['owner', 'captain', 'boat', 'region', 'governorate', 'port', 'fishQuantityStocks.fish'])
            ->find($id);

        if (! $data) {
            return redirect()->back()->with(['error' => 'حدث خطأ ما']);
        }

        $captains = User::Active()->CaptainRole()
            ->where('owner_id', $data->owner_id)
            ->select('id', 'name')
            ->get();

        $grouped_fish = collect([]);

        return view('admin.trips.show', compact('regions', 'owners', 'data', 'captains', 'grouped_fish'));
    }

    public function saveData($request)
    {
        try {
            DB::beginTransaction();
            $data['name'] = $request->name;
            $data['name_en'] = $request->name_en;
            $data['number'] = generateTripNumber();
            $data['status'] = 1;
            $data['license_number'] = $request->license_number;
            $data['start_date'] = $request->start_date;
            if ($request->filled('duration')) {
                $data['end_date'] = \Illuminate\Support\Carbon::parse($request->start_date)
                    ->addDays((int) $request->duration - 1);
            } elseif ($request->filled('end_date')) {
                $data['end_date'] = $request->end_date;
            }
            $data['owner_id'] = $request->owner_id;
            $data['captain_id'] = $request->captain_id;
            $data['notes'] = $request->notes;
            $guard = $request->guard ?? 'web';
            $data['created_by'] = Auth::guard($guard)->user()->name ?? 'Admin';
            $data = fillBoatAndCrewData($data, $request->boat_id, $request->captain_id);

            $trip = Trip::create($data);

            if ($guard === 'owner' && $request->filled('quick_expenses')) {
                app(\App\Repository\Owner\ExpenseRepository::class)->createQuickExpensesForTrip(
                    $trip,
                    $request->input('quick_expenses', []),
                    $request->input('quick_expenses_status', 'pending')
                );
            }

            DB::commit();
            session()->flash('success', 'تم اضافة البيانات بنجاح');

            if ($request->filled('redirect_to')) {
                return redirect($request->redirect_to)->with('success', 'تم اضافة البيانات بنجاح');
            }

            $guard = $request->guard ?? 'web';
            if ($guard === 'admin') {
                return redirect()->route('admin.trips.index');
            }

            return redirect()->route('owner.trips.index');

        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }
    }

    public function updateData($request, $id)
    {
        try {
            DB::beginTransaction();
            $trip = Trip::find($id);
            if (! $trip) {
                return redirect()->back()->with(['error' => 'حدث خطأ ما']);
            }

            $data['name'] = $request->name;
            $data['name_en'] = $request->name_en;
            $data['status'] = 1; // new
            $data['license_number'] = $request->license_number;
            $data['permit_type'] = $request->permit_type;
            $data['start_date'] = $request->start_date;
            $data['end_date'] = $request->end_date;
            $data['departure_time'] = $request->departure_time;
            $data['return_time'] = $request->return_time;
            $data['owner_id'] = $request->owner_id;
            $data['captain_id'] = $request->captain_id;
            $data['region_id'] = $request->region_id;
            $data['governorate_id'] = $request->governorate_id;
            //            $data['city_id'] = $request->city_id;
            $data['port_id'] = $request->port_id;
            $data['departure_port'] = Port::find($request->port_id)->name ?? '';
            $data['return_port'] = Port::find($request->port_id)->name ?? '';
            $data['notes'] = $request->notes;
            $guard = $request->guard ?? 'web';
            $data['updated_by'] = Auth::guard($guard)->user()->name ?? 'Admin';
            $data = fillBoatAndCrewData($data, $request->boat_id, $request->captain_id);

            if ($request->hasFile('license_attachment')) {
                if (! is_null($trip->getRawOriginal('license_attachment'))) {
                    deleteFile($trip->getRawOriginal('license_attachment'));
                }
                $path = UploadFile($request->file('license_attachment'), 'uploads/trips/license_attachment');

                $data['license_attachment'] = $path;
            }

            $trip->update($data);
            DB::commit();
            session()->flash('success', 'تم تحديث البيانات بنجاح');

            $guard = $request->guard ?? 'web';
            if ($guard === 'admin') {
                return redirect()->route('admin.trips.index');
            }

            return redirect()->route('owner.trips.index');

        } catch (\Exception $ex) {

            DB::rollBack();
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }
    }

    public function deleteData($id)
    {
        try {

            $trip = Trip::where('id', $id)->first();

            if (! is_null($trip->getRawOriginal('license_attachment'))) {
                deleteFile($trip->getRawOriginal('license_attachment'));
            }

            $trip->delete();
            Cache::forget('sidebar_trip_counts');

            DB::commit();
            session()->flash('success', 'تم حذف البيانات بنجاح');

            return response()->json(['message' => 'Data saved successfully'], 200);

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                session()->flash('error', 'حدث خطأ ما');
            }

            return response()->json(['message' => $ex->getMessage()], 403);

        }
    }
}
