$(function () {
    if ($.fn.DataTable.isDataTable('#datatableDefault')) {
        $('#datatableDefault').DataTable().destroy();
    }

    var table = $('#datatableDefault').DataTable({
        processing: true,
        serverSide: true,
        language: {
            url: "/dashboard/assets/js/ar.json"
        },
        ajax: {
            url: window.routes.getTripData,
            data: function (data) {
                data.boat_id = boat_id;
            },
            dataSrc: function (json) {
                $('#trip_count').text(json.trip_count);
                $('#trip_waiting_status').text(json.trip_waiting_status);
                $('#trip_completed_status').text(json.trip_completed_status);
                $('#sales_amount').text(json.sales_amount + 'ر.س');

                return json.data;
            }
        },

        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center' },
            { data: 'number', name: 'number', className: 'text-center' },
            { data: 'captain', name: 'captain', className: 'text-center' },
            { data: 'start_date', name: 'start_date', className: 'text-center' },
            { data: 'end_date', name: 'end_date', className: 'text-center' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'total_sales', name: 'total_sales', className: 'text-center' },
        ],
        responsive: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
    });

});