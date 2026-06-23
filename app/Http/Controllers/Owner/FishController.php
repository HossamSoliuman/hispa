<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\FishDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\FishRequest;
use App\Models\Fish;
use App\Models\Region;
use App\Traits\FishImportTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class FishController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FishImportTrait;

    private $datatable;

    public function __construct()
    {
        $this->datatable = new FishDataTable;
    }

    public function index()
    {
        $data = Fish::OrderByDesc('id')->get();
        $regions = Region::Active()->get();

        return view('owner.fish.index', compact('data', 'regions'));
    }

    /**
     * Printable fish list (HTML) for admin
     */
    public function print(Request $request)
    {
        $query = Fish::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('code')) {
            $query->where('code', 'like', '%'.$request->code.'%');
        }

        $fishes = $query->orderByDesc('id')->get();

        $total = $fishes->count();
        $active = $fishes->where('status', 1)->count();

        $settings = $this->getCompanySettings();

        return pdf_report(view('owner.report.fish_print', compact('fishes', 'total', 'active', 'settings')), [], 'fish-report.pdf');
    }

    private function getCompanySettings()
    {
        $companyName = currentCompany()?->name ?: 'حسبة';

        return ownerCompanySettings([
            'qr_code' => app(\App\Service\Owner\ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);
    }

    public function getFishData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FishRequest $request)
    {

        try {
            DB::beginTransaction();

            $data['code'] = $request->code;
            $data['scientific_name'] = $request->scientific_name;
            $data['english_name'] = $request->english_name;
            $data['status'] = $request->status == 1 ? 1 : 0;

            $fish = Fish::create($data);
            DB::commit();
            // session()->flash('success', 'تم اضافة البيانات بنجاح');

            // return redirect()->route('owner.fish.index');
            return redirect()->back()->withInput()->with(['success' => 'تم اضافة البيانات بنجاح']);

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
    public function update(FishRequest $request)
    {

        try {

            $id = $request->only('id');
            $fish = Fish::where('id', $id)->first();
            if (! $fish) {
                return redirect()->back()->with(['error' => 'لايوجد هذا الصنف']);

            }
            $data['code'] = $request->code;
            $data['scientific_name'] = $request->scientific_name;
            $data['english_name'] = $request->english_name;
            $data['status'] = $request->status == 1 ? 1 : 0;

            $fish->update($data);
            DB::commit();
            // session()->flash('success', 'تم تحديث البيانات بنجاح');

            return redirect()->back()->withInput()->with(['success' => 'تم تحديث البيانات بنجاح']);
            // return redirect()->route('owner.fish.index');

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
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
    public function destroy($id)
    {
        try {
            $fish = Fish::findOrFail($id);

            $protectedRelations = [
                'fishQuantityStocks',
                'catchDetails',
                'saleDetails',
                'returnDetails',
            ];

            foreach ($protectedRelations as $relation) {
                if ($fish->$relation()->exists()) {
                    return response()->json([
                        'message' => 'لا يمكن حذف هذا السمك لأنه مرتبط بالمبيعات أو المصيد',
                    ], 422);
                }
            }

            $fish->delete();

            return response()->json([
                'message' => 'تم حذف البيانات بنجاح',
            ], 200);

        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'حدث خطأ غير متوقع',
            ], 500);
        }
    }

    public function getFishStock() {}
}
