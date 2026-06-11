@extends('owner.layouts.master')
@section('title')
    {{ __('owner.catch.manage_title') }} - {{ __('owner.catch.add_catch') }}
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
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('owner/catch') }}">{{ __('owner.catch.manage_title') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.catch.add_catch') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.catch.add_catch') }}</h1>
        </div>
    </div>
    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">
                <form action="{{ route('owner.catch.store') }}" method="post" id="createForm"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-xl-4">
                            <div class="form-group">
                                <label for="trip_id" class="form-label">{{ __('owner.catch.trip_id') }}<span
                                        class="text-danger">*</span></label>
                                <select name="trip_id" id="trip_id" class="form-control" required>
                                    <option value="">{{ __('owner.actions.choose') }}</option>
                                    @foreach ($trips as $trip)
                                        <option value="{{ $trip->id }}"
                                            {{ old('trip_id', request('trip_id')) == $trip->id ? 'selected' : '' }}>
                                            {{ $trip->name }}</option>
                                    @endforeach
                                </select>
                                @error('trip_id')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="form-group">
                                <label for="boat_name" class="form-label">{{ __('owner.catch.boat_name') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="boat_name" id="boat_name" class="form-control" disabled
                                    value="{{ old('boat_name', $selectedTrip->boat_name ?? '') }}">
                                <input type="hidden" name="boat_id" id="boat_id"
                                    value="{{ old('boat_id', $selectedTrip->boat_id ?? '') }}">
                                @error('boat_name')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                                @error('boat_id')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-2 fw-bold">
                        <div class="col-md-5">{{ __('owner.catch.fish') }}</div>
                        <div class="col-md-3">{{ __('owner.catch.weight') }}</div>
                        <div class="col-md-3">{{ __('owner.catch.unit') }}</div>
                        <div class="col-md-1"></div>
                    </div>

                    <div id="fish-wrapper">
                        <div class="row fish-row mb-2">
                            <div class="col-md-5">
                                <select name="fish_id[]" class="form-select" required>
                                    <option value="">{{ __('owner.catch.choose_fish') }}</option>
                                    @foreach ($fish as $f)
                                        <option value="{{ $f->id }}">{{ $f->scientific_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <input type="number" step="0.01" name="weight[]" class="form-control weight"
                                    placeholder="{{ __('owner.catch.weight') }}" required>
                            </div>

                            <div class="col-md-3">
                                <select name="unit_id[]" class="form-select" required>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-1 text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-row">✕</button>
                            </div>

                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-secondary" onclick="addFishRow()">+
                        {{ __('owner.catch.add_type') }}</button>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">{{ __('owner.actions.save') }}</button>
                        <a href="{{ route('owner.boats.index') }}"
                            class="btn btn-secondary">{{ __('owner.actions.cancel') }}</a>
                    </div>
                </form>
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
    {{-- <script>
    function calculateRowTotal(row) {
        let weight = parseFloat(row.find('.weight').val()) || 0;
        let pricePerKg = parseFloat(row.find('.price-per-kg').val()) || 0;
        row.find('.total-price').val((weight * pricePerKg).toFixed(2));
    }

    $(document).on('input', '.weight, .price-per-kg', function () {
        let row = $(this).closest('.fish-row');
        calculateRowTotal(row);
    });

    function addFishRow() {
        let row = $('.fish-row:first').clone();
        row.find('input').val('');
        $('#fish-wrapper').append(row);
    }

     $(document).on('click', '.remove-row', function () {
        if ($('.fish-row').length > 1) {
            $(this).closest('.fish-row').remove();
            toggleRemoveButtons();
        }
    });

    function toggleRemoveButtons() {
        let rows = $('.fish-row');
        rows.find('.remove-row').toggle(rows.length > 1);
    }

    // عند تحميل الصفحة
    $(document).ready(function () {
        toggleRemoveButtons();
    });

</script> --}}

    <script>
        function addFishRow() {
            let row = $('.fish-row:first').clone(false);

            row.find('input').val('');
            row.find('select').prop('selectedIndex', 0);

            $('#fish-wrapper').append(row);
            updateRemoveButtons();
        }

        $(document).on('click', '.remove-row', function() {
            if ($('.fish-row').length > 1) {
                $(this).closest('.fish-row').remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            let rows = $('.fish-row');

            // أخفِ زر الحذف في أول صف فقط
            rows.find('.remove-row').show();
            rows.first().find('.remove-row').hide();
        }

        $(document).ready(function() {
            updateRemoveButtons();
        });
    </script>
@endsection
