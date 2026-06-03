<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CityRequest;
use App\Models\City;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $data = City::OrderByDesc('id')->get();
        $governorates = Governorate::select('id', 'name')->get();

        return view('owner.location.cities', compact('data', 'governorates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CityRequest $request)
    {

        try {
            DB::beginTransaction();

            $data['name'] = $request->name;
            $data['status'] = $request->status ? 1 : 0;
            $data['governorate_id'] = $request->governorate_id;

            $city = City::create($data);
            DB::commit();
            session()->flash('success', 'تم اضافة البيانات بنجاح');

            return redirect()->route('owner.cities.index');

        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CityRequest $request)
    {

        try {

            $id = $request->only('id');
            $city = City::where('id', $id)->first();
            $data['name'] = $request->name;
            $data['status'] = $request->status ? 1 : 0;
            $data['governorate_id'] = $request->governorate_id;
            $city->update($data);
            DB::commit();
            session()->flash('success', 'تم تحديث البيانات بنجاح');

            return redirect()->route('owner.cities.index');

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {

            $id = $request->only('id');
            $city = City::where('id', $id)->first();
            $city->delete();

            // $protectedRelations = ['ports'];

            // foreach ($protectedRelations as $relation) {
            //     if ($city->$relation()->exists()) {
            //         // return redirect()->back()->with('error', 'لا يمكن حذف هذه المحافظة لأنها مرتبطة بـ '.$relation);
            //         return response()->json([
            //             'message' => 'لا يمكن حذف هذا السمك لأنه مرتبط بالبيانات الأخرى'
            //         ], 422);
            //     }
            // }
            DB::commit();
            session()->flash('success', 'تم حذف البيانات بنجاح');

            return redirect()->route('owner.cities.index');

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }
    }
}
