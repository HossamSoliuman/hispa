<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\FishingEquipmentRequest;
use App\Models\FishingEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FishingEquipmentController extends Controller
{
    public function index()
    {
        return view('owner.fishing-equipments.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = FishingEquipment::where('owner_id', auth()->user()->id)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('quantity', function ($row) {
                    $totalExpense = $row->expenseFishingEquipments->sum('quantity');

                    return $totalExpense;
                })
                ->addColumn(
                    'status',
                    fn ($row) => $row->status
                        ? '<span class="badge bg-success">مفعل</span>'
                        : '<span class="badge bg-danger">غير مفعل</span>'
                )
                ->addColumn(
                    'quantity',
                    fn ($row) => $row->expenseFishingEquipments->sum('quantity')
                )
                ->addColumn('action', fn ($row) => '
                    <button class="btn btn-sm btn-warning editBtn"
                            data-id="'.$row->id.'"
                            data-name="'.e($row->name_ar).'"
                            data-name_en="'.e($row->name_en).'"
                            data-status="'.$row->status.'">
                            <i class="fa fa-edit me-1"></i>
                    </button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">
                        <i class="fa fa-trash me-1"></i>
                    </button>
                ')
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    public function store(FishingEquipmentRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['owner_id'] = auth()->user()->id;
            $fishingEquipment = FishingEquipment::create($data);
            DB::commit();

            return response()->json($fishingEquipment);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(FishingEquipmentRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $fishingEquipment = FishingEquipment::findOrFail($id);
            if (auth()->user()->id != $fishingEquipment->owner_id) {
                return response()->json(['error' => 'غير مسموح لك بتعديل هذا المعدة'], 403);
            }
            $fishingEquipment->update($data);
            DB::commit();

            return response()->json($fishingEquipment);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $fishingEquipment = FishingEquipment::findOrFail($id);
            if (auth()->user()->id != $fishingEquipment->owner_id) {
                return response()->json(['error' => 'غير مسموح لك بحذف هذا المعدة'], 403);
            }
            $fishingEquipment->delete();
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
