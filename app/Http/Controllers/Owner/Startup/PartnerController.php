<?php

namespace App\Http\Controllers\Owner\Startup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Startup\PartnerRequest;
use App\Models\Startup\Partner;
use App\Models\Startup\Project;
use App\Service\Owner\Startup\OwnershipAllocationService;
use Illuminate\Http\RedirectResponse;

class PartnerController extends Controller
{
    public function __construct(private readonly OwnershipAllocationService $allocation) {}

    public function store(PartnerRequest $r, Project $project): RedirectResponse
    {
        $project->partners()->create($r->validated() + ['owner_id' => auth()->id(), 'has_salary' => $r->boolean('has_salary')]);

        return back()->with('success', __('owner.startup.saved'));
    }

    public function edit(Partner $partner)
    {
        return redirect()->route('owner.startup.projects.show', ['project' => $partner->project_id, 'tab' => 'partners', 'edit_partner' => $partner->id]);
    }

    public function update(PartnerRequest $r, Partner $partner): RedirectResponse
    {
        $partner->update($r->validated() + ['has_salary' => $r->boolean('has_salary')]);

        return redirect()->route('owner.startup.projects.show', $partner->project_id)->with('success', __('owner.startup.saved'));
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        if ($partner->expenses()->exists() || $partner->contributions()->exists() || $partner->loans()->exists() || $partner->loanPayments()->exists()) {
            return back()->withErrors(['partner' => __('owner.startup.partner_in_use')]);
        }

        $remainingBasisPoints = $this->allocation->totalBasisPoints($partner->project, $partner);
        if (($partner->project->status !== 'setup' || $this->allocation->hasFinancialActivity($partner->project)) && $remainingBasisPoints !== 10000) {
            return back()->withErrors(['partner' => __('owner.startup.validation.shares_delete')]);
        }

        $partner->delete();

        return back()->with('success', __('owner.startup.deleted'));
    }
}
