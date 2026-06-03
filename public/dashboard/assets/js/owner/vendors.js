$(document).ready(function () {

    let table = $('#vendorsTable').DataTable({
        processing: true,
        serverSide: true,

        ajax: {
            url: window.routes.vendorsData,
            type: 'GET',
            data: function (d) {
                d.search = $('#searchInput').val();
                d.status = $('#statusFilter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'name', name: 'name', className: 'text-center' },
            { data: 'email', name: 'email', orderable: false, searchable: false, className: 'text-center' },
            { data: 'phone', name: 'phone', orderable: false, searchable: false, className: 'text-center' },
            { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        responsive: true,

    });

    $('#filterBtn').on('click', function () {
        table.ajax.reload();
    });

    $('#resetBtn').on('click', function () {
        $('#searchInput').val('');
        $('#statusFilter').val('');
        table.ajax.reload();
    });

    $(document).on('input change', 'input, select, textarea', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });

    function handleValidationErrors(form, errors) {
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        $.each(errors, function (key, messages) {
            let input = form.find('[name="' + key + '"]');
            if (input.length) {
                input.addClass('is-invalid');
                input.after('<div class="invalid-feedback d-block">' + messages[0] + '</div>');
            }
        });
    }

    $('#vendorForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = form.data('id');
        let url = id ? window.routes.vendorsUpdate.replace(':id', id) : window.routes.vendorsStore;
        let formData = new FormData(form[0]);
        if (id) formData.append('_method', 'PUT');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                toastr.success(id ? "تم التحديث بنجاح" : "تمت الإضافة بنجاح");
                window.location.href = window.routes.vendorsIndex;
            },
            error: function (xhr) {
                if (xhr.status === 422) handleValidationErrors(form, xhr.responseJSON.errors);
                else toastr.error("حدث خطأ غير متوقع، حاول لاحقاً");
            }
        });
    });

    $(document).on('click', '.deleteVendor', function () {
        let id = $(this).data('id');
        if (!id) return;
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "هل أنت متأكد من حذف هذا المورد؟",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، حذفه!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: window.routes.vendorsDestroy.replace(':id', id),
                    method: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        toastr.success("تم الحذف بنجاح");
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        toastr.error("حدث خطأ أثناء الحذف، حاول لاحقاً");
                    }
                });
            }
        });
    });
});

