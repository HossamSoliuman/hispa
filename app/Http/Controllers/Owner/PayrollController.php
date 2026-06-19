<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\PayrollDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\PayrollRequest;
use App\Models\Boat;
use App\Models\Payroll;
use App\Models\PayrollDetailsModel;
use App\Models\PayrollModel;
use App\Repository\Owner\PayrollRepository;
use App\Service\Owner\PayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class PayrollController extends Controller
{
    protected $service;

    protected $repository;

    private $datatable;

    public function __construct()
    {
        $this->service = new PayrollService;
        $this->repository = new PayrollRepository;
        $this->datatable = new PayrollDataTable;
    }

    public function getPercentage()
    {
        return view('owner.payroll.percentage');
    }

    public function getData(Request $request)
    {

        return $this->datatable->getData($request);
    }

    // Fetch already-paid monthly periods (to disable them in the datepicker)
    public function paidPeriods(Boat $boat)
    {
        $periods = PayrollModel::where('owner_id', $this->ownerId())
            ->where('is_paid', 1)
            ->select('year', 'month')
            ->distinct()
            ->get()
            ->map(fn ($p) => sprintf('%04d-%02d', $p->year, $p->month))
            ->values();

        return response()->json($periods);
    }

    private function ownerId(): int
    {
        $ownerId = Auth::guard('owner')->id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        return (int) $ownerId;
    }

    /**
     * Find an owner-scoped payroll or fail with 404 (never leak other tenants' payrolls).
     */
    private function findOwnerPayroll(int|string $id): PayrollModel
    {
        return PayrollModel::where('owner_id', $this->ownerId())->findOrFail($id);
    }

    public function percentageCreate(Request $request)
    {
        return view('owner.payroll.percentage_create');
    }

    public function percentageCheck(Request $request, PayrollService $service)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
        ]);

        $ownerId = $this->ownerId();

        $payroll = PayrollModel::where('owner_id', $ownerId)
            ->where('year', $request->year)
            ->where('month', $request->month)
            ->where('type', 'percentage')
            ->with('details')
            ->first();

        if ($payroll) {
            return redirect()->route('owner.payrolls.edit', $payroll);
        }

        $payroll = app(PayrollService::class)
            ->calculateMonthlyPayrollPercentage($ownerId, $request->year, $request->month);

        return redirect()->route('owner.payrolls.edit', $payroll);
    }

    public function store(PayrollRequest $request)
    {
        Boat::where('owner_id', $this->ownerId())->findOrFail($request->boat_id);

        return $this->repository->saveData($request);
    }

    public function edit($id)
    {
        $payroll = $this->findOwnerPayroll($id);

        return view('owner.payroll.edit', compact('payroll'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PayrollRequest $request)
    {
        $payroll = $this->findOwnerPayroll($request->id);

        if ($payroll) {
            if ($payroll->status == 'approved' && $payroll->is_paid) {
                return redirect()->back()->with('error', 'لا يمكن تعديل مسير مدفوع ومعتمد');
            }

            $payroll->update(['status' => $request->status]);

            if ($request->details) {
                foreach ($request->details as $d) {
                    $detail = $payroll->details()->find($d['id']);
                    // Already-paid rows are frozen; payment is recorded per person.
                    if (! $detail || $detail->is_paid) {
                        continue;
                    }

                    $detail->update([
                        'increase' => $d['increase'] ?? 0,
                        'deduction' => $d['deduction'] ?? 0,
                        'note' => $d['note'] ?? '',
                        'final_salary' => $this->detailFinalSalary($detail, (float) ($d['increase'] ?? 0), (float) ($d['deduction'] ?? 0)),
                    ]);
                }
            }

            $this->syncPayrollPaidState($payroll);
        }

        return redirect()->route('owner.percentage')->with('success', 'تم حفظ مسير الرواتب بنجاح');
    }

    /**
     * Record payment for a single crew/captain/employee row (per-person pay).
     * Saves the row's latest increase/deduction first, then freezes the paid amount.
     */
    public function payDetail(Request $request, PayrollDetailsModel $detail)
    {
        $payroll = $detail->payroll;
        abort_if(! $payroll || $payroll->owner_id !== $this->ownerId(), 403);

        $data = $request->validate([
            'increase' => 'nullable|numeric',
            'deduction' => 'nullable|numeric',
            'note' => 'nullable|string',
        ]);

        if ($detail->is_paid) {
            return response()->json([
                'message' => 'تم تسديد هذا الشخص مسبقاً',
                'final_salary' => (float) $detail->final_salary,
                'paid_at' => optional($detail->paid_at)->format('Y-m-d'),
            ], 422);
        }

        $increase = (float) ($data['increase'] ?? $detail->increase ?? 0);
        $deduction = (float) ($data['deduction'] ?? $detail->deduction ?? 0);
        $finalSalary = $this->detailFinalSalary($detail, $increase, $deduction);

        $detail->update([
            'increase' => $increase,
            'deduction' => $deduction,
            'note' => $data['note'] ?? $detail->note,
            'final_salary' => $finalSalary,
            'is_paid' => true,
            'paid_at' => now(),
            'paid_amount' => $finalSalary,
        ]);

        $this->syncPayrollPaidState($payroll);

        return response()->json([
            'message' => __('owner.payrolls.pay_success'),
            'final_salary' => $finalSalary,
            'paid_amount' => $finalSalary,
            'paid_at' => $detail->paid_at->format('Y-m-d'),
            'payroll_fully_paid' => (bool) $payroll->fresh()->is_paid,
        ]);
    }

    /**
     * Net pay for a detail row: percentage staff use the per-head share
     * (captins_amount / captins_count). Increase/deduction are applied on top.
     */
    private function detailFinalSalary(PayrollDetailsModel $detail, float $increase, float $deduction): float
    {
        $base = (int) $detail->captins_count > 0
            ? (float) $detail->captins_amount / (int) $detail->captins_count
            : 0.0;

        return round($base + $increase - $deduction, 2);
    }

    /**
     * Keep the parent payroll's paid flag in sync with its rows: a payroll is
     * "paid" only once every person on it has been paid.
     */
    private function syncPayrollPaidState(PayrollModel $payroll): void
    {
        $payroll->loadMissing('details');
        $details = $payroll->details;
        $allPaid = $details->isNotEmpty() && $details->every(fn ($d) => (bool) $d->is_paid);

        $payroll->update([
            'is_paid' => $allPaid ? 1 : 0,
            'paid_at' => $allPaid ? ($details->max('paid_at') ?? now()) : null,
        ]);
    }

    public function carryover(Request $request)
    {
        // هنا تحفظ الفائض أو العجز للشهر القادم
        // مثال: PayrollCarryOver::create([...]);

        return response()->json(['message' => 'Balance carried over successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $payroll = $this->findOwnerPayroll($id);
            $payroll->delete();
            PayrollDetailsModel::where('payroll_id', $payroll->id)->delete();
            DB::commit();

            return response()->json(['message' => 'Data saved successfully'], 200);
        } catch (\Exception $ex) {
            if (App::environment('local')) {
                session()->flash('error', 'حدث خطأ ما');
            }

            return response()->json(['message' => $ex->getMessage()], 403);
        }
    }

    /**
     * Print payroll report (uses report components)
     */
    public function print(PayrollModel $payroll)
    {
        abort_if($payroll->owner_id !== $this->ownerId(), 403);

        // Load company settings and generate QR code (link to the printable payroll URL)
        $settings = $this->getCompanySettings();
        $qrCode = app(\App\Service\Owner\ReportQrService::class)
            ->dataUri(route('owner.payrolls.print', $payroll->id));

        return pdf_report(view('owner.payroll.print', compact('payroll', 'settings', 'qrCode')), [], 'payroll.pdf');
    }

    // Local helpers reused for report generation (kept in-controller for now)
    private function getCompanySettings()
    {
        $user = auth()->user();
        $logoPath = public_path('default-logo.png');

        return [
            'title' => $user->company_name ?? $user->name ?? config('app.name'),
            'company_name' => $user->company_name ?? $user->name ?? config('app.name'),
            'logo' => $logoPath,
            'watermark' => $logoPath,
            'phone' => $user->phone ?? '',
            'email' => $user->email ?? '',
            'address' => $user->address ?? '',
        ];
    }
}
