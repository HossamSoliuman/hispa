@extends('admin.layouts.master')
@section('title')
    {{ __('admin.crew.add_new') ?? 'إضافة عضو طاقم' }}
@endsection
@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.crew.index') }}">{{ __('admin.crew.title') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admin.crew.add_new') ?? 'إضافة عضو طاقم' }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('admin.crew.add_new') ?? 'إضافة عضو طاقم' }}</h1>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="text-muted mb-3">{{ __('admin.crew.title') }} — {{ __('admin.crew.add_new') }}</p>
            <form action="{{ route('admin.crew.store') }}" id="createForm" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="owner_id" class="form-label">{{ __('admin.captains.form.owner') ?? 'الصياد' }} <span class="text-danger">*</span></label>
                        <select name="owner_id" class="form-control" required id="owner_id">
                            <option value="">{{ __('admin.actions.choose') ?? 'اختر' }}</option>
                            @foreach ($owners as $owner)
                                <option value="{{ $owner->id }}" {{ old('owner_id') == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                            @endforeach
                        </select>
                        @error('owner_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="boat_id" class="form-label">{{ __('admin.captains.form.boat') ?? 'القارب' }} <span class="text-danger">*</span></label>
                        <select name="boat_id" class="form-control" required id="boat_id">
                            <option value="">{{ __('admin.actions.choose') ?? 'اختر' }}</option>
                            @foreach ($boats as $boat)
                                <option value="{{ $boat->id }}" {{ old('boat_id') == $boat->id ? 'selected' : '' }}>{{ $boat->name }}</option>
                            @endforeach
                        </select>
                        @error('boat_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.name') ?? 'الاسم' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                        @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.email') ?? 'البريد' }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                        @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.owner.phone') ?? 'الهاتف' }} <span class="text-danger">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" required>
                        @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.crew.table.nationality') ?? 'الجنسية' }} <span class="text-danger">*</span></label>
                        <input type="text" name="nationality" value="{{ old('nationality', 'سعودي') }}" class="form-control" required>
                        @error('nationality')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.crew.table.region') ?? 'المنطقة' }} <span class="text-danger">*</span></label>
                        <select name="region_id" class="form-control" required id="region_id">
                            <option value="">{{ __('admin.actions.choose') ?? 'اختر' }}</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                        @error('region_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.crew.table.governorate') ?? 'المحافظة' }} <span class="text-danger">*</span></label>
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
                        <label class="form-label">{{ __('admin.crew.table.job_title') ?? 'المسمى الوظيفي' }} <span class="text-danger">*</span></label>
                        <input type="text" name="job_title" value="{{ old('job_title') }}" class="form-control" required>
                        @error('job_title')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.crew.table.id_number') ?? 'رقم الهوية' }}</label>
                        <input type="text" name="id_number" value="{{ old('id_number') }}" class="form-control">
                        @error('id_number')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.logo') ?? 'الصورة' }}</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        @error('logo')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.password') ?? 'كلمة المرور' }} <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                        @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.password_confirmation') ?? 'تأكيد كلمة المرور' }} <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.salary_type') ?? 'نوع الراتب' }} <span class="text-danger">*</span></label>
                        <select name="salary_type" class="form-control" required>
                            <option value="salary" {{ old('salary_type') == 'salary' ? 'selected' : '' }}>ثابت</option>
                            <option value="percentage" {{ old('salary_type') == 'percentage' ? 'selected' : '' }}>نسبة</option>
                            <option value="mixed" {{ old('salary_type') == 'mixed' ? 'selected' : '' }}>مختلط</option>
                        </select>
                        @error('salary_type')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.salary_amount') ?? 'الراتب' }}</label>
                        <input type="number" step="0.01" name="salary_amount" value="{{ old('salary_amount') }}" class="form-control">
                        @error('salary_amount')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.bank_name') ?? 'البنك' }} <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="form-control" required>
                        @error('bank_name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.account_number') ?? 'رقم الحساب' }} <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" value="{{ old('account_number') }}" class="form-control" required>
                        @error('account_number')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.IBAN') ?? 'الآيبان' }} <span class="text-danger">*</span></label>
                        <input type="text" name="IBAN" value="{{ old('IBAN') }}" class="form-control" required>
                        @error('IBAN')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.fishing_license_number') ?? 'رقم رخصة الصيد' }} <span class="text-danger">*</span></label>
                        <input type="text" name="fishing_license_number" value="{{ old('fishing_license_number') }}" class="form-control" required>
                        @error('fishing_license_number')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.captains.form.fishing_license_expiry') ?? 'انتهاء رخصة الصيد' }} <span class="text-danger">*</span></label>
                        <input type="date" name="fishing_license_expiry" value="{{ old('fishing_license_expiry') }}" class="form-control" required>
                        @error('fishing_license_expiry')<span class="text-danger">{{ $message }}</span>@enderror
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
        document.getElementById('region_id').addEventListener('change', function() {
            var regionId = this.value;
            var govSelect = document.getElementById('governorate_id');
            govSelect.innerHTML = '<option value="">{{ __("admin.actions.choose") ?? "اختر" }}</option>';
            if (!regionId) return;
            var url = "{{ route('admin.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID', regionId);
            fetch(url).then(r => r.json()).then(function(data) {
                data.forEach(function(g) { govSelect.innerHTML += '<option value="' + g.id + '">' + g.name + '</option>'; });
            });
        });
        document.getElementById('governorate_id').addEventListener('change', function() {
            var govId = this.value;
            var portSelect = document.getElementById('port_id');
            portSelect.innerHTML = '<option value="">{{ __("admin.actions.choose") ?? "اختر" }}</option>';
            if (!govId) return;
            var url = "{{ route('admin.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', govId);
            fetch(url).then(r => r.json()).then(function(data) {
                data.forEach(function(p) { portSelect.innerHTML += '<option value="' + p.id + '">' + p.name + '</option>'; });
            });
        });
    </script>
@endsection
