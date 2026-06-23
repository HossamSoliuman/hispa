<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PortRequest;
use App\Models\BoatType;
use App\Models\Governorate;
use App\Models\Port;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class PortController extends Controller
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
        $data = Port::OrderByDesc('id')->get();
        $governorates = Governorate::Active()->get();
        $boatTypes = BoatType::Active()->get();

        return view('owner.location.ports', compact('data', 'governorates', 'boatTypes'));
    }

    /**
     * Printable ports list (HTML) for admin
     */
    public function print(Request $request)
    {
        $query = Port::with('governorate');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('governorate_id')) {
            $query->where('governorate_id', $request->governorate_id);
        }

        $ports = $query->orderByDesc('id')->get();

        $total = $ports->count();
        $active = $ports->where('status', 1)->count();

        $companyName = currentCompany()?->name ?: 'حسبة';
        $settings = ownerCompanySettings([
            'qr_code' => app(\App\Service\Owner\ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);

        return pdf_report(view('owner.report.port_print', compact('ports', 'total', 'active', 'settings')), [], 'ports-report.pdf');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PortRequest $request)
    {
        try {
            DB::beginTransaction();

            $data['name'] = $request->name;
            $data['name_en'] = $request->name_en;
            $data['status'] = $request->status ? 1 : 0;
            $data['governorate_id'] = $request->governorate_id;
            $data['category_ar'] = $request->category_ar;
            $data['category_en'] = $request->category_en;
            $data['lat'] = $request->lat;
            $data['lng'] = $request->lng;

            $port = Port::create($data);

            if ($request->has('boat_types')) {
                foreach ($request->boat_types as $boatTypeId) {
                    $max = $request->max[$boatTypeId] ?? 0;
                    $port->boatTypes()->attach($boatTypeId, ['max' => $max]);
                }
            }

            DB::commit();
            // session()->flash('success', 'تم اضافة البيانات بنجاح');

            // return redirect()->route('owner.ports.index');
            return redirect()->back()->withInput()->with(['success' => 'تم اضافة البيانات بنجاح']);

        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getMessage()]);
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
    public function update(PortRequest $request, $id2)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;
            $port = Port::findOrFail($id);

            // تحديث البيانات الأساسية مع الفئة باللغتين
            $port->update([
                'name' => $request->name,
                'name_en' => $request->name_en,
                'status' => $request->status ? 1 : 0,
                'governorate_id' => $request->governorate_id,
                'category_ar' => $request->category_ar,
                'category_en' => $request->category_en,
                'lat' => $request->lat,
                'lng' => $request->lng,
            ]);

            // Sync boat types مع max values
            $boatData = [];
            if ($request->has('boat_types')) {
                foreach ($request->boat_types as $boatTypeId) {
                    $max = $request->max[$boatTypeId] ?? 0; // تصحيح هنا: استخدم الـ boatTypeId كمفتاح
                    $boatData[$boatTypeId] = ['max' => $max];
                }
            }
            $port->boatTypes()->sync($boatData);

            DB::commit();
            // session()->flash('success', 'تم تحديث البيانات بنجاح');

            // return redirect()->route('owner.ports.index');
            return redirect()->back()->withInput()->with(['success' => 'تم تحديث البيانات بنجاح']);

        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getMessage()]);
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
            $port = Port::where('id', $id)->first();
            $port->delete();

            DB::commit();
            // session()->flash('success', 'تم حذف البيانات بنجاح');

            // return redirect()->route('owner.ports.index');
            return redirect()->back()->withInput()->with(['success' => 'تم حذف البيانات بنجاح']);

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }
    }
}
