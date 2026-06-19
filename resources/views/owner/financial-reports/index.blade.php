@extends('owner.layouts.master')

@section('title', '{{ __('owner.generated.financial_data') }}')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">📊 {{ __('owner.generated.financial_data') }}</h4>
            <p class="text-muted mb-0">{{ __('owner.generated.professional_financial_reports') }}</p>
        </div>
        <div>
            <button class="btn btn-outline-success me-2"><i class="bi bi-file-earmark-arrow-down"></i> {{ __('owner.generated.export') }}CSV</button>
            <button class="btn btn-outline-secondary"><i class="bi bi-printer"></i> {{ __('owner.generated.print_report') }}</button>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="fw-bold text-dark mb-4"><i class="bi bi-sliders2 me-2"></i>{{ __('owner.generated.financial_report_params') }}</h5>

            <div class="row g-4">

                {{-- من التاريخ --}}
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">{{ __('owner.generated.from_date') }}</label>
                    <input type="date" class="form-control" value="2024-12-31">
                </div>

                {{-- إلى التاريخ --}}
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">{{ __('owner.generated.to_date') }}</label>
                    <input type="date" class="form-control" value="2025-07-28">
                </div>

                {{-- نوع التقرير --}}
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">{{ __('owner.generated.report_type') }}</label>
                    <select class="form-select">
                        <option selected>{{ __('owner.generated.income_statement') }}</option>
                        <option>{{ __('owner.generated.balance_sheet') }}</option>
                        <option>{{ __('owner.generated.cash_flow_statement') }}</option>
                    </select>
                </div>

                {{-- المقارنة --}}
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">{{ __('owner.generated.comparison_type') }}</label>
                    <select class="form-select">
                        <option selected>{{ __('owner.generated.no_comparison') }}</option>
                        <option>{{ __('owner.generated.annual_comparison') }}</option>
                        <option>{{ __('owner.generated.quarterly_comparison') }}</option>
                    </select>
                </div>

            </div>
        </div>
    </div>


    {{-- Financial KPIs: unified dashboard HUD stat-card style --}}
    <div class="row mb-4">
        @include('owner.components.stat-card', [
            'title' => __('owner.reports.total_revenue'),
            'value' => __('owner.generated.amount_0_sar'),
            'icon' => 'bi bi-coin',
            'footer' => __('owner.generated.from_0_catch_record'),
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.generated.net_income'),
            'value' => __('owner.generated.amount_0_sar'),
            'icon' => 'bi bi-graph-down',
            'footer' => __('owner.generated.profit_margin_0') . '%',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.generated.total_assets'),
            'value' => __('owner.generated.amount_10000_sar'),
            'icon' => 'bi bi-bank2',
            'footer' => __('owner.generated.ownership_100') . '%',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.generated.operating_cash_flow'),
            'value' => __('owner.generated.amount_0_sar'),
            'icon' => 'bi bi-cash-stack',
            'footer' => __('owner.generated.from_operating_activities'),
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#income">{{ __('owner.generated.income_list') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#balance">{{ __('owner.generated.budget') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#cash">{{ __('owner.generated.cash_flows_statement') }}</a></li>
    </ul>

    <div class="tab-content">
        {{-- Income Statement --}}
        <div class="tab-pane fade show active" id="income">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">📈 {{ __('owner.generated.income_list_2024') }}-12-{{ __('owner.generated.31_to_2025') }}-07-28</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('owner.payrolls.table.total_revenues') }}</h6>
                            <ul class="list-group mb-4">
                                <li class="list-group-item d-flex justify-content-between">{{ __('owner.generated.fish_sales') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between">{{ __('owner.generated.other_revenues') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between fw-bold">{{ __('owner.dalal_invoices.total') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                            </ul>

                            <h6 class="text-muted">{{ __('owner.generated.cost_of_goods') }}</h6>
                            <ul class="list-group mb-4">
                                <li class="list-group-item d-flex justify-content-between">{{ __('owner.generated.direct_labor') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between">{{ __('owner.generated.fuel') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between">{{ __('owner.generated.equipment') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between fw-bold">{{ __('owner.dalal_invoices.total') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                            </ul>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('owner.generated.operating_expenses') }}</h6>
                            <ul class="list-group mb-4">
                                <li class="list-group-item d-flex justify-content-between">{{ __('owner.boats.maintenance') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between">{{ __('owner.generated.requisites') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between">{{ __('owner.generated.insurance') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between">{{ __('owner.generated.permits') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between">{{ __('owner.generated.others') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between fw-bold">{{ __('owner.dalal_invoices.total') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                            </ul>

                            <h6 class="text-muted">{{ __('owner.generated.other_expenses') }}</h6>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between">{{ __('owner.generated.interest') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between">{{ __('owner.generated.depreciation') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between fw-bold">{{ __('owner.dalal_invoices.total') }}<span>{{ __('owner.generated.amount_0_sar') }}</span></li>
                            </ul>

                            <h5 class="fw-bold text-success">{{ __('owner.generated.net_income_1') }}<span class="float-end">{{ __('owner.generated.amount_0_sar') }}</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="balance">
            <div class="card shadow-sm border-0 p-4 mt-3 text-muted">
                <div class="mt-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-journal-check text-primary me-2"></i>
                                {{ __('owner.generated.balance_sheet') }}</h5>
                            <p class="text-muted small">{{ __('owner.generated.as_of_july_28') }}</p>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-1 h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold text-dark mb-3">📦 {{ __('owner.generated.assets') }}(ASSETS)</h6>

                                    <h6 class="text-muted">{{ __('owner.generated.current_assets') }}</h6>
                                    <ul class="list-unstyled mb-3">
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.cash') }}</span><span>{{ __('owner.generated.amount_10000_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.accounts_receivable') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.inventory') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between fw-bold">
                                            <span>{{ __('owner.generated.total_current_assets') }}</span><span>{{ __('owner.generated.amount_10000_sar') }}</span>
                                        </li>
                                    </ul>

                                    <h6 class="text-muted">{{ __('owner.generated.fixed_assets') }}</h6>
                                    <ul class="list-unstyled">
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.ships') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.equipment') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.accumulated_depreciation') }}</span><span>({{ __('owner.generated.amount_0_sar') }})</span>
                                        </li>
                                        <li class="d-flex justify-content-between fw-bold">
                                            <span>{{ __('owner.generated.net_fixed_assets') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                    </ul>

                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold text-primary">
                                        <span>{{ __('owner.generated.total_assets') }}</span><span>{{ __('owner.generated.amount_10000_sar') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm border-1 h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold text-dark mb-3">💼 {{ __('owner.generated.liabilities_equity') }}</h6>

                                    <h6 class="text-muted">{{ __('owner.generated.current_liabilities') }}</h6>
                                    <ul class="list-unstyled mb-3">
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.accounts_payable') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.short_term_debt') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.accrued_expenses') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between fw-bold">
                                            <span>{{ __('owner.generated.total_current_liabilities') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                    </ul>

                                    <h6 class="text-muted">{{ __('owner.generated.long_term_liabilities') }}</h6>
                                    <ul class="list-unstyled mb-3">
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.long_term_debt') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between fw-bold">
                                            <span>{{ __('owner.generated.total_long_term_liabilities') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                    </ul>

                                    <hr class="my-3">

                                    <h6 class="text-muted">{{ __('owner.generated.equity') }}</h6>
                                    <ul class="list-unstyled mb-3">
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.fisherman_equity') }}</span><span>{{ __('owner.generated.amount_6000_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.retained_earnings') }}</span><span>{{ __('owner.generated.amount_4000_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between fw-bold">
                                            <span>{{ __('owner.generated.total_equity') }}</span><span>{{ __('owner.generated.amount_10000_sar') }}</span>
                                        </li>
                                    </ul>

                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold text-primary">
                                        <span>{{ __('owner.generated.total_liabilities_equity') }}</span><span>{{ __('owner.generated.amount_10000_sar') }}</span>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="cash">
            <div class="card shadow-sm border-1 p-4 mt-3 text-muted">
                <div class="mt-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-cash-stack text-success me-2"></i>
                                {{ __('owner.generated.cash_flow_statement') }}- Fish House Trading Est.</h5>
                            <p class="text-muted small">{{ __('owner.generated.period_dec_2024_to_july_2025') }}</p>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card border-1 shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold text-dark mb-3">💼 {{ __('owner.generated.operating_activities') }}</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.net_income') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.depreciation_act') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.change_receivables') }}</span><span>-{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.change_payables') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between fw-bold mt-2 border-top pt-2">
                                            <span>{{ __('owner.generated.net_cash_operating') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-1 shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold text-dark mb-3">🏗️ {{ __('owner.generated.investing_activities') }}</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.buying_equip') }}</span><span>-{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.buying_ships') }}</span><span>-{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between fw-bold mt-2 border-top pt-2">
                                            <span>{{ __('owner.generated.net_cash_investing') }}</span><span>-{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-1 shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold text-dark mb-3">🏦 {{ __('owner.generated.financing_activities') }}</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.loan_proceeds') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.loan_repayment') }}</span><span>-{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.fisherman_withdrawals') }}</span><span>-{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between fw-bold mt-2 border-top pt-2">
                                            <span>{{ __('owner.generated.net_cash_financing') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mt-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-1">
                                <div class="card-body">
                                    <h6 class="fw-bold text-dark mb-3">🔄 {{ __('owner.generated.change_in_cash') }}</h6>
                                    <ul class="list-unstyled">
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.net_change_cash') }}</span><span>{{ __('owner.generated.amount_0_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>{{ __('owner.generated.balance_start') }}</span><span>{{ __('owner.generated.amount_10000_sar') }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between fw-bold mt-2 border-top pt-2">
                                            <span>{{ __('owner.generated.balance_end') }}</span><span class="text-success">{{ __('owner.generated.amount_10000_sar') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            <h5 class="fw-bold mb-4">🧮 {{ __('owner.generated.financial_analysis_summary') }}</h5>

            <div class="row row-cols-1 row-cols-md-3 g-3">
                <div class="col">
                    <div class="border p-3 h-100 rounded bg-light">
                        <h6 class="fw-bold text-muted">{{ __('owner.generated.profitability_analysis') }}</h6>
                        <ul class="list-unstyled mb-0">
                            <li>{{ __('owner.generated.revenue_performance') }}<span class="fw-bold text-dark">{{ __('owner.generated.amount_0_sar') }}</span></li>
                            <li>{{ __('owner.generated.cost_tuning') }}<span class="fw-bold text-dark">0% {{ __('owner.generated.profit_margin_kpi') }}</span></li>
                            <li>{{ __('owner.generated.general_profitability') }}<span class="fw-bold text-danger">{{ __('owner.generated.loss') }}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col">
                    <div class="border p-3 h-100 rounded bg-light">
                        <h6 class="fw-bold text-muted">{{ __('owner.generated.financial_position') }}</h6>
                        <ul class="list-unstyled mb-0">
                            <li>{{ __('owner.generated.total_assets_1') }}<span class="fw-bold text-dark">{{ __('owner.generated.amount_10000_sar') }}</span></li>
                            <li>{{ __('owner.generated.liquidity') }}<span class="fw-bold text-muted">N/A</span></li>
                            <li>{{ __('owner.generated.leverage') }}<span class="fw-bold text-dark">0.0% {{ __('owner.generated.debt_ratio') }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
