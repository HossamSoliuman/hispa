@extends('owner.layouts.master')
@section('title')
{{__('owner.assets.title')}}
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
                <h2 class="fw-bold text-dark mb-2">{{__('owner.assets.title')}}</h2>                
            </div>

            <div class="col-md-6 col-sm-12 text-md-end text-sm-start d-flex justify-content-md-end gap-2">
                <a href="{{route('owner.assets.create')}}" class="btn btn-outline-theme btn-equal">
                    <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{__('owner.assets.create')}}
                </a>
            </div>

        </div>

    <div class="row mb-4">
    @include('owner.components.stat-card', [
        'title' => __('owner.assets.all_count'),
        'value' => '<span id="totalAssets">0</span>',
        'icon' => 'bi bi-bar-chart-line',
        'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
        'colClass' => 'col-md-3 col-sm-6 mb-3'
    ])

    @include('owner.components.stat-card', [
        'title' => __('owner.assets.boat'),
        'value' => '<span id="totalBoat">0</span> ',
        'icon' => 'bi bi-tsunami',
        'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
        'colClass' => 'col-md-3 col-sm-6 mb-3'
    ])

    @include('owner.components.stat-card', [
        'title' => __('owner.assets.fishing_equipment'),
        'value' => '<span id="totalFishingEquipment">0</span>',
        'icon' => 'bi bi-tools',
        'gradient' => 'linear-gradient(135deg, #16a085, #1abc9c)',
        'colClass' => 'col-md-3 col-sm-6 mb-3'
    ])

    @include('owner.components.stat-card', [
        'title' => __('owner.assets.other'),
        'value' => '<span id="totalOther">0</span>',
        'icon' => 'bi bi-graph-up-arrow',
        'gradient' => 'linear-gradient(135deg, #f39c12, #f1c40f)',
        'colClass' => 'col-md-3 col-sm-6 mb-3'
    ])
</div>


<!-- Filters -->
<div class="card shadow-sm border-0 mt-4">
    <div class="card-header">
        <h5 class="card-title">{{ __('owner.catch.filters.title') }}</h5>
    </div>
    <div class="card-body">
        <div class="row align-items-end gy-2">

            <div class="col-md-2">
                <label class="form-label">{{ __('owner.generated.asset_type') }}</label>
                <select id="asset_type" class="form-control form-select">
                    <option value="">{{ __('owner.dalal_invoices.filters.all') }}</option>
                    <option value="boat">{{ __('owner.generated.boat') }}</option>
                    <option value="fishing_equipment">{{ __('owner.generated.fishing_equipment') }}</option>
                    <option value="other">{{ __('owner.generated.other_asset') }}</option>
                </select>
            </div>
      

        </div>
        <div class="text-end mt-3">
            <button class="btn btn-success sm me-2"><i class="bi bi-search"></i> {{ __('owner.catch.filters.search') }}</button>
            <button class="btn btn-light btn-sm me-2"><i class="bi bi-x-circle"></i> {{ __('owner.catch.filters.clear') }}</button>
        </div>
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
                        <th>{{__('owner.assets.name')}}</th>
                        <th>{{__('owner.assets.type')}}</t/h>
                        <th>{{__('owner.assets.status')}}</th>
                        <th>{{__('owner.assets.actions')}}</th>

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
                url: "{{ route('owner.getAssetsData') }}",
                data: function(d) {
                    d.asset_type = $('#asset_type').val();
                },
                dataSrc: function(json) {
                    let s = json.summary;
                    $('#totalAssets').text(s.total_assets.toLocaleString());
                    $('#totalBoat').text(s.total_boat.toLocaleString());
                    $('#totalFishingEquipment').text(s.total_fishing_equipment.toLocaleString());
                    $('#totalOther').text(s.total_other.toLocaleString());

                    return json.data;
                }
            },

            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'status',
                    name: 'status'
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: true,
                    searchable: false
                },
            ],
            responsive: false, scrollX: true,

            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
        });
        $('#from_date, #to_date').change(function() {
            table.draw();
        });

        $('.btn-success').on('click', function() {
                table.ajax.reload();
            });

            // زر مسح الكل يعيد تعيين الفلاتر ويحدث الجدول
            $('.btn-light').on('click', function() {
                $('#fish_id').val('');
                $('#boat_id').val('');
                $('input[type=date]').val('');
                table.ajax.reload();
            });
            
    });
</script>

<script>
    $("#createForm").validate();
</script>
<script>
    function deleteRecord(recordId) {
        Swal.fire({
            title: '{{__('owner.swal.confirm_title')}}',
            text: "{{__('owner.swal.confirm_text')}}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{__('owner.swal.confirm_yes')}}',
            cancelButtonText: '{{__('owner.swal.cancel')}}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('owner/assets') }}/" + recordId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('{{__('owner.swal.deleted')}}', response.message, 'success');
                        $('#datatableDefault').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || '{{__('owner.swal.unexpected_error')}}';
                        Swal.fire('{{__('owner.swal.error')}}', message, 'error');
                    }

                });
            }
        });
    }
</script>

<script>
    // Model Edit
    $('#editModel').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var name_ar = button.data('name_ar')
        var name_en = button.data('name_en')
        var status = button.data('status')


        // var image = button.data('image')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name_ar').val(name_ar);
        modal.find('.modal-body #name_en').val(name_en);
        modal.find('.modal-body #status').prop('checked', status == 1);


    });
</script>

@endsection
