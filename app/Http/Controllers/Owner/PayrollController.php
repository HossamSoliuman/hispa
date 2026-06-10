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

    public function index()
    {
        return view('owner.payroll.index');
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
        $periods = PayrollModel::where('is_paid', 1)
            ->select('year', 'month')
            ->distinct()
            ->get()
            ->map(fn ($p) => sprintf('%04d-%02d', $p->year, $p->month))
            ->values();

        return response()->json($periods);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('owner.payroll.create');
    }

    public function percentageCreate(Request $request)
    {
        return view('owner.payroll.percentage_create');
    }

    public function check(Request $request, PayrollService $service)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
        ]);

        $payroll = PayrollModel::where('year', $request->year)
            ->where('month', $request->month)
            ->where('type', 'salary')
            ->with('details')
            ->first();

        if ($payroll) {
            return redirect()->route('owner.payrolls.edit', $payroll);
        }

        $payroll = app(PayrollService::class)
            ->calculateMonthlyPayroll($request->year, $request->month);

        $year = $request->year;
        $month = $request->month;

        return redirect()->route('owner.payrolls.edit', $payroll);
    }

    public function percentageCheck(Request $request, PayrollService $service)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
        ]);

        $salaryPayroll = PayrollModel::where('year', $request->year)
            ->where('month', $request->month)
            ->where('type', 'salary')
            ->where('status', 'approved')
            ->where('is_paid', 1)
            ->first();
        if ($salaryPayroll) {
            $payroll = PayrollModel::where('year', $request->year)
                ->where('month', $request->month)
                ->where('type', 'percentage')
                ->with('details')
                ->first();

            if ($payroll) {
                return redirect()->route('owner.payrolls.edit', $payroll);
            }

            $payroll = app(PayrollService::class)
                ->calculateMonthlyPayrollPercentage($request->year, $request->month);

            $year = $request->year;
            $month = $request->month;

            return redirect()->route('owner.payrolls.edit', $payroll);
        }

        return redirect()->back()->with('error', 'لم بتم اعتماد كشف الرواتب الثابتة لهذا الشهر الرجاء اعتماده ومن ثاول مرة اخرى');
    }

    public function store(PayrollRequest $request)
    {
        return $this->repository->saveData($request);
    }

    public function edit($id)
    {
        $payroll = PayrollModel::find($id);

        return view('owner.payroll.edit', compact('payroll'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PayrollRequest $request)
    {

        $payroll = PayrollModel::find($request->id);

        if ($payroll) {
            if ($payroll->status == 'approved' && $payroll->is_paid) {
                return redirect()->back()->with('error', 'لا يمكن تعديل مسير مدفوع ومعتمد');
            }

            $payroll->update([
                'status' => $request->status,
                'is_paid' => $request->is_paid ?? 0,
                'paid_at' => $request->is_paid ? $request->payment_date ?? now()->format('Y-m-d H:i:s') : null,
            ]);

            if ($request->details) {
                foreach ($request->details as $d) {
                    $detail = $payroll->details()->find($d['id']);
                    $salary = 0;
                    if ($detail->user->salary_type == 'salary') {
                        $salary = $detail->base_salary;
                    } else {
                        // Percentage payroll stores final_salary as the per-head amount
                        // (captins_amount / captins_count) at creation; recompute the
                        // update on the same per-head basis, not the full sales amount.
                        $salary = $detail->captins_count > 0
                            ? $detail->captins_amount / $detail->captins_count
                            : 0;
                    }
                    if ($detail) {
                        $detail->update([
                            'increase' => $d['increase'] ?? 0,
                            'deduction' => $d['deduction'] ?? 0,
                            'note' => $d['note'] ?? '',
                            'final_salary' => $salary + ($d['increase'] ?? 0) - ($d['deduction'] ?? 0),
                        ]);
                    }
                }
            }
        }
        if ($payroll->type == 'salary') {
            return redirect()->route('owner.payrolls.index')->with('success', 'تم حفظ مسير الرواتب بنجاح');
        } else {
            return redirect()->route('owner.percentage')->with('success', 'تم حفظ مسير الرواتب بنجاح');
        }
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

            $payroll = PayrollModel::where('id', $id)->first();
            $payroll->delete();
            PayrollDetailsModel::where('payroll_id', $id)->delete();
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
        // Load company settings and generate QR code (link to the printable payroll URL)
        $settings = $this->getCompanySettings();
        $qrCode = app(\App\Service\Owner\ReportQrService::class)
            ->dataUri(route('owner.payrolls.print', $payroll->id));

        if (view()->exists('owner.payroll.print')) {
            return view('owner.payroll.print', compact('payroll', 'settings', 'qrCode'));
        }

        return view('owner.payroll.show', compact('payroll'));
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
