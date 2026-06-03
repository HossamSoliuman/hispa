$(document).ready(function () {
    let dataTable;
    let isEditMode = false;
    let currentEditId = null;

    dataTable = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.routes.fishingEquipmentsData,
        language: {
            url: "/dashboard/assets/js/ar.json"
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false,
            className: 'text-center'
        },
        {
            data: 'name_ar',
            name: 'name_ar',
            orderable: false,
            className: 'text-center'
        },
        {
            data: 'name_en',
            name: 'name_en',
            orderable: false,
            className: 'text-center'
        },
        {
            data: 'quantity',
            name: 'quantity',
            orderable: false,
            className: 'text-center'
        },
        {
            data: 'status',
            name: 'status',
            orderable: false,
            className: 'text-center'
        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            className: 'text-center'
        }
        ],
        responsive: true
    });

    // Submit form (add / edit)
    $('#modalForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        const url = isEditMode ?
            window.routes.fishingEquipmentsUpdate.replace(':id', currentEditId) :
            window.routes.fishingEquipmentsStore;
        $('#formMethod').val(isEditMode ? 'PUT' : 'POST');

        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            success: function (response) {
                $('#addModal').modal('hide');
                dataTable.ajax.reload();
                toastr.success(isEditMode ? 'تم تحديث العنصر بنجاح!' : 'تم إضافة العنصر بنجاح!');
                resetForm();
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
        $('#modalTitle').text('تعديل العنصر');
        $('#id').val(data.id);
        $('#name').val(data.name);
        $('#nameEn').val(data.name_en);
        $('#status').val(data.status);
        $('#addModal').modal('show');
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
                    url: window.routes.fishingEquipmentsDestroy.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        dataTable.ajax.reload();
                        toastr.success('تم حذف العنصر بنجاح!');
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        toastr.error('حدث خطأ أثناء حذف العنصر');
                    }
                });
            }
        });
    });


    function resetForm() {
        clearValidationErrors($('#modalForm'));
        $('#modalForm')[0].reset();
        $('#id').val('');
        $('#formMethod').val('POST');
        $('#modalTitle').text('إضافة عنصر');
        isEditMode = false;
        currentEditId = null;
    }

    $('#addModal').on('hide.bs.modal', function () {
        $(this).find(':focus').blur();
    });

    $('#addModal').on('hidden.bs.modal', function () {
        resetForm();
    });
    function clearValidationErrors(form) {
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.text-danger').remove();
    }
    
});