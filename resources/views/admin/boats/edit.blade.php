@extends('admin.layouts.master')
@section('title')
{{ __('admin.boats.edit.page_header') }}
@endsection
@section('content')
<div class="d-flex align-items-center mb-3">
    <div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.boats.index') }}">{{__('admin.menu.boats')}}</a></li>
            <li class="breadcrumb-item active">{{ __('admin.boats.edit.page_header') }}</li>
        </ul>
        <h1 class="page-header mb-0">{{ __('admin.boats.edit.page_header') }}: {{ $data->name }}</h1>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div id="formControls" class="mb-5">
    <div class="card">
        <div class="card-body pb-2">
            <form action="{{ route('admin.boats.update', $data->id) }}" method="post" id="editForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.owner')}} <span class="text-danger">*</span></label>
                        <select name="owner_id" required class="form-control">
                            <option value="">{{__('admin.actions.choose')}}</option>
                            @foreach($owners as $owner)
                            <option value="{{$owner->id}}" {{ old('owner_id', $data->owner_id) == $owner->id ? 'selected' : '' }}>{{$owner->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.name_ar')}} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control" required value="{{ old('name_ar', $data->name_ar) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.name_en')}}</label>
                        <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $data->name_en) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.category')}} <span class="text-danger">*</span></label>
                        <select name="boat_type_id" required class="form-control">
                            <option value="">{{__('admin.actions.choose')}}</option>
                            @foreach($boat_types as $boat_type)
                            <option value="{{$boat_type->id}}" {{ old('boat_type_id', $data->boat_type_id) == $boat_type->id ? 'selected' : '' }}>{{$boat_type->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.number')}} <span class="text-danger">*</span></label>
                        <input type="text" name="number" class="form-control" required value="{{ old('number', $data->number) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.status')}} <span class="text-danger">*</span></label>
                        <select name="status" required class="form-control">
                            <option value="1" {{ old('status', $data->status) == 1 ? 'selected' : '' }}>{{__('admin.status.active')}}</option>
                            <option value="0" {{ old('status', $data->status) == 0 ? 'selected' : '' }}>{{__('admin.status.inactive')}}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.length')}} <span class="text-danger">*</span></label>
                        <input type="number" name="length" class="form-control" required value="{{ old('length', $data->length) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.width')}} <span class="text-danger">*</span></label>
                        <input type="number" name="width" class="form-control" required value="{{ old('width', $data->width) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.color')}} <span class="text-danger">*</span></label>
                        <input type="text" name="color" class="form-control" required value="{{ old('color', $data->color) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.type')}}</label>
                        <input type="text" name="type" class="form-control" value="{{ old('type', $data->type) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.license_region')}} <span class="text-danger">*</span></label>
                        <select name="license_region_id" required class="form-control">
                            <option value="">{{__('admin.actions.choose')}}</option>
                            @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('license_region_id', $data->license_region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.license_date')}} <span class="text-danger">*</span></label>
                        <input type="date" name="license_date" required class="form-control" value="{{ old('license_date', \Illuminate\Support\Str::of($data->license_date)->substr(0,10)) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.license_date_expire')}} <span class="text-danger">*</span></label>
                        <input type="date" name="license_date_expire" required class="form-control" value="{{ old('license_date_expire', \Illuminate\Support\Str::of($data->license_date_expire)->substr(0,10)) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.body_number')}}</label>
                        <input type="text" name="body_number" class="form-control" value="{{ old('body_number', $data->body_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.body_type')}}</label>
                        <input type="text" name="body_type" class="form-control" value="{{ old('body_type', $data->body_type) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.callsign_number')}}</label>
                        <input type="text" name="callsign_number" class="form-control" value="{{ old('callsign_number', $data->callsign_number) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.serial_number')}}</label>
                        <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number', $data->serial_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.engine_type')}}</label>
                        <input type="text" name="engine_type" class="form-control" value="{{ old('engine_type', $data->engine_type) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.engine_power')}}</label>
                        <input type="text" name="engine_power" class="form-control" value="{{ old('engine_power', $data->engine_power) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.crew_number')}} <span class="text-danger">*</span></label>
                        <input type="number" required name="crew_number" class="form-control" value="{{ old('crew_number', $data->crew_number) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.payload')}} <span class="text-danger">*</span></label>
                        <input type="number" required step="0.01" name="payload" class="form-control" value="{{ old('payload', $data->payload) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.region')}} <span class="text-danger">*</span></label>
                        <select name="region_id" id="region_id" required class="form-control">
                            <option value="">{{__('admin.actions.choose')}}</option>
                            @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id', $data->region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.governorate')}} <span class="text-danger">*</span></label>
                        <select name="governorate_id" id="governorate_id" required class="form-control"
                            data-selected="{{ old('governorate_id', $data->governorate_id) }}">
                            <option value="{{ $data->governorate_id }}" selected>{{ optional($data->governorate)->name ?? $data->governorate_id }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{__('admin.boats.port')}} <span class="text-danger">*</span></label>
                        <select name="port_id" id="port_id" required class="form-control"
                            data-selected="{{ old('port_id', $data->port_id) }}">
                            <option value="{{ $data->port_id }}" selected>{{ optional($data->port)->name ?? $data->port_id }}</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">{{__('admin.actions.save')}}</button>
                    <a href="{{ route('admin.boats.index') }}" class="btn btn-secondary">{{__('admin.actions.cancel')}}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    (function () {
        const govUrl = "{{ url(app()->getLocale() . '/admin/getGovernorates') }}";
        const portUrl = "{{ url(app()->getLocale() . '/admin/getPorts') }}";
        const $region = $('#region_id'), $gov = $('#governorate_id'), $port = $('#port_id');
        const chooseTxt = "{{ __('admin.actions.choose') }}";

        function fill($sel, items, selected) {
            $sel.empty().append('<option value="">' + chooseTxt + '</option>');
            (items || []).forEach(function (it) {
                const id = it.id ?? it.value;
                const name = it.name ?? it.text ?? id;
                $sel.append('<option value="' + id + '"' + (String(id) === String(selected) ? ' selected' : '') + '>' + name + '</option>');
            });
        }

        function loadGov(regionId, selected, then) {
            if (!regionId) { return; }
            $.getJSON(govUrl + '/' + regionId, function (res) {
                fill($gov, Array.isArray(res) ? res : (res.data || []), selected);
                if (then) { then(); }
            });
        }
        function loadPort(govId, selected) {
            if (!govId) { return; }
            $.getJSON(portUrl + '/' + govId, function (res) {
                fill($port, Array.isArray(res) ? res : (res.data || []), selected);
            });
        }

        $region.on('change', function () { loadGov($(this).val(), null, function () { $port.empty().append('<option value="">' + chooseTxt + '</option>'); }); });
        $gov.on('change', function () { loadPort($(this).val(), null); });

        // Initial cascade: keep current gov/port selected.
        const curRegion = $region.val();
        const curGov = $gov.data('selected');
        const curPort = $port.data('selected');
        if (curRegion) { loadGov(curRegion, curGov, function () { if (curGov) { loadPort(curGov, curPort); } }); }
    })();
</script>
@endsection
