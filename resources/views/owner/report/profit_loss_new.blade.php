@extends('owner.layouts.master')

@section('title', __('owner.profit_loss.title'))

@section('css')
    <style>
        .currency-symbol {
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .currency-symbol svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card .label {
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .stat-card.revenue .value {
            color: #16a34a;
        }

        .stat-card.expense .value {
            color: #dc2626;
        }

        .stat-card.payroll .value {
            color: #d97706;
        }

        .stat-card.profit .value {
            color: #16a34a;
        }

        .stat-card.loss .value {
            color: #dc2626;
        }

        .filter-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        [data-bs-theme=dark] .stat-card,
        [data-bs-theme=dark] .filter-card { background: var(--bs-secondary-bg); box-shadow: none; }
        [data-bs-theme=dark] .stat-card .label { color: var(--bs-secondary-color); }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            display: block;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
        }

        .col-fifth {
            flex: 0 0 auto;
            width: 20%;
            padding-right: calc(var(--bs-gutter-x, 1.5rem) * .5);
            padding-left: calc(var(--bs-gutter-x, 1.5rem) * .5);
        }

        .stat-card .value {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: nowrap;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .section-subtitle {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }

        [data-bs-theme=dark] .section-subtitle {
            color: var(--bs-secondary-color);
            border-color: var(--bs-border-color);
        }

        @media (max-width: 992px) {
            .col-fifth { width: 33.333%; }
        }
        @media (max-width: 576px) {
            .col-fifth { width: 100%; }
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #printable-area,
            #printable-area * {
                visibility: visible;
            }

            #printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }

            .stat-card {
                page-break-inside: avoid;
            }
        }
    </style>
@endsection

@section('content')
    <div class="d-flex align-items-center mb-3 no-print">
        <div>
            <h2 class="mb-2">{{ __('owner.profit_loss.title') }}</h2>
        </div>
        <div class="ms-auto">
            <a href="{{ route('owner.profit.loss.print', request()->all()) }}" target="_blank" class="btn btn-outline-info btn-border-radius">
                <i class="fa fa-print me-2"></i>{{ __('owner.profit_loss.print') }}
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header">
            <h5 class="card-title">{{ __('owner.expenses.filters.title') }}</h5>
        </div>
        <div class="card-body">
            {{-- <div class="filter-card no-print"> --}}
            <form method="GET" action="{{ route('owner.profit.loss') }}">
                <div class="row align-items-end gy-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('owner.profit_loss.from_date') }}</label>
                            <input type="date" name="from" class="form-control" value="{{ $from }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('owner.profit_loss.to_date') }}</label>
                            <input type="date" name="to" class="form-control" value="{{ $to }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('owner.profit_loss.boat') }}</label>
                            <select name="boat_id" class="form-select">
                                <option value="">{{ __('owner.profit_loss.all_boats') }}</option>
                                @foreach ($boats as $boat)
                                    <option value="{{ $boat->id }}" {{ $boatId == $boat->id ? 'selected' : '' }}>
                                        {{ $boat->name ?? $boat->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa fa-search me-2"></i>{{ __('owner.profit_loss.update') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <hr>
        {{-- Summary Cards --}}
        <div id="printable-area" class="p-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 p-2 pb-3">
                            <h3>{{ __('owner.profit_loss.profit_loss_title') }}</h3>
                        </div>

                        {{-- Row 1: Revenues & Profit --}}
                        <div class="col-12 px-3 pb-1">
                            <div class="section-subtitle">{{ __('owner.profit_loss.total_sales') }} / {{ __('owner.profit_loss.net_profit') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card revenue">
                                <div class="label">{{ __('owner.profit_loss.total_sales') }}</div>
                                <div class="value">
                                    {{ number_format($f['gross_sales'], 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card {{ $f['net_profit'] >= 0 ? 'profit' : 'loss' }}">
                                <div class="label">{{ __('owner.profit_loss.net_profit') }}</div>
                                <div class="value">
                                    {{ number_format($f['net_profit'], 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card revenue">
                                <div class="label">{{ __('owner.profit_loss.owner_share') }} ({{ number_format($f['owner_percent'], 0) }}%)</div>
                                <div class="value">
                                    {{ number_format($f['owner_share'], 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card payroll">
                                <div class="label">{{ __('owner.profit_loss.crew_share') }}</div>
                                <div class="value">
                                    {{ number_format($f['crew_share'], 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>

                        {{-- Row 2: Expenses --}}
                        <div class="col-12 px-3 pb-1 pt-2">
                            <div class="section-subtitle">{{ __('owner.profit_loss.total_expenses') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card expense">
                                <div class="label">{{ __('owner.profit_loss.trip_expenses') }}</div>
                                <div class="value">
                                    {{ number_format($f['trip_expenses'], 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card expense">
                                <div class="label">{{ __('owner.profit_loss.general_expenses') }}</div>
                                <div class="value">
                                    {{ number_format($f['general_expenses'], 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card expense">
                                <div class="label">{{ __('owner.profit_loss.total_expenses') }}</div>
                                <div class="value">
                                    {{ number_format($f['total_expenses'], 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Crew share distribution (custom percentages + shares) --}}
            @if (!empty($f['crew_distribution']) && count($f['crew_distribution']) > 0)
                <div class="px-2 mt-4">
                    <h4 class="mb-3">{{ __('owner.profit_loss.crew_distribution_title') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('owner.month_closing.columns.member') }}</th>
                                    <th>{{ __('owner.month_closing.columns.role') }}</th>
                                    <th>{{ __('owner.month_closing.columns.custom_percent') }}</th>
                                    <th>{{ __('owner.month_closing.columns.shares') }}</th>
                                    <th class="text-end">{{ __('owner.month_closing.columns.due') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($f['crew_distribution'] as $member)
                                    <tr>
                                        <td>{{ $member['name'] }}</td>
                                        <td>{{ $member['role'] }}</td>
                                        <td>{{ $member['custom_percent'] !== null ? number_format($member['custom_percent'], 2) . '%' : '-' }}</td>
                                        <td>{{ $member['custom_percent'] !== null ? '-' : number_format($member['shares'], 2) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($member['due'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td colspan="4">{{ __('owner.profit_loss.crew_share') }}</td>
                                    <td class="text-end">{{ number_format(collect($f['crew_distribution'])->sum('due'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>



@endsection

@section('script')
@endsection
