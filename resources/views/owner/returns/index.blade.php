@extends('owner.layouts.master')
@section('title')
{{__('owner.returns.title')}}
@endsection
@section('css')

<link href="{{asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}"
    rel="stylesheet">
<link href="{{asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css')}}"
    rel="stylesheet">
<link href="{{asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}"
    rel="stylesheet">
<link href="{{asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css')}}" rel="stylesheet">
<style>
    #datatableDefault th,
    #datatableDefault td {
        text-align: center !important;
        vertical-align: middle;
    }

    /* {{ __('owner.generated.item_ed06b0') }} */
    .small-text th,
    .small-text td {
        font-size: 12px;
        /* {{ __('owner.generated.or') }} 13px {{ __('owner.generated.item_4cc9e8') }} */
        text-align: center !important;
        vertical-align: middle;
        font-weight: bold;

    }


    label.error {
        color: red;
        font-weight: bold;
        margin-top: 5px;
        display: block;
    }
</style>
@endsection
@section('content')

        <div class="row mb-4 align-items-center justify-content-between">
            <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
                <h2 class="fw-bold text-dark mb-2">{{__('owner.returns.title')}}</h2>                
            </div>

            <div class="col-md-6 col-sm-12 text-md-end text-sm-start d-flex justify-content-md-end gap-2">
                <a href="{{route('owner.returns.create')}}" class="btn btn-outline-theme btn-equal">
                    <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{__('owner.returns.create')}}
                </a>
            </div>

        </div>
<div class="tab-content py-4">
    <div class="tab-pane fade show active" id="allTab">
        <!-- BEGIN #datatable -->
        <!-- BEGIN #datatable -->
        <div id="datatable" class="mb-5">
            {{-- <div class="card">--}}
            {{-- <div class="card-body">--}}
            <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{__('owner.returns.sale_number')}}</th>
                        <th>{{__('owner.returns.total_amount')}}</th>
                        <th>{{__('owner.returns.returned_at')}}</th>
                        <th>{{__('owner.returns.status')}}</th>
                    </tr>
                </thead>
                <tbody>


                </tbody>
            </table>
        </div>
        <div class="card-arrow">
            <div class="card-arrow-top-left"></div>
            <div class="card-arrow-top-right"></div>
            <div class="card-arrow-bottom-left"></div>
            <div class="card-arrow-bottom-right"></div>
        </div>

    </div>
</div>
<!-- END #datatable -->


{{-- </div>--}}
{{-- </div>--}}
<!-- END #datatable -->

<div class="card-arrow">
    <div class="card-arrow-top-left"></div>
    <div class="card-arrow-top-right"></div>
    <div class="card-arrow-bottom-left"></div>
    <div class="card-arrow-bottom-right"></div>
</div>
</div>

@endsection
@section('script')

<script src="{{asset('dashboard/assets/plugins/@highlightjs/cdn-assets/highlight.min.js')}}"></script>
<script src="{{asset('dashboard/assets/js/demo/highlightjs.demo.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.colVis.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
<script
    src="{{asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
<script
    src="{{asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script
    src="{{asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.js')}}"></script>
<script src="{{asset('dashboard/assets/js/demo/table-plugins.demo.js')}}"></script>
<script src="{{asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js')}}"></script>
<script src="{{asset('dashboard/assets/js/jquery.validate.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>


<script type="text/javascript">
    $(function() {
        let appLocale = '{{ app()->getLocale() }}';
        let languageOptions = {};
        if (appLocale === 'ar') {
            languageOptions = {
                url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
            };
        }
        // Check if the DataTable is already initialized and destroy it
        if ($.fn.DataTable.isDataTable('#datatableDefault')) {
            $('#datatableDefault').DataTable().destroy();
        }



        // Initialize the DataTable
        var table = $('#datatableDefault').DataTable({
            processing: true,
            serverSide: true,

            language: languageOptions,

            ajax: {
                url: "{{ route('owner.getReturnsData') }}",
                data: function(d) {
                    d.status = '{{ request("status") }}'; // تمرير الحالة الحالية من الرابط
                },
                dataSrc: function(json) {
                    // ✅ عرض القيم في أي مكان خارج الجدول
                    $('#boat_active').text(json.boat_active_count);
                    $('#boats').text(json.boat_count);
                    // $('#trip_completed_status').text(json.trip_completed_status);
                    // $('#sales_amount').text(json.sales_amount + '{{ __('owner.generated.item_93fe61') }}');

                    return json.data;
                }
            },

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'sale_number', name: 'sale_number'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'returned_at', name: 'returned_at'},
                {data: 'status', name: 'status'},
            ],
            responsive: true,

            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
        });
        $('#from_date, #to_date').change(function() {
            table.draw();
        });
    });
</script>

@endsection
