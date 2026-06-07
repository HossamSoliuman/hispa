{{-- ================== TAB: TRIPS ================== --}}

<h4 class="mb-3">{{ __('owner.trips.title') }}</h4>

<div class="card">
    <div class="card-body pb-2">
        <form action="{{ route('owner.trips.store') }}" method="post" id="addTripForm">
            @csrf
            <input type="hidden" name="_form" value="add_trip">
            <input type="hidden" name="redirect_to" value="{{ route('owner.settings.index') . '?tab=trips' }}">
            <input type="hidden" name="owner_id" value="{{ auth()->user()->getAuthIdentifier() }}">

            <div class="row mb-3">
                <div class="col-xl-4">
                    <div class="form-group">
                        <label class="form-label">{{ __('owner.trips.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required placeholder="{{ __('owner.trips.name') }}">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="form-group">
                        <label class="form-label">{{ __('owner.trips.name_en') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control" required placeholder="{{ __('owner.trips.name_en') }}">
                        @error('name_en') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="form-group">
                        <label class="form-label">{{ __('owner.trips.license_number') }} <span class="text-danger">*</span></label>
                        <input type="text" name="license_number" value="{{ old('license_number') }}" class="form-control" required placeholder="{{ __('owner.trips.license_number') }}">
                        @error('license_number') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-xl-4">
                    <div class="form-group">
                        <label class="form-label">{{ __('owner.trips.start_date') }} <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date', now()->format('Y-m-d\TH:i')) }}" class="form-control" required>
                        @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="form-group">
                        <label class="form-label">{{ __('owner.trips.captain_name') }} <span class="text-danger">*</span></label>
                        <select name="captain_id" id="settings_captain_id" class="form-control" required>
                            <option value="">{{ __('owner.actions.choose') }}</option>
                            @foreach($captains as $captain)
                                <option value="{{ $captain->id }}" {{ old('captain_id') == $captain->id ? 'selected' : '' }}>{{ $captain->name }}</option>
                            @endforeach
                        </select>
                        @error('captain_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="form-group">
                        <label class="form-label">{{ __('owner.trips.boat_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="boat_name" id="settings_boat_name" class="form-control" readonly value="{{ old('boat_name') }}" placeholder="{{ __('owner.trips.boat_name') }}">
                        <input type="hidden" name="boat_id" id="settings_boat_id" value="{{ old('boat_id') }}">
                        @error('boat_name') <span class="text-danger">{{ $message }}</span> @enderror
                        @error('boat_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-xl-12">
                    <div class="form-group">
                        <label class="form-label">{{ __('owner.trips.notes') }}</label>
                        <textarea name="notes" class="form-control" placeholder="{{ __('owner.trips.notes') }}">{{ old('notes') }}</textarea>
                        @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-4 mb-3">
                <button type="submit" class="btn btn-success">{{ __('owner.actions.save') }}</button>
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
