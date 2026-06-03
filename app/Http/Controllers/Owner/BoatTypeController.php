<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\BoatTypeDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\BoatTypeRequest;
use App\Models\BoatType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class BoatTypeController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new BoatTypeDatatable;
    }

    public function index()
    {
        return view('owner.boat_type.index');
    }

    public function getBoatTypeData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    public function create()
    {
        return view('owner.boat_type.create');
    }

    public function store(BoatTypeRequest $request)
    {
        try {
            DB::beginTransaction();

            $data['name_ar'] = $request->name_ar;
            $data['name_en'] = $request->name_en;
            $data['status'] = $request->status ? 1 : 0;

            BoatType::create($data);
            DB::commit();
            session()->flash('success', 'تم اضافة البيانات بنجاح');

            return redirect()->route('owner.boat_types.index');

        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }
    }

    public function edit($id)
    {
        $data = BoatType::find($id);

        return view('owner.boat_type.edit', compact('data'));
    }

    public function update(BoatTypeRequest $request, $id)
    {

        try {
            $boat_type = BoatType::where('id', $id)->first();
            $data['name_ar'] = $request->name_ar;
            $data['name_en'] = $request->name_en;
            $data['status'] = $request->status ? 1 : 0;
            $boat_type->update($data);
            DB::commit();
            session()->flash('success', 'تم تحديث البيانات بنجاح');

            return redirect()->route('owner.boat_types.index');

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }
    }

    public function destroy($id)
    {

        try {

            $boat_type = BoatType::find($id);

            if (! $boat_type) {
                return response()->json(['message' => 'not found page !!!'], 404);
            }
            $boat_type->delete();

            DB::commit();
            session()->flash('success', trans('boat_deleted'));

            return response()->json(['message' => trans('boat_deleted')], 200);

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return response()->json(['message' => trans('error_deleting').$ex->getMessage()], 403);
            }

        }

    }
}
