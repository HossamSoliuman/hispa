@extends('admin.layouts.master')
@section('title')
{{ __('admin.trips.edit_trip') }}
@endsection
@section('content')
@php
    $ownerBoats = \App\Models\Boat::where('owner_id', $data->owner_id)->select('id', 'name_ar', 'name_en')->get();
@endphp
<div class="d-flex align-items-center mb-3">
    <div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.trips.index') }}">{{__('admin.menu.trips')}}</a></li>
            <li class="breadcrumb-item active">{{ __('admin.trips.edit_trip') }}</li>
        </ul>
        <h1 class="page-header mb-0">{{ __('admin.trips.edit_trip') }}: {{ $data->name }}</h1>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card mb-5">
    <div class="card-body pb-2">
        <form action="{{ route('admin.trips.update', $data->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('admin.trips.name.0') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $data->name) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('admin.trips.name_en.0') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name_en" class="form-control" required value="{{ old('name_en', $data->name_en) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('admin.trips.license_number.0') }} <span class="text-danger">*</span></label>
                    <input type="text" name="license_number" class="form-control" required value="{{ old('license_number', $data->license_number) }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">{{__('admin.trips.permit_type') ?? 'نوع الترخيص'}} <span class="text-danger">*</span></label>
                    <input type="text" name="permit_type" class="form-control" required value="{{ old('permit_type', $data->permit_type) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{__('admin.boats.owner')}} <span class="text-danger">*</span></label>
                    <select name="owner_id" class="form-control" required>
                        <option value="">{{__('admin.actions.choose')}}</option>
                        @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" {{ old('owner_id', $data->owner_id) == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{__('admin.menu.captains')}} <span class="text-danger">*</span></label>
                    <select name="captain_id" class="form-control" required>
                        <option value="">{{__('admin.actions.choose')}}</option>
                        @foreach($captains as $captain)
                        <option value="{{ $captain->id }}" {{ old('captain_id', $data->captain_id) == $captain->id ? 'selected' : '' }}>{{ $captain->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">{{__('admin.menu.boats')}} <span class="text-danger">*</span></label>
                    <select name="boat_id" class="form-control" required>
                        <option value="">{{__('admin.actions.choose')}}</option>
                        @foreach($ownerBoats as $boat)
                        <option value="{{ $boat->id }}" {{ old('boat_id', $data->boat_id) == $boat->id ? 'selected' : '' }}>{{ $boat->name_ar ?? $boat->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('admin.trips.boat_name.0') }}</label>
                    <input type="text" name="boat_name" class="form-control" value="{{ old('boat_name', $data->boat_name) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{__('admin.boats.region')}} <span class="text-danger">*</span></label>
                    <select name="region_id" id="region_id" class="form-control" required>
                        <option value="">{{__('admin.actions.choose')}}</option>
                        @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ old('region_id', $data->region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">{{__('admin.boats.governorate')}} <span class="text-danger">*</span></label>
                    <select name="governorate_id" id="governorate_id" class="form-control" required data-selected="{{ old('governorate_id', $data->governorate_id) }}">
                        <option value="{{ $data->governorate_id }}" selected>{{ optional($data->governorate)->name ?? $data->governorate_id }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{__('admin.boats.port')}} <span class="text-danger">*</span></label>
                    <select name="port_id" id="port_id" class="form-control" required data-selected="{{ old('port_id', $data->port_id) }}">
                        <option value="{{ $data->port_id }}" selected>{{ optional($data->port)->name ?? $data->port_id }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{__('admin.trips.license_attachment') ?? 'مرفق الترخيص'}}</label>
                    <input type="file" name="license_attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('admin.trips.start_date.0') }} <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" required value="{{ old('start_date', \Illuminate\Support\Str::of($data->start_date)->substr(0,10)) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('admin.trips.end_date.0') }} <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control" required value="{{ old('end_date', \Illuminate\Support\Str::of($data->end_date)->substr(0,10)) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{__('admin.trips.departure_time') ?? 'وقت البدء'}} <span class="text-danger">*</span></label>
                    <input type="time" name="departure_time" class="form-control" required value="{{ old('departure_time', $data->departure_time) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{__('admin.trips.return_time') ?? 'وقت الانتهاء'}} <span class="text-danger">*</span></label>
                    <input type="time" name="return_time" class="form-control" required value="{{ old('return_time', $data->return_time) }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">{{ __('admin.trips.notes.0') }}</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $data->notes) }}</textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">{{__('admin.actions.save')}}</button>
                <a href="{{ route('admin.trips.index') }}" class="btn btn-secondary">{{__('admin.actions.cancel')}}</a>
            </div>
        </form>
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

        const curRegion = $region.val(), curGov = $gov.data('selected'), curPort = $port.data('selected');
        if (curRegion) { loadGov(curRegion, curGov, function () { if (curGov) { loadPort(curGov, curPort); } }); }
    })();
</script>
@endsection
