@extends('admin.layouts.master')
@section('title')
{{__('admin.boats.create')}}
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
            <li class="breadcrumb-item"><a href="{{ route('admin.boats.index') }}">{{__('admin.menu.boats')}}</a></li>
            <li class="breadcrumb-item active">{{__('admin.boats.create')}}</li>
        </ul>
        <h1 class="page-header mb-0">{{__('admin.boats.create')}}</h1>
    </div>
</div>
<div id="formControls" class="mb-5">
    <div class="card">
        <div class="card-body pb-2">
            <form action="{{ route('admin.boats.store') }}" method="post" id="createForm" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="owner_id" class="form-label">{{__('admin.boats.owner')}} <span class="text-danger">*</span></label>
                        <select name="owner_id" required class="form-control">
                            <option value="">{{__('admin.actions.choose')}}</option>
                            @foreach($owners as $owner)
                            <option value="{{$owner->id}}" {{ old('owner_id') == $owner->id ? 'selected' : '' }}>{{$owner->name}}</option>
                            @endforeach
                        </select>
                        @error('owner_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="name_ar" class="form-label">{{__('admin.boats.name_ar')}} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control" required value="{{ old('name_ar') }}">
                        @error('name_ar') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="name_en" class="form-label">{{__('admin.boats.name_en')}}</label>
                        <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}">
                        @error('name_en') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="boat_type_id" class="form-label">{{__('admin.boats.category')}} <span class="text-danger">*</span></label>
                        <select name="boat_type_id" required class="form-control">
                            <option value="">{{__('admin.actions.choose')}}</option>
                            @foreach($boat_types as $boat_type)
                            <option value="{{$boat_type->id}}" {{ old('boat_type_id') == $boat_type->id ? 'selected' : '' }}>{{$boat_type->name}}</option>
                            @endforeach
                        </select>
                        @error('boat_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="number" class="form-label">{{__('admin.boats.number')}} <span class="text-danger">*</span></label>
                        <input type="text" name="number" class="form-control" required value="{{ old('number') }}">
                        @error('number') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">{{__('admin.boats.status')}} <span class="text-danger">*</span></label>
                        <select name="status" required class="form-control">
                            <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>{{__('admin.status.active')}}</option>
                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>{{__('admin.status.inactive')}}</option>
                        </select>
                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="length" class="form-label">{{__('admin.boats.length')}} <span class="text-danger">*</span></label>
                        <input type="number" name="length" class="form-control" required value="{{ old('length') }}">
                        @error('length') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="width" class="form-label">{{__('admin.boats.width')}} <span class="text-danger">*</span></label>
                        <input type="number" name="width" class="form-control" required value="{{ old('width') }}">
                        @error('width') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="color" class="form-label">{{__('admin.boats.color')}} <span class="text-danger">*</span></label>
                        <input type="text" name="color" class="form-control" required value="{{ old('color') }}">
                        @error('color') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="type" class="form-label">{{__('admin.boats.type')}}</label>
                        <input type="text" name="type" class="form-control" value="{{ old('type') }}">
                        @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="license_region_id" class="form-label">{{__('admin.boats.license_region')}} <span class="text-danger">*</span></label>
                        <select name="license_region_id" required class="form-control">
                            <option value="">{{__('admin.actions.choose')}}</option>
                            @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('license_region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                        @error('license_region_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="license_date" class="form-label">{{__('admin.boats.license_date')}} <span class="text-danger">*</span></label>
                        <input type="date" name="license_date" required class="form-control" value="{{ old('license_date') }}">
                        @error('license_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="license_date_expire" class="form-label">{{__('admin.boats.license_date_expire')}} <span class="text-danger">*</span></label>
                        <input type="date" name="license_date_expire" required class="form-control" value="{{ old('license_date_expire') }}">
                        @error('license_date_expire') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="body_number" class="form-label">{{__('admin.boats.body_number')}}</label>
                        <input type="text" name="body_number" class="form-control" value="{{ old('body_number') }}">
                        @error('body_number') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="body_type" class="form-label">{{__('admin.boats.body_type')}}</label>
                        <input type="text" name="body_type" class="form-control" value="{{ old('body_type') }}">
                        @error('body_type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="callsign_number" class="form-label">{{__('admin.boats.callsign_number')}}</label>
                        <input type="text" name="callsign_number" class="form-control" value="{{ old('callsign_number') }}">
                        @error('callsign_number') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="serial_number" class="form-label">{{__('admin.boats.serial_number')}}</label>
                        <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number') }}">
                        @error('serial_number') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="engine_type" class="form-label">{{__('admin.boats.engine_type')}}</label>
                        <input type="text" name="engine_type" class="form-control" value="{{ old('engine_type') }}">
                        @error('engine_type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="engine_power" class="form-label">{{__('admin.boats.engine_power')}}</label>
                        <input type="text" name="engine_power" class="form-control" value="{{ old('engine_power') }}">
                        @error('engine_power') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="crew_number" class="form-label">{{__('admin.boats.crew_number')}} <span class="text-danger">*</span></label>
                        <input type="number" required name="crew_number" class="form-control" value="{{ old('crew_number') }}">
                        @error('crew_number') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="payload" class="form-label">{{__('admin.boats.payload')}} <span class="text-danger">*</span></label>
                        <input type="number" required step="0.01" name="payload" class="form-control" value="{{ old('payload') }}">
                        @error('payload') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="region_id" class="form-label">{{__('admin.boats.region')}} <span class="text-danger">*</span></label>
                        <select name="region_id" id="region_id" required class="form-control">
                            <option value="">{{__('admin.actions.choose')}}</option>
                            @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                        @error('region_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="governorate_id" class="form-label">{{__('admin.boats.governorate')}} <span class="text-danger">*</span></label>
                        <select name="governorate_id" id="governorate_id" required class="form-control">
                            <option value="">{{__('admin.actions.choose')}}</option>
                        </select>
                        @error('governorate_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="port_id" class="form-label">{{__('admin.boats.port')}} <span class="text-danger">*</span></label>
                        <select name="port_id" id="port_id" required class="form-control">
                            <option value="">{{__('admin.actions.choose')}}</option>
                        </select>
                        @error('port_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">{{__('admin.actions.save')}}</button>
                    <a href="{{ route('admin.boats.index') }}" class="btn btn-secondary">{{__('admin.actions.cancel')}}</a>
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
<script src="{{asset('dashboard/assets/js/jquery.validate.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>
<script>
    $("#createForm").validate();
</script>
@endsection
