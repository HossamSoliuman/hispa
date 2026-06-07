$(function () {
    let table = $('#datatableinspections').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.routes.inspectionData,
            data: function (d) {
                if (window.currentBoatId) {
                    d.boat_id = window.currentBoatId;
                }
            }
        },
        language: languageOptions,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'boat_name', name: 'boat_name' },
            { data: 'status_badge', name: 'status_badge' },
            { data: 'check_date', name: 'check_date' },
            { data: 'next_check', name: 'next_check' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        responsive: true,
    });

    $('#inspectionForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);

        let id = $('#inspection_id').val();
        let actionUrl = id
            ? window.routes.inspectionUpdate.replace(':id', id)
            : window.routes.inspectionStore;
        let method = id ? 'PUT' : 'POST';

        form.find('.text-danger').remove();
        form.find('.is-invalid').removeClass('is-invalid');

        $.ajax({
            url: actionUrl,
            type: method,
            data: form.serialize(),
            success: function (response) {
                $('#inspectionModal').modal('hide');
                $('#datatableinspections').DataTable().ajax.reload();
                form[0].reset();
                $('#inspection_id').val('');
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


});

function editInspection(id) {

    $.get(window.routes.inspectionEdit.replace(':id', id), function (data) {
        $('#inspectionModalLabel').text('تعديل فحص');
        $('[name="id"]').val(data.id).trigger('change');
        $('[name="boat_id"]').val(data.boat_id).trigger('change');
        $('[name="status"]').val(data.status).trigger('change');
        $('[name="check_date"]').val(data.check_date);
        $('[name="next_check"]').val(data.next_check);
        $('#inspectionModal').modal('show');
    });
}

function deleteInspection(id) {
    Swal.fire({
        title: swalOptions.title,
        text: swalOptions.text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: swalOptions.confirmButtonText,
        cancelButtonText: swalOptions.cancelButtonText
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: window.routes.inspectionDestroy.replace(':id', id),
                type: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    Swal.fire(swalOptions.deleted_title, res.message, 'success');
                    $('#datatableinspections').DataTable().ajax.reload();
                },
                error: function (xhr) {
                    let message = xhr.responseJSON?.message || 'حدث خطأ غير متوقع';
                    Swal.fire(swalOptions.error_title, message, 'error');
                }
            });
        }
    });
}

function showDetails(id) {
    $.get(window.routes.inspectionShow.replace(':id', id), function (data) {
        $('#inspectionShowModalLabel').text('تفاصيل الفحص');
        $('#inspection_id').val(data.id);
        $('[name="boat_name"]').val(data.boat.name);
        $('[name="category_name"]').val(data.category.name);
        $('[name="date"]').val(data.date);
        $('[name="estimated_cost"]').val(data.estimated_cost);
        $('[name="technician"]').val(data.technician);
        $('[name="description"]').val(data.description);

        $('#inspectionShowModal').modal('show');
    });
}

$('#inspectionModal').on('hidden.bs.modal', function () {
    let form = $('#inspectionForm');
    form[0].reset();
    $('#inspection_id').val('');
    form.find('.text-danger').remove();
    form.find('.is-invalid').removeClass('is-invalid');
});

$('#inspectionModal').on('shown.bs.modal', function () {
    let form = $('#inspectionForm');
    form.find('.text-danger').remove();
    form.find('.is-invalid').removeClass('is-invalid');
});
