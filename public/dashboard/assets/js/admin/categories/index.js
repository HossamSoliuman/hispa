$(document).ready(function () {
    let categoriesTable;
    let isEditMode = false;
    let currentEditId = null;

    categoriesTable = $('#categoriesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.routes.categoriesData,
        language: {
            url: "/dashboard/assets/js/ar.json"
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
        },
        {
            data: 'name_ar',
            name: 'name_ar',
            orderable: false
        },
        {
            data: 'name_en',
            name: 'name_en',
            orderable: false
        },
        {
            data: 'type',
            name: 'type',
            orderable: false
        },
        {
            data: 'status',
            name: 'status',
            orderable: false
        },
        {
            data: 'parent_id',
            name: 'parent_id',
            orderable: false
        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
        }
        ],
        responsive: true
    });

    // Submit form (add / edit)
    $('#categoryForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        const url = isEditMode ?
            window.routes.categoriesUpdate.replace(':id', currentEditId) :
            window.routes.categoriesStore;
        $('#formMethod').val(isEditMode ? 'PUT' : 'POST');

        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            success: function (response) {
                $('#addCategoryModal').modal('hide');
                categoriesTable.ajax.reload();
                toastr.success(isEditMode ? 'تم تحديث التصنيف بنجاح!' : 'تم إضافة التصنيف بنجاح!');
                resetCategoryForm();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (field, messages) {
                        let input = form.find('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.after('<span class="text-danger">' + messages[0] + '</span>');
                    });
                }
                else {
                    let message = xhr.responseJSON?.message || 'حدث خطأ غير متوقع';
                    Swal.fire('هناك خطأ ما ');
                }
            }
        });
    });

    // Edit
    $(document).on('click', '.editBtn', function () {
        const data = $(this).data();
        isEditMode = true;
        currentEditId = data.id;
        $('#modalTitle').text('تعديل التصنيف الرئيسي');
        $('#categoryId').val(data.id);
        $('#nameAr').val(data.name_ar);
        $('#nameEn').val(data.name_en);
        $('#type').val(data.type);
        $('#parent_id').val(data.parent_id);
        $('#status').val(data.status);
        $('#addCategoryModal').modal('show');
    });

    // Delete
    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'هل أنت متأكد؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: window.routes.categoriesDestroy.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        categoriesTable.ajax.reload();
                        toastr.success('تم حذف التصنيف بنجاح!');
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        toastr.error('حدث خطأ أثناء حذف التصنيف');
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
        $('#modalTitle').text('إضافة تصنيف رئيسي');
        isEditMode = false;
        currentEditId = null;
    }

    $('#addCategoryModal').on('hide.bs.modal', function () {
        $(this).find(':focus').blur();
    });

    $('#addCategoryModal').on('hidden.bs.modal', function () {
        resetCategoryForm();
    });
    function clearValidationErrors(form) {
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.text-danger').remove();
    }
    
});