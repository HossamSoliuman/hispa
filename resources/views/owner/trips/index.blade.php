@extends('owner.layouts.master')
@section('title')
    {{ __('owner.trips.title') }}
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
    <!-- BEGIN page header -->
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class=" fw-bold text-dark mb-1">{{ __('owner.trips.title') }}</h2>
        </div>

        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('owner.trips.create') }}" class="btn btn-outline-theme btn-equal">
                <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('owner.trips.add_trip') }}
            </a>
            <a href="{{ route('owner.reports.print.all_trips') }}" target="_blank"
                class="btn btn-outline-info btn-border-radius">
                <i class="bi bi-printer me-1"></i> {{ __('owner.trips.print_all_trips') }}
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Use shared stat-card components for consistency --}}
        @include('owner.components.stat-card', [
            'title' => __('owner.trips.total_trips'),
            'value' => new \Illuminate\Support\HtmlString('<div id="trip_count">0</div>'),
            'icon' => 'fas fa-ship',
            'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.trips.waiting_status'),
            'value' => new \Illuminate\Support\HtmlString('<div id="trip_waiting_status">0</div>'),
            'icon' => 'bi bi-hourglass-split',
            'gradient' => 'linear-gradient(135deg, #e67e22, #f39c12)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.trips.completed_status'),
            'value' => new \Illuminate\Support\HtmlString('<div id="trip_completed_status">0</div>'),
            'icon' => 'bi bi-check-circle',
            'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])

        @include('owner.components.stat-card', [
            'title' => __('owner.generated.item_1693a3'),
            'value' => new \Illuminate\Support\HtmlString('<div id="trip_has_catches">0 </div>'),
            'icon' => 'bi bi-currency-dollar',
            'gradient' => 'linear-gradient(135deg, #f39c12, #f1c40f)',
            'colClass' => 'col-md-3 col-sm-6 mb-3',
        ])
    </div>


    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <!-- BEGIN #datatable -->
            <!-- BEGIN #datatable -->
            <div id="datatable" class="mb-5">
                {{--                    <div class="card"> --}}
                {{--                        <div class="card-body"> --}}
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('owner.trips.trip_number') }}</th>
                            <th>{{ __('owner.trips.boat_name') }}</th>
                            <th>{{ __('owner.trips.captain_name') }}</th>
                            <th>{{ __('owner.trips.departure_date') }}</th>
                            <th>{{ __('owner.trips.return_date') }}</th>
                            <th>{{ __('owner.trips.status') }}</th>
                            <th>{{ __('owner.trips.revenue') }}</th>
                            <th>{{ __('owner.trips.actions') }}</th>

                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>

        </div>
    </div>
    <!-- Modal: Create/Edit Trip with Tabs -->
    <div class="modal fade" id="tripModal" tabindex="-1" aria-labelledby="tripModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0" id="tripModalLabel">{{ __('owner.trips.create.title') }}</h5>
                        <small class="text-muted">{{ __('owner.generated.item_d341fc') }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('owner.generated.btn_close_modal') }}"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="tripTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic"
                                type="button" role="tab">{{ __('owner.dalal.modal.basic_info') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="crew-tab" data-bs-toggle="tab" data-bs-target="#crew"
                                type="button" role="tab">{{ __('owner.generated.item_00bbf8') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="planning-tab" data-bs-toggle="tab" data-bs-target="#planning"
                                type="button" role="tab">{{ __('owner.generated.item_9d6df1') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="costs-tab" data-bs-toggle="tab" data-bs-target="#costs"
                                type="button" role="tab">{{ __('owner.generated.item_742221') }}</button>
                        </li>
                    </ul>

                    <form>
                        <div class="tab-content" id="tripTabContent">
                            <!-- Tab: Basic Info -->
                            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('owner.generated.item_dcb5f6') }}</label>
                                        <input type="date" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('owner.generated.item_fad515') }}</label>
                                        <input type="date" class="form-control">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('owner.assets.status') }}</label>
                                    <select class="form-select">
                                        <option selected>{{ __('owner.generated.item_9b0f39') }}</option>
                                        <option>{{ __('owner.trips.status_in_progress') }}</option>
                                        <option>{{ __('owner.sales_report.status_completed') }}</option>
                                        <option>{{ __('owner.trips.status_cancelled') }}</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('owner.expenses.show.notes') }}</label>
                                    <textarea class="form-control" rows="3" placeholder="{{ __('owner.generated.item_19b51f') }}"></textarea>
                                </div>
                            </div>

                            <!-- Tab: Crew & Vessel -->
                            <div class="tab-pane fade" id="crew" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('owner.catch.filters.boat') }}</label>
                                        <select class="form-select">
                                            <option selected disabled>{{ __('owner.payrolls.create.choose_boat') }}
                                            </option>
                                            <option value="1">Al Bahar 1</option>
                                            <option value="2">Al Bahar 2</option>
                                            <option value="3">Support Vessel 1</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('owner.boats.captain') }}</label>
                                        <select class="form-select">
                                            <option selected disabled>{{ __('owner.generated.item_097dcd') }}</option>
                                            <option value="1">Ahmed Al-Rashid</option>
                                            <option value="2">Mohammed Al-Zahra</option>
                                            <option value="3">Aisha Al-Harith</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('owner.generated.item_deb0e0') }}</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="Ahmed Al-Rashid"
                                            id="crew1">
                                        <label class="form-check-label d-flex justify-content-between w-100"
                                            for="crew1">
                                            <span>Ahmed Al-Rashid</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="Fatima Al-Zahra"
                                            id="crew2">
                                        <label class="form-check-label d-flex justify-content-between w-100"
                                            for="crew2">
                                            <span>Fatima Al-Zahra</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Planning -->
                            <div class="tab-pane fade" id="planning" role="tabpanel">
                                <div class="mb-3">
                                    <label
                                        class="form-label">{{ __('owner.generated.fishing_areas') }}({{ __('owner.generated.item_b60be1') }})</label>
                                    <textarea class="form-control" rows="2" placeholder="{{ __('owner.generated.item_2fabb9') }}"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label
                                        class="form-label">{{ __('owner.generated.item_d9279c') }}({{ __('owner.generated.item_1a20b5') }})</label>
                                    <textarea class="form-control" rows="2" placeholder="{{ __('owner.generated.item_e5ff57') }}"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('owner.generated.item_7e1317') }}</label>
                                    <textarea class="form-control" rows="2" placeholder="{{ __('owner.generated.item_25111c') }}"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('owner.generated.item_485868') }}</label>
                                    <textarea class="form-control" rows="2" placeholder="{{ __('owner.generated.item_5961ca') }}"></textarea>
                                </div>
                            </div>


                            <!-- Tab: Costs & Revenue -->
                            <div class="tab-pane fade" id="costs" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">{{ __('owner.generated.item_48d1e5') }}</h6>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('owner.generated.fuel') }}</label>
                                            <input type="number" class="form-control" placeholder="0">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('owner.generated.requisites') }}</label>
                                            <input type="number" class="form-control" placeholder="0">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('owner.menu.crew') }}</label>
                                            <input type="number" class="form-control" placeholder="0">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('owner.generated.others') }}</label>
                                            <input type="number" class="form-control" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">{{ __('owner.generated.item_d752f5') }}</h6>
                                        <div class="mb-3">
                                            <label
                                                class="form-label">{{ __('owner.generated.item_00276c') }}({{ __('owner.generated.item_d7ec95') }})</label>
                                            <input type="number" class="form-control" placeholder="0">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('owner.generated.item_362aa6') }}($)</label>
                                            <input type="number" class="form-control" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('owner.generated.item_4151e4') }}</button>
                </div>
            </div>
        </div>
    </div>
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



    <script type="text/javascript">
        // Expose the SAR (Riyal) SVG markup rendered by the server and a helper to color it.
        @php
            $riyalSvgContent = view('components.riyal-icon', [
                'size' => 'sm',
                'style' => 'width:0.9rem; height:auto; display:inline-block; vertical-align:middle; margin-left:.25rem;',
            ])->render();
        @endphp
        const riyalSvg = @json($riyalSvgContent);

        function riyalIconHtml(color) {
            return '<span class="riyal-icon-wrapper" style="color:' + color +
                '; display:inline-block; vertical-align:middle;">' + riyalSvg + '</span>';
        }

        $(function() {
            // Check if the DataTable is already initialized and destroy it
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }

            let appLocale = '{{ app()->getLocale() }}';
            let languageOptions = {};
            if (appLocale === 'ar') {
                languageOptions = {
                    url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                };
            }
            // Initialize the DataTable
            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,

                language: languageOptions,

                ajax: {
                    url: "{{ route('owner.getTripData') }}",
                    data: function(d) {
                        d.status = '{{ request('status') }}'; // تمرير الحالة الحالية من الرابط
                    },
                    dataSrc: function(json) {
                        // ✅ عرض القيم في أي مكان خارج الجدول
                        $('#trip_count').text(json.trip_count);
                        $('#trip_waiting_status').text(json.trip_waiting_status);
                        $('#trip_completed_status').text(json.trip_completed_status);
                        // Use SVG riyal icon for sales amount (inject HTML)
                        if (typeof riyalIconHtml === 'function') {
                            $('#trip_has_catches').html(Number(json.trip_has_catches).toLocaleString());
                        } else {
                            $('#trip_has_catches').text(json.trip_has_catches);
                        }

                        return json.data;
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'number',
                        name: 'number'
                    },
                    {
                        data: 'boat',
                        name: 'boat'
                    },
                    {
                        data: 'captain',
                        name: 'captain'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'total_sales',
                        name: 'total_sales'
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let actions = `<a href="{{ url('owner/reports/print/trip') }}/${data}" target="_blank" class="btn btn-sm btn-outline-primary" title="{{ __('owner.trips.print_trip') }}">
                                    <i class="bi bi-printer"></i>
                                </a>`;
                            if (row.status_int != 3 && row.start_date && !row.end_date) {
                                actions += `<a href="#"  id="end_trip_button" onclick="endTrip(${row.id})" class="btn btn-sm btn-outline-red mx-2" title=" {{ __('owner.trips.end_trip') }}">
                                    <i class="bi bi-clock"></i>
                                </a>`;
                            }
                            if (row.status_int != 3 && row.status_int != 8) {
                                actions += `<a href="#"  id="end_trip_button" onclick="cancelTrip(${row.id})" class="btn btn-sm btn-outline-red" title="{{ __('owner.generated.item_452553') }}">
                                    <i class="bi bi-x"></i>
                                </a>`;
                            }
                            if (row.catches === null && row.end_date && row.status_int != 3 && row
                                .status_int == 8) {
                                actions +=
                                    `<a href="{{ route('owner.catch.create') }}?trip_id=${row.id}" class="btn btn-sm btn-outline-primary mx-2"><i class="bi bi-plus"></i>{{ __('owner.catch.add_catch') }}</a>`;
                            }

                            return actions;
                        }

                    }
                ],
                responsive: true,

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
        function endTrip(recordId) {
            Swal.fire({
                title: '{{ __('owner.trips.confirm_end_trip_title') }}',
                text: '{{ __('owner.trips.confirm_end_trip_text') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('owner.trips.confirm_end_trip_yes') }}',
                cancelButtonText: '{{ __('owner.trips.confirm_end_trip_cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('owner/endTrip') }}/" + recordId,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('{{ __('owner.trips.confirm_end_trip_ended') }}', response
                                .message, 'success');
                            $('#end_trip_button').hide();
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message ||
                                '{{ __('owner.swal.unexpected_error') }}';
                            Swal.fire('{{ __('owner.swal.error') }}', message, 'error');
                        }

                    });
                }
            });
        }
    </script>
    <script>
        function cancelTrip(recordId) {
            Swal.fire({
                title: '{{ __('owner.trips.confirm_cancel_trip_title') }}',
                text: '{{ __('owner.trips.confirm_cancel_trip_text') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('owner.trips.confirm_cancel_trip_yes') }}',
                cancelButtonText: '{{ __('owner.trips.confirm_cancel_trip_cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('owner/cancelTrip') }}/" + recordId,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('{{ __('owner.trips.confirm_cancel_trip_ended') }}', response
                                .message, 'success');
                            $('#end_trip_button').hide();
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message ||
                                '{{ __('owner.swal.unexpected_error') }}';
                            Swal.fire('{{ __('owner.swal.error') }}', message, 'error');
                        }

                    });
                }
            });
        }
    </script>
    <script>
        function deleteRecord(recordId) {
            Swal.fire({
                title: '{{ __('owner.swal.confirm_title') }}',
                text: '{{ __('owner.swal.confirm_text') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('owner.swal.confirm_yes') }}',
                cancelButtonText: '{{ __('owner.swal.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/trips') }}/" + recordId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('{{ __('owner.swal.deleted') }}', response.message, 'success');
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message ||
                                '{{ __('owner.swal.unexpected_error') }}';
                            Swal.fire('{{ __('owner.swal.error') }}', message, 'error');
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
