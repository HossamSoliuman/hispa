@extends('owner.layouts.master')

@section('title')
    {{ __('owner.generated.item_97b98a') }}
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex align-items-center mb-3">
            <div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a
                            href="{{ route('owner.vendors.index') }}">{{ __('owner.vendors.manage_title') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('owner.generated.item_97b98a') }}</li>
                </ul>
                <h1 class="page-header mb-0">{{ __('owner.generated.item_97b98a') }}</h1>
            </div>
        </div>

        <div id="formControls" class="mb-5">
            <div class="card">
                <div class="card-body pb-2">
                    <form id="vendorForm" method="post" enctype="multipart/form-data" data-id="{{ $vendor->id }}">
                        @csrf
                        @method('PUT')

                        <!-- Basic Info -->
                        <h6 class="fw-bold mb-3">{{ __('owner.dalal.modal.basic_info') }}</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.dalal.modal.form.contact_name') }}*</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $vendor->name) }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.generated.company_name') }}*</label>
                                <input type="text" name="company_name" class="form-control"
                                    value="{{ old('company_name', $vendor->company_name) }}">
                                @error('company_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.dalal.modal.form.email') }}*</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $vendor->email) }}">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.dalal.modal.form.phone') }}*</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $vendor->phone) }}">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6" id="tax_number_group">
                                <label class="form-label">{{ __('owner.generated.item_6d3d5e') }}</label>
                                <input type="text" name="tax_number" class="form-control"
                                    value="{{ old('tax_number', $vendor->tax_number) }}">
                                @error('tax_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.assets.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="1" {{ old('status', $vendor->status) == 1 ? 'selected' : '' }}>
                                        {{ __('owner.assets.active') }}</option>
                                    <option value="0" {{ old('status', $vendor->status) == 0 ? 'selected' : '' }}>
                                        {{ __('owner.fish.status_inactive') }}</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <h6 class="fw-bold mb-3">{{ __('owner.dalal.modal.address') }}</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.crew.edit.region') }}*</label>
                                <select name="region_id" id="region_id" class="form-control">
                                    <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}"
                                            {{ old('region_id', $vendor->region_id) == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('region_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.crew.edit.governorate') }}*</label>
                                <select name="governorate_id" id="governorate_id" class="form-control">
                                    <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                </select>
                                @error('governorate_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.fish.name_ar') }}</label>
                                <input type="text" name="address" class="form-control"
                                    value="{{ old('address', $vendor->address) }}">
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Banking -->
                        <h6 class="fw-bold mb-3">{{ __('owner.dalal.modal.banking') }}</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.dalal.modal.form.bank_name') }}</label>
                                <input type="text" name="bank_name" class="form-control"
                                    value="{{ old('bank_name', $vendor->bank_name) }}">
                                @error('bank_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.dalal.modal.form.bank_account') }}</label>
                                <input type="text" name="account_number" class="form-control"
                                    value="{{ old('account_number', $vendor->account_number) }}">
                                @error('account_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.generated.item_0909ef') }}(IBAN)</label>
                                <input type="text" name="IBAN" class="form-control"
                                    value="{{ old('IBAN', $vendor->IBAN) }}">
                                @error('IBAN')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="form-label">{{ __('owner.expenses.show.notes') }}</label>
                            <textarea class="form-control" name="notes" rows="3">{{ old('notes', $vendor->notes) }}</textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="text-end">
                            <button type="submit"
                                class="btn btn-primary">{{ __('owner.generated.item_f7e50d') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        window.routes = {
            vendorsUpdate: "{{ route('owner.vendors.update', ':id') }}",
            vendorsIndex: "{{ route('owner.vendors.index') }}",
        };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('dashboard/assets/js/owner/vendors.js') }}"></script>

    <script>
        $(document).ready(function() {
            let oldRegionId = '{{ old('region_id', $vendor->region_id) }}';
            let oldGovernorateId = '{{ old('governorate_id', $vendor->governorate_id) }}';

            function loadGovernorates(regionId, selectedGovId = null) {
                if (!regionId) {
                    $('#governorate_id').html(
                        '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');
                    return;
                }

                $('#governorate_id').html('<option value="">{{ __('owner.dalal.performance.loading') }}</option>');

                $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID',
                    regionId), function(data) {
                    let options =
                        '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>';
                    $.each(data, function(i, item) {
                        let selected = (item.id == selectedGovId) ? 'selected' : '';
                        options += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                    });
                    $('#governorate_id').html(options);
                });
            }

            $('#region_id').on('change', function() {
                loadGovernorates($(this).val());
            });

            if (oldRegionId) {
                loadGovernorates(oldRegionId, oldGovernorateId);
            }
        });
    </script>
@endsection
