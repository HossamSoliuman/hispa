@extends('owner.layouts.master')
@section('title')
{{__('owner.assets.title')}} - {{__('owner.assets.create')}}
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
            <li class="breadcrumb-item"><a href="{{ url('owner/assets') }}">{{__('owner.assets.title')}}</a></li>
            <li class="breadcrumb-item active">{{__('owner.assets.create')}}</li>
        </ul>
        <h1 class="page-header mb-0">{{__('owner.assets.create')}}</h1>
    </div>
</div>
<div id="formControls" class="mb-5">
    <div class="card">
        <div class="card-body pb-2">
            <form action="{{ route('owner.assets.store') }}" method="post" id="createForm" enctype="multipart/form-data">
                @csrf
<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('owner.generated.asset_type') }}</label>
        <select name="asset_type" id="asset_type" class="form-control" required>
            <option value="">-- {{ __('owner.crew.edit.select_placeholder') }}--</option>
            <option value="boat" @selected(old('asset_type',$asset->asset_type ?? '')=='boat')>{{ __('owner.generated.boat') }}</option>
            <option value="fishing_equipment" @selected(old('asset_type',$asset->asset_type ?? '')=='fishing_equipment')>{{ __('owner.generated.fishing_equipment') }}</option>
            <option value="other" @selected(old('asset_type',$asset->asset_type ?? '')=='other')>{{ __('owner.generated.other_asset') }}</option>
        </select>
    </div>

    <div class="col-md-4 d-none" id="boat-wrapper">
        <label class="form-label">{{ __('owner.catch.filters.boat') }}</label>
        <select name="boat_id" class="form-control">
            <option value="">-- {{ __('owner.payrolls.create.choose_boat') }}--</option>
            @foreach($boats as $boat)
                <option value="{{ $boat->id }}"
                    @selected(old('boat_id',$asset->boat_id ?? '')==$boat->id)>
                    {{ $boat->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">{{ __('owner.generated.asset_name') }}</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name',$asset->name ?? '') }}" required>
    </div>
</div>



<div class="mb-3">
    <label class="form-label">{{ __('owner.generated.asset_description') }}</label>
    <textarea name="description" class="form-control" rows="2">
        {{ old('description',$asset->description ?? '') }}
    </textarea>
</div>



<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('owner.generated.purchase_date') }}</label>
        <input type="date" name="purchase_date" class="form-control"
               value="{{ old('purchase_date',$asset->purchase_date ?? '') }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">{{ __('owner.generated.purchase_cost') }}</label>
        <input type="number" step="0.01" name="purchase_cost" class="form-control"
               value="{{ old('purchase_cost',$asset->purchase_cost ?? '') }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">{{ __('owner.generated.scrap_value') }}</label>
        <input type="number" step="0.01" name="salvage_value" class="form-control"
               value="{{ old('salvage_value',$asset->salvage_value ?? 0) }}">
    </div>
</div>



<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('owner.generated.depreciation_method') }}</label>
        <select name="depreciation_method" id="depreciation_method" class="form-control" required>
            <option value="straight_line"
                @selected(old('depreciation_method',$asset->depreciation_method ?? '')=='straight_line')>
                {{ __('owner.generated.useful_life') }}</option>
            <option value="percentage"
                @selected(old('depreciation_method',$asset->depreciation_method ?? '')=='percentage')>
                {{ __('owner.generated.percentage') }}</option>
        </select>
    </div>

    <div class="col-md-4 d-none" id="life-wrapper">
        <label class="form-label">{{ __('owner.generated.useful_life_span') }}({{ __('owner.generated.year') }})</label>
        <input type="number" name="useful_life_years" class="form-control"
               value="{{ old('useful_life_years',$asset->useful_life_years ?? '') }}">
    </div>

    <div class="col-md-4 d-none" id="rate-wrapper">
        <label class="form-label">{{ __('owner.generated.depreciation_rate') }}(%)</label>
        <input type="number" step="0.01" name="depreciation_rate" class="form-control"
               value="{{ old('depreciation_rate',$asset->depreciation_rate ?? '') }}">
    </div>
</div>



<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('owner.generated.asset_status') }}</label>
        <select name="status" class="form-control">
            <option value="active" @selected(old('status',$asset->status ?? '')=='active')>{{ __('owner.assets.active') }}</option>
            <option value="sold" @selected(old('status',$asset->status ?? '')=='sold')>{{ __('owner.assets.sold') }}</option>
            <option value="damaged" @selected(old('status',$asset->status ?? '')=='damaged')>{{ __('owner.generated.damaged_or_written_off') }}</option>
        </select>
    </div>
</div>



<div class="mb-3">
    <label class="form-label">{{ __('owner.expenses.show.notes') }}</label>
    <textarea name="notes" class="form-control" rows="2">
        {{ old('notes',$asset->notes ?? '') }}
    </textarea>
</div>


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
    $(document).ready(function() {
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

<script>
    function toggleAssetType() {
        document.getElementById('boat-wrapper')
            .classList.toggle('d-none', document.getElementById('asset_type').value !== 'boat');
    }

    function toggleDepreciation() {
        const method = document.getElementById('depreciation_method').value;
        document.getElementById('life-wrapper').classList.toggle('d-none', method !== 'straight_line');
        document.getElementById('rate-wrapper').classList.toggle('d-none', method !== 'percentage');
    }

    document.getElementById('asset_type').addEventListener('change', toggleAssetType);
    document.getElementById('depreciation_method').addEventListener('change', toggleDepreciation);

    toggleAssetType();
    toggleDepreciation();
</script>

@endsection
