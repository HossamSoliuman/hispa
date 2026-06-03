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

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
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
            {{-- <a href="{{ route('owner.profit.loss.print', request()->all()) }}" target="_blank" class="btn btn-outline-info btn-border-radius">
                <i class="fa fa-print me-2"></i>{{ __('owner.profit_loss.print') }}
            </a> --}}
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
        <div id="printable-area">
            <div class="row">
                <div class="col-md-8 border-end">
                    <div class="row">
                        <div class="col-md-12 p-2 pb-3">
                            <h3>{{ __('owner.profit_loss.profit_loss_title') }}</h3>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card revenue">
                                <div class="label">{{ __('owner.profit_loss.total_sales') }}</div>
                                <div class="value">
                                    {{ number_format($sales ?? 0, 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card expense">
                                <div class="label">{{ __('owner.generated.expenses_and_fixed_salaries') }}</div>
                                <div class="value">
                                    {{ number_format($total_expenses ?? 0, 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card revenue">
                                <div class="label">{{ __('owner.profit_loss.net_sales') }}</div>
                                <div class="value">
                                    {{ number_format($netSales ?? 0, 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card payroll">
                                <div class="label">{{ __('owner.generated.fishermen_ratio') }}</div>
                                <div class="value">
                                    {{ number_format($ownerPercent ?? 0, 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card revenue">
                                <div class="label">{{ __('owner.generated.owner_ratio') }}</div>
                                <div class="value">
                                    {{ number_format($ownerPercent ?? 0, 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card payroll">
                                <div class="label">{{ __('owner.generated.fishermen_count') }}</div>
                                <div class="value">
                                    {{ number_format($total_captins ?? 0, 0) }}
                                    {{-- <span class="currency-symbol"><x-riyal-icon size="sm" /></span> --}}
                                </div>
                            </div>
                        </div>
                        @if (filled($boatId))
                            <div class="col-md-12">
                                @if ($total_captins > 0)
                                    @php $netProfit = (float)(($ownerPercent/$total_captins) ?? 0); @endphp
                                @else
                                    @php $netProfit = 0; @endphp
                                @endif
                                <div class="stat-card {{ $netProfit >= 0 ? 'profit' : 'loss' }}">
                                    <div class="label">{{ __('owner.generated.fisherman_net_profit') }}</div>
                                    <div class="value">
                                        {{ number_format($netProfit, 2) }}
                                        <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                    </div>
                                    {{-- <small class="text-muted d-block mt-2">{{ __('owner.profit_loss.formula_note') }}</small> --}}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12 p-2 pb-3">
                            <h3>{{ __('owner.profit_loss.tax_title') }}</h3>
                        </div>
                        <div class="col-md-12">
                            <div class="stat-card payroll">
                                <div class="label">{{ __('owner.profit_loss.sales_tax') }}</div>
                                <div class="value">
                                    {{ number_format($sales_tax ?? 0, 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="stat-card expense">
                                <div class="label">{{ __('owner.profit_loss.expenses_tax') }}</div>
                                <div class="value">
                                    {{ number_format($expenses_tax ?? 0, 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="stat-card revenue">
                                <div class="label">{{ __('owner.generated.depreciation_ratio') }}</div>
                                <div class="value">
                                    {{ number_format($depreciation ?? 0, 2) }}
                                    <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('script')
@endsection
