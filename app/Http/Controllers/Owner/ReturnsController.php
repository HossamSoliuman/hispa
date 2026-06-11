<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\ReturnDatatable;
use App\Http\Controllers\Controller;
use App\Models\FishQuantityStock;
use App\Models\ReturnDetail;
use App\Models\ReturnModel;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $sales = $this->ownerSalesQuery()
            ->with('details')
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

            $sale = $this->ownerSalesQuery()->with('details')->findOrFail($request->sale_id);

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

                    $saleDetail = $sale->details->firstWhere('id', $returnedSale_DetailID);
                    $unitId = $saleDetail?->unit_id;

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
                        'unit_id' => $unitId,
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
                        ->when($unitId, fn ($q) => $q->where('unit_id', $unitId))
                        ->increment('quantity', $returnedWeight);
                }
            }
        });

        return redirect()
            ->route('owner.returns.index')
            ->with('success', 'تم تسجيل الإرجاع بنجاح');
    }

    public function saleDetails($saleId)
    {
        $sale = $this->ownerSalesQuery()
            ->with('details', 'details.fish', 'details.unit')
            ->findOrFail($saleId);

        return response()->json($sale);
    }

    public function show($id)
    {
        $return = ReturnModel::where('id', $id)
            ->whereHas('sale', function ($sale) {
                $sale->where('seller_type', 'owner')->where('seller_id', $this->ownerId());
            })
            ->with(['sale', 'details', 'details.fish', 'details.unit'])
            ->firstOrFail();

        return view('owner.returns.show', compact('return'));
    }

    private function ownerId(): int
    {
        $ownerId = Auth::guard('owner')->id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        return (int) $ownerId;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<Sale>
     */
    private function ownerSalesQuery()
    {
        return Sale::query()
            ->where('seller_type', 'owner')
            ->where('seller_id', $this->ownerId());
    }
}
