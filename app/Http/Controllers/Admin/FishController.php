<?php

namespace App\Http\Controllers\Admin;

use App\DataTable\FishDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\FishRequest;
use App\Models\Fish;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class FishController extends Controller
{
    private FishDataTable $datatable;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->datatable = new FishDataTable;
    }

    public function index(): \Illuminate\View\View
    {
        return view('admin.fish.index');
    }

    public function getFishData(Request $request): JsonResponse
    {
        return $this->datatable->getData($request);
    }

    public function store(FishRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            Fish::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'status' => $request->status == 1 ? 1 : 0,
            ]);

            DB::commit();

            return redirect()->back()->with(['success' => trans('admin.fish.created_successfully')]);
        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getMessage()]);
            }

            return redirect()->back()->with(['error' => trans('admin.fish.error_occurred')]);
        }
    }

    public function update(FishRequest $request): RedirectResponse
    {
        try {
            $fish = Fish::find($request->id);

            if (! $fish) {
                return redirect()->back()->with(['error' => trans('admin.fish.not_found')]);
            }

            $fish->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'status' => $request->status == 1 ? 1 : 0,
            ]);

            return redirect()->back()->with(['success' => trans('admin.fish.updated_successfully')]);
        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getMessage()]);
            }

            return redirect()->back()->with(['error' => trans('admin.fish.error_occurred')]);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $fish = Fish::findOrFail($id);

            $protectedRelations = [
                'fishQuantityStocks',
                'catchDetails',
                'saleDetails',
            ];

            foreach ($protectedRelations as $relation) {
                if ($fish->$relation()->exists()) {
                    return response()->json(['message' => trans('admin.fish.has_relations')], 422);
                }
            }

            $fish->delete();

            return response()->json(['message' => trans('admin.fish.deleted_successfully')], 200);
        } catch (\Exception $ex) {
            return response()->json(['message' => trans('admin.fish.error_occurred')], 500);
        }
    }
}
