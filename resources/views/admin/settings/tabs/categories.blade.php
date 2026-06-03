<div class="d-flex align-items-center mb-3">
    <div>
        <h4 class="mb-2">{{ __('admin.categories.title.0') ?? __('admin.categories.page_header') }}</h4>
    </div>
    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">
        <button class="btn btn-outline-theme btn-equal addSubBtn" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.regions.add_new') }}
        </button>
    </div>
</div>

<div class="card border-0 mb-3">
    <div class="card-body">
        <div class="table-responsive">
            <table id="categoriesTable" class="table table-sm table-bordered table-hover text-center small-text" style="width: 100% !important;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('admin.categories.name') ?? __('admin.categories.name_ar') }}</th>
                        <th>{{ __('admin.categories.status') }}</th>
                        <th>{{ __('admin.categories.parent') ?? __('admin.fish.parent') }}</th>
                        <th>{{ __('admin.categories.actions') ?? __('admin.actions.edit') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="modalTitle">{{ __('admin.categories.add_new_title') ?? __('admin.regions.add_new_title') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId">
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="type" value="child">
                <input type="hidden" name="tab" value="categories">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nameAr" class="form-label">{{ __('admin.categories.name_ar') ?? __('admin.regions.name_ar') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nameAr" name="name_ar">
                    </div>
                    <div class="mb-3">
                        <label for="nameEn" class="form-label">{{ __('admin.categories.name_en') ?? __('admin.regions.name_en') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nameEn" name="name_en">
                    </div>
                    <div class="mb-3">
                        <label for="parent_id" class="form-label">{{ __('admin.categories.parent') ?? __('admin.fish.parent') }}</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">{{ __('admin.actions.choose') }}</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="statusCat" class="form-label">{{ __('admin.categories.status') }}</label>
                        <select class="form-select" id="statusCat" name="status">
                            <option value="1">{{ __('admin.status.active') }}</option>
                            <option value="0">{{ __('admin.status.inactive') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('admin.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    var categoriesTable;
    var isEditMode = false;
    var currentEditId = null;
    var appLocale = '{{ app()->getLocale() }}';
    var languageOptions = appLocale === 'ar' ? { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" } : {};
    var routes = {
        categoriesData: "{{ route('admin.getCategoriesData') }}",
        categoriesStore: "{{ route('admin.categories.store') }}",
        categoriesUpdate: "{{ route('admin.categories.update', ':id') }}",
        categoriesDestroy: "{{ route('admin.categories.destroy', ':id') }}",
    };

    $(document).ready(function() {
        if ($.fn.DataTable && $('#categoriesTable').length && !$.fn.DataTable.isDataTable('#categoriesTable')) {
            categoriesTable = $('#categoriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: routes.categoriesData,
                language: languageOptions,
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name' },
                    { data: 'status' },
                    { data: 'parent_name' },
                    { data: 'action', orderable: false, searchable: false },
                ],
                responsive: true
            });
        }

        $(document).on('click', '.addSubBtn', function() {
            $('#categoryForm')[0].reset();
            $('#categoryId').val('');
            $('#formMethod').val('POST');
            isEditMode = false;
            currentEditId = null;
            $('#modalTitle').text('{{ __('admin.categories.add_new_title') ?? __('admin.regions.add_new_title') }}');
            $('#addCategoryModal').modal('show');
        });

        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = isEditMode ? routes.categoriesUpdate.replace(':id', currentEditId) : routes.categoriesStore;
            $.ajax({
                url: url,
                method: 'POST',
                data: form.serialize(),
                success: function() {
                    $('#addCategoryModal').modal('hide');
                    if (categoriesTable) categoriesTable.ajax.reload();
                    if (typeof toastr !== 'undefined') toastr.success(isEditMode ? '{{ __('admin.actions.updated') ?? "Updated" }}' : '{{ __('admin.actions.saved') ?? "Saved" }}');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            form.find('[name="' + field + '"]').addClass('is-invalid').after('<span class="text-danger">' + messages[0] + '</span>');
                        });
                    } else if (typeof toastr !== 'undefined') toastr.error('{{ __('admin.swal.error') }}');
                }
            });
        });

        $(document).on('click', '.editBtn', function() {
            var data = $(this).data();
            isEditMode = true;
            currentEditId = data.id;
            $('#formMethod').val('PUT');
            $('#modalTitle').text('{{ __('admin.categories.edit_title') ?? __('admin.regions.edit_title') }}');
            $('#categoryId').val(data.id);
            $('#nameAr').val(data.name_ar);
            $('#nameEn').val(data.name_en);
            $('#parent_id').val(data.parent_id);
            $('#statusCat').val(data.status);
            $('#addCategoryModal').modal('show');
        });

        $(document).on('click', '.deleteBtn', function() {
            var id = $(this).data('id');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '{{ __('admin.swal.confirm_title') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __('admin.swal.confirm_yes') }}',
                    cancelButtonText: '{{ __('admin.swal.cancel') ?? __('admin.actions.close') }}'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: routes.categoriesDestroy.replace(':id', id),
                            type: 'DELETE',
                            data: { _token: $('meta[name="csrf-token"]').attr('content') },
                            success: function() {
                                if (categoriesTable) categoriesTable.ajax.reload();
                                if (typeof toastr !== 'undefined') toastr.success('{{ __('admin.regions.deleted') ?? "Deleted" }}');
                            },
                            error: function() {
                                if (typeof toastr !== 'undefined') toastr.error('{{ __('admin.swal.error') }}');
                            }
                        });
                    }
                });
            } else if (confirm('{{ __('admin.swal.confirm_text') }}')) {
                $.ajax({
                    url: routes.categoriesDestroy.replace(':id', id),
                    type: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        if (categoriesTable) categoriesTable.ajax.reload();
                    }
                });
            }
        });
    });
})();
</script>
