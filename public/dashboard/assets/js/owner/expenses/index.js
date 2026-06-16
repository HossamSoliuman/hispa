let table;

function csrfToken() {
    return $('meta[name="csrf-token"]').attr('content');
}

function reloadExpensesTable() {
    if (table) {
        table.ajax.reload(null, false);
    } else {
        location.reload();
    }
}

$(document).ready(function () {
    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#expensesTable')) {
            $('#expensesTable').DataTable().destroy();
        }

        table = $('#expensesTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,

            ajax: {
                url: window.routes.expensesData,
                data: function (d) {
                    d.boat_id = $('#filterBoat').val();
                    d.category_id = $('#filterCategory').val();
                    d.status = $('#filterStatus').val();
                    d.from_date = $('#filterFromDate').val();
                    d.to_date = $('#filterToDate').val();
                },
                dataSrc: function (json) {
                    // updateStatistics(json);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'number', name: 'number', className: 'text-center' },
                {
                    data: 'date',
                    name: 'date',
                    className: 'text-center'
                },
                {
                    data: 'category_parent',
                    name: 'category_parent',
                    render: function (data) {
                        let color = 'secondary';
                        switch (data.type) {
                            case 'general':
                                color = 'primary'; break;
                            case 'government':
                                color = 'info'; break;
                            case 'operating':
                                color = 'warning'; break;
                            case 'maintenance':
                                color = 'success'; break;
                        }
                        return `<span class="badge bg-${color} expense-type-badge">${data.name}</span>`;
                    },
                    className: 'text-center'
                },
                {
                    data: 'expense_type',
                    name: 'expense_type',
                    className: 'text-center'
                },
                { data: 'boat_name', name: 'boat_name', className: 'text-center' },
                { data: 'vendor_name', name: 'vendor.name', className: 'text-center' },
                { data: 'formatted_total', name: 'total_price', className: 'text-center' },
                { data: 'formatted_final', name: 'final_price', className: 'text-center' },
                {
                    data: 'status_badge',
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    width: '150px',
                    className: 'text-center'
                }
            ],
            order: [[2, 'desc']],
        });
    }
    $('#applyFilters').click(function () {
        table.ajax.reload();
    });
    $('#clearFilters').click(function () {
        $('#filterBoat, #filterCategory, #filterStatus').val('');
        $('#filterFromDate, #filterToDate').val('');
        table.ajax.reload();
    });
    initDataTable();
});

function changeExpenseStatus(expenseId, newStatus) {
    const statusText = newStatus === 'paid' ? 'مدفوع' : 'معلق';

    Swal.fire({
        title: 'تغيير حالة المصروف',
        text: `هل أنت متأكد من تغيير الحالة إلى ${statusText}؟`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، تأكيد',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `${window.routes.expensesStatus.replace(':id', expenseId)}`,
                method: 'PATCH',
                data: {
                    status: newStatus,
                    _token: csrfToken()
                },
                success: function (response) {
                    Swal.fire('تم!', `تم تغيير حالة المصروف إلى ${statusText}`, 'success');
                    reloadExpensesTable();
                },
                error: function () {
                    Swal.fire('خطأ!', 'حدث خطأ أثناء تحديث الحالة', 'error');
                }
            });
        }
    });
}

function deleteExpense(expenseId) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: 'لن تتمكن من التراجع عن حذف هذا المصروف!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `${window.routes.expensesDestroy.replace(':id', expenseId)}`,
                method: 'DELETE',
                data: {
                    _token: csrfToken()
                },
                success: function (response) {
                    Swal.fire('تم الحذف!', response.message, 'success');
                    reloadExpensesTable();
                },
                error: function (xhr) {
                    let message = 'حدث خطأ أثناء حذف المصروف';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire('خطأ!', message, 'error');
                }
            });
        }
    });
}

function printExpense(expenseId) {
    window.open(`${window.routes.expensesPrint.replace(':id', expenseId)}`, '_blank');
}