@extends('admin.layouts.master')
@section('title')
    {{ __('admin.actions.edit') ?? 'تعديل' }} - {{ $data->name }}
@endsection
@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.crew.index') }}">{{ __('admin.crew.title') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.actions.edit') ?? 'تعديل' }} - {{ $data->name }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.actions.edit') ?? 'تعديل' }} - {{ $data->name }}</h1>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.crew.update', $data->id) }}" id="editForm" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="owner_id" class="form-label">{{ __('admin.captains.form.owner') ?? 'الصياد' }} <span class="text-danger">*</span></label>
                        <select name="owner_id" class="form-control" required id="owner_id">
                            @foreach ($owners as $owner)
                                <option value="{{ $owner->id }}" {{ old('owner_id', $data->owner_id) == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                            @endforeach
                        </select>
                        @error('owner_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="boat_id" class="form-label">{{ __('admin.captains.form.boat') ?? 'القارب' }} <span class="text-danger">*</span></label>
                        <select name="boat_id" class="form-control" required id="boat_id">
                            @foreach ($boats as $boat)
                                <option value="{{ $boat->id }}" {{ old('boat_id', $data->boat_id) == $boat->id ? 'selected' : '' }}>{{ $boat->name }}</option>
                            @endforeach
                        </select>
                        @error('boat_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.name') ?? 'الاسم' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $data->name) }}" class="form-control" required>
                        @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.email') ?? 'البريد' }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $data->email) }}" class="form-control" required>
                        @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.owner.phone') ?? 'الهاتف' }} <span class="text-danger">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $data->phone) }}" class="form-control" required>
                        @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.crew.table.job_title') ?? 'المسمى الوظيفي' }} <span class="text-danger">*</span></label>
                        <input type="text" name="job_title" value="{{ old('job_title', $data->job_title) }}" class="form-control" required>
                        @error('job_title')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.region') ?? 'المنطقة' }} <span class="text-danger">*</span></label>
                        <select name="region_id" class="form-control" required id="region_id">
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id', $data->region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                        @error('region_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.governorate') ?? 'المحافظة' }} <span class="text-danger">*</span></label>
                        <select name="governorate_id" class="form-control" required id="governorate_id">
                            <option value="">{{ __('admin.actions.choose') ?? 'اختر' }}</option>
                        </select>
                        @error('governorate_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.owner.port') ?? 'الميناء' }} <span class="text-danger">*</span></label>
                        <select name="port_id" class="form-control" required id="port_id">
                            <option value="">{{ __('admin.actions.choose') ?? 'اختر' }}</option>
                        </select>
                        @error('port_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.logo') ?? 'الصورة' }}</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        @error('logo')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.salary_type') ?? 'نوع الراتب' }} <span class="text-danger">*</span></label>
                        <select name="salary_type" class="form-control" required>
                            <option value="salary" {{ old('salary_type', $data->salary_type) == 'salary' ? 'selected' : '' }}>ثابت</option>
                            <option value="percentage" {{ old('salary_type', $data->salary_type) == 'percentage' ? 'selected' : '' }}>نسبة</option>
                            <option value="mixed" {{ old('salary_type', $data->salary_type) == 'mixed' ? 'selected' : '' }}>مختلط</option>
                        </select>
                        @error('salary_type')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.salary_amount') ?? 'الراتب' }}</label>
                        <input type="number" step="0.01" name="salary_amount" value="{{ old('salary_amount', $data->salary_amount) }}" class="form-control">
                        @error('salary_amount')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">{{ __('admin.actions.save') ?? 'حفظ' }}</button>
                    <a href="{{ route('admin.crew.index') }}" class="btn btn-secondary">{{ __('admin.actions.cancel') ?? 'إلغاء' }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        (function() {
            var regionId = "{{ old('region_id', $data->region_id) }}";
            var govId = "{{ old('governorate_id', $data->governorate_id) }}";
            var portId = "{{ old('port_id', $data->port_id) }}";
            if (regionId) {
                fetch("{{ route('admin.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID', regionId))
                    .then(r => r.json()).then(function(data) {
                        var g = document.getElementById('governorate_id');
                        g.innerHTML = '<option value="">{{ __("admin.actions.choose") ?? "اختر" }}</option>';
                        data.forEach(function(o) { g.innerHTML += '<option value="' + o.id + '"' + (o.id == govId ? ' selected' : '') + '>' + o.name + '</option>'; });
                        if (govId) {
                            fetch("{{ route('admin.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', govId))
                                .then(r => r.json()).then(function(ports) {
                                    var p = document.getElementById('port_id');
                                    p.innerHTML = '<option value="">{{ __("admin.actions.choose") ?? "اختر" }}</option>';
                                    ports.forEach(function(o) { p.innerHTML += '<option value="' + o.id + '"' + (o.id == portId ? ' selected' : '') + '>' + o.name + '</option>'; });
                                });
                        }
                    });
            }
            document.getElementById('region_id').addEventListener('change', function() {
                var id = this.value;
                var g = document.getElementById('governorate_id');
                g.innerHTML = '<option value="">{{ __("admin.actions.choose") ?? "اختر" }}</option>';
                document.getElementById('port_id').innerHTML = '<option value="">{{ __("admin.actions.choose") ?? "اختر" }}</option>';
                if (!id) return;
                fetch("{{ route('admin.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID', id)).then(r => r.json()).then(function(data) {
                    data.forEach(function(o) { g.innerHTML += '<option value="' + o.id + '">' + o.name + '</option>'; });
                });
            });
            document.getElementById('governorate_id').addEventListener('change', function() {
                var id = this.value;
                var p = document.getElementById('port_id');
                p.innerHTML = '<option value="">{{ __("admin.actions.choose") ?? "اختر" }}</option>';
                if (!id) return;
                fetch("{{ route('admin.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', id)).then(r => r.json()).then(function(data) {
                    data.forEach(function(o) { p.innerHTML += '<option value="' + o.id + '">' + o.name + '</option>'; });
                });
            });
        })();
    </script>
@endsection
