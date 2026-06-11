@extends('owner.layouts.master')
@section('title')
{{__('owner.sales.title')}} - {{__('owner.sales.view')}}
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
            <li class="breadcrumb-item"><a href="{{ url('owner/sales') }}">{{__('owner.sales.title')}}</a></li>
            <li class="breadcrumb-item active">{{__('owner.sales.view')}}</li>
        </ul>
        <h1 class="page-header mb-0">{{__('owner.sales.view')}}</h1>
    </div>
</div>
<div id="formControls" class="mb-5">
    <div class="card">
        <div class="card-body pb-2">

                <div class="row mb-3">
                    <h6 class="mb-4">#: {{ $sales->number }}</h6>
                    <h6 class="mb-4">{{ __('owner.sales.datetime') }}: {{ $sales->sale_datetime }}</h6>
                    <h5>{{ __('owner.sales.customer') }}: {{ $sales->customer->name }}</h5>
                    <h6>{{ __('owner.sales.payment_method_id') }}: {{ $sales->paymentMethod->name }}</h6>
                    <h6 class="mb-4">{{ __('owner.sales.payment_status') }}: @if($sales->payment_status == 'paid') {{ __('owner.generated.item_7a262d') }} @elseif ($sales->payment_status == 'partially_paid') {{ __('owner.generated.item_cd0da4') }} @else {{ __('owner.generated.item_55508f') }} @endif</h6>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('owner.sales.fish') }}</th>
                            <th>{{ __('owner.sales.weight') }}</th>
                            <th>{{ __('owner.sales.unit') }}</th>
                            <th>{{ __('owner.sales.total_price') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sales->details as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $detail->fish->scientific_name }}</td>
                                <td>{{ number_format($detail->weight, 2) }}</td>
                                <td>{{ $detail->unit->name ?? '—' }}</td>
                                <td>{{ number_format($detail->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="2">{{ __('owner.sales.total') }}</th>
                            <th>{{ number_format($sales->details->sum('weight'), 2) }}</th>
                            <th></th>
                            <th>{{ number_format($sales->details->sum('total_price'), 2) }}</th>
                        </tr>
                        @if ($sales->payment_status == 'partially_paid')
                            <tr>
                                <th colspan="4">{{ __('owner.status.paid') }}</th>
                                <th>{{ number_format($sales->details->sum('total_price')-$sales->remaining_total, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="4">{{ __('owner.generated.remaining_val') }}</th>
                                <th>{{ number_format($sales->remaining_total, 2) }}</th>
                            </tr>
                        @endif
                        </tfoot>
                    </table>
                </div>

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
    function addFishRow() {
        let row = document.querySelector('.fish-row').cloneNode(true);
        row.querySelectorAll('input, select').forEach(el => el.value = '');
        document.getElementById('fish-wrapper').appendChild(row);
    }
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

        $('#trip_id').change(function() {
            let tripId = $(this).val();

            if (!tripId) {
                $('[name="boat_id"], [name="boat_name"]').val('');
                return;
            }

            let url = `${baseUrl}/owner/getBoatInfoByTrip/${tripId}`;
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
