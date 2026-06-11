@extends('owner.layouts.master')
@section('title')
    {{ __('owner.catch.manage_title') }} - {{ __('owner.catch.edit_catch') }}
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
                <li class="breadcrumb-item active">{{ __('owner.catch.edit_catch') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.catch.edit_catch') }}</h1>
        </div>
    </div>
    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">
                <form action="{{ route('owner.catch.update', $catch->id) }}" method="post" id="createForm"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-xl-4">
                            <div class="form-group">
                                <label for="trip_id" class="form-label">{{ __('owner.catch.trip_id') }}<span
                                        class="text-danger">*</span></label>
                                <select name="trip_id" id="trip_id" class="form-control" required>
                                    <option value="">{{ __('owner.actions.choose') }}</option>
                                    @foreach ($trips as $trip)
                                        <option value="{{ $trip->id }}"
                                            {{ old('trip_id', $catch->trip_id) == $trip->id ? 'selected' : '' }}>
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
                        @php($details = old('fish_id') ? collect(old('fish_id'))->map(fn ($id, $i) => ['fish_id' => $id, 'weight' => old('weight')[$i] ?? '', 'unit_id' => old('unit_id')[$i] ?? null]) : $catch->details->map(fn ($d) => ['fish_id' => $d->fish_id, 'weight' => $d->weight, 'unit_id' => $d->unit_id]))
                        @forelse ($details as $detail)
                            <div class="row fish-row mb-2">
                                <div class="col-md-5">
                                    <select name="fish_id[]" class="form-select" required>
                                        <option value="">{{ __('owner.catch.choose_fish') }}</option>
                                        @foreach ($fish as $f)
                                            <option value="{{ $f->id }}"
                                                {{ $detail['fish_id'] == $f->id ? 'selected' : '' }}>
                                                {{ $f->scientific_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <input type="number" step="0.01" name="weight[]" class="form-control weight"
                                        placeholder="{{ __('owner.catch.weight') }}" value="{{ $detail['weight'] }}"
                                        required>
                                </div>

                                <div class="col-md-3">
                                    <select name="unit_id[]" class="form-select" required>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                {{ $detail['unit_id'] == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-1 text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-row">✕</button>
                                </div>
                            </div>
                        @empty
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
                        @endforelse
                    </div>

                    <button type="button" class="btn btn-sm btn-secondary" onclick="addFishRow()">+
                        {{ __('owner.catch.add_type') }}</button>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">{{ __('owner.actions.save') }}</button>
                        <a href="{{ route('owner.catch.index') }}"
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
        $(document).ready(function() {
            let baseUrl = "{{ LaravelLocalization::localizeUrl('/') }}";

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
        });
    </script>

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

            rows.find('.remove-row').show();
            rows.first().find('.remove-row').hide();
        }

        $(document).ready(function() {
            updateRemoveButtons();
        });
    </script>
@endsection
