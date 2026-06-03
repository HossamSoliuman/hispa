<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\PayrollDetailsModel;
use App\Models\PayrollModel;
use App\Models\Sale;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $ownerId = Auth::guard('owner')->id() ?? Auth::id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        // Fetch boats for this owner
        $boats = Boat::where('owner_id', $ownerId)->get();

        $boatId = $request->input('boat_id');

        $salesQ = Sale::query()
            ->where('seller_type', 'owner')
            ->where('seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [$from, $to]);

        //        $expQ = Expense::query()
        //            ->where('owner_id', $ownerId)
        //            ->whereBetween('date', [$from, $to]);

        $tripCategoryIds = Category::whereIn('parent_id', [8, 24])
            ->pluck('id')
            ->toArray();
        $generalCategoryIds = Category::whereIn('parent_id', [1, 17])
            ->pluck('id')
            ->toArray();

        $tripExpQ = Expense::query()
            ->where('owner_id', $ownerId)
            ->whereIn('category_id', $tripCategoryIds)
            ->whereBetween('date', [$from, $to]);

        $generalExpQ = Expense::query()
            ->where('owner_id', $ownerId)
            ->whereIn('category_id', $generalCategoryIds)
            ->whereBetween('date', [$from, $to]);

        $total_captins = 0;
        $boatIDs = Boat::active()->where('owner_id', auth()->id())->pluck('id');

        $payroll = PayrollModel::where('is_paid', 1)
            ->where('status', 'approved')
            ->where('type', 'salary')
            ->whereBetween('paid_at', [$from, $to])
            ->pluck('id');

        $employees = User::whereIn('role', ['employee'])->where('salary_type', 'salary');
        $employeePaidAmount = PayrollDetailsModel::whereIn('payroll_id', $payroll)->whereIn('user_id', $employees->pluck('id'))->sum('final_salary');

        if ($boatId) {
            $tripExpQ->where('boat_id', $boatId);
            $generalExpQ->where('boat_id', $boatId);
            $total_captins = User::whereIn('role', ['crew', 'captain'])->where('salary_type', 'percentage')->where('boat_id', $boatId)->count();
            $captins = User::whereIn('role', ['crew', 'captain'])->where('salary_type', 'salary')->where('boat_id', $boatId);


            $trips = Trip::where('boat_id', $boatId)->pluck('id');
            $salesQ = $salesQ->whereIn('trip_id', $trips);
            $tripExpQ = $tripExpQ->where('boat_id', $boatId);
            $generalExpQ = $generalExpQ->where('boat_id', $boatId);

            $employeePaidAmountSalary = ($employeePaidAmount / $boatIDs->count());
        } else {
            $total_captins = User::whereIn('role', ['crew', 'captain'])->where('salary_type', 'percentage')->whereIN('boat_id', $boatIDs)->count();
            $captins = User::whereIn('role', ['crew', 'captain'])->where('salary_type', 'salary')->whereIN('boat_id', $boatIDs);

            $employeePaidAmountSalary = $employeePaidAmount;
        }

        $sales = (float) $salesQ->sum('total_price');
        $depreciation = ($sales * (5 / 100));
        $sales = ($sales - $depreciation);
        $return = 0;

        $trip_expenses = (float) $tripExpQ->sum('final_price');
        $general_expenses = (float) $generalExpQ->sum('final_price');


        $paidAmount = PayrollDetailsModel::whereIn('payroll_id', $payroll)->whereIn('user_id', $captins->pluck('id'))->sum('final_salary');


        $total_expenses = $trip_expenses + $general_expenses + $paidAmount + $employeePaidAmountSalary;
        $netSales = ($sales - $total_expenses);

        $ownerPercent = ($netSales / 2);
        $total_profit = ($netSales - $trip_expenses);
        $net = ($total_profit - $general_expenses);


        //        $expenses = (float) $expQ->sum('final_price');        
        //        $net = $sales - ($expenses + $payrolls);        

        return view('owner.report.profit_loss_new', compact(
            'from',
            'to',
            'boatId',
            'boats',
            'sales',
            'total_profit',
            'net',
            'netSales',
            'general_expenses',
            'trip_expenses',
            'depreciation',
            'ownerPercent',
            'total_expenses',
            'total_captins'
        ));
    }

    public function print(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $ownerId = Auth::guard('owner')->id() ?? Auth::id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        // Fetch boats for this owner
        $boats = Boat::where('owner_id', $ownerId)->get();

        $boatId = $request->input('boat_id');

        $salesQ = Sale::query()
            ->where('seller_type', 'owner')
            ->where('seller_id', $ownerId)
            ->whereBetween(DB::raw('DATE(sale_datetime)'), [$from, $to]);

        $expQ = Expense::query()
            ->where('owner_id', $ownerId)
            ->whereBetween('date', [$from, $to]);

        $payroll = PayrollModel::where('is_paid', 1)
            ->where('status', 'approved')
            ->where('type', 'salary')
            ->whereBetween('paid_at', [$from, $to])
            ->pluck('id');


        if ($boatId) {
            $expQ->where('boat_id', $boatId);
        }

        $sales = (float) $salesQ->sum('total_price');
        $expenses = (float) $expQ->sum('final_price');
        $employees = User::whereIn('role', ['employee'])->where('salary_type', 'salary');
        $payrolls = PayrollDetailsModel::whereIn('payroll_id', $payroll)->whereIn('user_id', $employees->pluck('id'))->sum('final_salary');
        $net = $sales - ($expenses + $payrolls);

        $settings = $this->getCompanySettings();

        return view('owner.report.profit_loss_print', compact(
            'from',
            'to',
            'boatId',
            'boats',
            'sales',
            'expenses',
            'payrolls',
            'net',
            'settings'
        ));
    }

    private function getCompanySettings(): array
    {
        $owner = Auth::guard('owner')->user();
        $companyName = $owner->name ?? 'N/A';
        $vat = $owner->vat_number ?? '';
        $qrPayload = "Company: {$companyName}\nVAT: {$vat}";

        return [
            'name' => $companyName,
            'company_name' => $companyName,
            'phone' => $owner->phone ?? 'N/A',
            'email' => $owner->email ?? 'N/A',
            'address' => $owner->address ?? 'N/A',
            'logo' => $owner->logo ?? null,
            'qr_code' => $this->generateQRCodeImage($qrPayload),
        ];
    }

    private function generateQRCodeImage(string $data): ?string
    {
        try {
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($data);
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'ignore_errors' => true,
                ],
            ]);

            $imageData = @file_get_contents($qrUrl, false, $context);

            if ($imageData !== false && ! empty($imageData)) {
                return 'data:image/png;base64,' . base64_encode($imageData);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return null;
    }
}
