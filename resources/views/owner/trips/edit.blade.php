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
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('owner.trips.start_date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_date" value="{{ old('start_date', optional($trip->start_date)->format('Y-m-d\TH:i')) }}" class="form-control" required>
                                @error('start_date') <span class="text-danger error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('owner.trips.end_date') }}</label>
                                <input type="datetime-local" name="end_date" value="{{ old('end_date', optional($trip->end_date)->format('Y-m-d\TH:i')) }}" class="form-control">
                                @error('end_date') <span class="text-danger error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    @php
                        $selectedBoats = old('boats', $trip->tripBoats->map(fn ($tb) => [
                            'boat_id' => $tb->boat_id,
                            'captain_ids' => $tb->captains->pluck('id')->all(),
                        ])->all());
                        if (empty($selectedBoats) && $trip->boat_id) {
                            $selectedBoats = [['boat_id' => $trip->boat_id, 'captain_ids' => array_values(array_filter([$trip->captain_id]))]];
                        }
                    @endphp
                    @include('owner.trips._boats_fields', ['boats' => $boats, 'captains' => $captains, 'selected' => $selectedBoats])

                    <div class="row mb-3 mt-3">
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
    </script>
    @include('owner.trips._boats_script')
@endsection
