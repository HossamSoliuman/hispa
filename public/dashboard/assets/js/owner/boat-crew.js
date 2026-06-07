$(function () {
    $('#datatableCrew').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.routes.crewData,
            data: function (d) {
                if (window.currentBoatId) {
                    d.boat_id = window.currentBoatId;
                }
            }
        },
        language: languageOptions,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'job_title', name: 'job_title' },
            { data: 'phone', name: 'phone' },
            { data: 'id_number', name: 'id_number' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        responsive: true,
    });

    // Read-only details modal for a crew member (boat profile is view-only).
    // Read raw attributes (not jQuery .data()) so numeric-looking values such as
    // phone or ID numbers keep their leading zeros and exact format.
    $('#datatableCrew').on('click', '.viewCrewBtn', function () {
        const btn = this;
        const $modal = $('#crewViewModal');
        $modal.find('[data-field]').each(function () {
            const value = btn.getAttribute('data-' + $(this).data('field'));
            $(this).text(value === null || value === '' ? '--' : value);
        });
        $modal.modal('show');
    });
});
