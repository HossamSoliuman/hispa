@extends('admin.layouts.master')

@section('title')
    {{ __('admin.captains.show.page_header') ?? 'تفاصيل الكابتن' }} - {{ $captain->name }}
@endsection

@section('css')
    <style>
        .captain-avatar { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; }
        .nav-captain-tabs .nav-link { font-weight: 500; }
        .table-detail th { white-space: nowrap; width: 1%; }
        .table-detail td { vertical-align: middle; }
        .stat-card-show { border-radius: 12px; min-height: 100px; }
    </style>
@endsection

@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0 d-flex align-items-center gap-3">
            <img src="{{ $captain->logo ? asset($captain->logo) : asset('default-logo.png') }}" alt="{{ $captain->name }}" class="captain-avatar border border-2 border-primary">
            <div>
                <h2 class="fw-bold text-dark mb-1">{{ $captain->name }}</h2>
                <p class="text-muted mb-0 small">
                    {{ $captain->phone ?? '—' }} · {{ $captain->email ?? '—' }}
                </p>
                <div class="mt-1">
                    @if($captain->status == 1)
                        <span class="badge bg-success">{{ __('admin.captains.status.active') ?? __('admin.status.active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('admin.captains.status.inactive') ?? __('admin.status.inactive') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.captain.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.captains.breadcrumb') ?? __('admin.captains.title') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- إحصائيات سريعة (نفس تصميم صفحة قائمة الكباتن) --}}
    <div class="row">
        @include('admin.components.stat-card', [
            'title' => __('admin.captains.show.total_trips') ?? __('admin.captains.cards.total'),
            'value' => '<span class="num">' . (int)($stats->total_trips ?? 0) . '</span>',
            'icon' => 'bi bi-geo-alt-fill',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])
        @include('admin.components.stat-card', [
            'title' => __('admin.captains.show.total_items') ?? 'عناصر المصيد',
            'value' => '<span class="num">' . (int)($stats->corrected_items ?? 0) . '</span>',
            'icon' => 'bi bi-box-seam',
            'gradient' => 'linear-gradient(135deg, #20c997, #198754)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.captains.table.boat_name') ?? 'القارب',
            'value' => '<span class="text-white">' . e($captain->boat?->name ?: '—') . '</span>',
            'icon' => 'bi bi-signpost-split',
            'gradient' => 'linear-gradient(135deg, #0dcaf0, #0aa2c0)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
        ])
    </div>

    <ul class="nav nav-tabs nav-captain-tabs mb-3" id="captainTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-pane" type="button" role="tab">
                <i class="bi bi-person me-1"></i> {{ __('admin.owner.profile_tab') ?? 'الملف الشخصي' }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="boat-tab" data-bs-toggle="tab" data-bs-target="#boat-pane" type="button" role="tab">
                <i class="bi bi-signpost-split me-1"></i> {{ __('admin.captains.table.boat_name') ?? 'القارب' }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="trips-tab" data-bs-toggle="tab" data-bs-target="#trips-pane" type="button" role="tab">
                <i class="bi bi-geo-alt me-1"></i> {{ __('admin.captains.show.total_trips') ?? 'الرحلات' }}
                <span class="badge bg-primary bg-opacity-75 ms-1">{{ count($trips ?? []) }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="captainTabsContent">
        {{-- الملف الشخصي --}}
        <div class="tab-pane fade show active" id="profile-pane" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-detail table-borderless">
                                <tr><th class="text-muted">{{ __('admin.captains.table.name') ?? 'الاسم' }}</th><td>{{ $captain->name }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.phone') }}</th><td>{{ $captain->phone ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.email') }}</th><td>{{ $captain->email ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.captains.table.id_number') ?? 'رقم الهوية' }}</th><td>{{ $captain->id_number ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.crew.table.nationality') ?? 'الجنسية' }}</th><td>{{ $captain->nationality ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.captains.table.owner') ?? 'الصياد' }}</th><td>{{ $captain->owner?->name ?? '—' }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-detail table-borderless">
                                <tr><th class="text-muted">{{ __('admin.captains.form.region') ?? 'المنطقة' }}</th><td>{{ $captain->region?->name ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.captains.form.governorate') ?? 'المحافظة' }}</th><td>{{ $captain->governorate?->name ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.port') ?? 'الميناء' }}</th><td>{{ $captain->port?->name ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.registered_at') ?? 'تاريخ التسجيل' }}</th><td>{{ $captain->created_at?->format('Y-m-d H:i') ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.captains.table.status') }}</th><td>
                                    @if($captain->status == 1)
                                        <span class="badge bg-success">{{ __('admin.captains.status.active') ?? __('admin.status.active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('admin.captains.status.inactive') ?? __('admin.status.inactive') }}</span>
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
                    @if($captain->boat)
                        <table class="table table-detail table-bordered">
                            <tr><th class="text-muted">{{ __('admin.captains.table.boat_name') ?? 'اسم القارب' }}</th><td>{{ $captain->boat->name ?: '—' }}</td></tr>
                            <tr><th class="text-muted">{{ __('admin.boats.number') ?? 'رقم القارب' }}</th><td>{{ $captain->boat->number ?? '—' }}</td></tr>
                            <tr><th class="text-muted">{{ __('admin.captains.table.owner') ?? 'الصياد' }}</th><td>{{ $captain->boat->owner?->name ?? '—' }}</td></tr>
                            <tr><th class="text-muted">{{ __('admin.owner.status') ?? 'الحالة' }}</th><td>
                                @if($captain->boat->status == 1)
                                    <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                                @endif
                            </td></tr>
                            <tr><th class="text-muted">{{ __('admin.crew.table.boat') ?? 'عدد الطاقم' }}</th><td>{{ $captain->boat->crews->count() ?? 0 }}</td></tr>
                        </table>
                        <a href="{{ route('admin.boats.edit', $captain->boat->id) }}" class="btn btn-outline-primary btn-sm">{{ __('admin.actions.edit') }} {{ __('admin.captains.table.boat_name') }}</a>
                    @else
                        <p class="text-muted mb-0">{{ __('admin.captains.no_boat') ?? 'لا يوجد قارب مرتبط' }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- الرحلات --}}
        <div class="tab-pane fade" id="trips-pane" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if(isset($trips) && $trips->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-detail">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin.table.id') }}</th>
                                        <th>{{ __('admin.trips.table.name') ?? 'الرحلة' }}</th>
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
                                            <td>{{ $trip->start_date?->format('Y-m-d') ?? '—' }}</td>
                                            <td>
                                                @php $st = $trip->status ?? 0; @endphp
                                                @if($st == 2) <span class="badge bg-success">{{ __('admin.trips.status_completed') ?? 'مكتملة' }}</span>
                                                @elseif($st == 1) <span class="badge bg-info">{{ __('admin.trips.status_in_progress') ?? 'قيد التنفيذ' }}</span>
                                                @else <span class="badge bg-secondary">{{ __('admin.trips.status_pending') ?? 'قيد الانتظار' }}</span>
                                                @endif
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
                        <p class="text-muted mb-0">{{ __('admin.captains.no_trips') ?? 'لا توجد رحلات' }}</p>
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
