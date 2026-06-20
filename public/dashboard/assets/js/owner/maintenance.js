$(function () {
    let table = $('#datatableMaintenance').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.routes.maintenanceData,
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
            { data: 'category_name', name: 'category_name' },
            { data: 'date', name: 'date' },
            { data: 'technician', name: 'technician' },
            { data: 'estimated_cost', name: 'estimated_cost' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        responsive: true,
    });

    $('#maintenanceForm').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#maintenance_id').val();
        let actionUrl = id
            ? window.routes.maintenanceUpdate.replace(':id', id)
            : window.routes.maintenanceStore;
        let method = id ? 'PUT' : 'POST';

        form.find('.text-danger').remove();
        form.find('.is-invalid').removeClass('is-invalid');

        $.ajax({
            url: actionUrl,
            type: method,
            data: form.serialize(),
            success: function (response) {
                $('#maintenanceModal').modal('hide');
                $('#datatableMaintenance').DataTable().ajax.reload();
                form[0].reset();
                $('#maintenance_id').val('');
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

function editMaintenance(id) {
    $.get(window.routes.maintenanceEdit.replace(':id', id), function (data) {
        $('#maintenanceModalLabel').text('تعديل صيانة');
        $('#maintenance_id').val(data.id);
        $('[name="boat_id"]').val(data.boat_id).trigger('change');
        $('[name="category_id"]').val(data.category_id).trigger('change');
        $('[name="date"]').val(data.date);
        $('[name="next_maintenance_date"]').val(data.next_maintenance_date);
        $('[name="estimated_cost"]').val(data.estimated_cost);
        $('[name="technician"]').val(data.technician);
        $('[name="description"]').val(data.description);

        $('#maintenanceModal').modal('show');
    });
}

function deleteMaintenance(id) {
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
                url: window.routes.maintenanceDestroy.replace(':id', id),
                type: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    Swal.fire(swalOptions.deleted_title, res.message, 'success');
                    $('#datatableMaintenance').DataTable().ajax.reload();
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
    $.get(window.routes.maintenanceShow.replace(':id', id), function (data) {
        $('#maintenanceShowModalLabel').text('تفاصيل الصيانة');
        $('#maintenance_id').val(data.id);
        $('[name="boat_name"]').val(data.boat.name);
        $('[name="category_name"]').val(data.category.name);
        $('[name="date"]').val(data.date);
        $('[name="next_maintenance_date"]').val(data.next_maintenance_date);
        $('[name="estimated_cost"]').val(data.estimated_cost);
        $('[name="technician"]').val(data.technician);
        $('[name="description"]').val(data.description);

        $('#maintenanceShowModal').modal('show');
    });
}

$('#maintenanceModal').on('hidden.bs.modal', function () {
    let form = $('#maintenanceForm');
    form[0].reset();
    $('#maintenance_id').val('');
    form.find('.text-danger').remove();
    form.find('.is-invalid').removeClass('is-invalid');
});

$('#maintenanceModal').on('shown.bs.modal', function () {
    let form = $('#maintenanceForm');
    form.find('.text-danger').remove();
    form.find('.is-invalid').removeClass('is-invalid');
});
