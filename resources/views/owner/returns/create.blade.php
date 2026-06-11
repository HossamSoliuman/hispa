@extends('owner.layouts.master')
@section('title')
{{__('owner.returns.title')}} - {{__('owner.returns.create')}}
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
            <li class="breadcrumb-item"><a href="{{ url('owner/returns') }}">{{__('owner.returns.title')}}</a></li>
            <li class="breadcrumb-item active">{{__('owner.returns.create')}}</li>
        </ul>
        <h1 class="page-header mb-0">{{__('owner.returns.create')}}</h1>
    </div>
</div>
<div id="formControls" class="mb-5">
    <div class="card">
        <div class="card-body pb-2">
            <form action="{{ route('owner.returns.store') }}" method="post" id="createForm" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label for="name" class="form-label">{{ __('owner.trips.name') }} <span class="text-danger">*</span></label>
                            <select name="sale_id" id="sale_id" class="form-select">
                                <option value="">{{ __('owner.actions.choose') }}</option>
                                @foreach($sales as $sale)
                                    <option value="{{ $sale->id }}">
                                        {{ __('owner.generated.item_f95919') }} #{{ $sale->number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div id="fish-wrapper"></div>
                    </div>
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
    document.getElementById('sale_id').addEventListener('change', function () {
        const saleId = this.value;
        const wrapper = document.getElementById('fish-wrapper');

        wrapper.innerHTML = '';

        if (!saleId) return;
        const saleDetailsUrl = "{{ route('owner.saleDetails', ':id') }}";
        // fetch(`/owner/saleDetails/${saleId}`)
        fetch(saleDetailsUrl.replace(':id', saleId))
            .then(response => response.json())
            .then(data => {
                if (!data.details || data.details.length === 0) {
                    wrapper.innerHTML = '<p class="text-muted">{{ __('owner.generated.no_items') }}</p>';
                    return;
                }

                data.details.forEach(detail => {
                    const unitName = (detail.unit && detail.unit.name) ? detail.unit.name : '';
                    wrapper.insertAdjacentHTML('beforeend', `
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <input type="hidden" name="fish_id[]" value="${detail.fish_id}">
                            <input type="hidden" name="sale_detail_id[]" value="${detail.id}">
                            <input type="hidden" name="unit_id[]" value="${detail.unit_id || ''}">
                            <input type="hidden" name="price_per_kilo[]" value="${detail.price_per_kilo}">
                            <input type="text" class="form-control" value="${detail.fish.name}" disabled>
                        </div>

                        <div class="col-md-4">
                            <input type="number"
                                   step="0.01"
                                   name="weight[]"
                                   max="${detail.weight}"
                                   class="form-control"
                                   placeholder="{{ __('owner.generated.return_weight') }} (${unitName})"
                                   required>
                        </div>

                        <div class="col-md-3">
                            <input type="text" class="form-control" value="${unitName}" disabled>
                        </div>
                    </div>
                `);
                });
            })
            .catch(err => {
                console.error(err);
                wrapper.innerHTML = '<p class="text-danger">{{ __('owner.generated.error_fetching_data') }}</p>';
            });
    });
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
@endsection
