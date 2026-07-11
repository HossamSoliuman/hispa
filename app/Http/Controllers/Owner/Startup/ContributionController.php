<?php

namespace App\Http\Controllers\Owner\Startup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Startup\ContributionRequest;
use App\Models\Startup\Contribution;
use App\Models\Startup\Project;
use App\Service\Owner\Startup\OwnershipAllocationService;
use Illuminate\Http\RedirectResponse;

class ContributionController extends Controller
{
    public function __construct(private readonly OwnershipAllocationService $allocation) {}

    public function store(ContributionRequest $r, Project $project): RedirectResponse
    {
        $this->allocation->ensureComplete($project);

        $project->contributions()->create($r->validated() + ['owner_id' => auth()->id()]);

        return back()->with('success', __('owner.startup.saved'));
    }

    public function edit(Contribution $contribution)
    {
        return redirect()->route('owner.startup.projects.show', ['project' => $contribution->project_id, 'tab' => 'contributions', 'edit_contribution' => $contribution->id]);
    }

    public function update(ContributionRequest $r, Contribution $contribution): RedirectResponse
    {
        $contribution->update($r->validated());

        return redirect()->route('owner.startup.projects.show', $contribution->project_id)->with('success', __('owner.startup.saved'));
    }

    public function destroy(Contribution $contribution): RedirectResponse
    {
        $contribution->delete();

        return back()->with('success', __('owner.startup.deleted'));
    }
}
