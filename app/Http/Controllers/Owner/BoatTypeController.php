<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BoatTypeRequest;
use App\Models\BoatType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class BoatTypeController extends Controller
{
    public function store(BoatTypeRequest $request)
    {
        try {
            DB::beginTransaction();

            $boatType = BoatType::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'status' => $request->status ? 1 : 0,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'id' => $boatType->id,
                    'name' => $boatType->name,
                    'message' => __('owner.boat_type.create.title'),
                ], 201);
            }

            return redirect()->back()->with(['success' => __('owner.boat_type.create.title')]);
        } catch (\Exception $ex) {
            DB::rollBack();

            $message = App::environment('local') ? $ex->getMessage() : 'حدث خطأ ما';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 500);
            }

            return redirect()->back()->with(['error' => $message]);
        }
    }

    public function update(BoatTypeRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $boatType = BoatType::findOrFail($request->id);

            $boatType->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'status' => $request->status ? 1 : 0,
            ]);

            return redirect()->back()->with(['success' => __('owner.boat_type.edit.title')]);
        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getMessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);
        }
    }

    public function destroy(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $boatType = BoatType::findOrFail($request->id);
            $boatType->delete();

            return redirect()->back()->with(['success' => 'تم حذف البيانات بنجاح']);
        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getMessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);
        }
    }
}
