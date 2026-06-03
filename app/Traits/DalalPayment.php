<?php

namespace App\Traits;

use App\Models\DalalStock;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

trait DalalPayment
{
    public function getPayments(Request $request)
    {
        $sale = Sale::with('payments.payment_method')->findOrFail($request->sale_id);

        if ($sale->payments->isEmpty()) {
            return '<div class="alert alert-info text-center">لا توجد تسديدات</div>';
        }

        $html = '
<div class="table-responsive">
<table class="table table-striped table-bordered text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th>رقم العملية</th>
            <th>التاريخ</th>
            <th>المبلغ</th>
            <th>طريقة الدفع</th>
            <th>ملاحظات</th>
            <th>إجراءات</th>
        </tr>
    </thead>
    <tbody>
';

        foreach ($sale->payments as $payment) {
            $paidAt = $payment->paid_at ? Carbon::parse($payment->paid_at)->format('Y-m-d') : '-';
            $number = $payment->number ?? '-';
            $paymentMethod = $payment->payment_method->name ?? '-';
            $note = $payment->note ? e($payment->note) : '-';

            $editBtn = '<button class="btn btn-sm btn-primary edit-payment-btn" data-payment-id="'.$payment->id.'">تعديل</button>';

            $html .= "<tr>
        <td>{$number}</td>
        <td>{$paidAt}</td>
        <td>".number_format($payment->amount, 2)."</td>
        <td>{$paymentMethod}</td>
        <td>{$note}</td>
        <td>{$editBtn}</td>
    </tr>";
        }

        $html .= '
    </tbody>
</table>
</div>
';

        return $html;
    }

    public function topDalalsChart()
    {
        $ownerId = auth()->id();

        // Get dalals linked to this owner
        $dalalIds = DB::table('dalal_stocks')
            ->where('owner_id', $ownerId)
            ->where('status', 1)
            ->distinct()
            ->pluck('dalal_id');

        // Aggregate sales data for those dalals
        $topDalals = DB::table('sales')
            ->select('users.name',
                DB::raw('COUNT(sales.id) as total_sales'),
                DB::raw('SUM(sales.total_price) as total_amount')
            )
            ->join('users', 'users.id', '=', 'sales.seller_id')
            ->where('sales.seller_type', 'dalal')
            ->whereIn('sales.seller_id', $dalalIds)
            ->groupBy('users.name')
            ->orderByDesc('total_amount')
            ->limit(5) // top 5 dalals
            ->get();

        return response()->json([
            'labels' => $topDalals->pluck('name'),
            'salesCounts' => $topDalals->pluck('total_sales'),
            'salesAmounts' => $topDalals->pluck('total_amount'),
        ]);
    }

    public function topDalalsBarChart()
    {
        $ownerId = auth()->id();

        $dalalIds = DB::table('dalal_stocks')
            ->where('owner_id', $ownerId)
            ->where('status', 1)
            ->distinct()
            ->pluck('dalal_id');

        $totalDue = Sale::whereIn('seller_id', $dalalIds)
            ->where('seller_type', 'dalal')
            ->sum('net_owner_amount');

        $totalPaid = DB::table('payments')
            ->join('sales', 'sales.id', '=', 'payments.sale_id')
            ->whereIn('sales.seller_id', $dalalIds)
            ->where('sales.seller_type', 'dalal')
            ->sum('payments.amount');

        return response()->json([
            'labels' => ['المبالغ المستحقة', 'المبالغ المسددة'],
            'data' => [round($totalDue, 2), round($totalPaid, 2)],
        ]);
    }

    public function getDalalPerformanceStats()
    {
        $ownerId = auth()->id();

        $activeDalals = DalalStock::where('owner_id', $ownerId)
            ->where('status', 1)
            ->distinct('dalal_id')
            ->pluck('dalal_id');

        $totalActiveDalals = $activeDalals->count();

        // إذا ما في دلالين نشطين → نرجع صفرات مباشرة
        if ($totalActiveDalals === 0) {
            return response()->json([
                'totalActiveDalals' => 0,
                'topDalalName' => 'لا يوجد',
                'topDalalDue' => 0,
                'mostActiveDalalName' => 'لا يوجد',
                'mostActiveDalalCount' => 0,
                'avgSaleAmount' => 0,
                'newDalalsThisMonth' => 0,
                'dalalsWithDueBalance' => 0,
                'totalDue' => 0,
                'paidAmount' => 0,
                'dueBalance' => 0,
            ]);
        }

        $totalDue = Sale::whereIn('seller_id', $activeDalals)
            ->where('seller_type', 'dalal')
            ->sum('net_owner_amount');

        $paidAmount = DB::table('payments')
            ->whereIn('seller_id', $activeDalals)
            ->sum('amount');

        $dueBalance = max($totalDue - $paidAmount, 0);

        $topDalalRecord = Sale::select('seller_id', DB::raw('SUM(net_owner_amount) as total_due'))
            ->whereIn('seller_id', $activeDalals)
            ->where('seller_type', 'dalal')
            ->groupBy('seller_id')
            ->orderByDesc('total_due')
            ->with('seller')
            ->first();

        $mostActiveDalal = Sale::select('seller_id', DB::raw('COUNT(*) as sales_count'))
            ->whereIn('seller_id', $activeDalals)
            ->where('seller_type', 'dalal')
            ->groupBy('seller_id')
            ->orderByDesc('sales_count')
            ->with('seller')
            ->first();

        $avgSaleAmount = Sale::whereIn('seller_id', $activeDalals)
            ->where('seller_type', 'dalal')
            ->avg('total_price') ?? 0;

        $startOfMonth = Carbon::now()->startOfMonth();
        $newDalalsThisMonth = DalalStock::where('owner_id', $ownerId)
            ->where('status', 1)
            ->where('created_at', '>=', $startOfMonth)
            ->distinct('dalal_id')
            ->count('dalal_id');

        $dalalsWithDueBalance = 0;
        foreach ($activeDalals as $dalalId) {
            $due = Sale::where('seller_id', $dalalId)
                ->where('seller_type', 'dalal')
                ->sum('net_owner_amount');

            $paid = DB::table('payments')
                ->where('seller_id', $dalalId)
                ->sum('amount');

            if (($due - $paid) > 0) {
                $dalalsWithDueBalance++;
            }
        }

        return response()->json([
            'totalActiveDalals' => $totalActiveDalals,
            'topDalalName' => $topDalalRecord?->seller->name ?? 'لا يوجد',
            'topDalalDue' => $topDalalRecord->total_due ?? 0,
            'mostActiveDalalName' => $mostActiveDalal?->seller->name ?? 'لا يوجد',
            'mostActiveDalalCount' => $mostActiveDalal->sales_count ?? 0,
            'avgSaleAmount' => $avgSaleAmount,
            'newDalalsThisMonth' => $newDalalsThisMonth,
            'dalalsWithDueBalance' => $dalalsWithDueBalance,
            'totalDue' => $totalDue,
            'paidAmount' => $paidAmount,
            'dueBalance' => $dueBalance,
        ]);
    }
}
