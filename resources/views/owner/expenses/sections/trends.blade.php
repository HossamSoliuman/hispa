<div class="row g-4 mt-4">

    <!-- Highest Expense Category -->
    <div class="col-md-4">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">{{ __('owner.expenses.sections.trends.top_category_title') }}</h6>
                <h4 class="fw-bold mb-1 text-primary">{{ $topCategory?->category?->name ?? '-' }}</h4>
                <p class="mb-2">{{ __('owner.expenses.sections.trends.total_spending') }}
                    <span class="fw-bold text-success">{!! number_format($topCategory->total ?? 0, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</span>
                </p>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">{{ $topCategory->expenses_count ?? 0 }} {{ __('owner.expenses.sections.trends.expense_label') }}</small>
                    <small class="text-muted">{!! number_format($topCategory->avg ?? 0, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span> ' . __('owner.expenses.sections.trends.avg_label') !!}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Expensive Vessel -->
    <div class="col-md-4">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">{{ __('owner.expenses.sections.trends.top_boat_title') }}</h6>
                <h4 class="fw-bold mb-1 text-primary">{{ $topBoat?->boat?->name ?? '-' }}</h4>
                <p class="mb-2">{{ __('owner.expenses.sections.trends.total_expenses') }}
                    <span class="fw-bold text-info">{!! number_format($topBoat->total ?? 0, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</span>
                </p>
                <small class="text-muted">{{ __('owner.expenses.sections.trends.from_transactions', ['count' => $topBoat->expenses_count ?? 0]) }}</small>
            </div>
        </div>
    </div>

    <!-- Payment Status -->
    <div class="col-md-4">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">{{ __('owner.expenses.sections.trends.payment_status_title') }}</h6>
                <h4 class="fw-bold mb-1 text-primary">
                    {{ $topStatus?->status === 'pending' ? __('owner.pending') : __('owner.paid') }}
                </h4>
                <p class="mb-2">{{ __('owner.expenses.sections.trends.payments_count') }}
                    <span class="fw-bold text-danger">{{ $topStatus->count ?? 0 }}</span>
                </p>
                    <small class="text-muted">{!! number_format($topStatus->total ?? 0, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</small>
            </div>
        </div>
    </div>

</div>

<div class="row g-4 mt-4">

    <!-- Performance Highlights -->
    <div class="col-md-6">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-4">📊 {{ __('owner.dashboard.kpis') }}</h5>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted"><i class="bi bi-wallet2 me-2 text-success"></i>{{ __('owner.dashboard.total_expenses') }}</span>
                    <span class="fw-bold text-success">{!! number_format($totalAmount ?? 0, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted"><i class="bi bi-bar-chart-line me-2 text-primary"></i>{{ __('owner.generated.average_expense') }}</span>
                    <span class="fw-bold text-primary">{!! number_format($avgPerExpense ?? 0, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted"><i class="bi bi-cash me-2 text-warning"></i>{{ __('owner.expenses.cards.pending_amount') }}</span>
                    <span class="fw-bold text-warning">{!! number_format($pendingAmount ?? 0, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Metrics -->
    <div class="col-md-6">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-4">⚙️ {{ __('owner.generated.operational_indicators') }}</h5>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted"><i class="bi bi-folder2-open me-2 text-info"></i>{{ __('owner.generated.categories_count') }}</span>
                    <span class="fw-bold text-info">{{ $categoriesCount ?? 0 }} {{ __('owner.generated.item_b36375') }}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted"><i class="bi bi-hourglass-split me-2 text-danger"></i>{{ __('owner.generated.completed_payments_ratio') }}</span>
                    <span class="fw-bold text-danger">{{ $paymentCompletionRate ?? 0 }}%</span>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted"><i class="bi bi-check2-circle me-2 text-success"></i>{{ __('owner.generated.cost_control') }}</span>
                    <span class="fw-bold text-success">{{ __('owner.generated.all_expenses_monitored') }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
