@extends('owner.layouts.master')
@section('title')
    {{ __('owner.trips.edit.title') }}
@endsection
@section('css')
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
                <li class="breadcrumb-item"><a href="{{ route('owner.trips.index') }}">{{ __('owner.trips.title') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.trips.edit.title') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.trips.edit.title') }}</h1>
        </div>
    </div>

    <div id="formControls" class="mb-5">
        <div class="card">
            <div class="card-body pb-2">
                <form action="{{ route('owner.trips.update', $trip->id) }}" method="post" id="editTripForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="owner_id" value="{{ $trip->owner_id }}">

                    <div class="row mb-3">
                        <div class="col-xl-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('owner.trips.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $trip->getRawOriginal('name')) }}" class="form-control" required placeholder="{{ __('owner.trips.name') }}">
                                @error('name') <span class="text-danger error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('owner.trips.name_en') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name_en" value="{{ old('name_en', $trip->name_en) }}" class="form-control" required placeholder="{{ __('owner.trips.name_en') }}">
                                @error('name_en') <span class="text-danger error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('owner.trips.license_number') }} <span class="text-danger">*</span></label>
                                <input type="text" name="license_number" value="{{ old('license_number', $trip->license_number) }}" class="form-control" required placeholder="{{ __('owner.trips.license_number') }}">
                                @error('license_number') <span class="text-danger error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-xl-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('owner.trips.start_date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_date" value="{{ old('start_date', optional($trip->start_date)->format('Y-m-d\TH:i')) }}" class="form-control" required>
                                @error('start_date') <span class="text-danger error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('owner.trips.end_date') }}</label>
                                <input type="datetime-local" name="end_date" value="{{ old('end_date', optional($trip->end_date)->format('Y-m-d\TH:i')) }}" class="form-control">
                                @error('end_date') <span class="text-danger error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('owner.trips.captain_name') }} <span class="text-danger">*</span></label>
                                <select name="captain_id" id="captain_id" class="form-control" required>
                                    <option value="">{{ __('owner.actions.choose') }}</option>
                                    @foreach($captains as $captain)
                                        <option value="{{ $captain->id }}" {{ old('captain_id', $trip->captain_id) == $captain->id ? 'selected' : '' }}>{{ $captain->name }}</option>
                                    @endforeach
                                </select>
                                @error('captain_id') <span class="text-danger error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('owner.trips.boat_name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="boat_name" id="boat_name" class="form-control" readonly value="{{ old('boat_name', $trip->boat_name) }}" placeholder="{{ __('owner.trips.boat_name') }}">
                                <input type="hidden" name="boat_id" id="boat_id" value="{{ old('boat_id', $trip->boat_id) }}">
                                @error('boat_name') <span class="text-danger error">{{ $message }}</span> @enderror
                                @error('boat_id') <span class="text-danger error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('owner.trips.notes') }}</label>
                                <textarea name="notes" class="form-control" placeholder="{{ __('owner.trips.notes') }}">{{ old('notes', $trip->notes) }}</textarea>
                                @error('notes') <span class="text-danger error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">{{ __('owner.actions.save') }}</button>
                        <a href="{{ route('owner.trips.index') }}" class="btn btn-secondary">{{ __('owner.actions.cancel') }}</a>
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
    <script src="{{ asset('dashboard/assets/js/jquery.validate.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>

    <script>
        $("#editTripForm").validate();

        $(document).ready(function () {
            let baseUrl = "{{ LaravelLocalization::localizeUrl('/') }}";
            $('#captain_id').on('change', function () {
                let captainId = $(this).val();
                if (!captainId) {
                    $('#boat_id').val('');
                    $('#boat_name').val('');
                    return;
                }
                $.get(`${baseUrl}/owner/getBoatInfo/${captainId}`, function (data) {
                    $('#boat_id').val(data.boat_id);
                    $('#boat_name').val(data.boat_name);
                }).fail(function () {
                    console.error('Failed to load boat info');
                });
            });
        });
    </script>
@endsection
