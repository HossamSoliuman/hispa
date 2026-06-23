<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\MaintenanceDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\MaintenanceRequest;
use App\Models\Boat;
use App\Models\Category;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new MaintenanceDataTable;
    }

    public function index(Request $request)
    {
        $boats = Boat::where('owner_id', auth()->id())->get();
        $categories = Category::all();

        return view('owner.maintenance.index', compact('boats', 'categories'));
    }

    public function getMaintenanceData(Request $request)
    {
        if ($request->ajax()) {
            $query = Maintenance::with(['boat', 'category', 'owner'])->latest();

            if ($request->filled('boat_id')) {
                $query->where('boat_id', $request->boat_id);
            }

            return $this->datatable->getData($query);
        }
    }

    public function store(MaintenanceRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['owner_id'] = auth()->user()->id;
            $maintenance = Maintenance::create($data);
            DB::commit();

            return response()->json(['message' => 'تمت جدولة الصيانة بنجاح', 'data' => $maintenance]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'حدث خطأ أثناء الحفظ', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $maintenance = Maintenance::with(['boat', 'category'])->findOrFail($id);
        if ($maintenance->owner_id !== auth()->user()->id) {
            abort(403, 'غير مصرح لك');
        }

        return response()->json($maintenance);
    }

    public function edit($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        if ($maintenance->owner_id !== auth()->user()->id) {
            abort(403, 'غير مصرح لك');
        }

        return response()->json($maintenance);
    }

    public function update(MaintenanceRequest $request, $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        if ($maintenance->owner_id !== auth()->user()->id) {
            abort(403, 'غير مصرح لك');
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['owner_id'] = auth()->user()->id;
            $maintenance->update($data);
            DB::commit();

            return response()->json(['message' => 'تم تحديث الصيانة بنجاح', 'data' => $maintenance]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'حدث خطأ أثناء التحديث', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        if ($maintenance->owner_id !== auth()->user()->id) {
            abort(403, 'غير مصرح لك');
        }
        DB::beginTransaction();
        try {
            $maintenance->delete();
            DB::commit();

            return response()->json(['message' => 'تم حذف الصيانة بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'حدث خطأ أثناء الحذف', 'error' => $e->getMessage()], 500);
        }
    }
}
