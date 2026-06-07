{{-- ================== TAB: ADD BOAT ================== --}}
<div class="d-flex align-items-center mb-4">
    <h4 class="mb-0">{{ __('owner.boats.create.title') }}</h4>
</div>

<div class="card">
    <div class="card-body pb-2">
        <form action="{{ route('owner.boats.store') }}" method="post" id="addBoatForm" enctype="multipart/form-data">
            @csrf

            {{-- Row 1 --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.name_ara') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name_ar" class="form-control" required value="{{ old('name_ar') }}">
                    @error('name_ar') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.name_eng') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name_en" class="form-control" required value="{{ old('name_en') }}">
                    @error('name_en') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.class') }} <span class="text-danger">*</span></label>
                    <select name="boat_type_id" required class="form-control">
                        <option value="">{{ __('owner.actions.choose') }}</option>
                        @foreach ($boatTypes as $boat_type)
                            <option value="{{ $boat_type->id }}" {{ old('boat_type_id') == $boat_type->id ? 'selected' : '' }}>
                                {{ $boat_type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('boat_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.boat_number') }} <span class="text-danger">*</span></label>
                    <input type="text" name="number" class="form-control" required value="{{ old('number') }}">
                    @error('number') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Row 2 --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.status') }} <span class="text-danger">*</span></label>
                    <select name="status" required class="form-control">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>{{ __('owner.status.active') }}</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>{{ __('owner.status.inactive') }}</option>
                    </select>
                    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.length') }} <span class="text-danger">*</span></label>
                    <input type="number" name="length" class="form-control" required value="{{ old('length') }}">
                    @error('length') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.width') }} <span class="text-danger">*</span></label>
                    <input type="number" name="width" class="form-control" required value="{{ old('width') }}">
                    @error('width') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.color') }} <span class="text-danger">*</span></label>
                    <input type="text" name="color" class="form-control" required value="{{ old('color') }}">
                    @error('color') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Row 3 --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.type') }} <span class="text-danger">*</span></label>
                    <input type="text" name="type" class="form-control" required value="{{ old('type') }}">
                    @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.license_region') }} <span class="text-danger">*</span></label>
                    <select name="license_region_id" required class="form-control">
                        <option value="">-- {{ __('owner.boats.select_region') }} --</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region->id }}" {{ old('license_region_id') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('license_region_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.license_date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="license_date" required class="form-control" value="{{ old('license_date') }}">
                    @error('license_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.license_date_expire') }} <span class="text-danger">*</span></label>
                    <input type="date" name="license_date_expire" required class="form-control" value="{{ old('license_date_expire') }}">
                    @error('license_date_expire') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Row 4 --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.structure_number') }} <span class="text-danger">*</span></label>
                    <input type="text" name="body_number" required class="form-control" value="{{ old('body_number') }}">
                    @error('body_number') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.structure_type') }} <span class="text-danger">*</span></label>
                    <input type="text" name="body_type" required class="form-control" value="{{ old('body_type') }}">
                    @error('body_type') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.callsign_number') }} <span class="text-danger">*</span></label>
                    <input type="text" name="callsign_number" required class="form-control" value="{{ old('callsign_number') }}">
                    @error('callsign_number') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Row 5 --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.serial_number') }} <span class="text-danger">*</span></label>
                    <input type="text" name="serial_number" required class="form-control" value="{{ old('serial_number') }}">
                    @error('serial_number') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.engine_type') }} <span class="text-danger">*</span></label>
                    <input type="text" required name="engine_type" class="form-control" value="{{ old('engine_type') }}">
                    @error('engine_type') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.engine_power') }} <span class="text-danger">*</span></label>
                    <input type="text" required name="engine_power" class="form-control" value="{{ old('engine_power') }}">
                    @error('engine_power') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Row 6 --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.crew_count') }} <span class="text-danger">*</span></label>
                    <input type="number" required name="crew_number" class="form-control" value="{{ old('crew_number') }}">
                    @error('crew_number') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.cargo_capacity') }} <span class="text-danger">*</span></label>
                    <input type="number" required step="0.01" name="payload" class="form-control" value="{{ old('payload') }}">
                    @error('payload') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.region') }} <span class="text-danger">*</span></label>
                    <select name="region_id" id="addBoat_region_id" required class="form-control">
                        <option value="">-- {{ __('owner.boats.select_region') }} --</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('region_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('owner.boats.governorate') }} <span class="text-danger">*</span></label>
                    <select name="governorate_id" id="addBoat_governorate_id" required class="form-control">
                        <option value="">-- {{ __('owner.boats.select_governorate') }} --</option>
                        @if (old('region_id') && old('governorate_id'))
                            @php
                                $oldGovernorates = \App\Models\Governorate::where('region_id', old('region_id'))->get();
                            @endphp
                            @foreach ($oldGovernorates as $gov)
                                <option value="{{ $gov->id }}" {{ old('governorate_id') == $gov->id ? 'selected' : '' }}>
                                    {{ $gov->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('governorate_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3 mt-3">
                    <label class="form-label">{{ __('owner.boats.port') }} <span class="text-danger">*</span></label>
                    <select name="port_id" id="addBoat_port_id" required class="form-control">
                        <option value="">-- {{ __('owner.boats.select_port') }} --</option>
                        @if (old('port_id') && old('governorate_id'))
                            @php
                                $oldPorts = \App\Models\Port::where('governorate_id', old('governorate_id'))->get();
                            @endphp
                            @foreach ($oldPorts as $port)
                                <option value="{{ $port->id }}" {{ old('port_id') == $port->id ? 'selected' : '' }}>
                                    {{ $port->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('port_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4 mb-3">
                <button type="submit" class="btn btn-outline-theme">{{ __('owner.actions.save') }}</button>
                <a href="{{ route('owner.boats.index') }}" class="btn btn-outline-default">{{ __('owner.actions.cancel') }}</a>
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
