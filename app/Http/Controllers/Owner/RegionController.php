<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegionRequest;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
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
        $data = Region::OrderByDesc('id')->get();

        return view('owner.location.region', compact('data'));
    }

    /**
     * Printable regions list (HTML) for admin
     */
    public function print(Request $request)
    {
        $query = Region::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $regions = $query->orderByDesc('id')->get();

        $total = $regions->count();
        $active = $regions->where('status', 1)->count();

        // get basic company settings for report header
        $companyName = currentCompany()?->name ?: 'حسبة';
        $settings = ownerCompanySettings([
            'qr_code' => app(\App\Service\Owner\ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);

        return pdf_report(view('owner.report.region_print', compact('regions', 'total', 'active', 'settings')), [], 'regions-report.pdf');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegionRequest $request)
    {

        try {
            DB::beginTransaction();

            $data['name'] = $request->name;
            $data['name_en'] = $request->name_en;
            $data['status'] = $request->status ? 1 : 0;

            $region = Region::create($data);
            DB::commit();
            // session()->flash('success', 'تم اضافة البيانات بنجاح');

            // return redirect()->route('owner.regions.index');
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
    public function update(RegionRequest $request)
    {

        try {

            $id = $request->only('id');
            $region = Region::where('id', $id)->first();
            $data['name'] = $request->name;
            $data['name_en'] = $request->name_en;
            $data['status'] = $request->status ? 1 : 0;
            $region->update($data);
            DB::commit();
            // session()->flash('success', 'تم تحديث البيانات بنجاح');

            // return redirect()->route('owner.regions.index');
            return redirect()->back()->withInput()->with(['success' => 'تم تعديل البيانات بنجاح']);

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
            $region = Region::where('id', $id)->first();

            $protectedRelations = ['governorates'];

            foreach ($protectedRelations as $relation) {
                if ($region->$relation()->exists()) {
                    return redirect()->back()->withInput()->with('error', 'لا يمكن حذف هذه المنطقة لأنها مرتبطة بـ '.$relation);
                    // return response()->json([
                    //     'message' => 'لا يمكن حذف هذا السمك لأنه مرتبط بالبيانات الأخرى'
                    // ], 422);
                }
            }

            $region->delete();
            DB::commit();
            // session()->flash('success', 'تم حذف البيانات بنجاح');

            return redirect()->back()->with(['success' => 'تم حذف البيانات بنجاح']);
            // return redirect()->route('owner.regions.index');

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }
    }
}
