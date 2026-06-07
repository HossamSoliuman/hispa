{{-- ================== TAB: BOATS ================== --}}

{{-- Boats Management Header --}}
<div class="d-flex align-items-center mb-3">
    <h4 class="mb-0">{{ __('owner.boats.title') }}</h4>
    <div class="ms-auto">
        <button class="btn btn-outline-theme" type="button"
            data-bs-toggle="collapse" data-bs-target="#addBoatSection" aria-expanded="false">
            <i class="fa fa-plus-circle fa-fw me-1"></i> {{ __('owner.boats.add_boat') }}
        </button>
    </div>
</div>

{{-- Add Boat Collapsible Form --}}
<div class="collapse mb-4 {{ $errors->any() && old('_form') === 'add_boat' ? 'show' : '' }}" id="addBoatSection">
    <div class="card">
        <div class="card-body pb-2">
            <form action="{{ route('owner.boats.store') }}" method="post" id="addBoatForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_form" value="add_boat">
                <input type="hidden" name="redirect_to" value="{{ route('owner.settings.index') . '?tab=boats' }}">

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
                    <button type="button" class="btn btn-outline-default"
                        data-bs-toggle="collapse" data-bs-target="#addBoatSection">
                        {{ __('owner.actions.cancel') }}
                    </button>
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

{{-- Boats DataTable --}}
<table id="boatsSettingsTable" class="table table-sm table-bordered table-hover text-center small-text">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('owner.boats.name') }}</th>
            <th>{{ __('owner.boats.class') }}</th>
            <th>{{ __('owner.boats.type') }}</th>
            <th>{{ __('owner.boats.captain') }}</th>
            <th>{{ __('owner.boats.status') }}</th>
            <th>{{ __('owner.boats.actions') }}</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<hr class="my-4">

{{-- ================== BOAT TYPES SUBSECTION ================== --}}
<div class="d-flex align-items-center mb-3">
    <h5 class="mb-0">{{ __('owner.boat_type.page_header') }}</h5>
    <div class="ms-auto">
        <a href="#boatTypeModalCreate" data-bs-toggle="modal" class="btn btn-outline-theme btn-equal">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('owner.boat_type.add_boat_type') }}
        </a>
    </div>
</div>

<!-- CREATE -->
<div class="modal fade" id="boatTypeModalCreate">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('owner.boat-types.store') }}" method="POST" id="boatTypeCreateForm">
                @csrf
                <input type="hidden" name="tab" value="boats">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('owner.boat_type.create.title') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.boat_type.name_ar') }} *</label>
                            <input type="text" name="name_ar" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('owner.boat_type.name_en') }} *</label>
                            <input type="text" name="name_en" class="form-control" required>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="status" value="1" checked class="form-check-input">
                                <label class="form-check-label">{{ __('admin.boat_type.activate') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('owner.actions.close') }}</button>
                    <button class="btn btn-outline-theme">{{ __('owner.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT -->
<div class="modal fade" id="boatTypeEditModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('owner.boat-types.update', 'update') }}" method="POST" id="boatTypeEditForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="boatType_id">
                <input type="hidden" name="tab" value="boats">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('owner.boat_type.edit.title') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.boat_type.name_ar') }}</label>
                            <input type="text" name="name_ar" id="boatType_name_ar" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('owner.boat_type.name_en') }}</label>
                            <input type="text" name="name_en" id="boatType_name_en" class="form-control" required>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="status" id="boatType_status" value="1" class="form-check-input">
                                <label class="form-check-label">{{ __('admin.boat_type.activate') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('owner.actions.close') }}</button>
                    <button class="btn btn-outline-theme">{{ __('owner.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DELETE -->
<div class="modal fade" id="boatTypeDeleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('owner.boat-types.destroy', 'delete') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tab" value="boats">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.swal.confirm_title') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="text-danger">{{ __('admin.swal.confirm_title') }}</h6>
                    <input type="hidden" name="id" id="boatType_delete_id">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('owner.actions.close') }}</button>
                    <button class="btn btn-danger">{{ __('admin.actions.delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TABLE -->
<table id="boatTypesTable" class="table table-sm table-bordered table-hover text-center small-text">
    <thead>
        <tr>
            <th>{{ __('owner.boat_type.name') }}</th>
            <th>{{ __('owner.boat_type.status') }}</th>
            <th>{{ __('owner.boat_type.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($boatTypes as $boatType)
            <tr>
                <td>{{ $boatType->name_ar }}</td>
                <td>
                    @if ($boatType->status == 1)
                        <span class="badge bg-success">{{ __('owner.boat_type.status_active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('owner.boat_type.status_inactive') }}</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-outline-theme btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#boatTypeEditModal"
                        data-id="{{ $boatType->id }}"
                        data-name_ar="{{ $boatType->name_ar }}"
                        data-name_en="{{ $boatType->name_en }}"
                        data-status="{{ $boatType->status }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#boatTypeDeleteModal"
                        data-id="{{ $boatType->id }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<hr class="my-4">

{{-- ================== CREW SUBSECTION ================== --}}
<div class="d-flex align-items-center mb-3">
    <h5 class="mb-0">{{ __('owner.crew.title') }}</h5>
    <div class="ms-auto">
        <a href="{{ route('owner.crew.create') }}" class="btn btn-outline-theme btn-equal">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('owner.crew.add_new') }}
        </a>
    </div>
</div>

<table id="crewSettingsTable" class="table table-sm table-bordered table-hover text-center small-text">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('owner.crew.table.name') }}</th>
            <th>{{ __('owner.crew.table.email') }}</th>
            <th>{{ __('owner.crew.table.phone') }}</th>
            <th>{{ __('owner.crew.table.nationality') }}</th>
            <th>{{ __('owner.crew.table.id_number') }}</th>
            <th>{{ __('owner.crew.table.job_title') }}</th>
            <th>{{ __('owner.crew.table.boat') }}</th>
            <th>{{ __('owner.crew.table.region') }}</th>
            <th>{{ __('owner.crew.table.governorate') }}</th>
            <th>{{ __('owner.crew.table.port') }}</th>
            <th>{{ __('owner.crew.table.status') }}</th>
            <th>{{ __('owner.crew.table.actions') }}</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
