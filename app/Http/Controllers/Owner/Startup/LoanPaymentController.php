<?php

namespace App\Http\Controllers\Owner\Startup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Startup\LoanPaymentRequest;
use App\Models\Startup\Loan;
use App\Models\Startup\LoanPayment;
use App\Service\Owner\Startup\OwnershipAllocationService;
use Illuminate\Http\RedirectResponse;

class LoanPaymentController extends Controller
{
    public function __construct(private readonly OwnershipAllocationService $allocation) {}

    public function store(LoanPaymentRequest $r, Loan $loan): RedirectResponse
    {
        $this->allocation->ensureComplete($loan->project);

        $loan->payments()->create($r->validated() + ['owner_id' => auth()->id()]);
        $loan->recomputeStatus();

        return back()->with('success', __('owner.startup.saved'));
    }

    public function destroy(LoanPayment $payment): RedirectResponse
    {
        $loan = $payment->loan;
        $payment->delete();
        $loan->recomputeStatus();

        return back()->with('success', __('owner.startup.deleted'));
    }
}
