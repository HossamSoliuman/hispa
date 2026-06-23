<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Models\MonthClosing;
use App\Service\Owner\MonthClosingService;
use App\Service\Owner\PayrollService;
use App\Service\Owner\ReportQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MonthClosingController extends Controller
{
    public function __construct(
        private MonthClosingService $service,
        private PayrollService $payrollService,
    ) {}

    public function index(Request $request)
    {
        $ownerId = $this->ownerId();

        $closings = MonthClosing::where('owner_id', $ownerId)
            ->with('boat')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $boats = Boat::where('owner_id', $ownerId)->get();

        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $boatId = $request->filled('boat_id') ? (int) $request->input('boat_id') : null;

        return view('owner.month_closing.index', compact('closings', 'boats', 'year', 'month', 'boatId'));
    }

    public function preview(Request $request)
    {
        $data = $this->validatePeriod($request);

        $preview = $this->service->preview(
            $this->ownerId(),
            (int) $data['year'],
            (int) $data['month'],
            $data['boat_id'],
        );

        return view('owner.month_closing.preview', [
            'preview' => $preview,
            'boats' => Boat::where('owner_id', $this->ownerId())->get(),
            'year' => (int) $data['year'],
            'month' => (int) $data['month'],
            'boatId' => $data['boat_id'],
        ]);
    }

    public function close(Request $request)
    {
        $data = $this->validatePeriod($request);

        $ownerId = $this->ownerId();

        try {
            $closing = $this->service->close($ownerId, (int) $data['year'], (int) $data['month'], $ownerId, $data['boat_id']);
        } catch (\DomainException $e) {
            return redirect()->route('owner.month-closing.index')->with('error', $e->getMessage());
        }

        return redirect()->route('owner.month-closing.show', $closing)
            ->with('success', __('owner.month_closing.messages.closed_success'));
    }

    public function show(MonthClosing $monthClosing)
    {
        $this->authorizeOwner($monthClosing);
        $monthClosing->load('dues', 'boat');
        $this->linkPayrollPayments($monthClosing);

        return view('owner.month_closing.show', [
            'closing' => $monthClosing,
            'details' => $this->service->details($monthClosing),
            'payrollSummary' => $this->payrollService->monthlyPayrollSummary($monthClosing->owner_id, $monthClosing->year, $monthClosing->month),
        ]);
    }

    public function print(MonthClosing $monthClosing)
    {
        $this->authorizeOwner($monthClosing);
        $monthClosing->load('dues', 'boat');
        $this->linkPayrollPayments($monthClosing);

        return pdf_report(view('owner.month_closing.print', [
            'closing' => $monthClosing,
            'settings' => $this->companySettings(),
        ]), [], 'month-closing.pdf');
    }

    public function reopen(MonthClosing $monthClosing)
    {
        $this->authorizeOwner($monthClosing);

        try {
            $this->service->reopen($monthClosing);
        } catch (\DomainException $e) {
            return redirect()->route('owner.month-closing.index')->with('error', $e->getMessage());
        }

        return redirect()->route('owner.month-closing.index')
            ->with('success', __('owner.month_closing.messages.reopened_success'));
    }

    public function destroy(MonthClosing $monthClosing)
    {
        $this->authorizeOwner($monthClosing);

        $monthClosing->dues()->delete();
        $monthClosing->delete();

        return redirect()->route('owner.month-closing.index')
            ->with('success', __('owner.month_closing.messages.deleted_success'));
    }

    /**
     * Reflect the real percentage-payroll disbursements on the month-close dues
     * so the report's "paid"/"remaining" columns match what crew were actually
     * paid. Mutates the in-memory dues only; the frozen snapshot is untouched.
     */
    private function linkPayrollPayments(MonthClosing $monthClosing): void
    {
        $paidByUser = $this->payrollService->monthlyPercentagePaidByUser(
            $monthClosing->owner_id,
            $monthClosing->year,
            $monthClosing->month,
        );

        if ($paidByUser === []) {
            return;
        }

        foreach ($monthClosing->dues as $due) {
            $paid = (float) ($paidByUser[$due->user_id] ?? 0);

            if ($paid <= 0) {
                continue;
            }

            $due->paid_amount = $paid;
            $due->remaining = round((float) $due->due_amount - (float) $due->advances - $paid, 2);
        }
    }

    /**
     * Validate the period selection (year/month + optional boat) shared by
     * preview and close. The boat must belong to the authenticated owner;
     * an empty boat means the whole fleet ("all boats").
     *
     * @return array{year: int, month: int, boat_id: int|null}
     */
    private function validatePeriod(Request $request): array
    {
        $ownerId = $this->ownerId();

        $data = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|between:1,12',
            'boat_id' => ['nullable', 'integer', Rule::exists('boats', 'id')->where('owner_id', $ownerId)],
        ]);

        return [
            'year' => (int) $data['year'],
            'month' => (int) $data['month'],
            'boat_id' => ($data['boat_id'] ?? null) !== null ? (int) $data['boat_id'] : null,
        ];
    }

    private function ownerId(): int
    {
        $ownerId = Auth::guard('owner')->id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        return (int) $ownerId;
    }

    private function authorizeOwner(MonthClosing $monthClosing): void
    {
        abort_if($monthClosing->owner_id !== $this->ownerId(), 403);
    }

    /**
     * @return array<string, mixed>
     */
    private function companySettings(): array
    {
        $companyName = currentCompany()?->name ?: 'N/A';

        return ownerCompanySettings([
            'qr_code' => app(ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);
    }
}
