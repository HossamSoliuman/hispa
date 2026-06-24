@extends('owner.layouts.master')

@section('title', __('owner.analysis_reports.hub_title'))

@section('content')
    <div class="mb-4">
        <h2 class="mb-1">{{ __('owner.analysis_reports.hub_title') }}</h2>
        <p class="text-muted mb-0">{{ __('owner.analysis_reports.hub_subtitle') }}</p>
    </div>

    @php
        $sections = [
            [
                'title' => __('owner.analysis_reports.group_operational'),
                'icon' => 'bi-clipboard-data',
                'color' => 'primary',
                'items' => [
                    ['owner.trip-report', __('owner.menu.trip_report')],
                    ['owner.reports.trip-profitability', __('owner.analysis_reports.trip_profitability.title')],
                    ['owner.reports.boat-profitability', __('owner.analysis_reports.boat_profitability.title')],
                    ['owner.reports.production-species', __('owner.analysis_reports.production_species.title')],
                ],
            ],
            [
                'title' => __('owner.analysis_reports.group_financial'),
                'icon' => 'bi-cash-stack',
                'color' => 'success',
                'items' => [
                    ['owner.reports.month-summary', __('owner.month_summary.title')],
                    ['owner.profit.loss', __('owner.profit_loss.title')],
                    ['owner.sales-report', __('owner.menu.sales_report')],
                    ['owner.reports.expenses-by-category', __('owner.analysis_reports.expenses_by_category.title')],
                ],
            ],
            [
                'title' => __('owner.analysis_reports.group_crew'),
                'icon' => 'bi-people',
                'color' => 'warning',
                'items' => [
                    ['owner.month-closing.index', __('owner.menu.month_closing')],
                ],
            ],
            [
                'title' => __('owner.analysis_reports.group_admin'),
                'icon' => 'bi-gear',
                'color' => 'secondary',
                'items' => [
                    ['owner.fishQuntity', __('owner.menu.fish_quantity')],
                ],
            ],
        ];
    @endphp

    <div class="row g-3">
        @foreach ($sections as $section)
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-{{ $section['color'] }}-subtle border-0 d-flex align-items-center gap-2">
                        <i class="bi {{ $section['icon'] }} text-{{ $section['color'] }}"></i>
                        <span class="fw-bold">{{ $section['title'] }}</span>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach ($section['items'] as [$routeName, $label])
                            @if (\Illuminate\Support\Facades\Route::has($routeName))
                                <a href="{{ route($routeName) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                                    <span>{{ $label }}</span>
                                    <i class="bi bi-arrow-left-short"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
