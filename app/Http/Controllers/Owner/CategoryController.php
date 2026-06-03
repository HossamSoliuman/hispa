<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function __construct() {}

    public function index()
    {
        $parents = Category::whereNull('parent_id')->get();

        return view('owner.categories.index', compact('parents'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::with(['parent', 'children'])
                ->whereNotNull('parent_id')
                ->orderBy('parent_id', 'ASC')
                ->orderBy('id', 'ASC')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', fn ($row) => e($row->name))
                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="badge bg-success">'.__('owner.status.active').'</span>'
                        : '<span class="badge bg-danger">'.__('owner.status.inactive').'</span>';
                })

                ->addColumn(
                    'parent_name',
                    fn ($row) => $row->parent ? e($row->parent->name) : '-'
                )
                ->addColumn('action', fn ($row) => '
                    <button class="btn btn-sm btn-outline-success me-1 editBtn"
                            data-id="'.$row->id.'"
                            data-name_ar="'.e($row->name_ar).'"
                            data-name_en="'.e($row->name_en).'"
                            data-type="'.$row->type.'"
                            data-parent_id="'.$row->parent_id.'"
                            data-status="'.$row->status.'">
                            <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm deleteBtn" data-id="'.$row->id.'">
                        <i class="bi bi-trash"></i>
                    </button>
                ')
                ->rawColumns(['status', 'name', 'action'])
                ->make(true);
        }
    }

    public function store(CategoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $category = Category::create($data);
            DB::commit();

            return response()->json($category);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(CategoryRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $category = Category::findOrFail($id);
            $category->update($request->validated());
            DB::commit();

            return response()->json($category);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
