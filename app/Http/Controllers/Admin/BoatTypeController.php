<?php

namespace App\Http\Controllers\Admin;

use App\DataTable\BoatTypeDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BoatTypeRequest;
use App\Models\BoatType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class BoatTypeController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->datatable = new BoatTypeDataTable;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.boat_types.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.boat_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BoatTypeRequest $request)
    {
        try {
            DB::beginTransaction();

            $data['name_ar'] = $request->name_ar;
            $data['name_en'] = $request->name_en;
            $data['status'] = $request->status ? 1 : 0;

            BoatType::create($data);
            DB::commit();
            session()->flash('success', trans('admin.boat_types.created_successfully'));

            return redirect()->route('admin.boat_types.index');

        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getMessage()]);
            }

            return redirect()->back()->with(['error' => trans('admin.boat_types.error_occurred')]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = BoatType::withCount('boats')
            ->with(['boats' => function ($q) {
                $q->with('owner')->latest()->limit(30);
            }])
            ->findOrFail($id);
        return view('admin.boat_types.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = BoatType::findOrFail($id);
        return view('admin.boat_types.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BoatTypeRequest $request, $id)
    {
        try {
            $boat_type = BoatType::findOrFail($id);
            $data['name_ar'] = $request->name_ar;
            $data['name_en'] = $request->name_en;
            $data['status'] = $request->status ? 1 : 0;
            $boat_type->update($data);
            DB::commit();
            session()->flash('success', trans('admin.boat_types.updated_successfully'));

            return redirect()->route('admin.boat_types.index');

        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getMessage()]);
            }

            return redirect()->back()->with(['error' => trans('admin.boat_types.error_occurred')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $boat_type = BoatType::find($id);

            if (!$boat_type) {
                return response()->json(['message' => trans('admin.boat_types.not_found')], 404);
            }
            $boat_type->delete();

            DB::commit();
            session()->flash('success', trans('admin.boat_types.deleted_successfully'));

            return response()->json(['message' => trans('admin.boat_types.deleted_successfully')], 200);

        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return response()->json(['message' => trans('admin.boat_types.error_deleting').$ex->getMessage()], 403);
            }

            return response()->json(['message' => trans('admin.boat_types.error_deleting')], 403);
        }
    }

    public function getBoatTypeData(Request $request)
    {
        return $this->datatable->getData($request);
    }
}
