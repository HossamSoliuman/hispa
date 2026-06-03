<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\ReturnDatatable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\BoatTypeRequest;
use App\Models\BoatType;
use App\Models\FishQuantityStock;
use App\Models\ReturnDetail;
use App\Models\ReturnModel;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ReturnsController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new ReturnDatatable;
    }

    public function index()
    {
        return view('owner.returns.index');
    }

    public function getReturnsData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    public function create()
    {
        $sales = Sale::with('details')
            ->where('seller_id', auth()->id())
            ->latest()
            ->get();

        return view('owner.returns.create', compact('sales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'fish_id.*' => 'required|exists:fish,id',
            // 'weight.*' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {

            $sale = Sale::with('details')->findOrFail($request->sale_id);

            $return = ReturnModel::create([
                'sale_id' => $sale->id,
                'returned_by' => auth()->id(),
                'returned_at' => now()->format('Y-m-d H:i:s'),
                'status' => 'approved',
            ]);

            $totalPrice = 0;

            foreach ($request->fish_id as $index => $fishId) {
                $returnedWeight = $request->weight[$index];
                if ($returnedWeight > 0) {
                    $returnedSale_DetailID = $request->sale_detail_id[$index];
                    $returnedPricePerKilo = (float) $request->price_per_kilo[$index];

                    $soldWeight = $sale->details
                        ->where('fish_id', $fishId)
                        ->sum('weight');

                    $alreadyReturned = ReturnDetail::whereHas('return', function ($q) use ($sale) {
                        $q->where('sale_id', $sale->id);
                    })
                        ->where('fish_id', $fishId)
                        ->sum('weight');

                    if (($alreadyReturned + $returnedWeight) > $soldWeight) {
                        throw new \Exception('الكمية المرجعة أكبر من المباعة');
                    }

                    ReturnDetail::create([
                        'return_id' => $return->id,
                        'fish_id' => $fishId,
                        'sale_detail_id' => $returnedSale_DetailID,
                        'price_per_kilo' => $returnedPricePerKilo,
                        'total_price' => ($returnedWeight * $returnedPricePerKilo),
                        'weight' => $returnedWeight,
                    ]);

                    $totalPrice += ($returnedWeight * $returnedPricePerKilo);
                    $return->update([
                        'total_amount' => $totalPrice,
                    ]);

                    FishQuantityStock::where('fish_id', $fishId)
                        ->where('catch_id', $sale->catch_id)
                        ->where('trip_id', $sale->trip_id)
                        ->increment('quantity', $returnedWeight);
                }
            }
        });

        return redirect()
            ->route('owner.returns.index')
            ->with('success', 'تم تسجيل الإرجاع بنجاح');
    }

    public function edit($id)
    {
        $data = BoatType::find($id);

        return view('owner.returns.edit', compact('data'));
    }

    public function update(BoatTypeRequest $request, $id)
    {

        try {
            $returns = BoatType::where('id', $id)->first();
            $data['name_ar'] = $request->name_ar;
            $data['name_en'] = $request->name_en;
            $data['status'] = $request->status ? 1 : 0;
            $returns->update($data);
            DB::commit();
            session()->flash('success', 'تم تحديث البيانات بنجاح');

            return redirect()->route('owner.returns.index');
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

            $returns = BoatType::find($id);

            if (! $returns) {
                return response()->json(['message' => 'not found page !!!'], 404);
            }
            $returns->delete();

            DB::commit();
            session()->flash('success', trans('boat_deleted'));

            return response()->json(['message' => trans('boat_deleted')], 200);
        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return response()->json(['message' => trans('error_deleting') . $ex->getMessage()], 403);
            }
        }
    }

    public function saleDetails($saleId)
    {
        $sale = Sale::with('details', 'details.fish')->findOrFail($saleId);

        return response()->json($sale);
    }

    public function show($id)
    {
        $return = ReturnModel::where('id', $id)->with(['sale', 'details'])->first();

        return view('owner.returns.show', compact('return'));
    }
}
