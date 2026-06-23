<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GovernorateRequest;
use App\Models\Governorate;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class GovernorateController extends Controller
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
        $data = Governorate::OrderByDesc('id')->get();
        $regions = Region::get();

        return view('owner.location.governorate', compact('data', 'regions'));
    }

    /**
     * Printable governorates list (HTML) for admin
     */
    public function print(Request $request)
    {
        $query = Governorate::with('region');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        $governorates = $query->orderByDesc('id')->get();

        $total = $governorates->count();
        $active = $governorates->where('status', 1)->count();

        $companyName = currentCompany()?->name ?: 'حسبة';
        $settings = ownerCompanySettings([
            'qr_code' => app(\App\Service\Owner\ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);

        return pdf_report(view('owner.report.governorate_print', compact('governorates', 'total', 'active', 'settings')), [], 'governorates-report.pdf');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GovernorateRequest $request)
    {

        try {
            DB::beginTransaction();

            $data['name'] = $request->name;
            $data['name_en'] = $request->name_en;
            $data['status'] = $request->status ? 1 : 0;
            $data['region_id'] = $request->region_id;

            $governorate = Governorate::create($data);
            DB::commit();
            // session()->flash('success', 'تم اضافة البيانات بنجاح');

            // return redirect()->route('owner.governorates.index');
            return redirect()->back()->withInput()->with(['success' => 'تم اضافة البيانات بنجاح']);

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
    public function update(GovernorateRequest $request)
    {

        try {

            $id = $request->only('id');
            $governorate = Governorate::where('id', $id)->first();
            $data['name'] = $request->name;
            $data['name_en'] = $request->name_en;
            $data['status'] = $request->status ? 1 : 0;
            $data['region_id'] = $request->region_id;
            $governorate->update($data);
            DB::commit();
            // session()->flash('success', 'تم تحديث البيانات بنجاح');

            // return redirect()->route('owner.governorates.index');
            return redirect()->back()->withInput()->with(['success' => 'تم تحديث البيانات بنجاح']);

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
            $governorate = Governorate::where('id', $id)->first();

            $protectedRelations = ['ports'];
            foreach ($protectedRelations as $relation) {
                if ($governorate->$relation()->exists()) {
                    return redirect()->back()->withInput()->with('error', 'لا يمكن حذف هذه المحافظة لأنها مرتبطة بـ '.$relation);
                    //  return response()->json([
                    //     'message' => 'لا يمكن حذف هذا السمك لأنه مرتبط بالبيانات الأخرى'
                    // ], 422);
                }
            }

            $governorate->delete();

            DB::commit();
            // session()->flash('success', 'تم حذف البيانات بنجاح');

            // return redirect()->route('owner.governorates.index');
            return redirect()->back()->with(['success' => 'تم حذف البيانات بنجاح']);

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }
    }
}
