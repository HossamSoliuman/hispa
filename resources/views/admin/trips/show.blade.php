@extends('admin.layouts.master')

@section('title')
    {{ display_string(__('admin.trips.show_title'), 'تفاصيل الرحلة') }} - @displaySafe($data->number ?? $data->id)
@endsection

@section('css')
    <style>
        .table-detail th { white-space: nowrap; width: 1%; }
    </style>
@endsection

@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">
                {{ display_string(__('admin.trips.show_title'), 'تفاصيل الرحلة') }}:
                @if(display_string($data->name) !== '—')
                    @displaySafe($data->name)
                @else
                    #{{ $data->id }}
                @endif
            </h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.trips.edit', $data->id) }}" class="btn btn-primary btn-equal">
                <i class="bi bi-pencil"></i> {{ display_string(__('admin.actions.edit'), 'تعديل') }}
            </a>
            <a href="{{ route('admin.trips.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ display_string(__('admin.trips.index_title'), 'الرحلات') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- المصيد --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-droplet-fill me-2"></i> {{ display_string(__('admin.trips.catch_title'), 'المصيد') }} ({{ $data->fishQuantityStocks->count() }})
                </div>
                <div class="card-body">
                    @forelse($data->fishQuantityStocks as $stock)
                        <div class="row align-items-center py-2 border-bottom">
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="bg-light border rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                    <i class="bi bi-droplet-half text-success"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $stock->fish->scientific_name ?? $stock->fish_name ?? '—' }}</div>
                                    <div class="text-muted small">{{ number_format($stock->quantity ?? 0, 2) }} {{ display_string(__('admin.units.kg'), 'كغم') }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0 text-center">{{ display_string(__('admin.trips.no_catch'), 'لا يوجد مصيد') }}</p>
                    @endforelse
                </div>
                <div class="card-footer text-muted small text-center">
                    {{ display_string(__('admin.trips.total_items'), 'عدد العناصر') }}: {{ $data->fishQuantityStocks->count() }} |
                    {{ display_string(__('admin.trips.total_weight'), 'إجمالي الوزن') }}: {{ number_format($data->fishQuantityStocks->sum('quantity'), 2) }} {{ display_string(__('admin.units.kg'), 'كغم') }}
                </div>
            </div>

            {{-- تفاصيل الرحلة --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-signpost-split me-2"></i> {{ display_string(__('admin.trips.trip_details'), 'تفاصيل الرحلة') }}
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-detail m-0">
                        <tbody>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.trips.name'), 'الاسم') }}</th>
                                <td>@displaySafe($data->name)</td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.trips.license_number'), 'رقم الترخيص') }}</th>
                                <td>@displaySafe($data->license_number)</td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.trips.status'), 'الحالة') }}</th>
                                <td>
                                    <span class="badge bg-{{ $data->status->color() }}">{{ $data->status->label() }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.trips.departure_date'), 'تاريخ البداية') }}</th>
                                <td>{{ $data->start_date?->format('Y-m-d H:i') ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.trips.end_date'), 'تاريخ النهاية') }}</th>
                                <td>{{ $data->end_date?->format('Y-m-d H:i') ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- معلومات القارب --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-signpost-2 me-2"></i> {{ display_string(__('admin.trips.boat_info'), 'معلومات القارب') }}
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-detail m-0">
                        <tbody>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.trips.boat_name'), 'اسم القارب') }}</th>
                                <td>@displaySafe($data->boat_name)</td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.boats.number'), 'رقم القارب') }}</th>
                                <td>@displaySafe($data->boat_number)</td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.trips.boat_color'), 'لون القارب') }}</th>
                                <td>@displaySafe($data->boat_color)</td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ display_string(__('admin.trips.crew_count'), 'عدد الطاقم') }}</th>
                                <td>@displaySafe($data->crew_count)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- الملاحظات --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="bi bi-sticky me-2"></i> {{ display_string(__('admin.trips.notes'), 'الملاحظات') }}
                </div>
                <div class="card-body text-muted">
                    {!! nl2br(e(display_string($data->notes, display_string(__('admin.trips.no_notes'), '—')))) !!}
                </div>
            </div>

            {{-- الصياد --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-person-badge me-2"></i> {{ display_string(__('admin.trips.owner'), 'الصياد') }}
                </div>
                <div class="card-body d-flex align-items-center">
                    @if($data->owner)
                        <img src="{{ $data->owner->logo ? asset($data->owner->logo) : asset('default-logo.png') }}" alt="" class="rounded-circle border me-3" width="50" height="50" style="object-fit: cover;">
                        <div>
                            <div class="fw-bold">{{ $data->owner->name }}</div>
                            <div class="text-muted small">{{ $data->owner->phone ?? '—' }}</div>
                            <a href="{{ route('admin.owner.show', $data->owner->id) }}" class="btn btn-sm btn-outline-primary mt-1">{{ display_string(__('admin.actions.view'), 'عرض') }}</a>
                        </div>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>
            </div>

            {{-- الكابتن --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-person-walking me-2"></i> {{ display_string(__('admin.trips.captain'), 'الكابتن') }}
                </div>
                <div class="card-body d-flex align-items-center">
                    @if($data->captain)
                        <img src="{{ $data->captain->logo ? asset($data->captain->logo) : asset('default-logo.png') }}" alt="" class="rounded-circle border me-3" width="50" height="50" style="object-fit: cover;">
                        <div>
                            <div class="fw-bold">{{ $data->captain->name }}</div>
                            <div class="text-muted small">{{ $data->captain->phone ?? '—' }}</div>
                            <a href="{{ route('admin.captain.show', $data->captain->id) }}" class="btn btn-sm btn-outline-primary mt-1">{{ display_string(__('admin.actions.view'), 'عرض') }}</a>
                        </div>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
