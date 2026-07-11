@extends('owner.layouts.master')

@section('title', __('owner.analysis_reports.assets_register.title'))

@section('content')
    @php
        $assets = $register['assets'];
        $totals = $register['totals'];
        $typeLabels = [
            'boat' => __('owner.assets.boat'),
            'fishing_equipment' => __('owner.assets.fishing_equipment'),
            'other' => __('owner.assets.other'),
        ];
        $statusMeta = [
            'active' => ['label' => __('owner.assets.active'), 'class' => 'success'],
            'sold' => ['label' => __('owner.assets.sold'), 'class' => 'secondary'],
            'damaged' => ['label' => __('owner.assets.damaged'), 'class' => 'danger'],
        ];
        $printQuery = array_filter([
            'boat_id' => $filters['boat_id'],
            'type' => $filters['type'],
        ]);
    @endphp

    <div class="d-flex flex-wrap gap-2 align-items-start justify-content-between mb-3">
        <div>
            <h2 class="mb-1">{{ __('owner.analysis_reports.assets_register.title') }}</h2>
            <p class="text-muted mb-0">{{ __('owner.analysis_reports.assets_register.subtitle') }}</p>
        </div>
        <a href="{{ route('owner.assets.register-print', $printQuery) }}" target="_blank" class="btn btn-success">
            <i class="bi bi-printer"></i> {{ __('owner.analysis_reports.assets_register.print') }}
        </a>
    </div>

    <form method="GET" action="{{ route('owner.assets.register') }}" class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="row align-items-end gy-2">
                <div class="col-md-4">
                    <label class="form-label">{{ __('owner.analysis_reports.assets_register.filter_type') }}</label>
                    <select name="type" class="form-control form-select">
                        <option value="">{{ __('owner.analysis_reports.assets_register.all_types') }}</option>
                        @foreach ($typeLabels as $value => $label)
                            <option value="{{ $value }}" @selected($filters['type'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('owner.analysis_reports.assets_register.filter_boat') }}</label>
                    <select name="boat_id" class="form-control form-select">
                        <option value="">{{ __('owner.analysis_reports.assets_register.all_boats') }}</option>
                        @foreach ($boats as $boat)
                            <option value="{{ $boat->id }}" @selected($filters['boat_id'] === $boat->id)>{{ $boat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-theme">
                        <i class="bi bi-search"></i> {{ __('owner.analysis_reports.assets_register.show') }}
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="row g-3 mb-3">
        @foreach ([
            ['label' => __('owner.analysis_reports.assets_register.stats.total_cost'), 'value' => number_format($totals['cost'], 2), 'color' => 'primary', 'money' => true],
            ['label' => __('owner.analysis_reports.assets_register.stats.accumulated'), 'value' => number_format($totals['accumulated'], 2), 'color' => 'warning', 'money' => true],
            ['label' => __('owner.analysis_reports.assets_register.stats.book_value'), 'value' => number_format($totals['book_value'], 2), 'color' => 'success', 'money' => true],
            ['label' => __('owner.analysis_reports.assets_register.stats.count'), 'value' => (string) $totals['count'], 'color' => 'info'],
        ] as $card)
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">{{ $card['label'] }}</div>
                        <div class="h4 mb-0 text-{{ $card['color'] }}">{{ $card['value'] }}@if (!empty($card['money'])) <x-riyal-icon size="sm" />@endif</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.analysis_reports.assets_register.columns.asset') }}</th>
                        <th>{{ __('owner.analysis_reports.assets_register.columns.type') }}</th>
                        <th>{{ __('owner.analysis_reports.assets_register.columns.boat') }}</th>
                        <th>{{ __('owner.analysis_reports.assets_register.columns.purchase_date') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.assets_register.columns.cost') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.assets_register.columns.salvage') }}</th>
                        <th class="text-center">{{ __('owner.analysis_reports.assets_register.columns.useful_life') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.assets_register.columns.monthly') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.assets_register.columns.accumulated') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.assets_register.columns.book_value') }}</th>
                        <th class="text-center">{{ __('owner.analysis_reports.assets_register.columns.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assets as $asset)
                        @php $status = $statusMeta[$asset['status']] ?? ['label' => $asset['status'], 'class' => 'secondary']; @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $asset['name'] }}</td>
                            <td>{{ $typeLabels[$asset['type']] ?? $asset['type'] }}</td>
                            <td>{{ $asset['boat'] ?? '—' }}</td>
                            <td>{{ $asset['purchase_date'] ?? '—' }}</td>
                            <td class="text-end">{{ number_format($asset['purchase_cost'], 2) }} <x-riyal-icon size="sm" /></td>
                            <td class="text-end">{{ number_format($asset['salvage_value'], 2) }} <x-riyal-icon size="sm" /></td>
                            <td class="text-center">{{ $asset['useful_life_years'] }}</td>
                            <td class="text-end">{{ number_format($asset['monthly'], 2) }} <x-riyal-icon size="sm" /></td>
                            <td class="text-end">{{ number_format($asset['accumulated'], 2) }} <x-riyal-icon size="sm" /></td>
                            <td class="text-end fw-bold">{{ number_format($asset['book_value'], 2) }} <x-riyal-icon size="sm" /></td>
                            <td class="text-center"><span class="badge bg-{{ $status['class'] }}">{{ $status['label'] }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="text-center text-muted py-4">{{ __('owner.analysis_reports.assets_register.no_data') }}</td></tr>
                    @endforelse
                </tbody>
                @if (count($assets))
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="5">{{ __('owner.analysis_reports.assets_register.total') }}</td>
                            <td class="text-end">{{ number_format($totals['cost'], 2) }} <x-riyal-icon size="sm" /></td>
                            <td class="text-end">{{ number_format($totals['salvage'], 2) }} <x-riyal-icon size="sm" /></td>
                            <td></td>
                            <td></td>
                            <td class="text-end">{{ number_format($totals['accumulated'], 2) }} <x-riyal-icon size="sm" /></td>
                            <td class="text-end">{{ number_format($totals['book_value'], 2) }} <x-riyal-icon size="sm" /></td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
