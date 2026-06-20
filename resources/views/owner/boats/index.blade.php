@extends('owner.layouts.master')

@section('title')
    {{ __('owner.boats.title') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css') }}" rel="stylesheet">
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

        .stat-card {
            min-height: 150px;
            height: 100%;
            border-radius: 12px;
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 5px;
        }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('owner.boats.title') }}</h2>
        </div>

        <div class="col-md-6 col-sm-12 text-md-end text-sm-start d-flex justify-content-md-end gap-2">
            <a href="{{ route('owner.reports.print.all_boats') }}" target="_blank"
                class="btn btn-outline-info btn-border-radius">
                <i class="bi bi-printer me-1"></i> {{ __('owner.boats.print_all_boats') }}
            </a>
        </div>

    </div>


    {{-- <div class="alert alert-warning d-flex align-items-center" role="alert"> --}}
    {{-- <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> --}}
    {{-- <div> --}}
    {{-- يوجد <strong>{{ __('owner.generated.boats_3') }}</strong> {{ __('owner.generated.docs_expiring_within') }}<strong>{{ __('owner.generated.days_30') }}</strong>. --}}
    {{-- </div> --}}
    {{-- </div> --}}

    <!-- {{ __('owner.generated.item_84b1e0') }} -->
    <div class="row">
        {{-- Use shared stat-card components for consistency (same pattern as trips page) --}}
        @include('owner.components.stat-card', [
            'title' => __('owner.boats.active_boats'),
            'value' => new \Illuminate\Support\HtmlString('<div id="boat_active">0</div>'),
            'icon' => 'bi bi-speedometer2',
            'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.boats.total_boats'),
            'value' => new \Illuminate\Support\HtmlString('<div id="boats">0</div>'),
            'icon' => 'fas fa-ship',
            'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.boats.maintenance_cost'),
            'value' => new \Illuminate\Support\HtmlString(
                '<div id="boats_maintenance_cost">0.00 <span style="color:transparent;">' .
                    view('components.riyal-icon', [
                        'size' => 'sm',
                        'style' =>
                            'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.25rem;',
                    ])->render() .
                    '</span></div>'),
            'icon' => 'bi bi-tools',
            'gradient' => 'linear-gradient(135deg, #e67e22, #f39c12)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.boats.upcoming_tasks2'),
            'value' => new \Illuminate\Support\HtmlString('<div id="boats_upcoming_tasks">0</div>'),
            'icon' => 'bi bi-calendar-event',
            'gradient' => 'linear-gradient(135deg, #c0392b, #e74c3c)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
    </div>


    <!-- {{ __('owner.generated.item_032380') }} -->
    <table id="datatableDefault"
        class="table table-sm table-bordered table-hover text-center small-text mt-4" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('owner.boats.name') }}</th>
                <th>{{ __('owner.boats.class') }}</th>
                <th>{{ __('owner.boats.type') }}</th>
                <th>{{ __('owner.boats.captain') }}</th>
                <th>{{ __('owner.boats.status') }}</th>
                <th>{{ __('owner.boats.actions') }}</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>


@endsection
@section('script')
    <script src="{{ asset('dashboard/assets/plugins/@highlightjs/cdn-assets/highlight.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/highlightjs.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}">
    </script>
    <script src="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/table-plugins.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/jquery.validate.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        window.routes = {};
    </script>
    <script>
        let appLocale = '{{ app()->getLocale() }}';
        let languageOptions = {};
        if (appLocale === 'ar') {
            languageOptions = {
                url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
            };
        }

        let swalOptions = {
            title: '{{ __('owner.swal.confirm_title') }}',
            text: '{{ __('owner.swal.confirm_text') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __('owner.swal.confirm_yes') }}',
            cancelButtonText: '{{ __('owner.swal.cancel') }}'
        };
    </script>
    <script type="text/javascript">
        $(function() {
            // Check if the DataTable is already initialized and destroy it
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }


            // Initialize the DataTable
            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,

                language: languageOptions,

                ajax: {
                    url: "{{ route('owner.getBoatData') }}",
                    data: function(d) {
                        d.status = '{{ request('status') }}'; // تمرير الحالة الحالية من الرابط
                    },
                    dataSrc: function(json) {
                        // ✅ عرض القيم في أي مكان خارج الجدول
                        $('#boat_active').text(json.boat_active_count);
                        $('#boats_upcoming_tasks').text(json.boats_upcoming_tasks);
                        $('#boats').text(json.boat_count);
                        // $('#trip_completed_status').text(json.trip_completed_status);
                        // $('#sales_amount').text(json.sales_amount + '{{ __('owner.generated.item_93fe61') }}');

                        return json.data;
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'captain',
                        name: 'captain'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: false
                    },
                ],
                responsive: false, scrollX: true,

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
            $('#from_date, #to_date').change(function() {
                table.draw();
            });
        });
    </script>

    <script>
        $("#createForm").validate();
    </script>
    <script>
        function deleteBoatRecord(recordId) {
            Swal.fire({
                title: swalOptions.title,
                text: swalOptions.text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: swalOptions.confirmButtonText,
                cancelButtonText: swalOptions.cancelButtonText
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('owner/boats') }}/" + recordId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(swalOptions.deleted_title, response.message, 'success');
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message ||
                                '{{ __('owner.generated.item_201cac') }}';
                            Swal.fire(swalOptions.error_title, message, 'error');
                        }

                    });
                }
            });
        }

        // Model Edit
        $('#modelEdit').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var name = button.data('name')
            var email = button.data('email')
            var phone = button.data('phone')
            var notes = button.data('notes')

            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #name').val(name);
            modal.find('.modal-body #email').val(email);
            modal.find('.modal-body #phone').val(phone);
            modal.find('.modal-body #notes').val(notes);

        });
    </script>
@endsection
