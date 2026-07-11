<?php

namespace App\Http\Controllers\Owner\Startup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Startup\ProjectRequest;
use App\Models\Startup\ExpenseCategory;
use App\Models\Startup\LoanPayment;
use App\Models\Startup\Project;
use App\Service\Owner\Startup\OwnershipAllocationService;
use App\Service\Owner\Startup\ProjectAccountingService;
use App\Service\Owner\Startup\StartupDefaultsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectAccountingService $accounting,
        private readonly StartupDefaultsService $defaults,
        private readonly OwnershipAllocationService $allocation,
    ) {}

    public function index(): View
    {
        $this->defaults->seed();
        $projects = Project::withCount('partners')->withSum('expenses', 'amount')->latest()->paginate(15);

        return view('owner.startup.projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('owner.startup.projects.form', ['project' => new Project]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $project = Project::create($request->validated());

        return redirect()->route('owner.startup.projects.show', $project)->with('success', __('owner.startup.saved'));
    }

    public function show(Request $request, Project $project): View
    {
        $project->load(['partners', 'contributions.partner', 'loans.payments.partner', 'loans.partner']);
        $expenses = $project->expenses()->with(['category', 'partner', 'loan'])
            ->when($request->filled('exp_category'), fn ($query) => $query->where('category_id', $request->integer('exp_category')))
            ->when($request->filled('exp_payer'), fn ($query) => $query->where('payer_type', $request->input('exp_payer')))
            ->when($request->filled('exp_from'), fn ($query) => $query->whereDate('date', '>=', $request->date('exp_from')))
            ->when($request->filled('exp_to'), fn ($query) => $query->whereDate('date', '<=', $request->date('exp_to')))
            ->latest('date')->latest('id')->get();
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name_ar')->get();
        $summary = $this->accounting->summary($project);
        $partnerAccounting = $project->partners->mapWithKeys(fn ($partner) => [$partner->id => $this->accounting->partner($project, $partner)]);
        $allocationComplete = $this->allocation->isComplete($project);
        $allocationPercentage = $this->allocation->percentage($project);
        $editingPartner = $request->filled('edit_partner') ? $project->partners()->findOrFail($request->integer('edit_partner')) : null;
        $editingExpense = $request->filled('edit_expense') ? $project->expenses()->findOrFail($request->integer('edit_expense')) : null;
        $editingContribution = $request->filled('edit_contribution') ? $project->contributions()->findOrFail($request->integer('edit_contribution')) : null;
        $editingLoan = $request->filled('edit_loan') ? $project->loans()->findOrFail($request->integer('edit_loan')) : null;

        return view('owner.startup.projects.show', compact(
            'project',
            'expenses',
            'categories',
            'summary',
            'partnerAccounting',
            'allocationComplete',
            'allocationPercentage',
            'editingPartner',
            'editingExpense',
            'editingContribution',
            'editingLoan',
        ));
    }

    public function edit(Project $project): View
    {
        return view('owner.startup.projects.form', compact('project'));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('owner.startup.projects.show', $project)->with('success', __('owner.startup.saved'));
    }

    /**
     * Children are removed in dependency order because expenses/contributions/
     * payments hold restrictOnDelete FKs to partners and loans, which would
     * abort the DB-level cascade fired by deleting the project row directly.
     */
    public function destroy(Project $project): RedirectResponse
    {
        DB::transaction(function () use ($project): void {
            $project->expenses()->whereNotNull('attachment')->pluck('attachment')
                ->each(fn (string $path) => Storage::disk('public')->delete($path));
            LoanPayment::whereIn('loan_id', $project->loans()->select('id'))->delete();
            $project->expenses()->delete();
            $project->contributions()->delete();
            $project->loans()->delete();
            $project->partners()->delete();
            $project->delete();
        });

        return redirect()->route('owner.startup.projects.index')->with('success', __('owner.startup.deleted'));
    }
}
