@extends('owner.layouts.master')
@section('title')
{{__('owner.boat_type.title')}}
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

    <div class="row">
        <!-- {{ __('owner.generated.item_e1cafe') }} -->
        <div class="row mb-4 align-items-center justify-content-between">
            <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
                <h1 class="h3 fw-bold text-dark mb-1">{{__('owner.boat_type.page_header')}}</h1>
                <p class="text-muted mb-0"></p>
            </div>

            <div class="col-md-6 col-sm-12 text-md-end text-sm-start d-flex justify-content-md-end gap-2">
                <a href="{{route('owner.boat_types.create')}}" class="btn btn-black btn-sm">
                    <i class="bi bi-plus"></i> {{__('owner.boat_type.add_boat_type')}}
                </a>
            </div>

        </div>
    </div>
<div class="d-flex align-items-center mb-3">

    @can('create_ports')
    <div class="ms-auto">
        <a href="#modalCreate" data-bs-toggle="modal" class="btn btn-outline-theme"><i
                class="fa fa-plus-circle btn-success fa-fw me-1"></i>{{__('owner.boat_type.create.title')}}</a>
        {{-- Print button: open printable report in new tab --}}
        <form action="{{ route('owner.boat_types.print') }}" method="GET" target="_blank" class="d-inline ms-2">
            <button type="submit" class="btn btn-outline-secondary"><i class="fa fa-print me-1"></i>{{ __('owner.report.stock.print') }}</button>
        </form>
    </div>
    @endcan
</div>
<!-- BEGIN #modalCreate -->
<div class="modal fade" id="modalCreate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('owner.boat_type.create.title')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{route('owner.boat_types.store')}}" id="createForm" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="row">

                        <div class="col-6 ">

                            <div class="form-group ">
                                <label for="name_ar" class="form-label">{{__('owner.boat_type.name_ar')}}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name_ar" value="{{old('name_ar')}}"
                                    class="form-control  " required
                                    placeholder="{{__('owner.boat_type.name_ar')}}">


                                @error('name_ar') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>

                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="name_en" class="form-label">{{__('owner.boat_type.name_en')}}<span class="text-danger">*</span></label>
                                <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control" placeholder="{{__('owner.boat_type.name_en')}}">
                                @error('name_en') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="col-6 ">
                            <div class="form-check form-switch " style="margin-top: 35px">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label" for="status">{{__('owner.boat_type.activate')}}</label>
                                @error('status') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>
                        </div>


                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{__('owner.actions.close')}}</button>
                    <button type="submit" class="btn btn-outline-theme">{{__('owner.actions.save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END #modalCreate-->
<div class="modal fade" id="editModel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('owner.boat_type.edit.title')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{route('owner.boat_types.update','update')}}" id="editForm"
                method="post"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body justify-content-center">


                    <div class="row">
                        <input type="hidden" name="id" id="id" value="">
                        <div class="col-6 ">

                            <div class="form-group ">
                                <label for="name_ar" class="form-label">{{__('owner.boat_type.name_ar')}}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name_ar" id="name_ar" value="{{old('name_ar')}}"
                                    class="form-control  " required
                                    placeholder="{{__('owner.boat_type.name_ar')}}">


                                @error('name_ar') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>

                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="name_en" class="form-label">{{__('owner.boat_type.name_en')}}<span class="text-danger">*</span></label>
                                <input type="text" name="name_en" value="{{ old('name_en') }}" id="name_en" class="form-control" placeholder="{{__('owner.boat_type.name_en')}}">
                                @error('name_en') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-6 ">
                            <div class="form-check form-switch " style="margin-top: 35px">
                                <input type="checkbox" name="status" id="status" class="form-check-input" value="1">
                                <label class="form-check-label" for="status">{{__('owner.boat_type.activate')}}</label>
                                @error('status') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>
                        </div>


                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{__('owner.actions.close')}}</button>
                    <button type="submit" class="btn btn-outline-theme">{{__('owner.actions.save')}}</button>
                </div>
            </form>
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
                        <th>{{__('owner.boat_type.name')}}</th>
                        <th>{{__('owner.boat_type.status')}}</th>
                        <th>{{__('owner.boat_type.actions')}}</th>

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
                url: "{{ route('owner.getBoatTypeData') }}",
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

            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'name',
                    name: 'name'
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
                    url: "{{ url('owner/boat_types') }}/" + recordId,
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
