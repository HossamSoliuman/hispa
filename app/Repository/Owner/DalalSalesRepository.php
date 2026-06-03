<?php

namespace App\Repository\Owner;

use App\Interfaces\CRUD;
use App\Models\Boat;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class DalalSalesRepository implements CRUD
{
    public function getList($request)
    {

        return view('owner.dalal.index');

    }

    public function getDetail($id)
    {
        // TODO: Implement getDetail() method.
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

            if ($request['guard'] == 'owner') {
                return redirect()->route('owner.boats.index');

            } else {
                return redirect()->route('admin.boats.index');

            }

        } catch (\Exception $e) {
            DB::rollback();

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

            return response()->json(['message' => 'Data saved successfully'], 200);

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return response()->json(['message' => $ex->getMessage()], 403);
            }

        }
    }
}
