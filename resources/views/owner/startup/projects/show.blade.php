@extends('owner.layouts.master')

@section('title', $project->name)

@section('content')
@php
    $activeTab = old('tab', request('tab', 'partners'));
    $activeTab = in_array($activeTab, ['partners', 'expenses', 'contributions', 'loans', 'reports'], true) ? $activeTab : 'partners';
    $partnerForm = $editingPartner ?? new \App\Models\Startup\Partner();
    $expenseForm = $editingExpense ?? new \App\Models\Startup\Expense();
    $contributionForm = $editingContribution ?? new \App\Models\Startup\Contribution();
    $loanForm = $editingLoan ?? new \App\Models\Startup\Loan();
    $summaryCards = [
        ['total_cost', 'bi-receipt', true],
        ['project_expenses', 'bi-wallet2', true],
        ['contributions', 'bi-piggy-bank', true],
        ['loans_total', 'bi-bank', true],
        ['loans_paid', 'bi-check2-circle', true],
        ['loans_remaining', 'bi-hourglass-split', true],
        ['partners_count', 'bi-person-badge', false],
        ['status', 'bi-activity', false],
    ];
@endphp

<div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('owner.startup.projects.index') }}" class="text-muted small">
            <i class="bi bi-arrow-right"></i> {{ __('owner.startup.menu.projects') }}
        </a>
        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
            <h1 class="h3 mb-0">{{ $project->name }}</h1>
            <span class="badge bg-secondary">{{ __('owner.startup.statuses.'.$project->status) }}</span>
        </div>
    </div>
    <div class="d-flex gap-2 align-items-start">
        @if($allocationComplete)
            <a target="_blank" class="btn btn-outline-theme" href="{{ route('owner.startup.reports.print.summary', $project) }}">
                <i class="bi bi-printer"></i> {{ __('owner.startup.summary') }}
            </a>
            <a class="btn btn-success" href="{{ route('owner.startup.reports.excel', ['project' => $project, 'report' => 'summary']) }}">
                <i class="bi bi-file-earmark-excel"></i> {{ __('owner.startup.export_excel') }}
            </a>
        @endif
        <a class="btn btn-outline-secondary" href="{{ route('owner.startup.projects.edit', $project) }}">
            <i class="bi bi-pencil"></i> {{ __('owner.startup.edit') }}
        </a>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex align-items-center gap-2">
        <i class="bi bi-briefcase text-theme"></i>
        <h2 class="h6 mb-0">{{ __('owner.startup.project_information') }}</h2>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-muted small">{{ __('owner.startup.type') }}</div>
                <div class="fw-semibold mt-1">{{ __('owner.startup.types.'.$project->type) }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">{{ __('owner.startup.start_date') }}</div>
                <div class="fw-semibold mt-1">{{ $project->start_date->format('Y-m-d') }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">{{ __('owner.startup.status') }}</div>
                <div class="fw-semibold mt-1">{{ __('owner.startup.statuses.'.$project->status) }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">{{ __('owner.startup.ownership_total') }}</div>
                <div class="fw-semibold mt-1">{{ $allocationPercentage }}%</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">{{ __('owner.startup.description') }}</div>
                <div class="mt-1">{!! nl2br(e($project->description ?: '—')) !!}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">{{ __('owner.startup.notes') }}</div>
                <div class="mt-1">{!! nl2br(e($project->notes ?: '—')) !!}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach($summaryCards as [$key, $icon, $isMoney])
        <div class="col-xl-3 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <i class="bi {{ $icon }} text-theme fs-4"></i>
                    <div class="text-muted small mt-2">{{ __('owner.startup.'.$key) }}</div>
                    <strong class="fs-5">
                        @if($key === 'status')
                            {{ __('owner.startup.statuses.'.$summary[$key]) }}
                        @elseif($isMoney)
                            {{ number_format($summary[$key], 2) }} <x-riyal-icon size="sm" />
                        @else
                            {{ $summary[$key] }}
                        @endif
                    </strong>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($allocationComplete)
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="bi bi-shield-check fs-5"></i>
        <span>{{ __('owner.startup.allocation_complete') }}</span>
    </div>
@else
    <div class="alert alert-warning d-flex align-items-start gap-2">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>
            <strong class="d-block">{{ __('owner.startup.allocation_draft') }}</strong>
            {{ __('owner.startup.shares_warning', ['total' => $allocationPercentage]) }}
        </div>
    </div>
@endif

<ul class="nav nav-tabs" role="tablist">
    @foreach(['partners', 'expenses', 'contributions', 'loans', 'reports'] as $tab)
        <li class="nav-item">
            <button class="nav-link @if($activeTab === $tab) active @endif" data-bs-toggle="tab" data-bs-target="#{{ $tab }}" data-startup-tab="{{ $tab }}" type="button">
                {{ __('owner.startup.'.($tab === 'reports' ? 'reports_label' : $tab)) }}
            </button>
        </li>
    @endforeach
</ul>

<div class="tab-content card border-top-0 shadow-sm">
    <div id="partners" class="tab-pane fade @if($activeTab === 'partners') show active @endif p-3 p-lg-4">
        <form method="POST" action="{{ $editingPartner ? route('owner.startup.partners.update', $editingPartner) : route('owner.startup.projects.partners.store', $project) }}" class="card border bg-light-subtle mb-4">
            @csrf
            @if($editingPartner) @method('PUT') @endif
            <input type="hidden" name="tab" value="partners">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 mb-0">{{ $editingPartner ? __('owner.startup.edit_partner') : __('owner.startup.add_partner') }}</h3>
                    @if($editingPartner)<a href="{{ route('owner.startup.projects.show', ['project' => $project, 'tab' => 'partners']) }}" class="small">{{ __('owner.startup.cancel') }}</a>@endif
                </div>
                <div class="row g-3">
                    <div class="col-lg-4"><label class="form-label">{{ __('owner.startup.partner_name') }}</label><input name="name" required class="form-control" value="{{ old('name', $partnerForm->name) }}"></div>
                    <div class="col-lg-2"><label class="form-label">{{ __('owner.startup.share_percent') }}</label><input name="share_percent" type="number" min="0.01" max="100" step="0.01" required class="form-control" value="{{ old('share_percent', $partnerForm->share_percent) }}"></div>
                    <div class="col-lg-3"><label class="form-label">{{ __('owner.startup.partner_type') }}</label><select name="partner_type" class="form-select">@foreach(['owner', 'investor', 'manager'] as $value)<option value="{{ $value }}" @selected(old('partner_type', $partnerForm->partner_type ?? 'owner') === $value)>{{ __('owner.startup.partner_types.'.$value) }}</option>@endforeach</select></div>
                    <div class="col-lg-3"><label class="form-label">{{ __('owner.startup.phone') }}</label><input name="phone" class="form-control" value="{{ old('phone', $partnerForm->phone) }}"></div>
                    <div class="col-lg-9"><label class="form-label">{{ __('owner.startup.notes') }}</label><textarea name="notes" rows="2" class="form-control">{{ old('notes', $partnerForm->notes) }}</textarea></div>
                    <div class="col-lg-3 d-flex align-items-end"><div class="form-check mb-2"><input type="hidden" name="has_salary" value="0"><input class="form-check-input" type="checkbox" name="has_salary" value="1" id="has-salary" @checked(old('has_salary', $partnerForm->has_salary))><label class="form-check-label" for="has-salary">{{ __('owner.startup.has_salary') }}</label></div></div>
                </div>
            </div>
            <div class="card-footer bg-transparent text-end"><button class="btn btn-theme"><i class="bi bi-check2"></i> {{ __('owner.startup.save') }}</button></div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>{{ __('owner.startup.partner_name') }}</th><th>{{ __('owner.startup.partner_type') }}</th><th>{{ __('owner.startup.phone') }}</th><th>{{ __('owner.startup.share_percent') }}</th><th>{{ __('owner.startup.has_salary') }}</th><th>{{ __('owner.startup.required') }}</th><th>{{ __('owner.startup.paid') }}</th><th>{{ __('owner.startup.balance') }}</th><th>{{ __('owner.startup.notes') }}</th><th></th></tr></thead>
                <tbody>
                    @forelse($project->partners as $partner)
                        <tr>
                            <td class="fw-semibold">{{ $partner->name }}</td>
                            <td>{{ __('owner.startup.partner_types.'.$partner->partner_type) }}</td>
                            <td>{{ $partner->phone ?: '—' }}</td>
                            <td>{{ number_format((float) $partner->share_percent, 2) }}%</td>
                            <td>{{ $partner->has_salary ? __('owner.startup.yes') : __('owner.startup.no') }}</td>
                            @foreach(['required', 'paid', 'balance'] as $metric)
                                <td class="@if($metric === 'balance') {{ $partnerAccounting[$partner->id][$metric] >= 0 ? 'text-success' : 'text-danger' }} @endif">{{ number_format($partnerAccounting[$partner->id][$metric], 2) }}</td>
                            @endforeach
                            <td>{{ $partner->notes ?: '—' }}</td>
                            <td><div class="d-flex gap-1"><a href="{{ route('owner.startup.partners.edit', $partner) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a><form method="POST" action="{{ route('owner.startup.partners.destroy', $partner) }}" onsubmit="return confirm(@js(__('owner.startup.confirm_delete')))">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">{{ __('owner.startup.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="expenses" class="tab-pane fade @if($activeTab === 'expenses') show active @endif p-3 p-lg-4">
        <form method="POST" enctype="multipart/form-data" action="{{ $editingExpense ? route('owner.startup.expenses.update', $editingExpense) : route('owner.startup.projects.expenses.store', $project) }}" class="card border bg-light-subtle mb-4">
            @csrf
            @if($editingExpense) @method('PUT') @endif
            <input type="hidden" name="tab" value="expenses">
            <fieldset @disabled(! $allocationComplete)>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3"><h3 class="h6 mb-0">{{ $editingExpense ? __('owner.startup.edit_expense') : __('owner.startup.add_expense') }}</h3>@if($editingExpense)<a href="{{ route('owner.startup.projects.show', ['project' => $project, 'tab' => 'expenses']) }}" class="small">{{ __('owner.startup.cancel') }}</a>@endif</div>
                    <div class="row g-3">
                        <div class="col-lg-2"><label class="form-label">{{ __('owner.startup.date') }}</label><input type="date" name="date" required class="form-control" value="{{ old('date', $expenseForm->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"></div>
                        <div class="col-lg-4"><label class="form-label">{{ __('owner.startup.expense_name') }}</label><input name="name" required class="form-control" value="{{ old('name', $expenseForm->name) }}"></div>
                        <div class="col-lg-2"><label class="form-label">{{ __('owner.startup.amount') }}</label><input type="number" min="0.01" step="0.01" name="amount" required class="form-control" value="{{ old('amount', $expenseForm->amount) }}"></div>
                        <div class="col-lg-4"><label class="form-label">{{ __('owner.startup.category') }}</label><select name="category_id" class="form-select">@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', $expenseForm->category_id) === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></div>
                        <div class="col-lg-4"><label class="form-label">{{ __('owner.startup.description') }}</label><textarea name="description" rows="2" class="form-control">{{ old('description', $expenseForm->description) }}</textarea></div>
                        <div class="col-lg-2"><label class="form-label">{{ __('owner.startup.payer') }}</label><select name="payer_type" class="form-select startup-payer">@foreach(['project', 'partner', 'loan'] as $value)<option value="{{ $value }}" @selected(old('payer_type', $expenseForm->payer_type ?? 'project') === $value)>{{ __('owner.startup.payer_types.'.$value) }}</option>@endforeach</select></div>
                        <div class="col-lg-3 d-none" data-payer-field="partner"><label class="form-label">{{ __('owner.startup.select_partner') }}</label><select name="partner_id" class="form-select" disabled><option value="">{{ __('owner.startup.select_partner') }}</option>@foreach($project->partners as $partner)<option value="{{ $partner->id }}" @selected((string) old('partner_id', $expenseForm->partner_id) === (string) $partner->id)>{{ $partner->name }}</option>@endforeach</select></div>
                        <div class="col-lg-3 d-none" data-payer-field="loan"><label class="form-label">{{ __('owner.startup.select_loan') }}</label><select name="loan_id" class="form-select" disabled><option value="">{{ __('owner.startup.select_loan') }}</option>@foreach($project->loans as $loan)<option value="{{ $loan->id }}" @selected((string) old('loan_id', $expenseForm->loan_id) === (string) $loan->id)>{{ $loan->name }}</option>@endforeach</select></div>
                        <div class="col-lg-3"><label class="form-label">{{ __('owner.startup.payment_method') }}</label><select name="payment_method" class="form-select">@foreach(['cash', 'transfer', 'card'] as $value)<option value="{{ $value }}" @selected(old('payment_method', $expenseForm->payment_method ?? 'cash') === $value)>{{ __('owner.startup.methods.'.$value) }}</option>@endforeach</select></div>
                        <div class="col-lg-3"><label class="form-label">{{ __('owner.startup.attachment') }}</label><input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="form-control"></div>
                        <div class="col-lg-6"><label class="form-label">{{ __('owner.startup.notes') }}</label><textarea name="notes" rows="2" class="form-control">{{ old('notes', $expenseForm->notes) }}</textarea></div>
                        <div class="col-lg-3 d-flex align-items-end"><div class="form-check mb-2"><input type="hidden" name="is_shared" value="0"><input class="form-check-input" type="checkbox" name="is_shared" value="1" id="is-shared" @checked(old('is_shared', $expenseForm->exists ? $expenseForm->is_shared : true))><label class="form-check-label" for="is-shared">{{ __('owner.startup.is_shared') }}</label></div></div>
                        @if($editingExpense && $editingExpense->attachment)<div class="col-lg-3 d-flex align-items-end"><div class="form-check mb-2"><input type="hidden" name="remove_attachment" value="0"><input class="form-check-input" type="checkbox" name="remove_attachment" value="1" id="remove-attachment"><label class="form-check-label" for="remove-attachment">{{ __('owner.startup.remove_attachment') }}</label></div></div>@endif
                    </div>
                </div>
                <div class="card-footer bg-transparent text-end"><button class="btn btn-theme"><i class="bi bi-check2"></i> {{ __('owner.startup.save') }}</button></div>
            </fieldset>
        </form>

        <form method="GET" class="row g-2 align-items-end mb-3">
            <input type="hidden" name="tab" value="expenses">
            <div class="col-md-3"><label class="form-label small">{{ __('owner.startup.category') }}</label><select name="exp_category" class="form-select form-select-sm"><option value="">{{ __('owner.startup.all') }}</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) request('exp_category') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label small">{{ __('owner.startup.payer') }}</label><select name="exp_payer" class="form-select form-select-sm"><option value="">{{ __('owner.startup.all') }}</option>@foreach(['project', 'partner', 'loan'] as $value)<option value="{{ $value }}" @selected(request('exp_payer') === $value)>{{ __('owner.startup.payer_types.'.$value) }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label small">{{ __('owner.startup.from') }}</label><input type="date" name="exp_from" value="{{ request('exp_from') }}" class="form-control form-control-sm"></div>
            <div class="col-md-2"><label class="form-label small">{{ __('owner.startup.to') }}</label><input type="date" name="exp_to" value="{{ request('exp_to') }}" class="form-control form-control-sm"></div>
            <div class="col-auto d-flex gap-1"><button class="btn btn-sm btn-outline-theme">{{ __('owner.startup.filter') }}</button><a href="{{ route('owner.startup.projects.show', ['project' => $project, 'tab' => 'expenses']) }}" class="btn btn-sm btn-outline-secondary">{{ __('owner.startup.clear') }}</a></div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>{{ __('owner.startup.date') }}</th><th>{{ __('owner.startup.category') }}</th><th>{{ __('owner.startup.expense_name') }}</th><th>{{ __('owner.startup.description') }}</th><th>{{ __('owner.startup.amount') }}</th><th>{{ __('owner.startup.payer') }}</th><th>{{ __('owner.startup.payment_method') }}</th><th>{{ __('owner.startup.expense_type') }}</th><th>{{ __('owner.startup.attachment') }}</th><th>{{ __('owner.startup.notes') }}</th><th></th></tr></thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->date->format('Y-m-d') }}</td><td>{{ $expense->category->name }}</td><td class="fw-semibold">{{ $expense->name }}</td><td>{{ $expense->description ?: '—' }}</td><td>{{ number_format((float) $expense->amount, 2) }}</td>
                            <td>{{ $expense->payer_type === 'partner' ? ($expense->partner?->name ?? '—') : ($expense->payer_type === 'loan' ? ($expense->loan?->name ?? '—') : __('owner.startup.payer_types.project')) }}</td>
                            <td>{{ __('owner.startup.methods.'.$expense->payment_method) }}</td><td><span class="badge {{ $expense->is_shared ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $expense->is_shared ? __('owner.startup.shared') : __('owner.startup.not_shared') }}</span></td>
                            <td>@if($expense->attachment)<a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($expense->attachment) }}" target="_blank" rel="noopener">{{ __('owner.startup.view_attachment') }}</a>@else — @endif</td><td>{{ $expense->notes ?: '—' }}</td>
                            <td><div class="d-flex gap-1"><a href="{{ route('owner.startup.expenses.edit', $expense) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a><form method="POST" action="{{ route('owner.startup.expenses.destroy', $expense) }}" onsubmit="return confirm(@js(__('owner.startup.confirm_delete')))">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-center text-muted py-4">{{ __('owner.startup.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="contributions" class="tab-pane fade @if($activeTab === 'contributions') show active @endif p-3 p-lg-4">
        <form method="POST" action="{{ $editingContribution ? route('owner.startup.contributions.update', $editingContribution) : route('owner.startup.projects.contributions.store', $project) }}" class="card border bg-light-subtle mb-4">
            @csrf @if($editingContribution) @method('PUT') @endif
            <input type="hidden" name="tab" value="contributions">
            <fieldset @disabled(! $allocationComplete)>
                <div class="card-body"><div class="d-flex justify-content-between mb-3"><h3 class="h6 mb-0">{{ $editingContribution ? __('owner.startup.edit_contribution') : __('owner.startup.add_contribution') }}</h3>@if($editingContribution)<a href="{{ route('owner.startup.projects.show', ['project' => $project, 'tab' => 'contributions']) }}" class="small">{{ __('owner.startup.cancel') }}</a>@endif</div><div class="row g-3">
                    <div class="col-lg-2"><label class="form-label">{{ __('owner.startup.date') }}</label><input type="date" name="date" value="{{ old('date', $contributionForm->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-control" required></div>
                    <div class="col-lg-3"><label class="form-label">{{ __('owner.startup.select_partner') }}</label><select name="partner_id" class="form-select">@foreach($project->partners as $partner)<option value="{{ $partner->id }}" @selected((string) old('partner_id', $contributionForm->partner_id) === (string) $partner->id)>{{ $partner->name }}</option>@endforeach</select></div>
                    <div class="col-lg-2"><label class="form-label">{{ __('owner.startup.amount') }}</label><input name="amount" type="number" min="0.01" step="0.01" class="form-control" value="{{ old('amount', $contributionForm->amount) }}" required></div>
                    <div class="col-lg-2"><label class="form-label">{{ __('owner.startup.payment_method') }}</label><select name="payment_method" class="form-select">@foreach(['cash', 'transfer', 'card'] as $value)<option value="{{ $value }}" @selected(old('payment_method', $contributionForm->payment_method ?? 'cash') === $value)>{{ __('owner.startup.methods.'.$value) }}</option>@endforeach</select></div>
                    <div class="col-lg-3"><label class="form-label">{{ __('owner.startup.contribution_type') }}</label><select name="type" class="form-select">@foreach(['capital', 'reimbursement', 'extra'] as $value)<option value="{{ $value }}" @selected(old('type', $contributionForm->type ?? 'capital') === $value)>{{ __('owner.startup.contribution_types.'.$value) }}</option>@endforeach</select></div>
                    <div class="col-12"><label class="form-label">{{ __('owner.startup.notes') }}</label><textarea name="notes" rows="2" class="form-control">{{ old('notes', $contributionForm->notes) }}</textarea></div>
                </div></div><div class="card-footer bg-transparent text-end"><button class="btn btn-theme"><i class="bi bi-check2"></i> {{ __('owner.startup.save') }}</button></div>
            </fieldset>
        </form>
        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>{{ __('owner.startup.date') }}</th><th>{{ __('owner.startup.partner_name') }}</th><th>{{ __('owner.startup.amount') }}</th><th>{{ __('owner.startup.payment_method') }}</th><th>{{ __('owner.startup.contribution_type') }}</th><th>{{ __('owner.startup.notes') }}</th><th></th></tr></thead><tbody>@forelse($project->contributions as $contribution)<tr><td>{{ $contribution->date->format('Y-m-d') }}</td><td>{{ $contribution->partner->name }}</td><td>{{ number_format((float) $contribution->amount, 2) }}</td><td>{{ __('owner.startup.methods.'.$contribution->payment_method) }}</td><td>{{ __('owner.startup.contribution_types.'.$contribution->type) }}</td><td>{{ $contribution->notes ?: '—' }}</td><td><div class="d-flex gap-1"><a href="{{ route('owner.startup.contributions.edit', $contribution) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a><form method="POST" action="{{ route('owner.startup.contributions.destroy', $contribution) }}" onsubmit="return confirm(@js(__('owner.startup.confirm_delete')))">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></div></td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-4">{{ __('owner.startup.empty') }}</td></tr>@endforelse</tbody></table></div>
    </div>

    <div id="loans" class="tab-pane fade @if($activeTab === 'loans') show active @endif p-3 p-lg-4">
        <form method="POST" action="{{ $editingLoan ? route('owner.startup.loans.update', $editingLoan) : route('owner.startup.projects.loans.store', $project) }}" class="card border bg-light-subtle mb-4">
            @csrf @if($editingLoan) @method('PUT') @endif
            <input type="hidden" name="tab" value="loans">
            <fieldset @disabled(! $allocationComplete)>
                <div class="card-body"><div class="d-flex justify-content-between mb-3"><h3 class="h6 mb-0">{{ $editingLoan ? __('owner.startup.edit_loan') : __('owner.startup.add_loan') }}</h3>@if($editingLoan)<a href="{{ route('owner.startup.projects.show', ['project' => $project, 'tab' => 'loans']) }}" class="small">{{ __('owner.startup.cancel') }}</a>@endif</div><div class="row g-3">
                    <div class="col-lg-3"><label class="form-label">{{ __('owner.startup.loan_name') }}</label><input name="name" required class="form-control" value="{{ old('name', $loanForm->name) }}"></div><div class="col-lg-3"><label class="form-label">{{ __('owner.startup.lender') }}</label><input name="lender" required class="form-control" value="{{ old('lender', $loanForm->lender) }}"></div><div class="col-lg-2"><label class="form-label">{{ __('owner.startup.amount') }}</label><input name="amount" type="number" min="0.01" step="0.01" class="form-control" value="{{ old('amount', $loanForm->amount) }}" required></div><div class="col-lg-2"><label class="form-label">{{ __('owner.startup.date') }}</label><input name="date" type="date" value="{{ old('date', $loanForm->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-control" required></div>
                    <div class="col-lg-2"><label class="form-label">{{ __('owner.startup.installments_count') }}</label><input name="installments_count" type="number" min="1" class="form-control" value="{{ old('installments_count', $loanForm->installments_count) }}"></div><div class="col-lg-2"><label class="form-label">{{ __('owner.startup.installment_amount') }}</label><input name="installment_amount" type="number" min="0.01" step="0.01" class="form-control" value="{{ old('installment_amount', $loanForm->installment_amount) }}"></div><div class="col-lg-2"><label class="form-label">{{ __('owner.startup.first_installment_date') }}</label><input name="first_installment_date" type="date" class="form-control" value="{{ old('first_installment_date', $loanForm->first_installment_date?->format('Y-m-d')) }}"></div>
                    <div class="col-lg-2"><label class="form-label">{{ __('owner.startup.borne_by') }}</label><select name="borne_by" class="form-select startup-borne-by">@foreach(['project', 'partner'] as $value)<option value="{{ $value }}" @selected(old('borne_by', $loanForm->borne_by ?? 'project') === $value)>{{ __('owner.startup.borne_types.'.$value) }}</option>@endforeach</select></div><div class="col-lg-3 d-none" data-borne-partner><label class="form-label">{{ __('owner.startup.select_partner') }}</label><select name="partner_id" class="form-select" disabled><option value="">{{ __('owner.startup.select_partner') }}</option>@foreach($project->partners as $partner)<option value="{{ $partner->id }}" @selected((string) old('partner_id', $loanForm->partner_id) === (string) $partner->id)>{{ $partner->name }}</option>@endforeach</select></div><div class="col-lg-2"><label class="form-label">{{ __('owner.startup.status') }}</label><select name="status" class="form-select">@foreach(['active', 'defaulted'] as $value)<option value="{{ $value }}" @selected(old('status', $loanForm->status === 'defaulted' ? 'defaulted' : 'active') === $value)>{{ __('owner.startup.statuses.'.$value) }}</option>@endforeach</select></div><div class="col-lg"><label class="form-label">{{ __('owner.startup.notes') }}</label><textarea name="notes" rows="1" class="form-control">{{ old('notes', $loanForm->notes) }}</textarea></div>
                </div></div><div class="card-footer bg-transparent text-end"><button class="btn btn-theme"><i class="bi bi-check2"></i> {{ __('owner.startup.save') }}</button></div>
            </fieldset>
        </form>

        <div class="d-grid gap-3">
            @forelse($project->loans as $loan)
                <div class="border rounded p-3">
                    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3"><div><strong class="fs-5">{{ $loan->name }}</strong><div class="text-muted">{{ $loan->lender }}</div></div><div class="text-end"><div class="fs-5 fw-semibold">{{ number_format((float) $loan->amount, 2) }} <x-riyal-icon size="sm" /></div><span class="badge bg-secondary">{{ __('owner.startup.statuses.'.$loan->status) }}</span></div></div>
                    <div class="row g-3 small mb-3"><div class="col-md-2"><span class="text-muted d-block">{{ __('owner.startup.date') }}</span>{{ $loan->date->format('Y-m-d') }}</div><div class="col-md-2"><span class="text-muted d-block">{{ __('owner.startup.installments_count') }}</span>{{ $loan->installments_count ?: '—' }}</div><div class="col-md-2"><span class="text-muted d-block">{{ __('owner.startup.installment_amount') }}</span>{{ $loan->installment_amount ? number_format((float) $loan->installment_amount, 2) : '—' }}</div><div class="col-md-2"><span class="text-muted d-block">{{ __('owner.startup.first_installment_date') }}</span>{{ $loan->first_installment_date?->format('Y-m-d') ?: '—' }}</div><div class="col-md-2"><span class="text-muted d-block">{{ __('owner.startup.borne_by') }}</span>{{ $loan->borne_by === 'partner' ? ($loan->partner?->name ?? '—') : __('owner.startup.borne_types.project') }}</div><div class="col-md-2"><span class="text-muted d-block">{{ __('owner.startup.notes') }}</span>{{ $loan->notes ?: '—' }}</div></div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 border-top border-bottom py-2 mb-2"><div class="small"><span class="text-muted">{{ __('owner.startup.paid') }}:</span> {{ number_format((float) $loan->payments->sum('amount'), 2) }} · <span class="text-muted">{{ __('owner.startup.loans_remaining') }}:</span> {{ number_format(max(0, (float) $loan->amount - (float) $loan->payments->sum('amount')), 2) }}</div><div class="d-flex gap-1"><a href="{{ route('owner.startup.loans.edit', $loan) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a><form method="POST" action="{{ route('owner.startup.loans.destroy', $loan) }}" onsubmit="return confirm(@js(__('owner.startup.confirm_delete')))">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></div></div>
                    @if($loan->payments->isNotEmpty())<div class="table-responsive"><table class="table table-sm mb-3"><thead><tr><th>{{ __('owner.startup.date') }}</th><th>{{ __('owner.startup.amount') }}</th><th>{{ __('owner.startup.payer') }}</th><th>{{ __('owner.startup.payment_method') }}</th><th>{{ __('owner.startup.notes') }}</th><th></th></tr></thead><tbody>@foreach($loan->payments->sortBy('date') as $payment)<tr><td>{{ $payment->date->format('Y-m-d') }}</td><td>{{ number_format((float) $payment->amount, 2) }}</td><td>{{ $payment->payer_type === 'partner' ? ($payment->partner?->name ?? '—') : __('owner.startup.payer_types.project') }}</td><td>{{ __('owner.startup.methods.'.$payment->payment_method) }}</td><td>{{ $payment->notes ?: '—' }}</td><td><form method="POST" action="{{ route('owner.startup.loans.payments.destroy', $payment) }}" onsubmit="return confirm(@js(__('owner.startup.confirm_delete')))">@csrf @method('DELETE')<button class="btn btn-sm p-0 border-0 bg-transparent text-danger"><i class="bi bi-trash"></i></button></form></td></tr>@endforeach</tbody></table></div>@endif
                    <form method="POST" action="{{ route('owner.startup.loans.payments.store', $loan) }}" class="row g-2 align-items-end startup-payment-form">@csrf<input type="hidden" name="tab" value="loans"><div class="col-md-2"><label class="form-label small">{{ __('owner.startup.date') }}</label><input name="date" type="date" value="{{ now()->format('Y-m-d') }}" class="form-control form-control-sm" required></div><div class="col-md-2"><label class="form-label small">{{ __('owner.startup.amount') }}</label><input name="amount" type="number" min="0.01" max="{{ max(0, (float) $loan->amount - (float) $loan->payments->sum('amount')) }}" step="0.01" class="form-control form-control-sm" required></div><div class="col-md-2"><label class="form-label small">{{ __('owner.startup.payer') }}</label><select name="payer_type" class="form-select form-select-sm startup-payer"><option value="project">{{ __('owner.startup.payer_types.project') }}</option><option value="partner">{{ __('owner.startup.payer_types.partner') }}</option></select></div><div class="col-md-2 d-none" data-payer-field="partner"><label class="form-label small">{{ __('owner.startup.select_partner') }}</label><select name="partner_id" class="form-select form-select-sm" disabled>@foreach($project->partners as $partner)<option value="{{ $partner->id }}">{{ $partner->name }}</option>@endforeach</select></div><div class="col-md-2"><label class="form-label small">{{ __('owner.startup.payment_method') }}</label><select name="payment_method" class="form-select form-select-sm">@foreach(['cash', 'transfer', 'card'] as $value)<option value="{{ $value }}">{{ __('owner.startup.methods.'.$value) }}</option>@endforeach</select></div><div class="col-md"><label class="form-label small">{{ __('owner.startup.notes') }}</label><input name="notes" class="form-control form-control-sm"></div><div class="col-auto"><button class="btn btn-sm btn-outline-theme" @disabled(! $allocationComplete)><i class="bi bi-plus"></i> {{ __('owner.startup.payment') }}</button></div></form>
                </div>
            @empty
                <div class="text-center text-muted py-4">{{ __('owner.startup.empty') }}</div>
            @endforelse
        </div>
    </div>

    <div id="reports" class="tab-pane fade @if($activeTab === 'reports') show active @endif p-4">
        @if(! $allocationComplete)<div class="alert alert-warning">{{ __('owner.startup.validation.shares_incomplete') }}</div>@endif
        <div class="row g-3">@foreach(['expenses', 'partners', 'loans', 'partner-statement'] as $report)<div class="col-md-3"><a class="card card-body text-decoration-none h-100 @if(! $allocationComplete) opacity-50 pe-none @endif" href="{{ route('owner.startup.reports.'.$report, $project) }}"><i class="bi bi-file-earmark-bar-graph fs-3 text-theme"></i><strong class="mt-2">{{ __('owner.startup.reports.'.($report === 'partner-statement' ? 'statement' : $report)) }}</strong></a></div>@endforeach</div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.querySelectorAll('[data-startup-tab]').forEach(function (button) {
        button.addEventListener('shown.bs.tab', function () {
            var url = new URL(window.location.href);
            url.searchParams.set('tab', button.dataset.startupTab);
            ['edit_partner', 'edit_expense', 'edit_contribution', 'edit_loan'].forEach(function (parameter) {
                url.searchParams.delete(parameter);
            });
            window.history.replaceState({}, '', url);
        });
    });

    document.querySelectorAll('.startup-payer').forEach(function (select) {
        var form = select.closest('form');
        var toggle = function () {
            ['partner', 'loan'].forEach(function (type) {
                var wrapper = form.querySelector('[data-payer-field="' + type + '"]');
                if (!wrapper) {
                    return;
                }
                var active = select.value === type;
                wrapper.classList.toggle('d-none', !active);
                var field = wrapper.querySelector('select');
                field.disabled = !active;
                field.required = active;
            });
        };
        select.addEventListener('change', toggle);
        toggle();
    });

    document.querySelectorAll('.startup-borne-by').forEach(function (select) {
        var form = select.closest('form');
        var wrapper = form.querySelector('[data-borne-partner]');
        var toggle = function () {
            var active = select.value === 'partner';
            wrapper.classList.toggle('d-none', !active);
            var field = wrapper.querySelector('select');
            field.disabled = !active;
            field.required = active;
        };
        select.addEventListener('change', toggle);
        toggle();
    });
</script>
@endsection
