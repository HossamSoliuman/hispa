<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\SalesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\SalesRequest;
use App\Models\Boat;
use App\Models\CatchModel;
use App\Models\Customer;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $datatable;

    public function __construct()
    {
        $this->datatable = new SalesDataTable;

    }

    public function index(Request $request)
    {
        $type = $request->get('type');
        $fish = Fish::Active()->get();
        $boats = Boat::Active()->get();
        $trips = Trip::all();

        return view('owner.sales.index', compact('type', 'fish', 'boats', 'trips'));
    }

    public function getSalesData(Request $request)
    {

        return $this->datatable->getData($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getShowDetailSalesData(Request $request, $fish_id)
    {
        return $this->datatable->getShowData($request, $fish_id);
    }

    public function show($id)
    {
        $sales = Sale::with(['customer', 'paymentMethod', 'details', 'details.fish'])
            ->findOrFail($id);

        return view('owner.sales.show', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::Active()->get();
        $trips = Trip::where('owner_id', auth()->user()->id)->get();
        $fish = Fish::Active()->get();
        $paymentMethods = PaymentMethod::active()->get();

        return view('owner.sales.create', compact('fish', 'customers', 'paymentMethods', 'trips'));
    }

    public function store(SalesRequest $request)
    {
        try {
            DB::beginTransaction();

            $customer = Customer::find($request->customer_id);
            $trip = Trip::find($request->trip_id);
            $catch = CatchModel::where('trip_id', $request->trip_id)->first();

            $sale = Sale::create([
                'number' => $request->customer_id.'_'.time(),
                'seller_type' => 'owner',
                'seller_id' => auth()->id(),
                'customer_id' => $request->customer_id,
                'customer_name' => $customer?->name,
                'total_price' => 0,
                'payment_method_id' => auth()->user()->getAuthIdentifier(),
                'payment_status' => $request->payment_status,
                'status' => $request->payment_status == 'paid' ? 2 : 1,
                'sale_datetime' => $request->sale_datetime,
                'catch_id' => $catch->id ?? 0,
                'trip_id' => $request->trip_id,
                'boat_id' => $trip->boat_id ?? 0,
            ]);

            $totalPrice = 0;

            foreach ($request->fish_id as $index => $fishId) {
                $weight = $request->weight[$index];
                $price = $request->price_per_kilo[$index];
                if ($weight > 0 && $price > 0) {
                    $fishStock = FishQuantityStock::where('fish_id', $fishId)
                        ->where('catch_id', ($catch->id ?? 0))
                        ->where('trip_id', $request->trip_id)
                        ->first();
                    if (! $fishStock || $fishStock->quantity < $weight) {
                        throw new \Exception('الكمية المطلوبة أكبر من المخزون المتوفر');
                    }
                    $fishStock->decrement('quantity', $weight);

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'fish_id' => $fishId,
                        'fish_name' => optional(Fish::find($fishId))->scientific_name,
                        'weight' => $weight,
                        'price_per_kilo' => $price,
                        'total_price' => ($price * $weight),
                    ]);

                    $totalPrice += ($price * $weight);
                }
            }

            $sale->update([
                'total_price' => $totalPrice,
                'remaining_total' => ($totalPrice - $request->paid_amount),
            ]);

            DB::commit();

            return redirect()
                ->route('owner.sales.index')
                ->with('success', 'تم إضافة فاتورة البيع بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function catchDetails($tripId)
    {
        $catch = CatchModel::where('trip_id', $tripId)->with('details', 'details.fish')->first();
        if ($catch) {
            $fishQuntity = FishQuantityStock::with('fish')
                ->where('catch_id', $catch->id)
                ->where('trip_id', $tripId)->get();

            return response()->json($fishQuntity);
        }

        return response()->json([]);
    }
}
