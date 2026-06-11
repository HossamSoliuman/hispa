<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\UnitRequest;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    public function index()
    {
        return redirect()->route('owner.settings.index', ['tab' => 'units']);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Unit::orderByDesc('is_default')->orderBy('id')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', fn ($row) => e($row->name_ar))
                ->addColumn('name_en', fn ($row) => e($row->name_en ?? '-'))
                ->addColumn('is_default', function ($row) {
                    return $row->is_default
                        ? '<span class="badge bg-primary">'.__('owner.units.default').'</span>'
                        : '-';
                })
                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="badge bg-success">'.__('owner.status.active').'</span>'
                        : '<span class="badge bg-danger">'.__('owner.status.inactive').'</span>';
                })
                ->addColumn('action', fn ($row) => '
                    <button class="btn btn-sm btn-outline-success me-1 unitEditBtn"
                            data-id="'.$row->id.'"
                            data-name_ar="'.e($row->name_ar).'"
                            data-name_en="'.e($row->name_en).'"
                            data-is_default="'.(int) $row->is_default.'"
                            data-status="'.(int) $row->status.'">
                            <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm unitDeleteBtn" data-id="'.$row->id.'">
                        <i class="bi bi-trash"></i>
                    </button>
                ')
                ->rawColumns(['is_default', 'status', 'name', 'action'])
                ->make(true);
        }
    }

    public function store(UnitRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $unit = Unit::create($data);
            $this->syncDefault($unit);
            DB::commit();

            return response()->json($unit);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(UnitRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $unit = Unit::findOrFail($id);
            $unit->update($request->validated());
            $this->syncDefault($unit);
            DB::commit();

            return response()->json($unit);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);

        if ($unit->is_default) {
            return response()->json(['error' => __('owner.units.cannot_delete_default')], 422);
        }

        $unit->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Ensure exactly one default unit exists.
     */
    private function syncDefault(Unit $unit): void
    {
        if ($unit->is_default) {
            Unit::where('id', '!=', $unit->id)->update(['is_default' => false]);

            return;
        }

        $hasDefault = Unit::where('is_default', true)->exists();
        if (! $hasDefault) {
            $unit->forceFill(['is_default' => true])->save();
        }
    }
}
