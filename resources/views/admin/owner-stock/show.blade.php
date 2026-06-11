@extends('admin.layouts.master')

@section('title')
    {{ __('admin.owner_stock_details.title') }} - {{ $owner->name }}
@endsection

@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <style>
        .small-text th, .small-text td { font-size: 12px; text-align: center !important; vertical-align: middle; }
    </style>
@endsection

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.menu.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.owner-stock.index') }}">{{ __('admin.owner_stocks.breadcrumb_owner') }}</a></li>
                <li class="breadcrumb-item active">{{ $owner->name }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.owner_stock_details.title') }}</h1>
            <p class="text-muted mb-0 small">{{ __('admin.owner_stock_fish_quantity.subtitle_detail') }}: {{ $owner->name }}</p>
        </div>
    </div>

    {{-- Filters (same as owner/fish-quntity) --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.owner-stock.show', $owner->id) }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.owner_stock_fish_quantity.from_date') }}</label>
                    <input type="date" name="from" class="form-control" value="{{ $from }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.owner_stock_fish_quantity.to_date') }}</label>
                    <input type="date" name="to" class="form-control" value="{{ $to }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.owner_stock_fish_quantity.boat') }}</label>
                    <select name="boat_id" class="form-select">
                        <option value="">{{ __('admin.owner_stock_fish_quantity.all_boats') }}</option>
                        @foreach($boats as $boat)
                            <option value="{{ $boat->id }}" {{ $boatId == $boat->id ? 'selected' : '' }}>{{ $boat->name ?? $boat->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.owner_stock_fish_quantity.trip') }}</label>
                    <select name="trip_id" class="form-select">
                        <option value="">{{ __('admin.owner_stock_fish_quantity.all_trips') }}</option>
                        @foreach($trips as $trip)
                            <option value="{{ $trip->id }}" {{ $tripId == $trip->id ? 'selected' : '' }}>{{ $trip->name ?? $trip->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.owner_stock_fish_quantity.fish') }}</label>
                    <select name="fish_id" class="form-select">
                        <option value="">{{ __('admin.owner_stock_fish_quantity.all_fish') }}</option>
                        @foreach($fishs as $fish)
                            <option value="{{ $fish->id }}" {{ $fishId == $fish->id ? 'selected' : '' }}>{{ $fish->name ?? $fish->name_ar ?? $fish->scientific_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">{{ __('admin.owner_stock_fish_quantity.apply') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary --}}
    <div class="row mb-3 g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <div class="card-body d-flex justify-content-between align-items-center text-white p-3">
                    <div>
                        <h6 class="mb-1 opacity-90">{{ __('admin.owner_stock_fish_quantity.total_quantity') }}</h6>
                        <h4 class="mb-0" id="detail_total_quantity">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
                <div class="card-body d-flex justify-content-between align-items-center text-white p-3">
                    <div>
                        <h6 class="mb-1 opacity-90">{{ __('admin.owner_stock_fish_quantity.total_value') }}</h6>
                        <h4 class="mb-0" id="detail_total_value">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('admin.owner_stock_fish_quantity.table_title') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="ownerStockDetailTable" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('admin.owner_stocks.table.id') }}</th>
                            <th>{{ __('admin.owner_stock_fish_quantity.boat') }}</th>
                            <th>{{ __('admin.owner_stock_fish_quantity.trip') }}</th>
                            <th>{{ __('admin.owner_stock_fish_quantity.date') }}</th>
                            <th>{{ __('admin.owner_stocks.table.fish_name') }}</th>
                            <th>{{ __('owner.catch.unit') }}</th>
                            <th>{{ __('admin.owner_stock_fish_quantity.quantity') }}</th>
                            <th>{{ __('admin.owner_stock_fish_quantity.price_per_kg') }}</th>
                            <th>{{ __('admin.owner_stock_fish_quantity.total_price') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function () {
            const appLocale = '{{ app()->getLocale() }}';
            const noData = @json(__('admin.owner_stocks.statistics.no_data'));
            const languageOptions = (appLocale === 'ar') ? { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" } : {};

            const params = new URLSearchParams(window.location.search);
            const from = params.get('from') || "{{ $from }}";
            const to = params.get('to') || "{{ $to }}";
            const boatId = params.get('boat_id') || '';
            const tripId = params.get('trip_id') || '';
            const fishId = params.get('fish_id') || '';

            if ($.fn.DataTable.isDataTable('#ownerStockDetailTable')) {
                $('#ownerStockDetailTable').DataTable().destroy();
            }

            $('#ownerStockDetailTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.owner-stock.detail-data', $owner->id) }}",
                    data: function () {
                        return { from: from, to: to, boat_id: boatId, trip_id: tripId, fish_id: fishId };
                    },
                    dataSrc: function (json) {
                        const qty = parseFloat(json.total_quantity) || 0;
                        $('#detail_total_quantity').text(qty.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        const val = parseFloat(json.total_value) || 0;
                        $('#detail_total_value').text(val.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        return json.data ?? [];
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
                    { data: 'boat_name', name: 'boat_name' },
                    { data: 'trip_name', name: 'trip_name' },
                    { data: 'stock_date', name: 'stock_date' },
                    { data: 'fish_name', name: 'fish_name' },
                    { data: 'unit_name', name: 'unit_name' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'price_per_kg', name: 'price_per_kg' },
                    { data: 'total_price', name: 'total_price' }
                ],
                order: [[3, 'desc']],
                responsive: true,
                pageLength: 25,
                language: Object.assign({}, languageOptions, { emptyTable: noData })
            });
        });
    </script>
@endsection
