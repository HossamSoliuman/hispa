@extends('owner.layouts.master')
@section('title')
{{__('owner.sales.fish_quntity')}}
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
            <h2 class="mb-2">{{__('owner.sales.fish_quntity')}}</h2>
        </div>
    </div>


        
<div id="formControls" class="mb-5">
    <div class="card">
        
        <div class="card-body pb-2">

            <form class="pl-form" method="GET" action="{{ route('owner.fishQuntity') }}">
                <div class="row align-items-end gy-2">       
                <div class="col-md-2">                     
                    <label>{{ __('owner.profit_loss.from_date') }}</label>
                    <input type="date" class="form-control" name="from" value="{{ $from }}">
                </div>                
                <div class="col-md-2">                
                    <label>{{ __('owner.profit_loss.to_date') }}</label>
                    <input type="date" class="form-control" name="to" value="{{ $to }}">
                </div>
                <div class="col-md-2">            
                    <label>{{ __('owner.profit_loss.boat') }}</label>
                    <select name="boat_id" class="form-select">
                        <option value="">{{ __('owner.profit_loss.all_boats') }}</option>
                        @foreach($boats as $boat)
                            <option value="{{ $boat->id }}" {{ $boatId == $boat->id ? 'selected' : '' }}>
                                {{ $boat->name ?? $boat->name_ar }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">                
                    <label>{{ __('owner.sales.trip') }}</label>
                    <select name="trip_id" class="form-select">
                        <option value="">{{ __('owner.stock_report.all') }}</option>
                        @foreach($trips as $trip)
                            <option value="{{ $trip->id }}" {{ $tripId == $trip->id ? 'selected' : '' }}>
                                {{ $trip->name ?? $trip->name_ar }}
                            </option>
                        @endforeach
                    </select>            
                </div>
                <div class="col-md-2">                
                    <label>{{ __('owner.sales.fish') }}</label>
                    <select name="fish_id" class="form-select">
                        <option value="">{{ __('owner.stock_report.all') }}</option>
                        @foreach($fishs as $fish)
                            <option value="{{ $fish->id }}" {{ $fishId == $fish->id ? 'selected' : '' }}>
                                {{ $fish->name ?? $fish->name_ar }}
                            </option>
                        @endforeach
                    </select>            
                </div>
                <div class="col-md-2">
                <div style="align-self:end; display:flex; gap:8px;">
                    <button class="pl-btn btn btn-success sm me-2" type="submit">{{ __('owner.profit_loss.update') }}</button>                
                </div>
                </div>
                </div>
            </form>
        

            <div class="row mb-3">            
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.catch.fish') }}</th>
                        <th>{{ __('owner.catch.weight') }}</th>
                        <th>{{ __('owner.catch.unit') }}</th>
                        <th>{{ __('owner.sales.price_per_kilo') }}</th>
                        <th>{{ __('owner.catch.total_price') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @php $total_price = 0; @endphp
                    @foreach($stocks as $stock)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $stock->fish->scientific_name }}</td>
                            <td>{{ number_format($stock->weight, 2) }}</td>
                            <td>{{ $stock->unit->name ?? '—' }}</td>
                            <td>{{ number_format($stock->price_per_kg, 2) }}</td>
                            @php $fish_total_price = ($stock->weight * $stock->price_per_kg); @endphp
                            @php $total_price += $fish_total_price; @endphp
                            <td>{{ number_format($fish_total_price, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    @foreach($stocks->groupBy(fn ($s) => $s->unit->name ?? '—') as $unitName => $group)
                        <tr>
                            <th colspan="2">{{ __('owner.catch.total') }} ({{ $unitName }})</th>
                            <th>{{ number_format($group->sum('weight'), 2) }}</th>
                            <th colspan="3">{{ $unitName }}</th>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="2">{{ __('owner.catch.total_price') }}</th>
                        <th colspan="4">{{ number_format($total_price, 2) }}</th>
                    </tr>
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
