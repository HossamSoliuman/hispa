@extends('admin.layouts.master')
@section('title')
    {{ __('admin.menu.fish_types') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css') }}" rel="stylesheet">
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
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.menu.fish_types') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start d-flex justify-content-md-end gap-2">
            <button type="button" data-bs-toggle="modal" data-bs-target="#modalCreate" class="btn btn-outline-theme btn-equal">
                <i class="bi bi-plus-circle me-1"></i> {{ __('admin.fish.add_new.1') }}
            </button>
        </div>
    </div>

    <!-- BEGIN #modalCreate -->
    <div class="modal fade" id="modalCreate">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.fish.add_new_title.1') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.fish.store') }}" id="createForm" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="name_ar" class="form-label">{{ __('admin.fish.name_ar.0') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="name_ar" value="{{ old('name_ar') }}" class="form-control" required placeholder="{{ __('admin.fish.name_ar.0') }}">
                                    @error('name_ar') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="name_en" class="form-label">{{ __('admin.fish.name_en.0') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control" required placeholder="{{ __('admin.fish.name_en.0') }}">
                                    @error('name_en') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-4">
                                <div class="form-check form-switch" style="margin-top: 10px">
                                    <input type="checkbox" name="status" checked class="form-check-input" value="1">
                                    <label class="form-check-label" for="status">{{ __('admin.fish.activate.0') }}</label>
                                    @error('status') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                        <button type="submit" class="btn btn-outline-theme">{{ __('admin.actions.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END #modalCreate -->

    <!-- BEGIN #modelEdit -->
    <div class="modal fade" id="modelEdit">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.fish.edit_title.1') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.fish.update', 'update') }}" id="editForm" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="name_ar" class="form-label">{{ __('admin.fish.name_ar.0') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="name_ar" id="name_ar" class="form-control" required placeholder="{{ __('admin.fish.name_ar.0') }}">
                                    @error('name_ar') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="name_en" class="form-label">{{ __('admin.fish.name_en.0') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="name_en" id="name_en" class="form-control" required placeholder="{{ __('admin.fish.name_en.0') }}">
                                    @error('name_en') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-4">
                                <div class="form-check form-switch" style="margin-top: 10px">
                                    <input type="checkbox" name="status" id="status" class="form-check-input" value="1">
                                    <label class="form-check-label" for="status">{{ __('admin.fish.activate.0') }}</label>
                                    @error('status') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                        <button type="submit" class="btn btn-outline-theme">{{ __('admin.actions.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END #modelEdit -->

    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <div id="datatable" class="mb-5">
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text">
                    <thead>
                        <tr>
                            <th>{{ __('admin.table.id') }}</th>
                            <th>{{ __('admin.fish.name_ar.0') }}</th>
                            <th>{{ __('admin.fish.name_en.0') }}</th>
                            <th>{{ __('admin.fish.status.0') }}</th>
                            <th>{{ __('admin.fish.actions.0') }}</th>
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
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/jquery.validate.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>

    <script type="text/javascript">
        $(function() {
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }

            $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.getFishData') }}",
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'name_ar', name: 'name_ar' },
                    { data: 'name_en', name: 'name_en' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                responsive: true,
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
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
                title: '{{ __('admin.swal.confirm_title') }}',
                text: "{{ __('admin.swal.confirm_text') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('admin.swal.confirm_yes') }}',
                cancelButtonText: '{{ __('admin.swal.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/fish') }}/" + recordId,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            Swal.fire('{{ __('admin.swal.deleted') }}', response.message, 'success');
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message || '{{ __('admin.swal.unexpected_error') }}';
                            Swal.fire('{{ __('admin.swal.error') }}', message, 'error');
                        }
                    });
                }
            });
        }
    </script>

    <script>
        $('#modelEdit').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('.modal-body #id').val(button.data('id'));
            modal.find('.modal-body #name_ar').val(button.data('name_ar'));
            modal.find('.modal-body #name_en').val(button.data('name_en'));
            modal.find('.modal-body #status').prop('checked', button.data('status') == 1);
        });
    </script>
@endsection
