@extends('admin.layouts.master')

@section('title')
    {{ __('admin.report.hub.title') }}
@endsection

@section('content')
    <div class="mb-4">
        <h2 class="mb-1">{{ __('admin.report.hub.title') }}</h2>
        <p class="text-muted mb-0">{{ __('admin.report.hub.subtitle') }}</p>
    </div>

    @php
        $sections = [
            [
                'title' => __('admin.report.hub.group_operational'),
                'icon' => 'bi-clipboard-data',
                'color' => 'primary',
                'items' => [
                    ['admin.trip-report', __('admin.report.trip.title')],
                    ['admin.boat-report', __('admin.report.boats.title')],
                ],
            ],
            [
                'title' => __('admin.report.hub.group_sales'),
                'icon' => 'bi-cash-stack',
                'color' => 'success',
                'items' => [
                    ['admin.sales-report', __('admin.report.sales.title')],
                    ['admin.revenue-report', __('admin.report.revenue.title')],
                ],
            ],
            [
                'title' => __('admin.report.hub.group_inventory'),
                'icon' => 'bi-box-seam',
                'color' => 'info',
                'items' => [
                    ['admin.stock-report', __('admin.report.stock.title')],
                    ['admin.fish-history-report', __('admin.report.fish_history.title')],
                ],
            ],
            [
                'title' => __('admin.report.hub.group_platform'),
                'icon' => 'bi-people',
                'color' => 'secondary',
                'items' => [
                    ['admin.owner-report', __('admin.report.owners.title')],
                ],
            ],
        ];
    @endphp

    <div class="row g-3">
        @foreach ($sections as $section)
            @php
                $available = collect($section['items'])->filter(fn ($item) => \Illuminate\Support\Facades\Route::has($item[0]));
            @endphp
            @if ($available->isNotEmpty())
                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-{{ $section['color'] }}-subtle border-0 d-flex align-items-center gap-2">
                            <i class="bi {{ $section['icon'] }} text-{{ $section['color'] }}"></i>
                            <span class="fw-bold">{{ $section['title'] }}</span>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach ($available as [$routeName, $label])
                                <a href="{{ route($routeName) }}"
                                    class="list-group-item list-group-item-action d-flex align-items-center justify-content-between bg-transparent">
                                    <span><i class="bi bi-file-earmark-text text-muted me-2"></i>{{ $label }}</span>
                                    <i class="bi {{ app()->getLocale() == 'ar' ? 'bi-arrow-left-short' : 'bi-arrow-right-short' }}"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endsection
