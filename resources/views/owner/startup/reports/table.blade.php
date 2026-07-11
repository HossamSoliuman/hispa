@extends('owner.layouts.master')

@section('title', $title)

@section('content')
@php
    $moneyColumns = ['amount', 'required', 'paid', 'balance', 'principal', 'remaining', 'installment'];
    $query = array_filter(request()->only([
        'from',
        'to',
        'category_id',
        'partner_id',
        'payment_method',
        'payer_type',
        'is_shared',
        'invoice',
    ]), fn (mixed $value): bool => $value !== null && $value !== '');
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <a href="{{ route('owner.startup.projects.show', $project) }}" class="text-muted small">
            <i class="bi bi-arrow-right"></i> {{ $project->name }}
        </a>
        <h1 class="h3 mt-2 mb-0">{{ $title }}</h1>
    </div>
    <div class="d-flex gap-2">
        <a target="_blank" class="btn btn-outline-theme" href="{{ route('owner.startup.reports.print.'.$report, array_merge(['project' => $project->id], $query)) }}">
            <i class="bi bi-printer"></i> {{ __('owner.startup.print_pdf') }}
        </a>
        <a class="btn btn-success" href="{{ route('owner.startup.reports.excel', array_merge(['project' => $project->id, 'report' => $report], $query)) }}">
            <i class="bi bi-file-earmark-excel"></i> {{ __('owner.startup.export_excel') }}
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

@if($report === 'expenses')
    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small">{{ __('owner.startup.from') }}</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small">{{ __('owner.startup.to') }}</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small">{{ __('owner.startup.category') }}</label>
                    <select name="category_id" class="form-select">
                        <option value="">{{ __('owner.startup.all') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small">{{ __('owner.startup.select_partner') }}</label>
                    <select name="partner_id" class="form-select">
                        <option value="">{{ __('owner.startup.all') }}</option>
                        @foreach($project->partners as $projectPartner)
                            <option value="{{ $projectPartner->id }}" @selected((string) request('partner_id') === (string) $projectPartner->id)>{{ $projectPartner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small">{{ __('owner.startup.payment_method') }}</label>
                    <select name="payment_method" class="form-select">
                        <option value="">{{ __('owner.startup.all') }}</option>
                        @foreach(['cash', 'transfer', 'card'] as $method)
                            <option value="{{ $method }}" @selected(request('payment_method') === $method)>{{ __('owner.startup.methods.'.$method) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small">{{ __('owner.startup.payer') }}</label>
                    <select name="payer_type" class="form-select">
                        <option value="">{{ __('owner.startup.all') }}</option>
                        @foreach(['project', 'partner', 'loan'] as $payerType)
                            <option value="{{ $payerType }}" @selected(request('payer_type') === $payerType)>{{ __('owner.startup.payer_types.'.$payerType) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small">{{ __('owner.startup.expense_type') }}</label>
                    <select name="is_shared" class="form-select">
                        <option value="">{{ __('owner.startup.all') }}</option>
                        <option value="1" @selected(request('is_shared') === '1')>{{ __('owner.startup.shared') }}</option>
                        <option value="0" @selected(request('is_shared') === '0')>{{ __('owner.startup.not_shared') }}</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small">{{ __('owner.startup.attachment') }}</label>
                    <select name="invoice" class="form-select">
                        <option value="">{{ __('owner.startup.all') }}</option>
                        <option value="with" @selected(request('invoice') === 'with')>{{ __('owner.startup.with_invoice') }}</option>
                        <option value="without" @selected(request('invoice') === 'without')>{{ __('owner.startup.without_invoice') }}</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-theme"><i class="bi bi-funnel"></i> {{ __('owner.startup.filter') }}</button>
                    <a href="{{ route('owner.startup.reports.expenses', $project) }}" class="btn btn-outline-secondary">{{ __('owner.startup.clear') }}</a>
                </div>
            </div>
        </div>
    </form>
@elseif($report === 'partner-statement' && $project->partners->isNotEmpty())
    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <select name="partner_id" class="form-select" onchange="this.form.submit()">
                @foreach($project->partners as $projectPartner)
                    <option value="{{ $projectPartner->id }}" @selected(($partner->id ?? null) === $projectPartner->id)>{{ $projectPartner->name }}</option>
                @endforeach
            </select>
        </div>
    </form>
@endif

@if(! empty($statementSummary))
    <div class="row g-3 mb-4">
        @foreach(['required', 'paid', 'balance'] as $metric)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <span class="text-muted small">{{ __('owner.startup.'.$metric) }}</span>
                        <div class="fs-4 fw-semibold mt-1">{{ number_format($statementSummary[$metric], 2) }} <x-riyal-icon size="sm" /></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle text-center mb-0">
            <thead class="table-light">
                <tr>
                    @foreach($columns as $column)
                        <th>{{ __('owner.startup.columns.'.$column) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        @foreach($columns as $column)
                            <td>
                                @if($column === 'invoice' && filter_var($row[$column] ?? null, FILTER_VALIDATE_URL))
                                    <a href="{{ $row[$column] }}" target="_blank" rel="noopener">{{ __('owner.startup.view_attachment') }}</a>
                                @elseif(in_array($column, $moneyColumns, true) && ($row[$column] ?? '') !== '')
                                    {{ number_format((float) $row[$column], 2) }} <x-riyal-icon size="sm" />
                                @else
                                    {{ is_numeric($row[$column] ?? null) ? number_format((float) $row[$column], 2) : ($row[$column] ?? '—') }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ count($columns) }}" class="text-center py-5 text-muted">{{ __('owner.startup.empty') }}</td></tr>
                @endforelse
            </tbody>
            @if(! empty($totals))
                <tfoot class="table-light fw-semibold">
                    <tr>
                        @foreach($columns as $column)
                            <td>
                                @if(in_array($column, $moneyColumns, true) && ($totals[$column] ?? '') !== '')
                                    {{ number_format((float) $totals[$column], 2) }} <x-riyal-icon size="sm" />
                                @else
                                    {{ $totals[$column] ?? '' }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
