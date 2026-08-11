@extends('admin.layouts.master')

@section('title')
    {{ __('admin.trips.show.title') }} - @displaySafe($data->number ?? $data->id)
@endsection

@section('css')
    <style>
        /* The layout pins `th { text-align: center !important }`, so body cells
           are centred too — otherwise every header sits off its column. */
        .detail-table th,
        .detail-table td {
            font-size: 12.5px;
            text-align: center;
            vertical-align: middle;
            overflow-wrap: anywhere;
        }

        .detail-table th {
            font-weight: 600;
            opacity: .85;
        }

        /* Fixed label column so the label/value divider sits at the same x in
           every stacked card instead of shifting with the longest label. */
        .detail-table--kv,
        .detail-table--quad {
            table-layout: fixed;
        }

        .detail-table--kv th {
            width: 42%;
        }

        .detail-table--quad th {
            width: 22%;
        }

        .person-card {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        /* The icon sits underneath the photo, so a missing/《404》 logo degrades
           to the role glyph instead of a broken-image box. */
        .person-avatar {
            position: relative;
            overflow: hidden;
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(var(--hud-accent-rgb), .08);
            color: var(--hud-accent);
            font-size: 18px;
        }

        .person-avatar img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .person-role {
            font-size: 11px;
            letter-spacing: .3px;
            text-transform: uppercase;
            opacity: .55;
        }
    </style>
@endsection

@php
    $catch = $data->catches;
    $catchDetails = $catch?->details ?? collect();
    $catchValue = (float) $catchDetails->sum('total_price');
    $catchWeightText = $catchDetails->isNotEmpty() ? formatWeightByUnit($catchDetails) : '0';

    $sales = $data->sales;
    $expenses = $financials['expenses'];
    $crewMembers = $financials['crew_members'];

    $riyal = view('components.riyal-icon', ['size' => 'sm'])->render();
    $money = fn ($amount) => new \Illuminate\Support\HtmlString(
        '<span class="text-nowrap">' . number_format((float) $amount, 2) . ' ' . $riyal . '</span>',
    );

    $people = [
        ['role' => __('admin.trips.show.owner'), 'user' => $data->owner, 'icon' => 'bi-person-badge', 'route' => 'admin.owner.show'],
        ['role' => __('admin.trips.show.captain'), 'user' => $data->captain ?? $data->boat?->captain, 'icon' => 'bi-person-walking', 'route' => 'admin.captain.show'],
        ['role' => __('admin.trips.show.counter'), 'user' => $data->counter, 'icon' => 'bi-clipboard-data', 'route' => null],
    ];
@endphp

@section('content')
    {{-- ── Page header ───────────────────────────────────────────── --}}
    <div class="row mb-3 align-items-center justify-content-between g-3">
        <div class="col-lg-7">
            <ul class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.menu.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.trips.index') }}">{{ __('admin.trips.show.breadcrumb_manage') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.trips.show.breadcrumb_show') }}</li>
            </ul>
            <h2 class="fw-bold text-dark mb-1 d-flex flex-wrap align-items-center gap-2">
                {{ __('admin.trips.show.title') }}:
                @if (display_string($data->number) !== '—')
                    <span dir="ltr">#{{ $data->number }}</span>
                @else
                    <span dir="ltr">#{{ $data->id }}</span>
                @endif
                <span class="badge bg-{{ $data->status->color() }} align-middle">{{ $data->status->label() }}</span>
            </h2>
            <p class="text-muted small mb-0">
                <i class="bi bi-person-badge me-1"></i>{{ display_string($data->owner?->name) }}
                <span class="mx-2 opacity-50">|</span>
                <i class="bi bi-tsunami me-1"></i>{{ display_string($data->boat_name ?: $data->boat?->name) }}
                <span class="mx-2 opacity-50">|</span>
                <i class="bi bi-calendar3 me-1"></i>{{ $data->start_date?->format('Y-m-d') ?? '—' }} — {{ $data->end_date?->format('Y-m-d') ?? '—' }}
            </p>
        </div>
        <div class="col-lg-5 text-lg-end">
            <a href="{{ route('admin.trips.edit', $data->id) }}" class="btn btn-primary btn-equal">
                <i class="bi bi-pencil"></i> {{ __('admin.actions.edit') }}
            </a>
            <a href="{{ route('admin.trips.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.trips.index_title') }}
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($data->status === \App\Enums\TripStatus::Cancelled && $data->cancel_reason)
        <div class="alert alert-danger d-flex align-items-start gap-2">
            <i class="bi bi-x-octagon-fill mt-1"></i>
            <div>
                <div class="fw-bold">{{ __('admin.trips.show.cancel_reason') }}</div>
                <div class="small">{{ $data->cancel_reason }}</div>
            </div>
        </div>
    @endif

    {{-- ── KPI row ───────────────────────────────────────────────── --}}
    <div class="row g-3 mb-2">
        @include('owner.components.stat-card', [
            'title' => __('admin.trips.show.catch_weight'),
            'value' => $catchWeightText,
            'icon' => 'bi bi-box-seam',
            'colClass' => 'col-6 col-lg-3 mb-3',
            'footer' => __('admin.trips.show.items_count') . ': ' . $catchDetails->count(),
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.trips.show.total_income'),
            'value' => $money($financials['total_income']),
            'icon' => 'bi bi-cash-coin',
            'colClass' => 'col-6 col-lg-3 mb-3',
            'footer' => __('admin.trips.show.sales_count') . ': ' . $sales->count(),
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.trips.show.total_costs'),
            'value' => $money($financials['total_costs']),
            'icon' => 'bi bi-receipt',
            'colClass' => 'col-6 col-lg-3 mb-3',
            'footer' => __('admin.trips.show.expenses_count') . ': ' . $expenses->count(),
        ])
        @include('owner.components.stat-card', [
            'title' => __('admin.trips.show.net_profit'),
            'value' => $money($financials['net_profit']),
            'icon' => 'bi bi-graph-up-arrow',
            'colClass' => 'col-6 col-lg-3 mb-3',
            'trend' => $financials['net_profit'] >= 0 ? 'up' : 'down',
            'footer' => __('admin.trips.show.outstanding') . ': ' . number_format($financials['outstanding'], 2),
        ])
    </div>

    <div class="row g-4">
        {{-- ══ Main column ═══════════════════════════════════════════ --}}
        <div class="col-xl-8">

            {{-- المصيد --}}
            <div class="hud-section-head">
                <span class="ico"><i class="bi bi-droplet-fill"></i></span>
                <h5>{{ __('admin.trips.show.catch_details') }}</h5>
                <span class="hud-line"></span>
                <small>{{ __('admin.trips.show.items_count') }}: {{ $catchDetails->count() }}</small>
            </div>
            <div class="card mb-4">
                <div class="card-body p-0">
                    @if ($catchDetails->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:1%">#</th>
                                        <th>{{ __('admin.trips.show.fish') }}</th>
                                        <th>{{ __('admin.trips.show.weight') }}</th>
                                        <th>{{ __('admin.trips.show.unit') }}</th>
                                        <th>{{ __('admin.trips.show.price_per_unit') }}</th>
                                        <th>{{ __('admin.trips.show.total_price') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($catchDetails as $detail)
                                        <tr>
                                            <td class="text-muted">{{ $loop->iteration }}</td>
                                            <td class="fw-semibold">
                                                {{ $detail->fish?->name ?: display_string($detail->fish_name) }}
                                            </td>
                                            <td>{{ number_format((float) $detail->weight, 2) }}</td>
                                            <td>{{ $detail->unit?->name ?: __('admin.units.kg') }}</td>
                                            <td>{!! $money($detail->price_per_kg) !!}</td>
                                            <td class="fw-bold">{!! $money($detail->total_price) !!}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2">{{ __('admin.trips.show.total_weight') }}</th>
                                        <th colspan="2">{{ $catchWeightText }}</th>
                                        <th>{{ __('admin.trips.show.catch_value') }}</th>
                                        <th>{!! $money($catchValue) !!}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center small mb-0 py-4">
                            <i class="bi bi-inbox d-block fs-4 mb-2 opacity-50"></i>
                            {{ __('admin.trips.show.no_catch_data') }}
                        </p>
                    @endif
                </div>
            </div>

            @if ($catch)
                {{-- بيانات التحميل --}}
                <div class="card mb-4">
                    <div class="card-header fw-bold">
                        <i class="bi bi-truck me-2"></i>{{ __('admin.trips.show.catch_info') }}
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-0 detail-table detail-table--quad">
                                <tbody>
                                    <tr>
                                        <th>{{ __('admin.trips.show.catch_date') }}</th>
                                        <td>{{ $catch->catch_date?->format('Y-m-d H:i') ?? '—' }}</td>
                                        <th>{{ __('admin.trips.show.fish_source') }}</th>
                                        <td>@displaySafe($catch->fish_source)</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('admin.trips.show.car_type') }}</th>
                                        <td>@displaySafe($catch->car_type)</td>
                                        <th>{{ __('admin.trips.show.car_plate_number') }}</th>
                                        <td>@displaySafe($catch->car_plate_number)</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('admin.trips.show.driver_name') }}</th>
                                        <td>@displaySafe($catch->driver_name)</td>
                                        <th>{{ __('admin.trips.show.temperature') }}</th>
                                        <td>@displaySafe($catch->temperature)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- المبيعات --}}
            <div class="hud-section-head">
                <span class="ico"><i class="bi bi-cash-stack"></i></span>
                <h5>{{ __('admin.trips.show.sales_title') }}</h5>
                <span class="hud-line"></span>
                <small>{{ __('admin.trips.show.sales_count') }}: {{ $sales->count() }}</small>
            </div>
            <div class="card mb-4">
                <div class="card-body p-0">
                    @if ($sales->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:1%">#</th>
                                        <th>{{ __('admin.trips.show.sale_number') }}</th>
                                        <th>{{ __('admin.trips.show.customer') }}</th>
                                        <th>{{ __('admin.trips.show.sale_date') }}</th>
                                        <th>{{ __('admin.trips.show.amount') }}</th>
                                        <th>{{ __('admin.trips.show.remaining') }}</th>
                                        <th>{{ __('admin.trips.show.payment_status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td class="text-muted">{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('admin.sales.show', $sale->id) }}" class="fw-semibold">
                                                    {{ display_string($sale->number) }}
                                                </a>
                                            </td>
                                            <td>{{ display_string($sale->customer_name ?: $sale->customer?->name) }}</td>
                                            <td>{{ $sale->sale_datetime?->format('Y-m-d') ?? '—' }}</td>
                                            <td class="fw-bold">{!! $money($sale->total_price) !!}</td>
                                            <td class="{{ (float) $sale->remaining_total > 0 ? 'text-danger' : '' }}">
                                                {!! $money($sale->remaining_total) !!}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $sale->payment_status === 'paid' ? 'success' : ($sale->payment_status === 'partially_paid' ? 'warning text-dark' : 'danger') }}">
                                                    {{ \App\Models\Sale::paymentStatusText($sale->payment_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4">{{ __('admin.trips.show.total_income') }}</th>
                                        <th>{!! $money($sales->sum('total_price')) !!}</th>
                                        <th>{!! $money($sales->sum('remaining_total')) !!}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center small mb-0 py-4">
                            <i class="bi bi-inbox d-block fs-4 mb-2 opacity-50"></i>
                            {{ __('admin.trips.show.no_sales') }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- المصاريف --}}
            <div class="hud-section-head">
                <span class="ico"><i class="bi bi-receipt-cutoff"></i></span>
                <h5>{{ __('admin.trips.show.expenses_title') }}</h5>
                <span class="hud-line"></span>
                <small>{{ __('admin.trips.show.expenses_count') }}: {{ $expenses->count() }}</small>
            </div>
            <div class="card mb-4">
                <div class="card-body p-0">
                    @if ($expenses->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:1%">#</th>
                                        <th>{{ __('admin.trips.show.expense_number') }}</th>
                                        <th>{{ __('admin.trips.show.expense_date') }}</th>
                                        <th>{{ __('admin.trips.show.category') }}</th>
                                        <th>{{ __('admin.trips.show.vendor') }}</th>
                                        <th>{{ __('admin.trips.show.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $expense)
                                        <tr>
                                            <td class="text-muted">{{ $loop->iteration }}</td>
                                            <td class="fw-semibold">{{ display_string($expense->number) }}</td>
                                            <td>{{ display_string($expense->date) }}</td>
                                            <td>{{ display_string($expense->category?->name) }}</td>
                                            <td>{{ display_string($expense->vendor?->name) }}</td>
                                            <td class="fw-bold">{!! $money($expense->final_price) !!}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="5">{{ __('admin.trips.show.total_costs') }}</th>
                                        <th>{!! $money($expenses->sum('final_price')) !!}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center small mb-0 py-4">
                            <i class="bi bi-inbox d-block fs-4 mb-2 opacity-50"></i>
                            {{ __('admin.trips.show.no_expenses') }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- الملخص المالي + أنصبة الطاقم --}}
            <div class="hud-section-head">
                <span class="ico"><i class="bi bi-calculator"></i></span>
                <h5>{{ __('admin.trips.show.financial_summary') }}</h5>
                <span class="hud-line"></span>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-body p-0">
                            <table class="table table-bordered table-sm align-middle mb-0 detail-table detail-table--kv">
                                <tbody>
                                    <tr>
                                        <th>{{ __('admin.trips.show.total_income') }}</th>
                                        <td class="text-success fw-bold">{!! $money($financials['total_income']) !!}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('admin.trips.show.total_costs') }}</th>
                                        <td class="text-danger fw-bold">{!! $money($financials['total_costs']) !!}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('admin.trips.show.outstanding') }}</th>
                                        <td>{!! $money($financials['outstanding']) !!}</td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <th>{{ __('admin.trips.show.net_profit') }}</th>
                                        <td class="{{ $financials['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {!! $money($financials['net_profit']) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('admin.trips.show.owner_share') }} (50%)</th>
                                        <td>{!! $money($financials['owner_share']) !!}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('admin.trips.show.crew_share') }} (50%)</th>
                                        <td>{!! $money($financials['crew_share']) !!}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-header fw-bold">
                            <i class="bi bi-people me-2"></i>{{ __('admin.trips.show.crew_shares') }}
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-sm align-middle text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin.trips.show.member_name') }}</th>
                                        <th>{{ __('admin.trips.show.role') }}</th>
                                        <th>{{ __('admin.trips.show.percentage') }}</th>
                                        <th>{{ __('admin.trips.show.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($crewMembers as $member)
                                        <tr>
                                            <td class="fw-semibold">{{ $member['name'] }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $member['role'] === 'captain' ? __('admin.trips.show.captain') : __('admin.menu.crew') }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($member['percent'], 2) }}%</td>
                                            <td class="fw-bold">{!! $money($member['due']) !!}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted small py-4">
                                                {{ __('admin.trips.show.no_crew') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ Side column ═══════════════════════════════════════════ --}}
        <div class="col-xl-4">

            {{-- تفاصيل الرحلة --}}
            <div class="hud-section-head">
                <span class="ico"><i class="bi bi-signpost-split"></i></span>
                <h5>{{ __('admin.trips.show.trip_details') }}</h5>
                <span class="hud-line"></span>
            </div>
            <div class="card mb-4">
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm align-middle mb-0 detail-table detail-table--kv">
                        <tbody>
                            <tr>
                                <th>{{ __('admin.trips.show.trip_number') }}</th>
                                <td>@displaySafe($data->number)</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.name') }}</th>
                                <td>@displaySafe($data->name)</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.license_number') }}</th>
                                <td>@displaySafe($data->license_number)</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.permit_type') }}</th>
                                <td>@displaySafe($data->permit_type)</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.status') }}</th>
                                <td><span class="badge bg-{{ $data->status->color() }}">{{ $data->status->label() }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.date_depart_return') }}</th>
                                <td>{{ $data->start_date?->format('Y-m-d') ?? '—' }} — {{ $data->end_date?->format('Y-m-d') ?? '—' }}</td>
                            </tr>
                            @if ($data->duration_text)
                                <tr>
                                    <th>{{ __('admin.trips.show.duration') }}</th>
                                    <td>{{ $data->duration_text }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>{{ __('admin.trips.show.actual_start') }}</th>
                                <td>{{ $data->actual_start_datetime?->format('Y-m-d H:i') ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.actual_end') }}</th>
                                <td>{{ $data->actual_end_datetime?->format('Y-m-d H:i') ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.crew_count') }}</th>
                                <td>@displaySafe($data->crew_count)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- معلومات الموقع --}}
            <div class="hud-section-head">
                <span class="ico"><i class="bi bi-geo-alt"></i></span>
                <h5>{{ __('admin.trips.show.location_info') }}</h5>
                <span class="hud-line"></span>
            </div>
            <div class="card mb-4">
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm align-middle mb-0 detail-table detail-table--kv">
                        <tbody>
                            <tr>
                                <th>{{ __('admin.trips.show.region') }}</th>
                                <td>@displaySafe($data->region?->name)</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.governorate') }}</th>
                                <td>@displaySafe($data->governorate?->name)</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.port') }}</th>
                                <td>@displaySafe($data->port?->name)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- معلومات القارب --}}
            <div class="hud-section-head">
                <span class="ico"><i class="bi bi-tsunami"></i></span>
                <h5>{{ __('admin.trips.show.boat_info') }}</h5>
                <span class="hud-line"></span>
            </div>
            <div class="card mb-4">
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm align-middle mb-0 detail-table detail-table--kv">
                        <tbody>
                            <tr>
                                <th>{{ __('admin.trips.boat_name.0') }}</th>
                                <td>
                                    @if ($data->boat)
                                        <a href="{{ route('admin.boats.show', $data->boat->id) }}">
                                            {{ display_string($data->boat_name ?: $data->boat->name) }}
                                        </a>
                                    @else
                                        @displaySafe($data->boat_name)
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.boat_number') }}</th>
                                <td>@displaySafe($data->boat_number)</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.boat_color') }}</th>
                                <td>@displaySafe($data->boat_color)</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.boat_length') }}</th>
                                <td>@displaySafe($data->boat_length)</td>
                            </tr>
                            <tr>
                                <th>{{ __('admin.trips.show.boat_width') }}</th>
                                <td>@displaySafe($data->boat_width)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- أطراف الرحلة --}}
            <div class="hud-section-head">
                <span class="ico"><i class="bi bi-people"></i></span>
                <h5>{{ __('admin.trips.show.people_info') }}</h5>
                <span class="hud-line"></span>
            </div>
            <div class="card mb-4">
                <div class="list-group list-group-flush">
                    @foreach ($people as $person)
                        <div class="list-group-item bg-transparent">
                            <div class="person-card">
                                <span class="person-avatar rounded-circle border">
                                    <i class="bi {{ $person['icon'] }}"></i>
                                    @if ($person['user']?->logo)
                                        <img src="{{ asset($person['user']->logo) }}" alt="" onerror="this.remove()">
                                    @endif
                                </span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="person-role">
                                        <i class="bi {{ $person['icon'] }} me-1"></i>{{ $person['role'] }}
                                    </div>
                                    <div class="fw-bold text-truncate">
                                        {{ display_string($person['user']?->name, __('admin.trips.show.not_assigned')) }}
                                    </div>
                                    @if ($person['user']?->phone)
                                        <div class="text-muted small" dir="ltr">{{ $person['user']->phone }}</div>
                                    @endif
                                </div>
                                @if ($person['user'] && $person['route'])
                                    <a href="{{ route($person['route'], $person['user']->id) }}"
                                        class="btn btn-sm btn-outline-primary flex-shrink-0">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- مرفق التصريح --}}
            @if ($data->getRawOriginal('license_attachment'))
                <div class="hud-section-head">
                    <span class="ico"><i class="bi bi-paperclip"></i></span>
                    <h5>{{ __('admin.trips.show.license_attachment') }}</h5>
                    <span class="hud-line"></span>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <a href="{{ $data->license_attachment }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>{{ __('admin.trips.show.view_attachment') }}
                        </a>
                    </div>
                </div>
            @endif

            {{-- الملاحظات --}}
            <div class="hud-section-head">
                <span class="ico"><i class="bi bi-sticky"></i></span>
                <h5>{{ __('admin.trips.show.notes') }}</h5>
                <span class="hud-line"></span>
            </div>
            <div class="card mb-4">
                <div class="card-body text-muted small">
                    {!! nl2br(e(display_string($data->notes, __('admin.trips.show.no_notes')))) !!}
                </div>
            </div>
        </div>
    </div>
@endsection
