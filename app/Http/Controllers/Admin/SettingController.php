<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\BoatType;
use App\Models\Category;
use App\Models\Governorate;
use App\Models\Port;
use App\Models\Region;
use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('permission:read_settings', ['only' => ['index', 'show']]);
        $this->middleware('permission:create_settings', ['only' => ['create', 'store']]);
        $this->middleware('permission:update_settings', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_settings', ['only' => ['destroy']]);
    }

    /**
     * Admin settings index with tabs: General, Company, Fish, Categories, Regions, Governorates, Ports.
     */
    public function index()
    {
        $data = Setting::get();
        $regions = Region::orderByDesc('id')->get();
        $governorates = Governorate::with('region')->orderByDesc('id')->get();
        $ports = Port::with('governorate')->orderByDesc('id')->get();
        $boatTypes = BoatType::active()->get();
        $parents = Category::whereNull('parent_id')->get();

        return view('admin.settings.index', compact(
            'data',
            'regions',
            'governorates',
            'ports',
            'boatTypes',
            'parents'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SettingRequest $request)
    {

        try {
            DB::beginTransaction();

            $data = $request->except('image');

            if ($request->hasFile('image')) {
                $path = UploadFile($request->file('image'), 'uploads/settings');
                $data['value'] = $path;

            }
            if ($request->type == 'text') {
                $data['value'] = $request->text;
            }
            if ($request->type == 'integer') {
                $data['value'] = $request->integer;
            }
            if ($request->type == 'decimal') {
                $data['value'] = $request->decimal;
            }
            if ($request->type == 'boolean') {

                if ($request->boolean == 'on') {
                    $data['value'] = 1;
                } else {
                    $data['value'] = 0;
                }

            }
            if ($request->type == 'color') {
                $data['value'] = $request->color;
            }

            $setting = Setting::create($data);
            DB::commit();
            session()->flash('success', 'تم اضافة البيانات بنجاح');

            return redirect()->route('admin.settings.index');

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
    public function update(SettingRequest $request)
    {

        try {

            $id = $request->only('id');
            $data = $request->except('image');
            $setting = Setting::where('id', $id)->first();

            if ($request->hasFile('image')) {
                if (! is_null($setting->getRawOriginal('value'))) {
                    $path = $setting->getRawOriginal('value');

                    // حذف الملف من DigitalOcean Space
                    if (Storage::disk('ocean')->exists($path)) {

                        deleteFile($path);

                    }
                }

                // رفع الملف الجديد
                $data['value'] = UploadFile($request->file('image'), 'uploads/settings');
            }
            if ($request->type == 'text') {
                $data['value'] = $request->text;
            }
            if ($request->type == 'integer') {
                $data['value'] = $request->integer;
            }
            if ($request->type == 'decimal') {
                $data['value'] = $request->decimal;
            }
            if ($request->type == 'boolean') {

                if ($request->boolean == 'on') {
                    $data['value'] = 1;
                } else {
                    $data['value'] = 0;
                }

            }
            if ($request->type == 'color') {
                $data['value'] = $request->color;
            }

            $setting->update($data);
            DB::commit();
            session()->flash('success', 'تم تحديث البيانات بنجاح');

            return redirect()->route('admin.settings.index');

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }

    }
}
