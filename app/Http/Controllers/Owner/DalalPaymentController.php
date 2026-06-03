<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\DalalDataTable;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use App\Traits\DalalPayment;
use Illuminate\Http\Request;

class DalalPaymentController extends Controller
{
    use DalalPayment;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $datatable;

    private $rep;

    public function __construct()
    {
        $this->datatable = new DalalDataTable;

    }

    public function index(Request $request) {}

    public function getDalalPaymentData(Request $request, $id)
    {
        $dalal = User::find($id);

        if (! $dalal) {
            return redirect()->back()->with(['error' => 'الصفحة غير موجودة']);

        }

        return $this->datatable->getDalalPaymentData($request, $id);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $sale = Sale::with('payments')->findOrFail($request->sale_id);

        $paidSum = $sale->payments->sum('amount');
        $remaining = $sale->net_owner_amount - $paidSum;

        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', function ($attribute, $value) use ($remaining) {
                if ($value > $remaining) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        $attribute => 'المبلغ المدفوع أكبر من المتبقي وهو: '.number_format($remaining, 2),
                    ]);
                }
            }],
            'paid_at' => 'required|date',
            'payment_method' => 'nullable|string',
        ]);

        Payment::create([
            'sale_id' => $sale->id,
            'owner_id' => auth()->user()->id,
            'seller_id' => $sale->seller_id,
            'amount' => $request->amount,
            'paid_at' => $request->paid_at,
            'notes' => $request->notes,
            'payment_method_id' => $request->payment_method_id,
        ]);

        return response()->json(['success' => true]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $payment = Payment::find($id);

        return response()->json($payment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'paid_at' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'note' => 'nullable|string',
        ]);
        $payment = Payment::find($id);
        $sale = $payment->sale;

        $paidExceptCurrent = $sale->payments()->where('id', '!=', $payment->id)->sum('amount');

        $remaining = $sale->total_price - $paidExceptCurrent;

        if ($request->amount > $remaining) {
            return response()->json(['message' => 'لا يمكن دفع أكثر من المبلغ المتبقي: '.number_format($remaining, 2)], 422);
        }

        $payment->update($request->only('amount', 'paid_at', 'payment_method_id', 'note'));

        return response()->json(['message' => 'تم تحديث الدفع بنجاح']);
    }
}
