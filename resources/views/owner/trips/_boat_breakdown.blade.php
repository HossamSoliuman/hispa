@php
    $boatCatchWeight = $boat['catch_weight_by_unit']->isNotEmpty()
        ? $boat['catch_weight_by_unit']->map(fn ($w, $u) => number_format(round($w), 0) . ' ' . $u)->implode('، ')
        : '0';
@endphp
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header fw-bold d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <i class="fas fa-anchor me-2"></i>{{ $boat['boat_name'] }}@if($boat['boat_number']) <span class="text-muted">({{ $boat['boat_number'] }})</span>@endif
        </div>
        <div class="small text-muted">
            <i class="fas fa-user-ninja me-1"></i>{{ $boat['captains']->pluck('name')->implode('، ') ?: __('owner.trips.no_captain') }}
        </div>
    </div>
    <div class="card-body">
        <div class="row text-center mb-3 g-2">
            <div class="col-6 col-md-3"><div class="text-muted small">{{ __('owner.reports.catch_weight') }}</div><div class="fw-bold">{{ $boatCatchWeight }}</div></div>
            <div class="col-6 col-md-3"><div class="text-muted small">{{ __('owner.reports.total_income') }}</div><div class="fw-bold">{{ number_format($boat['total_income'], 2) }}</div></div>
            <div class="col-6 col-md-3"><div class="text-muted small">{{ __('owner.reports.depreciation') }} ({{ number_format($boat['depreciation_percent'], 2) }}%)</div><div class="fw-bold">{{ number_format($boat['depreciation'], 2) }}</div></div>
            <div class="col-6 col-md-3"><div class="text-muted small">{{ __('owner.reports.net_profit') }}</div><div class="fw-bold">{{ number_format($boat['net_profit'], 2) }}</div></div>
        </div>

        <h6 class="fw-bold"><i class="fas fa-fish me-1"></i>{{ __('owner.reports.catch_breakdown') }}</h6>
        <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.catch.fish') }}</th>
                        <th>{{ __('owner.catch.weight') }}</th>
                        <th>{{ __('owner.catch.unit') }}</th>
                        <th>{{ __('owner.sales.price_per_kilo') }}</th>
                        <th>{{ __('owner.catch.total_price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boat['catch_details'] as $i => $detail)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $detail->fish->scientific_name ?? '---' }}</td>
                            <td>{{ number_format($detail->weight, 2) }}</td>
                            <td>{{ $detail->unit->name ?? '—' }}</td>
                            <td>{{ number_format($detail->price_per_kg, 2) }}</td>
                            <td>{{ number_format($detail->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">{{ __('owner.trips.show.no_catch_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h6 class="fw-bold"><i class="fas fa-cash-register me-1"></i>{{ __('owner.reports.sales_breakdown') }}</h6>
        <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.reports.sale_number') }}</th>
                        <th>{{ __('owner.reports.customer') }}</th>
                        <th>{{ __('owner.reports.sale_date') }}</th>
                        <th>{{ __('owner.reports.amount') }}</th>
                        <th>{{ __('owner.reports.payment_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boat['sales'] as $i => $sale)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $sale->number }}</td>
                            <td>{{ $sale->customer_name ?? ($sale->customer->name ?? '-') }}</td>
                            <td>{{ $sale->sale_datetime ? $sale->sale_datetime->format('Y-m-d') : '-' }}</td>
                            <td>{{ number_format($sale->total_price, 2) }}</td>
                            <td>{{ \App\Models\Sale::paymentStatusText($sale->payment_status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">{{ __('owner.reports.no_sales') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <table class="table table-bordered table-sm m-0 text-center">
                    <tbody>
                        <tr><th class="w-50">{{ __('owner.reports.total_income') }}</th><td>{{ number_format($boat['total_income'], 2) }}</td></tr>
                        <tr><th>{{ __('owner.reports.depreciation_percent') }}</th><td>{{ number_format($boat['depreciation_percent'], 2) }}%</td></tr>
                        <tr><th>{{ __('owner.reports.total_expenses') }}</th><td>{{ number_format($boat['total_expenses'], 2) }}</td></tr>
                        <tr><th>{{ __('owner.reports.outstanding') }}</th><td>{{ number_format($boat['outstanding'], 2) }}</td></tr>
                        <tr class="table-success fw-bold"><th>{{ __('owner.reports.net_profit') }}</th><td>{{ number_format($boat['net_profit'], 2) }}</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered table-sm m-0 text-center">
                    <tbody>
                        <tr><th class="w-50">{{ __('owner.reports.owner_share') }} (50%)</th><td>{{ number_format($boat['owner_share'], 2) }}</td></tr>
                        <tr><th>{{ __('owner.reports.crew_share') }} (50%)</th><td>{{ number_format($boat['crew_share'], 2) }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <h6 class="fw-bold mt-3"><i class="fas fa-users me-1"></i>{{ __('owner.reports.crew_salaries') }}</h6>
        <table class="table table-sm table-bordered text-center mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('owner.reports.member_name') }}</th>
                    <th>{{ __('owner.reports.percentage') }}</th>
                    <th>{{ __('owner.reports.amount') }}</th>
                    <th>{{ __('owner.reports.signature') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($boat['crew_members'] as $member)
                    <tr>
                        <td>{{ $member['name'] }}</td>
                        <td>{{ number_format($member['percent'], 2) }}%</td>
                        <td>{{ number_format($member['due'], 2) }}</td>
                        <td></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">{{ __('owner.reports.no_crew') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-arrow">
        <div class="card-arrow-top-left"></div>
        <div class="card-arrow-top-right"></div>
        <div class="card-arrow-bottom-left"></div>
        <div class="card-arrow-bottom-right"></div>
    </div>
</div>
