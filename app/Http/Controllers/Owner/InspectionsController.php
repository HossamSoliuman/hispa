<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\InspectionDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\InspectionRequest;
use App\Models\Boat;
use App\Models\Category;
use App\Models\Inspection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InspectionsController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new InspectionDataTable;
    }

    public function index(Request $request)
    {
        $boats = Boat::where('owner_id', auth()->id())->get();
        $categories = Category::all();

        return view('owner.inspection.index', compact('boats', 'categories'));
    }

    public function getInspectionData(Request $request)
    {
        $query = Inspection::with(['boat'])->latest();

        if ($request->filled('boat_id')) {
            $query->where('boat_id', $request->boat_id);
        }

        return $this->datatable->getData($query->get());
    }

    public function store(InspectionRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            if (blank($data['next_check']) && filled($data['check_date'])) {
                $data['next_check'] = Carbon::parse($data['check_date'])
                    ->addYear()
                    ->subDays(10)
                    ->format('Y-m-d');
            }
            $inspection = Inspection::create($data);
            DB::commit();

            return response()->json(['message' => 'تمت اضافة الفحص بنجاح', 'data' => $inspection]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'حدث خطأ أثناء الحفظ', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $inspection = Inspection::with(['boat', 'category'])->findOrFail($id);
        if ($inspection->owner_id !== auth()->user()->id) {
            abort(403, 'غير مصرح لك');
        }

        return response()->json($inspection);
    }

    public function edit($id)
    {
        $inspection = Inspection::findOrFail($id);

        return response()->json($inspection);
    }

    public function update(InspectionRequest $request, $id)
    {
        $inspection = Inspection::findOrFail($id);
        DB::beginTransaction();
        try {
            $data = $request->validated();
            if (blank($data['next_check']) && filled($data['check_date'])) {
                $data['next_check'] = Carbon::parse($data['check_date'])
                    ->addYear()
                    ->subDays(10)
                    ->format('Y-m-d');
            }
            $inspection->update($data);
            DB::commit();

            return response()->json(['message' => 'تم تحديث الفحص بنجاح', 'data' => $inspection]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'حدث خطأ أثناء التحديث', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $inspection = Inspection::findOrFail($id);
        DB::beginTransaction();
        try {
            $inspection->delete();
            DB::commit();

            return response()->json(['message' => 'تم حذف الفحص بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'حدث خطأ أثناء الحذف', 'error' => $e->getMessage()], 500);
        }
    }
}
