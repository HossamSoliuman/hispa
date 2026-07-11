<?php

namespace App\Http\Controllers\Owner\Startup;

use App\Exports\Startup\TabularExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Startup\StartupReportFilterRequest;
use App\Models\Startup\ExpenseCategory;
use App\Models\Startup\Partner;
use App\Models\Startup\Project;
use App\Service\Owner\ReportQrService;
use App\Service\Owner\Startup\OwnershipAllocationService;
use App\Service\Owner\Startup\ProjectAccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /** @var array<string, list<string>> Column order per report, matching the spec tables. */
    private const COLUMNS = [
        'expenses' => ['date', 'category', 'name', 'description', 'amount', 'payer', 'method', 'invoice'],
        'partners' => ['partner', 'share', 'required', 'paid', 'balance'],
        'loans' => ['loan', 'principal', 'paid', 'remaining', 'installment', 'status'],
        'partner-statement' => ['date', 'type', 'description', 'amount'],
        'summary' => ['metric', 'value'],
    ];

    private const FILTER_KEYS = ['from', 'to', 'category_id', 'partner_id', 'payment_method', 'payer_type', 'is_shared', 'invoice'];

    public function __construct(
        private readonly ProjectAccountingService $accounting,
        private readonly OwnershipAllocationService $allocation,
    ) {}

    public function expenses(StartupReportFilterRequest $r, Project $project)
    {
        $this->allocation->ensureComplete($project);
        $rows = $this->expenseRows($r, $project);

        return view('owner.startup.reports.table', [
            'project' => $project,
            'title' => __('owner.startup.reports.expenses'),
            'columns' => self::COLUMNS['expenses'],
            'rows' => $rows,
            'totals' => $this->totalsRow('expenses', ['amount' => $rows->sum('amount')]),
            'report' => 'expenses',
            'categories' => ExpenseCategory::orderBy('name_ar')->get(),
        ]);
    }

    public function partners(Project $project)
    {
        $this->allocation->ensureComplete($project);
        $rows = $this->partnerRows($project);

        return view('owner.startup.reports.table', [
            'project' => $project,
            'title' => __('owner.startup.reports.partners'),
            'columns' => self::COLUMNS['partners'],
            'rows' => $rows,
            'totals' => $this->partnerTotals($rows),
            'report' => 'partners',
        ]);
    }

    public function loans(Project $project)
    {
        $this->allocation->ensureComplete($project);
        $rows = $this->loanRows($project);

        return view('owner.startup.reports.table', [
            'project' => $project,
            'title' => __('owner.startup.reports.loans'),
            'columns' => self::COLUMNS['loans'],
            'rows' => $rows,
            'totals' => $this->loanTotals($rows),
            'report' => 'loans',
        ]);
    }

    public function partnerStatement(StartupReportFilterRequest $r, Project $project)
    {
        $this->allocation->ensureComplete($project);
        $partner = $this->resolvePartner($r, $project);
        $rows = $this->statementRows($project, $partner);

        return view('owner.startup.reports.table', [
            'project' => $project,
            'title' => __('owner.startup.reports.statement'),
            'columns' => self::COLUMNS['partner-statement'],
            'rows' => $rows,
            'totals' => $this->totalsRow('partner-statement', ['amount' => $rows->sum('amount')]),
            'report' => 'partner-statement',
            'partner' => $partner,
            'statementSummary' => $partner ? $this->accounting->partner($project, $partner) : null,
        ]);
    }

    public function printSummary(StartupReportFilterRequest $r, Project $project)
    {
        $this->allocation->ensureComplete($project);
        $partnerRows = $this->partnerRows($project);
        $loanRows = $this->loanRows($project);

        return $this->pdf($r, $project, 'project-summary', [
            'summary' => $this->accounting->summary($project),
            'partnerRows' => $partnerRows,
            'partnerTotals' => $this->partnerTotals($partnerRows),
            'loanRows' => $loanRows,
            'loanTotals' => $this->loanTotals($loanRows),
        ]);
    }

    public function printExpenses(StartupReportFilterRequest $r, Project $project)
    {
        $this->allocation->ensureComplete($project);
        $rows = $this->expenseRows($r, $project);

        return $this->pdf($r, $project, 'expenses-report', [
            'rows' => $rows,
            'columns' => self::COLUMNS['expenses'],
            'totals' => $this->totalsRow('expenses', ['amount' => $rows->sum('amount')]),
        ]);
    }

    public function printPartners(StartupReportFilterRequest $r, Project $project)
    {
        $this->allocation->ensureComplete($project);
        $rows = $this->partnerRows($project);

        return $this->pdf($r, $project, 'partners-report', [
            'rows' => $rows,
            'columns' => self::COLUMNS['partners'],
            'totals' => $this->partnerTotals($rows),
        ]);
    }

    public function printLoans(StartupReportFilterRequest $r, Project $project)
    {
        $this->allocation->ensureComplete($project);
        $rows = $this->loanRows($project);

        return $this->pdf($r, $project, 'loans-report', [
            'rows' => $rows,
            'columns' => self::COLUMNS['loans'],
            'totals' => $this->loanTotals($rows),
        ]);
    }

    public function printPartnerStatement(StartupReportFilterRequest $r, Project $project)
    {
        $this->allocation->ensureComplete($project);
        $partner = $this->resolvePartner($r, $project);
        $rows = $this->statementRows($project, $partner);

        return $this->pdf($r, $project, 'partner-statement', [
            'rows' => $rows,
            'columns' => self::COLUMNS['partner-statement'],
            'totals' => $this->totalsRow('partner-statement', ['amount' => $rows->sum('amount')]),
            'partner' => $partner,
            'statementSummary' => $partner ? $this->accounting->partner($project, $partner) : null,
        ]);
    }

    public function excel(StartupReportFilterRequest $r, Project $project, string $report)
    {
        $this->allocation->ensureComplete($project);
        abort_unless(array_key_exists($report, self::COLUMNS), 404);

        $rows = match ($report) {
            'expenses' => $this->expenseRows($r, $project),
            'partners' => $this->partnerRows($project),
            'loans' => $this->loanRows($project),
            'partner-statement' => $this->statementRows($project, $this->resolvePartner($r, $project)),
            'summary' => $this->summaryRows($project),
        };
        if ($report === 'summary') {
            return Excel::download(new TabularExport($rows, self::COLUMNS[$report]), $report.'-'.$project->id.'.xlsx');
        }

        $totals = match ($report) {
            'expenses', 'partner-statement' => $this->totalsRow($report, ['amount' => $rows->sum('amount')]),
            'partners' => $this->partnerTotals($rows),
            'loans' => $this->loanTotals($rows),
        };

        return Excel::download(new TabularExport($rows->push($totals), self::COLUMNS[$report]), $report.'-'.$project->id.'.xlsx');
    }

    private function summaryRows(Project $project): Collection
    {
        $summary = $this->accounting->summary($project);

        return collect([
            ['metric' => __('owner.startup.total_cost'), 'value' => $summary['total_cost']],
            ['metric' => __('owner.startup.project_expenses'), 'value' => $summary['project_expenses']],
            ['metric' => __('owner.startup.contributions'), 'value' => $summary['contributions']],
            ['metric' => __('owner.startup.loans_total'), 'value' => $summary['loans_total']],
            ['metric' => __('owner.startup.loans_paid'), 'value' => $summary['loans_paid']],
            ['metric' => __('owner.startup.loans_remaining'), 'value' => $summary['loans_remaining']],
            ['metric' => __('owner.startup.partners_count'), 'value' => $summary['partners_count']],
            ['metric' => __('owner.startup.status'), 'value' => __('owner.startup.statuses.'.$summary['status'])],
        ]);
    }

    private function expenseRows(Request $r, Project $p): Collection
    {
        return $p->expenses()->with(['category', 'partner'])
            ->when($r->filled('from'), fn ($q) => $q->whereDate('date', '>=', $r->date('from')))
            ->when($r->filled('to'), fn ($q) => $q->whereDate('date', '<=', $r->date('to')))
            ->when($r->filled('category_id'), fn ($q) => $q->where('category_id', $r->integer('category_id')))
            ->when($r->filled('partner_id'), fn ($q) => $q->where('payer_type', 'partner')->where('partner_id', $r->integer('partner_id')))
            ->when($r->filled('payment_method'), fn ($q) => $q->where('payment_method', $r->input('payment_method')))
            ->when($r->filled('payer_type'), fn ($q) => $q->where('payer_type', $r->input('payer_type')))
            ->when($r->filled('is_shared'), fn ($q) => $q->where('is_shared', $r->input('is_shared') === '1'))
            ->when($r->input('invoice') === 'with', fn ($q) => $q->whereNotNull('attachment'))
            ->when($r->input('invoice') === 'without', fn ($q) => $q->whereNull('attachment'))
            ->latest('date')->latest('id')->get()
            ->map(fn ($x) => [
                'date' => $x->date->format('Y-m-d'),
                'category' => $x->category->name,
                'name' => $x->name,
                'description' => $x->description ?: '—',
                'amount' => (float) $x->amount,
                'payer' => $x->payer_type === 'partner' ? ($x->partner?->name ?? __('owner.startup.payer_types.partner')) : __('owner.startup.payer_types.'.$x->payer_type),
                'method' => __('owner.startup.methods.'.$x->payment_method),
                'invoice' => $x->attachment ? Storage::disk('public')->url($x->attachment) : __('owner.startup.without_invoice'),
            ]);
    }

    private function partnerRows(Project $project): Collection
    {
        return $project->partners->map(fn ($p) => ['partner' => $p->name, 'share' => (float) $p->share_percent] + $this->accounting->partner($project, $p));
    }

    private function partnerTotals(Collection $rows): array
    {
        return $this->totalsRow('partners', [
            'share' => $rows->sum('share'),
            'required' => $rows->sum('required'),
            'paid' => $rows->sum('paid'),
            'balance' => $rows->sum('balance'),
        ]);
    }

    private function loanRows(Project $project): Collection
    {
        return $project->loans()->withSum('payments', 'amount')->get()->map(fn ($l) => [
            'loan' => $l->name,
            'principal' => (float) $l->amount,
            'paid' => (float) ($l->payments_sum_amount ?? 0),
            'remaining' => (float) $l->amount - (float) ($l->payments_sum_amount ?? 0),
            'installment' => $l->installment_amount !== null ? (float) $l->installment_amount : null,
            'status' => __('owner.startup.statuses.'.$l->status),
        ]);
    }

    private function loanTotals(Collection $rows): array
    {
        return $this->totalsRow('loans', [
            'principal' => $rows->sum('principal'),
            'paid' => $rows->sum('paid'),
            'remaining' => $rows->sum('remaining'),
        ]);
    }

    /** Full-width totals row: the first column carries the label, missing keys stay blank. */
    private function totalsRow(string $report, array $sums): array
    {
        $columns = self::COLUMNS[$report];
        $row = array_fill_keys($columns, '');
        $row[$columns[0]] = __('owner.startup.total');

        return array_merge($row, $sums);
    }

    private function resolvePartner(Request $r, Project $project): ?Partner
    {
        return $project->partners()->find($r->integer('partner_id')) ?? $project->partners()->first();
    }

    private function statementRows(Project $project, ?Partner $partner): Collection
    {
        $rows = collect();
        if (! $partner) {
            return $rows;
        }
        $partner->contributions->each(fn ($x) => $rows->push([
            'date' => $x->date->format('Y-m-d'),
            'type' => __('owner.startup.contribution'),
            'description' => __('owner.startup.contribution_types.'.$x->type).($x->notes ? ' — '.$x->notes : ''),
            'amount' => (float) $x->amount,
        ]));
        $partner->expenses()->where('payer_type', 'partner')->get()->each(fn ($x) => $rows->push([
            'date' => $x->date->format('Y-m-d'),
            'type' => __('owner.startup.expense'),
            'description' => $x->name,
            'amount' => (float) $x->amount,
        ]));
        $partner->loanPayments()->with('loan')->whereHas('loan', fn ($q) => $q->where('project_id', $project->id)->where('borne_by', 'project'))->get()->each(fn ($x) => $rows->push([
            'date' => $x->date->format('Y-m-d'),
            'type' => __('owner.startup.loan_repayment'),
            'description' => $x->loan->name,
            'amount' => (float) $x->amount,
        ]));

        return $rows->sortBy('date')->values();
    }

    private function pdf(Request $r, Project $p, string $view, array $data)
    {
        $settings = ownerCompanySettings(['qr_code' => app(ReportQrService::class)->dataUri($p->name)]);

        return pdf_report(view('owner.startup.reports.print.'.$view, $data + ['project' => $p, 'settings' => $settings, 'filters' => $r->only(self::FILTER_KEYS)]), [], $view.'-'.$p->id.'.pdf', $r->boolean('download') ? 'attachment' : 'inline');
    }
}
