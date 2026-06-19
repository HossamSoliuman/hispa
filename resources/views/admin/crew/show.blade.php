@extends('admin.layouts.master')

@section('title')
    {{ __('admin.crew.show_page') ?? 'تفاصيل عضو الطاقم' }} - {{ $crew->name }}
@endsection

@section('css')
    <style>
        .crew-avatar { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; }
        .nav-crew-tabs .nav-link { font-weight: 500; }
        .table-detail th { white-space: nowrap; width: 1%; }
        .table-detail td { vertical-align: middle; }
        .stat-card-show { border-radius: 12px; min-height: 100px; }
    </style>
@endsection

@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0 d-flex align-items-center gap-3">
            <img src="{{ $crew->logo ? asset($crew->logo) : asset('default-logo.png') }}" alt="{{ $crew->name }}" class="crew-avatar border border-2 border-primary">
            <div>
                <h2 class="fw-bold text-dark mb-1">{{ $crew->name }}</h2>
                <p class="text-muted mb-0 small">
                    {{ $crew->phone ?? '—' }} · {{ $crew->email ?? '—' }}
                </p>
                <div class="mt-1">
                    @if($crew->status == 1)
                        <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                    @endif
                    @if($crew->job_title)
                        <span class="badge bg-secondary">{{ $crew->job_title }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.crew.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.crew.title') ?? 'الطاقم' }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- إحصائيات سريعة (كومبونانت الأدمن فقط) --}}
    <div class="row">
        @include('admin.components.stat-card', [
            'title' => __('admin.crew.trips_on_boat'),
            'value' => '<span class="num">' . (int)($stats->total_trips ?? 0) . '</span>',
            'icon' => 'bi bi-geo-alt-fill',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
            'stats' => [],
        ])
        @include('admin.components.stat-card', [
            'title' => __('admin.crew.table.boat') ?? 'القارب',
            'value' => '<span class="text-white">' . e($crew->boat?->name ?: '—') . '</span>',
            'icon' => 'bi bi-signpost-split',
            'gradient' => 'linear-gradient(135deg, #0dcaf0, #0aa2c0)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
            'stats' => [],
        ])
    </div>

    <ul class="nav nav-tabs nav-crew-tabs mb-3" id="crewTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-pane" type="button" role="tab">
                <i class="bi bi-person me-1"></i> {{ __('admin.owner.profile_tab') ?? 'الملف الشخصي' }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="boat-tab" data-bs-toggle="tab" data-bs-target="#boat-pane" type="button" role="tab">
                <i class="bi bi-signpost-split me-1"></i> {{ __('admin.crew.table.boat') ?? 'القارب' }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="trips-tab" data-bs-toggle="tab" data-bs-target="#trips-pane" type="button" role="tab">
                <i class="bi bi-geo-alt me-1"></i> {{ __('admin.crew.trips_on_boat') ?? 'رحلات القارب' }}
                <span class="badge bg-primary bg-opacity-75 ms-1">{{ is_countable($trips) ? count($trips) : 0 }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="crewTabsContent">
        {{-- الملف الشخصي --}}
        <div class="tab-pane fade show active" id="profile-pane" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-detail table-borderless">
                                <tr><th class="text-muted">{{ __('admin.crew.table.name') ?? 'الاسم' }}</th><td>{{ $crew->name }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.phone') }}</th><td>{{ $crew->phone ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.email') }}</th><td>{{ $crew->email ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.crew.table.id_number') ?? 'رقم الهوية' }}</th><td>{{ $crew->id_number ?? $crew->passport_number ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.crew.table.nationality') ?? 'الجنسية' }}</th><td>{{ $crew->nationality ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.crew.table.job_title') ?? 'المسمى الوظيفي' }}</th><td>{{ $crew->job_title ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.captains.table.owner') ?? 'الصياد' }}</th><td>{{ $crew->owner?->name ?? '—' }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-detail table-borderless">
                                <tr><th class="text-muted">{{ __('admin.crew.table.region') ?? 'المنطقة' }}</th><td>{{ $crew->region?->name ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.crew.table.governorate') ?? 'المحافظة' }}</th><td>{{ $crew->governorate?->name ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.crew.table.port') ?? 'الميناء' }}</th><td>{{ $crew->port?->name ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.registered_at') ?? 'تاريخ التسجيل' }}</th><td>{{ $crew->created_at?->format('Y-m-d H:i') ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.crew.salary_type') ?? 'نوع الراتب' }}</th><td>{{ $crew->salary_type ? ($crew->salary_type === 'salary' ? __('admin.owner.payroll_fixed') : __('admin.owner.payroll_percentage')) : '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.captains.table.status') }}</th><td>
                                    @if($crew->status == 1)
                                        <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                                    @endif
                                </td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- القارب --}}
        <div class="tab-pane fade" id="boat-pane" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($crew->boat)
                        <table class="table table-detail table-bordered">
                            <tr><th class="text-muted">{{ __('admin.crew.table.boat') ?? 'اسم القارب' }}</th><td>{{ $crew->boat->name ?: '—' }}</td></tr>
                            <tr><th class="text-muted">{{ __('admin.boats.number') ?? 'رقم القارب' }}</th><td>{{ $crew->boat->number ?? '—' }}</td></tr>
                            <tr><th class="text-muted">{{ __('admin.captains.table.owner') ?? 'الصياد' }}</th><td>{{ $crew->boat->owner?->name ?? '—' }}</td></tr>
                            <tr><th class="text-muted">{{ __('admin.captains.table.name') ?? 'الكابتن' }}</th><td>{{ $crew->boat->captain?->name ?? '—' }}</td></tr>
                            <tr><th class="text-muted">{{ __('admin.owner.status') ?? 'الحالة' }}</th><td>
                                @if($crew->boat->status == 1)
                                    <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                                @endif
                            </td></tr>
                            <tr><th class="text-muted">{{ __('admin.crew.crew_count') ?? 'عدد الطاقم' }}</th><td>{{ $crew->boat->crews->count() ?? 0 }}</td></tr>
                        </table>
                        <a href="{{ route('admin.boats.edit', $crew->boat->id) }}" class="btn btn-outline-primary btn-sm">{{ __('admin.actions.edit') }} {{ __('admin.crew.table.boat') }}</a>
                    @else
                        <p class="text-muted mb-0">{{ __('admin.crew.no_boat') ?? 'لا يوجد قارب مرتبط' }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- رحلات القارب --}}
        <div class="tab-pane fade" id="trips-pane" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if(is_countable($trips) && count($trips) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-detail">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin.table.id') }}</th>
                                        <th>{{ __('admin.trips.table.name') ?? 'الرحلة' }}</th>
                                        <th>{{ __('admin.captains.table.name') ?? 'الكابتن' }}</th>
                                        <th>{{ __('admin.trips.departure_date') ?? 'تاريخ البداية' }}</th>
                                        <th>{{ __('admin.trips.table.status') ?? 'الحالة' }}</th>
                                        <th>{{ __('admin.actions.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trips as $trip)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $trip->name ?? $trip->number ?? '—' }}</td>
                                            <td>{{ $trip->captain?->name ?? '—' }}</td>
                                            <td>{{ $trip->start_date?->format('Y-m-d') ?? '—' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $trip->status->color() }}">{{ $trip->status->label() }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.trips.show', $trip->id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($stats->total_trips > 50)
                            <p class="text-muted small mt-2">{{ __('admin.filters.showing_last') ?? 'عرض آخر 50 رحلة' }}</p>
                        @endif
                    @else
                        <p class="text-muted mb-0">{{ __('admin.crew.no_trips') ?? 'لا توجد رحلات لهذا القارب' }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    (function() {
        function activateTabFromHash() {
            var hash = window.location.hash;
            if (!hash || hash.indexOf('-pane') === -1) return;
            var tabId = hash.replace('#', '').replace('-pane', '-tab');
            var tabEl = document.getElementById(tabId);
            if (tabEl && typeof bootstrap !== 'undefined') {
                try {
                    var tab = bootstrap.Tab.getOrCreateInstance(tabEl);
                    tab.show();
                } catch (e) {
                    tabEl.click();
                }
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() { setTimeout(activateTabFromHash, 50); });
        } else {
            setTimeout(activateTabFromHash, 50);
        }
        window.addEventListener('hashchange', activateTabFromHash);
    })();
</script>
@endsection
