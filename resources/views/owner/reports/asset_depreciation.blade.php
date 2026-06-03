@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.depreciation_report') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/tag-it/css/jquery.tagit.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/summernote/dist/summernote-lite.css') }}" rel="stylesheet">

    <style>
        label.error {
            color: red;
            font-weight: bold;
            margin-top: 5px;
            display: block;
        }
    </style>
@endsection
@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-2">{{ __('owner.generated.depreciation_report') }}</h2>
        </div>
    </div>



    <div id="formControls" class="mb-5">
        <div class="card">

            <div class="card-body pb-2">

                <form class="pl-form" method="GET" action="{{ route('owner.assetDepreciation') }}">
                    <div class="row align-items-end gy-2">
                        <div class="col-md-2">
                            <label>{{ __('owner.profit_loss.from_date') }}</label>
                            <input type="date" class="form-control" name="from" value="{{ $from }}">
                        </div>
                        <div class="col-md-2">
                            <label>{{ __('owner.profit_loss.to_date') }}</label>
                            <input type="date" class="form-control" name="to" value="{{ $to }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('owner.generated.asset_type') }}</label>
                            <select name="asset_type" id="asset_type" class="form-control">
                                <option value="">{{ __('owner.dalal_invoices.filters.all') }}</option>
                                <option value="boat" @selected(old('asset_type', $asset_type ?? '') == 'boat')>{{ __('owner.generated.boat') }}
                                </option>
                                <option value="fishing_equipment" @selected(old('asset_type', $asset_type ?? '') == 'fishing_equipment')>
                                    {{ __('owner.generated.fishing_equipment') }}</option>
                                <option value="other" @selected(old('asset_type', $asset_type ?? '') == 'other')>{{ __('owner.generated.other_asset') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('owner.generated.depreciation_method') }}</label>
                            <select name="depreciation_method" id="depreciation_method" class="form-control">
                                <option value="">{{ __('owner.dalal_invoices.filters.all') }}</option>
                                <option value="straight_line" @selected(old('depreciation_method', $depreciation_method ?? '') == 'straight_line')>
                                    {{ __('owner.generated.useful_life') }}</option>
                                <option value="percentage" @selected(old('depreciation_method', $depreciation_method ?? '') == 'percentage')>
                                    {{ __('owner.generated.percentage') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('owner.generated.asset_status') }}</label>
                            <select name="status" class="form-control">
                                <option value="">{{ __('owner.dalal_invoices.filters.all') }}</option>
                                <option value="active" @selected(old('status', $status ?? '') == 'active')>{{ __('owner.assets.active') }}
                                </option>
                                <option value="sold" @selected(old('status', $status ?? '') == 'sold')>{{ __('owner.assets.sold') }}</option>
                                <option value="damaged" @selected(old('status', $status ?? '') == 'damaged')>
                                    {{ __('owner.generated.damaged_or_written_off') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div style="align-self:end; display:flex; gap:8px;">
                                <button class="pl-btn btn btn-success sm me-2"
                                    type="submit">{{ __('owner.profit_loss.update') }}</button>
                            </div>
                        </div>
                    </div>
                </form>


                <div class="row mb-3 p-2">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('owner.generated.asset_name') }}</th>
                                <th>{{ __('owner.assets.type') }}</th>
                                <th>{{ __('owner.generated.purchase_date') }}</th>
                                <th>{{ __('owner.generated.purchase_cost') }}</th>
                                <th>{{ __('owner.generated.depreciation_method') }}</th>
                                <th>{{ __('owner.generated.last_year') }}</th>
                                <th>{{ __('owner.generated.year_depreciation') }}</th>
                                <th>{{ __('owner.generated.accumulated_depreciation') }}</th>
                                <th>{{ __('owner.generated.book_value') }}</th>
                                <th>{{ __('owner.assets.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                                @php
                                    $dep = $asset->latestDepreciation;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $asset->name }}</td>

                                    <td>
                                        @switch($asset->asset_type)
                                            @case('boat')
                                                {{ __('owner.generated.boat') }}
                                            @break

                                            @case('fishing_equipment')
                                                {{ __('owner.generated.fishing_equipment') }}
                                            @break

                                            @default
                                                {{ __('owner.generated.other_asset') }}
                                        @endswitch
                                    </td>

                                    {{-- <td>{{ $asset->purchase_date }}</td> --}}
                                    <td>{{ \Carbon\Carbon::parse($asset->purchase_date)->toDateString() }}</td>

                                    <td>{{ number_format($asset->purchase_cost, 2) }}</td>

                                    <td>
                                        {{ $asset->depreciation_method == 'straight_line' ? __('owner.generated.fixed') : __('owner.generated.relative') }}
                                    </td>

                                    <td>{{ $dep->year ?? '-' }}</td>

                                    <td>{{ number_format($dep->depreciation_amount ?? 0, 2) }}</td>

                                    <td>{{ number_format($dep->accumulated_depreciation ?? 0, 2) }}</td>

                                    <td>
                                        <strong>
                                            {{ number_format($dep->book_value ?? $asset->purchase_cost, 2) }}
                                        </strong>
                                    </td>

                                    <td>
                                        <span class="badge bg-{{ $asset->status == 'active' ? 'success' : 'secondary' }}">
                                            @if ($asset->status == 'active')
                                                <span class="badge bg-success">{{ __('owner.assets.active') }}</span>
                                            @elseif ($asset->status == 'sold')
                                                <span class="badge bg-success">{{ __('owner.assets.sold') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('owner.assets.damaged') }}</span>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">
                                            {{ __('owner.dashboard.no_data') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
        </div>
    @endsection
    @section('script')
        <script src="{{ asset('dashboard/assets/plugins/jquery-migrate/dist/jquery-migrate.min.js') }}"></script>

        <script src="{{ asset('dashboard/assets/plugins/@highlightjs/cdn-assets/highlight.min.js') }}"></script>
        <script src="{{ asset('dashboard/assets/js/demo/highlightjs.demo.js') }}"></script>
        <script src="{{ asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js') }}"></script>
        <script src="{{ asset('dashboard/assets/js/jquery.validate.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>
        <script src="{{ asset('dashboard/assets/plugins/summernote/dist/summernote-lite.min.js') }}"></script>


        <script>
            $("#createForm").validate();
        </script>
        <script>
            function addFishRow() {
                let row = document.querySelector('.fish-row').cloneNode(true);
                row.querySelectorAll('input, select').forEach(el => el.value = '');
                document.getElementById('fish-wrapper').appendChild(row);
            }
        </script>
        <script>
            $(document).ready(function() {
                let baseUrl = "{{ LaravelLocalization::localizeUrl('/') }}";
                let oldRegionId = '{{ old('region_id') }}';
                let oldGovernorateId = '{{ old('governorate_id') }}';
                let oldPortId = '{{ old('port_id') }}';

                // تحميل المحافظات عند اختيار المنطقة
                $('#region_id').on('change', function() {
                    let regionId = $(this).val();
                    $('#governorate_id').empty().append('<option value="">{{ __('owner.loading') }}</option>');
                    $('#port_id').empty().append('<option value="">{{ __('owner.actions.choose') }}</option>');

                    if (regionId) {
                        $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace(
                            'REGION_ID', regionId), function(data) {
                            $('#governorate_id').empty().append(
                                '<option value="">{{ __('owner.actions.choose') }}</option>');
                            $.each(data, function(i, item) {
                                $('#governorate_id').append('<option value="' + item.id + '">' +
                                    item.name + '</option>');
                            });
                        });
                    }
                });

                // تحميل المدن عند اختيار المحافظة
                $('#governorate_id').on('change', function() {
                    let govId = $(this).val();
                    $('#port_id').empty().append('<option value="">{{ __('owner.loading') }}</option>');

                    if (govId) {
                        $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', govId),
                            function(data) {
                                $('#port_id').empty().append(
                                    '<option value="">{{ __('owner.actions.choose') }}</option>');
                                $.each(data, function(i, item) {
                                    $('#port_id').append('<option value="' + item.id + '">' + item
                                        .name + '</option>');
                                });
                            });
                    }
                });

                $('#trip_id').change(function() {
                    let tripId = $(this).val();

                    if (!tripId) {
                        $('[name="boat_id"], [name="boat_name"]').val('');
                        return;
                    }

                    let url = `${baseUrl}/owner/getBoatInfoByTrip/${tripId}`;
                    $.get(url, function(data) {
                        $('[name="boat_id"]').val(data.boat_id);
                        $('[name="boat_name"]').val(data.boat_name);
                    }).fail(function() {
                        console.error('Failed to load boat info');
                    });
                });

                // عند تحميل الصفحة إذا في old value للمنطقة والمحافظة والمدينة
                if (oldRegionId && !$('#governorate_id option:selected').val()) {
                    $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID',
                        oldRegionId), function(governorates) {
                        $('#governorate_id').empty().append(
                            '<option value="">{{ __('owner.actions.choose') }}</option>');
                        $.each(governorates, function(i, item) {
                            let selected = (item.id == oldGovernorateId) ? 'selected' : '';
                            $('#governorate_id').append('<option value="' + item.id + '" ' + selected +
                                '>' + item.name + '</option>');
                        });

                        if (oldGovernorateId) {
                            $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID',
                                oldGovernorateId), function(ports) {
                                $('#port_id').empty().append(
                                    '<option value="">{{ __('owner.actions.choose') }}</option>');
                                $.each(ports, function(i, item) {
                                    let selected = (item.id == oldPortId) ? 'selected' : '';
                                    $('#port_id').append('<option value="' + item.id + '" ' +
                                        selected + '>' + item.name + '</option>');
                                });
                            });
                        }
                    });
                }
            });
        </script>
    @endsection
