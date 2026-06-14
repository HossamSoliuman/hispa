@extends('owner.layouts.master')

@section('title', __('owner.categories.title'))
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
                <h1 class="h3 fw-bold text-dark mb-1">{{__('owner.categories.title')}}</h1>
                <p class="text-muted mb-0"></p>
            </div>

            <div class="col-md-6 col-sm-12 text-md-end text-sm-start d-flex justify-content-md-end gap-2">
                <button class="btn btn-black btn-sm addSubBtn" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus"></i>{{ __('owner.categories.add_new') }}
                </button>
            </div>

        </div>
    </div>

    <div class="card border-0 mb-3">
        <div class="card-body">
            <div class="table-responsive">
                <table id="categoriesTable" class="table table-sm table-bordered table-hover text-center small-text">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('owner.categories.name') }}</th>
                            <th>{{ __('owner.categories.status') }}</th>
                            <th>{{ __('owner.categories.parent') }}</th>
                            <th>{{ __('owner.categories.actions') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-l">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalTitle">{{ __('owner.categories.add_new_title') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="categoryForm">
                    @csrf
                    <input type="hidden" id="categoryId">
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="type" value="child"><!-- {{ __('owner.generated.item_e73629') }} -->

                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nameAr" class="form-label">{{ __('owner.categories.name_ar') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nameAr" name="name_ar">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nameEn" class="form-label">{{ __('owner.categories.name_en') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nameEn" name="name_en">
                            </div>
                        </div>

                        <!-- {{ __('owner.generated.item_7c7254') }} -->
                        <div class="col-md-12 parent-field">
                            <div class="mb-3">
                                <label for="parent_id" class="form-label">{{ __('owner.categories.parent') }}</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">{{ __('owner.actions.choose') }}</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('owner.categories.status') }}</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="1">{{ __('owner.status.active') }}</option>
                                    <option value="0">{{ __('owner.status.inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div><!-- modal-body -->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('owner.actions.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('owner.actions.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        window.routes = {
            categoriesData: "{{ route('owner.getCategoriesData') }}",
            categoriesStore: "{{ route('owner.categories.store') }}",
            categoriesUpdate: "{{ route('owner.categories.update', ':id') }}",
            categoriesDestroy: "{{ route('owner.categories.destroy', ':id') }}",
        };
    </script>
    <script>
        $(document).ready(function() {
            let categoriesTable;
            let isEditMode = false;
            let currentEditId = null;
            let appLocale = '{{ app()->getLocale() }}';
            let languageOptions = {};
            if (appLocale === 'ar') {
                languageOptions = {
                    url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                };
            }
            categoriesTable = $('#categoriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: window.routes.categoriesData,
                language: languageOptions,
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'parent_name'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                responsive: false, scrollX: true
            });

            $(document).on('click', '.addSubBtn', function() {
                resetCategoryForm();
                $('#modalTitle').text('{{ __('owner.categories.add_new_title') }}');
                $('#addCategoryModal').modal('show');
            });

            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                const url = isEditMode ? window.routes.categoriesUpdate.replace(':id', currentEditId) :
                    window.routes.categoriesStore;
                $('#formMethod').val(isEditMode ? 'PUT' : 'POST');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: form.serialize(),
                    success: function() {
                        $('#addCategoryModal').modal('hide');
                        categoriesTable.ajax.reload();
                        toastr.success(isEditMode ? '{{ __('owner.generated.item_fe2368') }}' :
                            '{{ __('owner.generated.item_26a187') }}');
                        resetCategoryForm();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                let input = form.find('[name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.after('<span class="text-danger">' + messages[0] +
                                    '</span>');
                            });
                        } else {
                            Swal.fire('{{ __('owner.generated.item_e4c800') }}');
                        }
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                const data = $(this).data();
                isEditMode = true;
                currentEditId = data.id;
                $('#modalTitle').text('{{ __('owner.categories.edit_title') }}');
                $('#categoryId').val(data.id);
                $('#nameAr').val(data.name_ar);
                $('#nameEn').val(data.name_en);
                $('#parent_id').val(data.parent_id);
                $('#status').val(data.status);

                $('#addCategoryModal').modal('show');
            });

            $(document).on('click', '.deleteBtn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: '{{ __('owner.swal.confirm_title') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __('owner.swal.confirm_yes') }}',
                    cancelButtonText: '{{ __('owner.swal.cancel') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: window.routes.categoriesDestroy.replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function() {
                                categoriesTable.ajax.reload();
                                toastr.success('{{ __('owner.categories.deleted') }}');
                            },
                            error: function() {
                                toastr.error('{{ __('owner.swal.error') }}');
                            }
                        });
                    }
                });
            });

            function resetCategoryForm() {
                clearValidationErrors($('#categoryForm'));
                $('#categoryForm')[0].reset();
                $('#categoryId').val('');
                $('#formMethod').val('POST');
                $('#modalTitle').text('{{ __('owner.categories.add_new_title') }}');
                isEditMode = false;
                currentEditId = null;
            }

            function clearValidationErrors(form) {
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.text-danger').remove();
            }
        });
    </script>
@endsection
