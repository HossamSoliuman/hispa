@extends('owner.layouts.master')

@section('title', __('owner.month_summary.title'))

@section('css')
    <style>
        .ms-statement { width: 100%; border-collapse: collapse; margin: 0 0 18px; font-size: 14px; }
        .ms-statement th, .ms-statement td { padding: 9px 16px; }
        .ms-statement thead th { background: #34495e; color: #fff; text-align: start; font-weight: 600; }
        .ms-section td { background: #f1f5f9; color: #1e293b; font-weight: 700; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; }
        .ms-line td { border-bottom: 1px solid #eef2f6; color: #475569; }
        .ms-label { text-align: start; }
        .ms-indent { padding-inline-start: 38px !important; }
        .ms-amount { text-align: end; font-variant-numeric: tabular-nums; white-space: nowrap; }
        .ms-pos { color: #16a34a; }
        .ms-neg { color: #dc2626; }
        .ms-muted { color: #94a3b8; }
        .ms-subtotal td { font-weight: 700; color: #1e293b; border-top: 1px solid #cbd5e1; border-bottom: 2px solid #cbd5e1; background: #fafbfc; }
        .ms-subtotal-light td { background: #fff; border-bottom: 1px solid #e2e8f0; font-weight: 600; }
        .ms-total td { font-weight: 700; font-size: 17px; padding: 13px 16px; }
        .ms-total-profit td { background: #16a34a; color: #fff; }
        .ms-total-loss td { background: #dc2626; color: #fff; }
        .ms-statement-distribution thead th { background: #d97706; }
        .ms-statement-wrap { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

        [data-bs-theme=dark] .ms-statement-wrap { background: var(--bs-secondary-bg); box-shadow: none; }
        [data-bs-theme=dark] .ms-section td { background: rgba(255,255,255,.05); color: var(--bs-emphasis-color); border-color: var(--bs-border-color); }
        [data-bs-theme=dark] .ms-line td { color: var(--bs-body-color); border-color: var(--bs-border-color); }
        [data-bs-theme=dark] .ms-subtotal td { background: rgba(255,255,255,.04); color: var(--bs-emphasis-color); border-color: var(--bs-border-color); }
        [data-bs-theme=dark] .ms-subtotal-light td { background: transparent; border-color: var(--bs-border-color); }
        [data-bs-theme=dark] .ms-muted { color: var(--bs-secondary-color); }
    </style>
@endsection

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ __('owner.month_summary.title') }}</h2>
            <p class="text-muted mb-0">{{ __('owner.month_summary.subtitle') }}</p>
        </div>
        <div class="ms-auto">
            <a href="{{ route('owner.reports.month-summary.print', request()->all()) }}" target="_blank"
                class="btn btn-outline-info btn-border-radius">
                <i class="fa fa-print me-2"></i>{{ __('owner.month_summary.print') }}
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('owner.reports.month-summary') }}">
                <div class="row align-items-end gy-2">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('owner.month_summary.from_date') }}</label>
                        <input type="date" name="from" class="form-control" value="{{ $from }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('owner.month_summary.to_date') }}</label>
                        <input type="date" name="to" class="form-control" value="{{ $to }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">{{ __('owner.month_summary.boat') }}</label>
                        <select name="boat_id" class="form-select">
                            <option value="">{{ __('owner.month_summary.all_boats') }}</option>
                            @foreach ($boats as $boat)
                                <option value="{{ $boat->id }}" {{ $boatId == $boat->id ? 'selected' : '' }}>
                                    {{ $boat->name ?? $boat->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa fa-search me-2"></i>{{ __('owner.month_summary.update') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="ms-statement-wrap p-3 p-md-4">
        @include('owner.report._month_summary_statement', ['f' => $f, 'expenses' => $expenses, 'from' => $from, 'to' => $to])
        <small class="text-muted d-block mt-2 px-1">{{ __('owner.profit_loss.formula_note') }}</small>
    </div>
@endsection
