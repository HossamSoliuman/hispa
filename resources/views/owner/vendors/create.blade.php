@extends('owner.layouts.master')

@section('title')
    {{ __('owner.generated.item_37c629') }}
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
                    <li class="breadcrumb-item active">{{ __('owner.vendors.add_button') }}</li>
                </ul>
                <h1 class="page-header mb-0">{{ __('owner.vendors.add_button') }}</h1>
            </div>
        </div>
        <div id="formControls" class="mb-5">
            <div class="card">
                <div class="card-body pb-2">
                    <form id="vendorForm" method="post" enctype="multipart/form-data" data-id="">
                        @csrf
                        <!-- Basic Info -->
                        <h6 class="fw-bold mb-3">{{ __('owner.dalal.modal.basic_info') }}</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.dalal.modal.form.contact_name') }}*</label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="{{ __('owner.generated.item_9f4a16') }}" value="{{ old('name') }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.generated.company_name') }}*</label>
                                <input type="text" name="company_name" class="form-control"
                                    placeholder="{{ __('owner.generated.item_e45b66') }}"
                                    value="{{ old('company_name') }}">
                                @error('company_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.dalal.modal.form.email') }}*</label>
                                <input type="email" name="email" class="form-control" placeholder="vendor@example.com"
                                    value="{{ old('email') }}">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.dalal.modal.form.phone') }}*</label>
                                <input type="text" name="phone" class="form-control" placeholder="+966501234567"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6" id="tax_number_group">
                                <label class="form-label">{{ __('owner.generated.item_6d3d5e') }}</label>
                                <input id="tax_number" type="text" name="tax_number" class="form-control"
                                    placeholder="{{ __('owner.generated.item_6d3d5e') }}" value="{{ old('tax_number') }}">
                                @error('tax_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.assets.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="0">{{ __('owner.fish.status_inactive') }}</option>
                                    <option value="1" selected>{{ __('owner.assets.active') }}</option>
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
                                            {{ old('region_id') == $region->id ? 'selected' : '' }}>
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
                                    placeholder="{{ __('owner.generated.item_e69669') }}" value="{{ old('address') }}">
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
                                    value="{{ old('bank_name') }}">
                                @error('bank_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.dalal.modal.form.bank_account') }}</label>
                                <input type="text" name="account_number" class="form-control"
                                    value="{{ old('account_number') }}">
                                @error('account_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.generated.item_0909ef') }}(IBAN)</label>
                                <input type="text" name="IBAN" class="form-control" value="{{ old('IBAN') }}">
                                @error('IBAN')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="form-label">{{ __('owner.expenses.show.notes') }}</label>
                            <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="text-end">
                            <button type="submit"
                                class="btn btn-success">{{ __('owner.generated.item_7479b7') }}</button>
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
    </div>
@endsection
@section('script')
    <script>
        window.routes = {
            vendorsStore: "{{ route('owner.vendors.store') }}",
            vendorsIndex: "{{ route('owner.vendors.index') }}",
        };
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('dashboard/assets/js/owner/vendors.js') }}"></script>

    <script>
        $(document).ready(function() {
            let oldRegionId = '{{ old('region_id') }}';
            let oldGovernorateId = '{{ old('governorate_id') }}';

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
