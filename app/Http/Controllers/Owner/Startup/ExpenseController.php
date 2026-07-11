<?php

namespace App\Http\Controllers\Owner\Startup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Startup\ExpenseRequest;
use App\Models\Startup\Expense;
use App\Models\Startup\Project;
use App\Service\Owner\Startup\OwnershipAllocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
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

    public function store(ExpenseRequest $r, Project $project): RedirectResponse
    {
        $this->allocation->ensureComplete($project);

        $data = $r->validated();
        $data['is_shared'] = $r->boolean('is_shared');
        unset($data['remove_attachment']);
        if ($r->hasFile('attachment')) {
            $data['attachment'] = $r->file('attachment')->store('startup/expenses/'.auth()->id(), 'public');
        }$project->expenses()->create($data + ['owner_id' => auth()->id()]);

        return back()->with('success', __('owner.startup.saved'));
    }

    public function edit(Expense $expense)
    {
        return redirect()->route('owner.startup.projects.show', ['project' => $expense->project_id, 'tab' => 'expenses', 'edit_expense' => $expense->id]);
    }

    public function update(ExpenseRequest $r, Expense $expense): RedirectResponse
    {
        $data = $r->validated();
        $data['is_shared'] = $r->boolean('is_shared');
        unset($data['remove_attachment']);
        if ($r->boolean('remove_attachment') && $expense->attachment) {
            Storage::disk('public')->delete($expense->attachment);
            $data['attachment'] = null;
        }
        if ($r->hasFile('attachment')) {
            if ($expense->attachment) {
                Storage::disk('public')->delete($expense->attachment);
            }
            $data['attachment'] = $r->file('attachment')->store('startup/expenses/'.auth()->id(), 'public');
        }
        $expense->update($data);

        return redirect()->route('owner.startup.projects.show', $expense->project_id)->with('success', __('owner.startup.saved'));
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        if ($expense->attachment) {
            Storage::disk('public')->delete($expense->attachment);
        }
        $expense->delete();

        return back()->with('success', __('owner.startup.deleted'));
    }
}
