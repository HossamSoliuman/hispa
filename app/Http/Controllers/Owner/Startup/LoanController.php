<?php

namespace App\Http\Controllers\Owner\Startup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Startup\LoanRequest;
use App\Models\Startup\Loan;
use App\Models\Startup\Project;
use App\Service\Owner\Startup\OwnershipAllocationService;
use Illuminate\Http\RedirectResponse;

class LoanController extends Controller
{
    public function __construct(private readonly OwnershipAllocationService $allocation) {}

    public function index(Project $project)
    {
        return redirect()->route('owner.startup.projects.show', $project);
    }

    public function create(Project $project)
    {
        return redirect()->route('owner.startup.projects.show', $project);
    }

    public function store(LoanRequest $r, Project $project): RedirectResponse
    {
        $this->allocation->ensureComplete($project);

        $project->loans()->create($r->validated() + ['owner_id' => auth()->id()]);

        return back()->with('success', __('owner.startup.saved'));
    }

    public function edit(Loan $loan)
    {
        return redirect()->route('owner.startup.projects.show', ['project' => $loan->project_id, 'tab' => 'loans', 'edit_loan' => $loan->id]);
    }

    public function update(LoanRequest $r, Loan $loan): RedirectResponse
    {
        $loan->update($r->validated());
        $loan->recomputeStatus();

        return redirect()->route('owner.startup.projects.show', $loan->project_id)->with('success', __('owner.startup.saved'));
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        if ($loan->expenses()->exists()) {
            return back()->withErrors(['loan' => __('owner.startup.loan_in_use')]);
        }
        $loan->delete();

        return back()->with('success', __('owner.startup.deleted'));
    }
}
