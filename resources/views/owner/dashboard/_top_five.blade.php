{{-- Client's "أهم 5" landing section (plan §4.3). Server-rendered for the
     current month from the canonical financial + reports services. --}}
@php($tf = $topFive)
<div class="mb-2 d-flex align-items-center">
    <h5 class="fw-bold mb-0"><i class="bi bi-star-fill text-warning me-2"></i>{{ __('owner.dashboard.top_five.heading') }}</h5>
</div>
<div class="row g-3 mb-3">
    {{-- 1. Month profit --}}
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex flex-column gap-2">
                <div class="text-muted small">{{ __('owner.dashboard.top_five.month_profit') }}</div>
                <div class="h4 fw-bold mb-0 {{ $tf['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                    {!! number_format($tf['net_profit'], 0) !!}
                    {!! view('components.riyal-icon', ['size' => 'sm', 'style' => 'width:.85rem;height:auto;display:inline-block;'])->render() !!}
                </div>
                @php($up = $tf['profit_change'] >= 0)
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge {{ $up ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                        <i class="bi bi-arrow-{{ $up ? 'up' : 'down' }}"></i> {{ number_format(abs($tf['profit_change']), 1) }}%
                    </span>
                    <small class="text-muted">{{ __('owner.dashboard.top_five.vs_last_month') }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Crew dues --}}
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex flex-column gap-2">
                <div class="text-muted small">{{ __('owner.dashboard.top_five.crew_dues') }}</div>
                <div class="h4 fw-bold mb-0 text-warning">
                    {!! number_format($tf['crew_pool'], 0) !!}
                    {!! view('components.riyal-icon', ['size' => 'sm', 'style' => 'width:.85rem;height:auto;display:inline-block;'])->render() !!}
                </div>
                @if ($tf['is_closed'])
                    <div class="small text-muted">
                        {{ __('owner.dashboard.top_five.unpaid_dues') }}:
                        <span class="fw-bold text-danger">{{ number_format($tf['unpaid_dues'], 0) }}</span>
                    </div>
                    <div><span class="badge bg-success-subtle text-success">{{ __('owner.dashboard.top_five.closed_badge') }}</span></div>
                @else
                    <div><span class="badge bg-secondary-subtle text-secondary">{{ __('owner.dashboard.top_five.open_badge') }}</span></div>
                @endif
                <a href="{{ route('owner.month-closing.index') }}" class="small mt-auto">{{ __('owner.dashboard.top_five.go_to_closing') }} <i class="bi bi-arrow-left-short"></i></a>
            </div>
        </div>
    </div>

    {{-- 3. Boat profitability (bar list) --}}
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold">{{ __('owner.dashboard.top_five.boat_profitability') }}</span>
                    <a href="{{ route('owner.reports.boat-profitability') }}" class="small">{{ __('owner.dashboard.top_five.view_all') }}</a>
                </div>
                @forelse ($tf['boats'] as $boat)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small">
                            <span class="text-truncate" style="max-width:55%">{{ $boat['boat_name'] }}</span>
                            <span class="fw-bold {{ $boat['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($boat['net_profit'], 0) }}</span>
                        </div>
                        <div class="progress" style="height:5px;">
                            <div class="progress-bar {{ $boat['net_profit'] >= 0 ? 'bg-success' : 'bg-danger' }}"
                                style="width: {{ $tf['boats_max'] > 0 ? min(100, abs($boat['net_profit']) / $tf['boats_max'] * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">{{ __('owner.dashboard.top_five.no_data') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 4. Trip profitability (top 5 table) --}}
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold">{{ __('owner.dashboard.top_five.trip_profitability') }}</span>
                    <a href="{{ route('owner.reports.trip-profitability') }}" class="small">{{ __('owner.dashboard.top_five.view_all') }}</a>
                </div>
                @if (count($tf['trips']))
                    <table class="table table-sm mb-0">
                        <tbody>
                            @foreach ($tf['trips'] as $trip)
                                <tr>
                                    <td class="small text-truncate" style="max-width:120px">{{ $trip['number'] }}</td>
                                    <td class="small text-muted text-truncate" style="max-width:90px">{{ $trip['boat_name'] }}</td>
                                    <td class="small text-end fw-bold {{ $trip['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($trip['net_profit'], 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted small mb-0">{{ __('owner.dashboard.top_five.no_data') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- 5. Production by species --}}
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold">{{ __('owner.dashboard.top_five.production_species') }}</span>
                    <a href="{{ route('owner.reports.production-species') }}" class="small">{{ __('owner.dashboard.top_five.view_all') }}</a>
                </div>
                @forelse ($tf['species'] as $fish)
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-truncate" style="max-width:60%">{{ $fish['fish_name'] }}</span>
                        <span class="text-muted">{{ number_format($fish['sold_weight'], 0) }} {{ __('owner.dashboard.kg') }}</span>
                    </div>
                @empty
                    <p class="text-muted small mb-0">{{ __('owner.dashboard.top_five.no_data') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 6. Months closing status (current year) --}}
    <div class="col-xl-9 col-md-6">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="small fw-bold">{{ __('owner.dashboard.top_five.months_status') }} {{ $tf['year'] }}</span>
                    <a href="{{ route('owner.month-closing.index') }}" class="small">{{ __('owner.dashboard.top_five.view_all') }}</a>
                </div>
                <div class="row g-2">
                    @foreach ($tf['months'] as $month)
                        @php($closed = $month['is_closed'])
                        @php($future = $month['is_future'])
                        @php($cell = $closed
                            ? 'border-success bg-success-subtle text-success'
                            : ($future ? 'border-light bg-light text-muted' : 'border-warning bg-warning-subtle text-warning-emphasis'))
                        @php($status = $closed
                            ? __('owner.dashboard.top_five.closed')
                            : ($future ? __('owner.dashboard.top_five.upcoming') : __('owner.dashboard.top_five.not_closed')))
                        <div class="col-6 col-sm-4 col-xl-2">
                            <{{ $closed ? 'a' : 'div' }}
                                @if ($closed) href="{{ route('owner.month-closing.show', $month['closing_id']) }}" @endif
                                class="d-block text-center text-decoration-none border rounded py-2 px-1 h-100 {{ $cell }}">
                                <div class="small fw-semibold text-truncate">{{ $month['name'] }}</div>
                                <div class="d-flex align-items-center justify-content-center gap-1 mt-1" style="font-size:.7rem;">
                                    <i class="bi {{ $closed ? 'bi-lock-fill' : ($future ? 'bi-dash-circle' : 'bi-unlock') }}"></i>
                                    <span>{{ $status }}</span>
                                </div>
                            </{{ $closed ? 'a' : 'div' }}>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
