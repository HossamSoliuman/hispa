<?php

namespace App\Http\Controllers\Owner\Report;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\PayrollDetailsModel;
use App\Models\Sale;
use App\Models\User;
use App\Service\Owner\ReportQrService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * Hub account statements (كشف الحساب): pick one customer / vendor / crew member
 * and view or print their full statement. The printable views are the same ones
 * used from the individual show pages so the look stays identical.
 */
class AccountStatementController extends Controller
{
    public function customer(Request $request)
    {
        [$from, $to] = $this->period($request);
        $customerId = $request->integer('customer_id') ?: null;

        $customers = Customer::where('owner_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $customer = null;
        $statistics = null;

        if ($customerId) {
            $customer = $this->loadCustomer($customerId, $from, $to);
            $statistics = $customer ? $this->customerStatistics($customer) : null;
        }

        return view('owner.report.statements.customer', compact('customers', 'customer', 'statistics', 'customerId', 'from', 'to'));
    }

    public function customerPrint(Request $request): \Illuminate\Http\Response
    {
        [$from, $to] = $this->period($request);
        $customer = $this->loadCustomer($request->integer('customer_id'), $from, $to);
        abort_if(! $customer, 404);

        $statistics = $this->customerStatistics($customer);
        $settings = $this->reportSettings();

        return pdf_report(
            view('owner.reports.print.customer-statement', compact('customer', 'statistics', 'settings')),
            [], 'customer-statement-'.$customer->id.'.pdf', $this->disposition($request)
        );
    }

    public function vendor(Request $request)
    {
        [$from, $to] = $this->period($request);
        $vendorId = $request->integer('vendor_id') ?: null;

        $vendors = User::where('role', 'vendor')
            ->where('owner_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $vendor = null;
        $expenses = collect();
        $totalDue = 0.0;
        $totalExpenses = 0.0;

        if ($vendorId) {
            $vendor = User::where('role', 'vendor')->where('owner_id', auth()->id())->find($vendorId);

            if ($vendor) {
                $expenses = $this->vendorExpenses($vendor, $from, $to);
                $totalDue = (float) $expenses->where('status', 'pending')->sum('final_price');
                $totalExpenses = (float) $expenses->sum('final_price');
            }
        }

        return view('owner.report.statements.vendor', compact('vendors', 'vendor', 'expenses', 'totalDue', 'totalExpenses', 'vendorId', 'from', 'to'));
    }

    public function vendorPrint(Request $request): \Illuminate\Http\Response
    {
        [$from, $to] = $this->period($request);
        $vendor = User::where('role', 'vendor')->where('owner_id', auth()->id())->find($request->integer('vendor_id'));
        abort_if(! $vendor, 404);

        $expenses = $this->vendorExpenses($vendor, $from, $to);
        $totalDue = (float) $expenses->where('status', 'pending')->sum('final_price');
        $totalExpenses = (float) $expenses->sum('final_price');
        $settings = $this->reportSettings();
        $qrCode = $settings['qr_code'] ?? '';

        return pdf_report(
            view('owner.reports.vendor', compact('vendor', 'expenses', 'totalDue', 'totalExpenses', 'settings', 'qrCode')),
            [], 'vendor-statement-'.$vendor->id.'.pdf', $this->disposition($request)
        );
    }

    public function crew(Request $request)
    {
        [$from, $to] = $this->period($request);
        $personId = $request->integer('person_id') ?: null;

        $captains = User::captainRole()->where('owner_id', auth()->id())->orderBy('name')->get(['id', 'name']);
        $fishers = User::crewRole()->where('owner_id', auth()->id())->orderBy('name')->get(['id', 'name']);

        $user = null;
        $details = collect();

        if ($personId) {
            $user = User::where('owner_id', auth()->id())
                ->whereIn('role', ['captain', 'crew'])
                ->with('boat')
                ->find($personId);

            if ($user) {
                $details = $this->crewDetails($user, $from, $to);
            }
        }

        return view('owner.report.statements.crew', compact('captains', 'fishers', 'user', 'details', 'personId', 'from', 'to'));
    }

    public function crewPrint(Request $request): \Illuminate\Http\Response
    {
        [$from, $to] = $this->period($request);
        $user = User::where('owner_id', auth()->id())
            ->whereIn('role', ['captain', 'crew'])
            ->with('boat')
            ->find($request->integer('person_id'));
        abort_if(! $user, 404);

        $details = $this->crewDetails($user, $from, $to);
        $settings = $this->reportSettings();
        $title = __('owner.payrolls.statement.title', ['name' => $user->name]);

        return pdf_report(
            view('owner.reports.print.payroll-statement', compact('user', 'details', 'settings', 'title')),
            [], 'payroll-statement-'.$user->id.'.pdf', $this->disposition($request)
        );
    }

    private function loadCustomer(int $id, ?string $from, ?string $to): ?Customer
    {
        return Customer::where('owner_id', auth()->id())
            ->with([
                'sales' => fn ($q) => $q->orderByDesc('sale_datetime')
                    ->when($from, fn ($qq) => $qq->whereDate('sale_datetime', '>=', $from))
                    ->when($to, fn ($qq) => $qq->whereDate('sale_datetime', '<=', $to)),
                'sales.paymentMethod',
                'sales.details',
                'sales.details.unit',
            ])
            ->find($id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Expense>
     */
    private function vendorExpenses(User $vendor, ?string $from, ?string $to): Collection
    {
        return Expense::where('vendor_id', $vendor->id)
            ->with(['category', 'trip'])
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection<int, PayrollDetailsModel>
     */
    private function crewDetails(User $user, ?string $from, ?string $to): \Illuminate\Support\Collection
    {
        $details = PayrollDetailsModel::statementFor((int) $user->id, (int) auth()->id());

        $periodOf = fn ($d) => sprintf('%04d-%02d', (int) ($d->payroll?->year ?? 0), (int) ($d->payroll?->month ?? 0));

        if ($from) {
            $fromYm = substr($from, 0, 7);
            $details = $details->filter(fn ($d) => $periodOf($d) >= $fromYm);
        }
        if ($to) {
            $toYm = substr($to, 0, 7);
            $details = $details->filter(fn ($d) => $periodOf($d) <= $toYm);
        }

        return $details->values();
    }

    /**
     * @return array{total_orders:int, total_purchases:float, total_paid:float, total_remaining:float, total_weight:float, last_order:?string}
     */
    private function customerStatistics(Customer $customer): array
    {
        $sales = $customer->sales;
        $totalPurchases = (float) $sales->sum('total_price');
        $totalRemaining = (float) $sales->sum('remaining_total');

        return [
            'total_orders' => $sales->count(),
            'total_purchases' => $totalPurchases,
            'total_paid' => $totalPurchases - $totalRemaining,
            'total_remaining' => $totalRemaining,
            'total_weight' => (float) $sales->sum(fn (Sale $sale) => $sale->details->sum('weight')),
            'last_order' => $sales->max('sale_datetime')?->format('Y-m-d'),
        ];
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function period(Request $request): array
    {
        return [
            $request->input('from') ?: null,
            $request->input('to') ?: null,
        ];
    }

    private function disposition(Request $request): string
    {
        return $request->boolean('download') ? 'attachment' : 'inline';
    }

    /**
     * @return array<string, mixed>
     */
    private function reportSettings(): array
    {
        $companyName = currentCompany()?->name ?: 'حسبة';

        return ownerCompanySettings([
            'qr_code' => app(ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);
    }
}
