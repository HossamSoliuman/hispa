@extends('admin.layouts.master')
@section('title')
{{__('admin.menu.boat_types')}}
@endsection
@section('css')
<link href="{{asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet">
<link href="{{asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css')}}" rel="stylesheet">
<link href="{{asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet">
<link href="{{asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css')}}" rel="stylesheet">
<style>
    #datatableDefault th, #datatableDefault td {
        text-align: center !important;
        vertical-align: middle;
    }
    .small-text th, .small-text td {
        font-size: 12px;
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
        <h2 class="fw-bold text-dark mb-1">{{__('admin.menu.boat_types')}}</h2>
    </div>
    <div class="col-md-6 col-sm-12 text-md-end text-sm-start d-flex justify-content-md-end gap-2">
        <a href="{{route('admin.boat_types.create')}}" class="btn btn-outline-theme btn-equal">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{__('admin.boat_types.add')}}
        </a>
    </div>
</div>

<div class="modal fade" id="modalCreate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('admin.boat_types.create')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{route('admin.boat_types.store')}}" id="createForm" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name_ar" class="form-label">{{__('admin.boat_types.name_ar')}}<span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" value="{{old('name_ar')}}" class="form-control" required placeholder="{{__('admin.boat_types.name_ar')}}">
                                @error('name_ar') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name_en" class="form-label">{{__('admin.boat_types.name_en')}}<span class="text-danger">*</span></label>
                                <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control" required placeholder="{{__('admin.boat_types.name_en')}}">
                                @error('name_en') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch" style="margin-top: 35px">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label" for="status">{{__('admin.boat_types.activate')}}</label>
                                @error('status') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{__('admin.actions.close')}}</button>
                    <button type="submit" class="btn btn-outline-theme">{{__('admin.actions.save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('admin.boat_types.edit')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{route('admin.boat_types.update','update')}}" id="editForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id" id="id" value="">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name_ar" class="form-label">{{__('admin.boat_types.name_ar')}}<span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" id="name_ar" value="{{old('name_ar')}}" class="form-control" required placeholder="{{__('admin.boat_types.name_ar')}}">
                                @error('name_ar') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name_en" class="form-label">{{__('admin.boat_types.name_en')}}<span class="text-danger">*</span></label>
                                <input type="text" name="name_en" value="{{ old('name_en') }}" id="name_en" class="form-control" required placeholder="{{__('admin.boat_types.name_en')}}">
                                @error('name_en') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch" style="margin-top: 35px">
                                <input type="checkbox" name="status" id="status" class="form-check-input" value="1">
                                <label class="form-check-label" for="status">{{__('admin.boat_types.activate')}}</label>
                                @error('status') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{__('admin.actions.close')}}</button>
                    <button type="submit" class="btn btn-outline-theme">{{__('admin.actions.save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    @include('owner.components.stat-card', [
        'title' => __('admin.boat_types.total_types') ?? __('admin.menu.boat_types'),
        'value' => new \Illuminate\Support\HtmlString('<div id="boat_type_total">0</div>'),
        'icon' => 'bi bi-list-ul',
        'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
        'colClass' => 'col-md-4 col-sm-6 mb-3',
    ])

    @include('owner.components.stat-card', [
        'title' => __('admin.boat_types.active_types') ?? __('admin.status.active'),
        'value' => new \Illuminate\Support\HtmlString('<div id="boat_type_active">0</div>'),
        'icon' => 'bi bi-check-circle-fill',
        'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
        'colClass' => 'col-md-4 col-sm-6 mb-3',
    ])

    @include('owner.components.stat-card', [
        'title' => __('admin.boat_types.inactive_types') ?? __('admin.status.inactive'),
        'value' => new \Illuminate\Support\HtmlString('<div id="boat_type_inactive">0</div>'),
        'icon' => 'bi bi-x-circle-fill',
        'gradient' => 'linear-gradient(135deg, #c0392b, #e74c3c)',
        'colClass' => 'col-md-4 col-sm-6 mb-3',
    ])
</div>

@php
    $boatTypeStatusOptions = [
        ['value' => '', 'label' => __('admin.filters.all')],
        ['value' => '1', 'label' => __('admin.status.active')],
        ['value' => '0', 'label' => __('admin.status.inactive')],
    ];
@endphp
<x-admin.components.datatable-filters
    formId="boatTypesFilters"
    :filters="[
        ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.boat_types.name'), 'value' => request('search')],
        ['type' => 'select-static', 'id' => 'status', 'name' => 'status', 'label' => __('admin.boat_types.status'), 'options' => $boatTypeStatusOptions, 'selected' => request('status')],
    ]"
    :showArrow="false"
/>

<div class="tab-content py-4">
    <div class="tab-pane fade show active" id="allTab">
        <div id="datatable" class="mb-5">
            <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text">
                <thead>
                    <tr>
                        <th>{{__('admin.table.id')}}</th>
                        <th>{{__('admin.boat_types.name')}}</th>
                        <th>{{__('admin.boat_types.status')}}</th>
                        <th>{{__('admin.boat_types.actions')}}</th>
                    </tr>
                </thead>
                <tbody></tbody>
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
<script src="{{asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.js')}}"></script>
<script src="{{asset('dashboard/assets/js/demo/table-plugins.demo.js')}}"></script>
<script src="{{asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js')}}"></script>
<script src="{{asset('dashboard/assets/js/jquery.validate.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    $(function() {
        let appLocale = '{{ app()->getLocale() }}';
        let languageOptions = {};
        if (appLocale === 'ar') {
            languageOptions = { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" };
        }
        if ($.fn.DataTable.isDataTable('#datatableDefault')) {
            $('#datatableDefault').DataTable().destroy();
        }

        var table = $('#datatableDefault').DataTable({
            processing: true,
            serverSide: true,
            language: languageOptions,
            ajax: {
                url: "{{ route('admin.getBoatTypeData') }}",
                data: function(d) {
                    // نربط فلاتر الفورم مع طلب الداتا تيبل
                    d.search = $('#boatTypesFilters').find('input[name="search"]').val();
                    d.status = $('#boatTypesFilters').find('select[name="status"]').val();
                },
                dataSrc: function(json) {
                    // تحديث كروت الإحصائيات مع نفس نتائج الفلترة/البحث
                    $('#boat_type_total').text(json.total_count || 0);
                    $('#boat_type_active').text(json.active_count || 0);
                    $('#boat_type_inactive').text(json.inactive_count || 0);
                    return json.data;
                }
            },
            columns: [{
                data: 'DT_RowIndex', name: 'DT_RowIndex'
            }, {
                data: 'name', name: 'name'
            }, {
                data: 'status', name: 'status'
            }, {
                data: 'action', name: 'action', orderable: false, searchable: false
            }],
            responsive: true,
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        });

        $('#boatTypesFilters').on('change keyup', 'select, input', function() {
            $('#datatableDefault').DataTable().ajax.reload();
        });
    });
</script>

<script>
    $("#createForm").validate();
    $("#editForm").validate();
</script>

<script>
    function deleteRecord(recordId) {
        Swal.fire({
            title: '{{__('admin.swal.confirm_title')}}',
            text: "{{__('admin.swal.confirm_text')}}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{__('admin.swal.confirm_yes')}}',
            cancelButtonText: '{{__('admin.swal.cancel')}}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/boat_types') }}/" + recordId,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('{{__('admin.swal.deleted')}}', response.message, 'success');
                        $('#datatableDefault').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || '{{__('admin.swal.error_occurred')}}';
                        Swal.fire('{{__('admin.swal.error')}}', message, 'error');
                    }
                });
            }
        });
    }
</script>

<script>
    $('#editModel').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var name_ar = button.data('name_ar')
        var name_en = button.data('name_en')
        var status = button.data('status')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name_ar').val(name_ar);
        modal.find('.modal-body #name_en').val(name_en);
        modal.find('.modal-body #status').prop('checked', status == 1);
    });
</script>
@endsection
