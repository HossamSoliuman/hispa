@extends('admin.layouts.master')

@section('title')
    {{ display_string(__('admin.boat_types.show_title'), 'تفاصيل نوع القارب') }} - {{ $data->name }}
@endsection

@section('css')
    <style>
        .table-detail th { white-space: nowrap; width: 1%; }
        .table-detail td { vertical-align: middle; }
    </style>
@endsection

@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">
                {{ display_string(__('admin.boat_types.show_title'), 'تفاصيل نوع القارب') }}: {{ $data->name }}
            </h2>
            <p class="text-muted mb-0 small">
                {{ display_string(__('admin.boat_types.boats_count'), 'عدد القوارب') }}: {{ $data->boats_count ?? 0 }}
            </p>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.boat_types.edit', $data->id) }}" class="btn btn-primary btn-equal">
                <i class="bi bi-pencil"></i> {{ display_string(__('admin.actions.edit'), 'تعديل') }}
            </a>
            <a href="{{ route('admin.boat_types.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ display_string(__('admin.menu.boat_types'), 'أنواع القوارب') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- إحصائيات سريعة --}}
    <div class="row mb-4">
        @include('admin.components.stat-card', [
            'title' => display_string(__('admin.boat_types.boats_count'), 'عدد القوارب'),
            'value' => '<span class="num">' . (int)($data->boats_count ?? 0) . '</span>',
            'icon' => 'bi bi-signpost-split',
            'gradient' => 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
            'stats' => [],
        ])
        @include('admin.components.stat-card', [
            'title' => display_string(__('admin.boat_types.status'), 'الحالة'),
            'value' => $data->status == 1
                ? '<span class="badge bg-success">' . display_string(__('admin.status.active'), 'نشط') . '</span>'
                : '<span class="badge bg-danger">' . display_string(__('admin.status.inactive'), 'غير نشط') . '</span>',
            'icon' => $data->status == 1 ? 'bi bi-check-circle-fill' : 'bi bi-x-circle-fill',
            'gradient' => $data->status == 1 ? 'linear-gradient(135deg, #198754, #20c997)' : 'linear-gradient(135deg, #dc3545, #fd7e14)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
            'stats' => [],
        ])
        @include('admin.components.stat-card', [
            'title' => display_string(__('admin.boat_types.name'), 'الاسم'),
            'value' => '<span class="text-white">' . e($data->name ?: '—') . '</span>',
            'icon' => 'bi bi-tag-fill',
            'gradient' => 'linear-gradient(135deg, #6f42c1, #0dcaf0)',
            'colClass' => 'col-md-4 col-sm-6 mb-3',
            'stats' => [],
        ])
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            {{-- تفاصيل نوع القارب --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-info-circle me-2"></i> {{ display_string(__('admin.boat_types.details'), 'تفاصيل النوع') }}
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-detail m-0">
                        <tbody>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.boat_types.name_ar'), 'الاسم بالعربية') }}</th>
                                <td>{{ $data->name_ar ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.boat_types.name_en'), 'الاسم بالإنجليزية') }}</th>
                                <td>{{ $data->name_en ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.boat_types.status'), 'الحالة') }}</th>
                                <td>
                                    @if($data->status == 1)
                                        <span class="badge bg-success">{{ display_string(__('admin.status.active'), 'نشط') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ display_string(__('admin.status.inactive'), 'غير نشط') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.boat_types.created_at'), 'تاريخ الإنشاء') }}</th>
                                <td>{{ $data->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            {{-- قائمة القوارب من هذا النوع --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-signpost-2 me-2"></i> {{ display_string(__('admin.boat_types.boats_of_type'), 'القوارب من هذا النوع') }}</span>
                    <span class="badge bg-light text-dark">{{ $data->boats->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($data->boats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-detail m-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ display_string(__('admin.boat_types.name'), 'اسم القارب') }}</th>
                                        <th>{{ display_string(__('admin.trips.owner'), 'الصياد') }}</th>
                                        <th>{{ display_string(__('admin.boat_types.status'), 'الحالة') }}</th>
                                        <th>{{ display_string(__('admin.actions.actions'), 'الإجراءات') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data->boats as $boat)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $boat->name ?: ($boat->number ?? '—') }}</td>
                                            <td>{{ $boat->owner?->name ?? '—' }}</td>
                                            <td>
                                                @if($boat->status == 1)
                                                    <span class="badge bg-success">{{ display_string(__('admin.status.active'), 'نشط') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ display_string(__('admin.status.inactive'), 'غير نشط') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.boats.show', $boat->id) }}" class="btn btn-sm btn-outline-primary" title="{{ display_string(__('admin.actions.view'), 'عرض') }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.boats.edit', $boat->id) }}" class="btn btn-sm btn-outline-success" title="{{ display_string(__('admin.actions.edit'), 'تعديل') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($data->boats_count > $data->boats->count())
                            <div class="card-footer text-muted small text-center">
                                {{ display_string(__('admin.filters.showing_last'), 'عرض آخر') }} {{ $data->boats->count() }} {{ display_string(__('admin.menu.boats'), 'قارب') }}
                                · <a href="{{ route('admin.boats.index') }}?boat_type_id={{ $data->id }}">{{ display_string(__('admin.boat_types.view_all_boats'), 'عرض الكل') }}</a>
                            </div>
                        @endif
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-signpost-2 display-4 d-block mb-2"></i>
                            <p class="mb-0">{{ display_string(__('admin.boat_types.no_boats'), 'لا توجد قوارب مسجلة من هذا النوع') }}</p>
                            <a href="{{ route('admin.boats.create') }}" class="btn btn-sm btn-outline-primary mt-2">{{ display_string(__('admin.boat_types.add'), 'إضافة قارب') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
