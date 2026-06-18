@extends('owner.layouts.master')
@section('title')
{{__('owner.boats.create.title')}}
@endsection
@section('css')
<link href="{{asset('dashboard/assets/plugins/tag-it/css/jquery.tagit.css')}}" rel="stylesheet">
<link href="{{asset('dashboard/assets/plugins/summernote/dist/summernote-lite.css')}}" rel="stylesheet">

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
            <li class="breadcrumb-item"><a href="#">{{__('owner.trips.title')}}</a></li>
            <li class="breadcrumb-item active">{{__('owner.trips.create.title')}}</li>
        </ul>
        <h1 class="page-header mb-0">{{__('owner.trips.create.title')}}</h1>
    </div>
</div>
<div id="formControls" class="mb-5">
    <div class="card">
        <div class="card-body pb-2">
            <form action="{{ route('owner.trips.store') }}" method="post" id="createForm" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label for="name" class="form-label">{{ __('owner.trips.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required placeholder="{{ __('owner.trips.name') }}">
                            @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label for="name_en" class="form-label">{{ __('owner.trips.name_en') }}<span class="text-danger">*</span></label>
                            <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control" required placeholder="{{ __('owner.trips.name_en') }}">
                            @error('name_en') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="form-group">
                            <label for="license_number" class="form-label">{{ __('owner.trips.license_number') }}<span class="text-danger">*</span></label>
                            <input type="text" name="license_number" value="{{ old('license_number') }}" class="form-control" required placeholder="{{ __('owner.trips.license_number') }}">
                            @error('license_number') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label for="start_date" class="form-label">{{ __('owner.trips.start_date') }}<span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_date" value="{{ old('start_date', now()->format('Y-m-d\TH:i')) }}" class="form-control" required>
                            @error('start_date') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="form-group">
                            <label for="captain_id" class="form-label">{{ __('owner.trips.captain_name') }}<span class="text-danger">*</span></label>
                            <input type="hidden" name="owner_id" id="owner_id" value="{{ auth()->user()->getAuthIdentifier() }}">
                            <select name="captain_id" id="captain_id" class="form-control" required>
                                <option value="">{{ __('owner.actions.choose') }}</option>
                                    {{-- @php
                                        $ownerId = auth()->user()->getAuthIdentifier();
                                        $captains = \App\Models\User::where('owner_id', $ownerId)->get();
                                    @endphp --}}
                                    @foreach($captains as $captain)
                                        <option value="{{ $captain->id }}" {{ old('captain_id', $trip->captain_id ?? '') == $captain->id ? 'selected' : '' }}>{{ $captain->name }}</option>
                                    @endforeach
                            </select>
                            @error('captain_id') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="form-group">
                            <label for="boat_name" class="form-label">{{ __('owner.trips.boat_name') }}<span class="text-danger">*</span></label>
                            <input type="text" name="boat_name" id="boat_name" class="form-control" disabled value="{{ old('boat_name', $trip->boat_name ?? '') }}" placeholder="{{ __('owner.trips.boat_name') }}">
                            <input type="hidden" name="boat_id" id="boat_id" value="{{ old('boat_id', $trip->boat_id ?? '') }}">
                            @error('boat_name') <span class="text-danger error">{{ $message }}</span>@enderror
                            @error('boat_id') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                </div>

                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div class="form-group">
                            <label for="notes" class="form-label">{{ __('owner.trips.notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" placeholder="{{ __('owner.trips.notes') }}">{{ old('notes', $trip->notes ?? '') }}</textarea>
                            @error('notes') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                @if(isset($quickExpenseCategories) && $quickExpenseCategories->count())
                @php $quickExpenseRows = old('quick_expenses') ?: [['category_id' => '', 'vendor_id' => '', 'amount' => '']]; @endphp
                <hr>
                <div class="row mb-2">
                    <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h5 class="mb-1">{{ __('owner.trips.quick_expenses.title') }}</h5>
                            <small class="text-muted">{{ __('owner.trips.quick_expenses.hint') }}</small>
                        </div>
                        <div class="form-group mb-0" style="min-width: 180px;">
                            <label for="quick_expenses_status" class="form-label">{{ __('owner.trips.quick_expenses.status') }}</label>
                            <select name="quick_expenses_status" id="quick_expenses_status" class="form-control">
                                <option value="pending" {{ old('quick_expenses_status', 'pending') == 'pending' ? 'selected' : '' }}>{{ __('owner.trips.quick_expenses.status_pending') }}</option>
                                <option value="paid" {{ old('quick_expenses_status') == 'paid' ? 'selected' : '' }}>{{ __('owner.trips.quick_expenses.status_paid') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="quickExpensesRows" data-next-index="{{ count($quickExpenseRows) }}">
                    @foreach($quickExpenseRows as $index => $row)
                    <div class="row g-2 align-items-end mb-2 quick-expense-row">
                        <div class="col-xl-4 col-md-4">
                            <label class="form-label">{{ __('owner.trips.quick_expenses.category') }}</label>
                            <select name="quick_expenses[{{ $index }}][category_id]" class="form-control">
                                <option value="">{{ __('owner.trips.quick_expenses.choose_category') }}</option>
                                @foreach($quickExpenseCategories as $category)
                                    <option value="{{ $category->id }}" {{ (string)($row['category_id'] ?? '') === (string)$category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <label class="form-label">{{ __('owner.trips.quick_expenses.provider') }}</label>
                            <select name="quick_expenses[{{ $index }}][vendor_id]" class="form-control">
                                <option value="">{{ __('owner.trips.quick_expenses.choose_provider') }}</option>
                                @foreach($quickExpenseVendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ (string)($row['vendor_id'] ?? '') === (string)$vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-md-3">
                            <label class="form-label">{{ __('owner.trips.quick_expenses.amount') }}</label>
                            <input type="number" step="0.01" min="0"
                                name="quick_expenses[{{ $index }}][amount]"
                                value="{{ $row['amount'] ?? '' }}"
                                class="form-control quick-expense-amount"
                                placeholder="{{ __('owner.trips.quick_expenses.amount') }}">
                        </div>
                        <div class="col-xl-1 col-md-1">
                            <button type="button" class="btn btn-danger w-100 btn-remove-expense" title="{{ __('owner.trips.quick_expenses.remove_row') }}"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="row mb-3">
                    <div class="col-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <button type="button" id="addQuickExpense" class="btn btn-outline-primary btn-sm"><i class="fa fa-plus me-1"></i>{{ __('owner.trips.quick_expenses.add_row') }}</button>
                        <div>
                            <span class="fw-bold">{{ __('owner.trips.quick_expenses.total') }}:</span>
                            <span id="quickExpensesTotal" class="fw-bold">0.00</span>
                        </div>
                    </div>
                </div>

                <template id="quickExpenseRowTemplate">
                    <div class="row g-2 align-items-end mb-2 quick-expense-row">
                        <div class="col-xl-4 col-md-4">
                            <label class="form-label">{{ __('owner.trips.quick_expenses.category') }}</label>
                            <select name="quick_expenses[__INDEX__][category_id]" class="form-control">
                                <option value="">{{ __('owner.trips.quick_expenses.choose_category') }}</option>
                                @foreach($quickExpenseCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <label class="form-label">{{ __('owner.trips.quick_expenses.provider') }}</label>
                            <select name="quick_expenses[__INDEX__][vendor_id]" class="form-control">
                                <option value="">{{ __('owner.trips.quick_expenses.choose_provider') }}</option>
                                @foreach($quickExpenseVendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-md-3">
                            <label class="form-label">{{ __('owner.trips.quick_expenses.amount') }}</label>
                            <input type="number" step="0.01" min="0"
                                name="quick_expenses[__INDEX__][amount]"
                                class="form-control quick-expense-amount"
                                placeholder="{{ __('owner.trips.quick_expenses.amount') }}">
                        </div>
                        <div class="col-xl-1 col-md-1">
                            <button type="button" class="btn btn-danger w-100 btn-remove-expense" title="{{ __('owner.trips.quick_expenses.remove_row') }}"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </template>
                @endif


                <div class="mt-4">
                    <button type="submit" class="btn btn-success">{{__('owner.actions.save')}}</button>
                    <a href="{{ route('owner.boats.index') }}" class="btn btn-secondary">{{__('owner.actions.cancel')}}</a>
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
<script src="{{asset('dashboard/assets/plugins/jquery-migrate/dist/jquery-migrate.min.js')}}"></script>

<script src="{{asset('dashboard/assets/plugins/@highlightjs/cdn-assets/highlight.min.js')}}"></script>
<script src="{{asset('dashboard/assets/js/demo/highlightjs.demo.js')}}"></script>
<script src="{{asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js')}}"></script>
<script src="{{asset('dashboard/assets/js/jquery.validate.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>
<script src="{{asset('dashboard/assets/plugins/summernote/dist/summernote-lite.min.js')}}"></script>


<script>
    $("#createForm").validate();
</script>
<script>
    function recalcQuickExpensesTotal() {
        let total = 0;
        $('.quick-expense-amount').each(function() {
            let value = parseFloat($(this).val());
            if (!isNaN(value) && value > 0) {
                total += value;
            }
        });
        $('#quickExpensesTotal').text(total.toFixed(2));
    }

    $(document).on('input', '.quick-expense-amount', recalcQuickExpensesTotal);

    $(document).on('click', '#addQuickExpense', function() {
        let container = document.getElementById('quickExpensesRows');
        let index = parseInt(container.dataset.nextIndex, 10) || 0;
        let html = document.getElementById('quickExpenseRowTemplate').innerHTML.replace(/__INDEX__/g, index);
        container.insertAdjacentHTML('beforeend', html);
        container.dataset.nextIndex = index + 1;
    });

    $(document).on('click', '.btn-remove-expense', function() {
        let rows = $('#quickExpensesRows .quick-expense-row');
        if (rows.length > 1) {
            $(this).closest('.quick-expense-row').remove();
        } else {
            $(this).closest('.quick-expense-row').find('select, input').val('');
        }
        recalcQuickExpensesTotal();
    });
</script>
<script>
    $(document).ready(function() {
        let baseUrl = "{{ LaravelLocalization::localizeUrl('/') }}";
        let oldRegionId = '{{ old('region_id') }}';
        let oldGovernorateId = '{{ old('governorate_id') }}';
        let oldPortId = '{{ old('port_id') }}';

        // تحميل المحافظات عند اختيار المنطقة
        $('#region_id').on('change', function() {
            let regionId = $(this).val();
            $('#governorate_id').empty().append('<option value="">{{__('owner.loading')}}</option>');
            $('#port_id').empty().append('<option value="">{{__('owner.actions.choose')}}</option>');

            if (regionId) {
                $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID', regionId), function(data) {
                    $('#governorate_id').empty().append('<option value="">{{__('owner.actions.choose')}}</option>');
                    $.each(data, function(i, item) {
                        $('#governorate_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                });
            }
        });

        // تحميل المدن عند اختيار المحافظة
        $('#governorate_id').on('change', function() {
            let govId = $(this).val();
            $('#port_id').empty().append('<option value="">{{__('owner.loading')}}</option>');

            if (govId) {
                $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', govId), function(data) {
                    $('#port_id').empty().append('<option value="">{{__('owner.actions.choose')}}</option>');
                    $.each(data, function(i, item) {
                        $('#port_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                });
            }
        });

        $('#captain_id').change(function() {
            let captainId = $(this).val();

            if (!captainId) {
                $('[name="boat_id"], [name="boat_name"]').val('');
                return;
            }

            let url = `${baseUrl}/owner/getBoatInfo/${captainId}`;
            $.get(url, function(data) {
                $('[name="boat_id"]').val(data.boat_id);
                $('[name="boat_name"]').val(data.boat_name);
            }).fail(function() {
                console.error('Failed to load boat info');
            });
        });

        // عند تحميل الصفحة إذا في old value للمنطقة والمحافظة والمدينة
        if (oldRegionId && !$('#governorate_id option:selected').val()) {
            $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID', oldRegionId), function(governorates) {
                $('#governorate_id').empty().append('<option value="">{{__('owner.actions.choose')}}</option>');
                $.each(governorates, function(i, item) {
                    let selected = (item.id == oldGovernorateId) ? 'selected' : '';
                    $('#governorate_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                });

                if (oldGovernorateId) {
                    $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', oldGovernorateId), function(ports) {
                        $('#port_id').empty().append('<option value="">{{__('owner.actions.choose')}}</option>');
                        $.each(ports, function(i, item) {
                            let selected = (item.id == oldPortId) ? 'selected' : '';
                            $('#port_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                        });
                    });
                }
            });
        }
    });
</script>
@endsection
