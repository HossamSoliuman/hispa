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

        /* Keep the row-index (#) column on a single line and tight */
        #datatableDefault th:first-child,
        #datatableDefault td:first-child {
            white-space: nowrap;
            width: 56px;
        }

        /* Status badge should never wrap */
        #datatableDefault td .badge {
            white-space: nowrap;
        }

        /* Action buttons: consistent sizing and centered alignment */
        #datatableDefault td .trip-actions .btn {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
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
    <!-- BEGIN page header -->
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class=" fw-bold text-dark mb-1">{{ __('owner.trips.title') }}</h2>
        </div>

        <div class="col-md-6 col-sm-12 text-md-end text-sm-start d-flex gap-2 justify-content-md-end">
            <button type="button" class="btn btn-success btn-border-radius" data-bs-toggle="modal" data-bs-target="#addTripModal">
                <i class="bi bi-plus me-1"></i> {{ __('owner.trips.add_trip') }}
            </button>
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
            'title' => __('owner.trips.has_catch_trips'),
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
    <!-- Modal: Add Trip -->
    <div class="modal fade" id="addTripModal" tabindex="-1" aria-labelledby="addTripModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTripModalLabel">{{ __('owner.trips.title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('owner.generated.btn_close_modal') }}"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('owner.trips.store') }}" method="post" id="addTripForm">
                        @csrf
                        <input type="hidden" name="_form" value="add_trip">
                        <input type="hidden" name="redirect_to" value="{{ route('owner.trips.index') }}">
                        <input type="hidden" name="owner_id" value="{{ auth()->user()->getAuthIdentifier() }}">

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.trips.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required placeholder="{{ __('owner.trips.name') }}">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.trips.name_en') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control" required placeholder="{{ __('owner.trips.name_en') }}">
                                @error('name_en') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('owner.trips.license_number') }} <span class="text-danger">*</span></label>
                                <input type="text" name="license_number" value="{{ old('license_number') }}" class="form-control" required placeholder="{{ __('owner.trips.license_number') }}">
                                @error('license_number') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">{{ __('owner.trips.start_date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_date" id="trip_start_date" value="{{ old('start_date', now()->format('Y-m-d\TH:i')) }}" class="form-control" required>
                                @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('owner.trips.duration_days') }}</label>
                                <input type="number" name="duration" id="trip_duration" min="1" step="1" value="{{ old('duration', 1) }}" class="form-control" placeholder="{{ __('owner.trips.duration_days') }}">
                                <small class="text-muted">{{ __('owner.trips.duration_hint') }}</small>
                                @error('duration') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('owner.trips.captain_name') }} <span class="text-danger">*</span></label>
                                <select name="captain_id" id="trip_captain_id" class="form-control" required>
                                    <option value="">{{ __('owner.actions.choose') }}</option>
                                    @foreach($captains as $captain)
                                        <option value="{{ $captain->id }}" {{ old('captain_id') == $captain->id ? 'selected' : '' }}>{{ $captain->name }}</option>
                                    @endforeach
                                </select>
                                @error('captain_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('owner.trips.boat_name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="boat_name" id="trip_boat_name" class="form-control" readonly value="{{ old('boat_name') }}" placeholder="{{ __('owner.trips.boat_name') }}">
                                <input type="hidden" name="boat_id" id="trip_boat_id" value="{{ old('boat_id') }}">
                                @error('boat_name') <span class="text-danger">{{ $message }}</span> @enderror
                                @error('boat_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('owner.trips.notes') }}</label>
                                <textarea name="notes" class="form-control" placeholder="{{ __('owner.trips.notes') }}">{{ old('notes') }}</textarea>
                                @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        @if(isset($quickExpenseCategories) && $quickExpenseCategories->count())
                        @php $quickExpenseRows = old('quick_expenses') ?: [['category_id' => '', 'vendor_id' => '', 'amount' => '']]; @endphp
                        <hr>
                        <div class="row mb-2">
                            <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h6 class="mb-1">{{ __('owner.trips.quick_expenses.title') }}</h6>
                                    <small class="text-muted">{{ __('owner.trips.quick_expenses.hint') }}</small>
                                </div>
                                <div style="min-width: 160px;">
                                    <label class="form-label">{{ __('owner.trips.quick_expenses.status') }}</label>
                                    <select name="quick_expenses_status" class="form-control">
                                        <option value="pending" {{ old('quick_expenses_status', 'pending') == 'pending' ? 'selected' : '' }}>{{ __('owner.trips.quick_expenses.status_pending') }}</option>
                                        <option value="paid" {{ old('quick_expenses_status') == 'paid' ? 'selected' : '' }}>{{ __('owner.trips.quick_expenses.status_paid') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="quickExpensesRows" data-next-index="{{ count($quickExpenseRows) }}">
                            @foreach($quickExpenseRows as $index => $row)
                            <div class="row g-2 align-items-end mb-2 quick-expense-row">
                                <div class="col-md-4 col-12">
                                    <label class="form-label">{{ __('owner.trips.quick_expenses.category') }}</label>
                                    <select name="quick_expenses[{{ $index }}][category_id]" class="form-control">
                                        <option value="">{{ __('owner.trips.quick_expenses.choose_category') }}</option>
                                        @foreach($quickExpenseCategories as $category)
                                            <option value="{{ $category->id }}" {{ (string)($row['category_id'] ?? '') === (string)$category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="form-label">{{ __('owner.trips.quick_expenses.provider') }}</label>
                                    <select name="quick_expenses[{{ $index }}][vendor_id]" class="form-control">
                                        <option value="">{{ __('owner.trips.quick_expenses.choose_provider') }}</option>
                                        @foreach($quickExpenseVendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ (string)($row['vendor_id'] ?? '') === (string)$vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 col-9">
                                    <label class="form-label">{{ __('owner.trips.quick_expenses.amount') }}</label>
                                    <input type="number" step="0.01" min="0"
                                        name="quick_expenses[{{ $index }}][amount]"
                                        value="{{ $row['amount'] ?? '' }}"
                                        class="form-control quick-expense-amount"
                                        placeholder="{{ __('owner.trips.quick_expenses.amount') }}">
                                </div>
                                <div class="col-md-1 col-3">
                                    <button type="button" class="btn btn-danger w-100 btn-remove-expense" title="{{ __('owner.trips.quick_expenses.remove_row') }}"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <button type="button" id="addQuickExpense" class="btn btn-outline-primary btn-sm"><i class="fa fa-plus me-1"></i>{{ __('owner.trips.quick_expenses.add_row') }}</button>
                                <div>
                                    <span class="fw-bold">{{ __('owner.trips.quick_expenses.total') }}:</span>
                                    <span id="quickExpensesTotal" class="fw-bold">0.00</span>
                                </div>
                            </div>
                        </div>

                        <template id="quickExpenseRowTemplate">
                            <div class="row g-2 align-items-end mb-2 quick-expense-row">
                                <div class="col-md-4 col-12">
                                    <label class="form-label">{{ __('owner.trips.quick_expenses.category') }}</label>
                                    <select name="quick_expenses[__INDEX__][category_id]" class="form-control">
                                        <option value="">{{ __('owner.trips.quick_expenses.choose_category') }}</option>
                                        @foreach($quickExpenseCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="form-label">{{ __('owner.trips.quick_expenses.provider') }}</label>
                                    <select name="quick_expenses[__INDEX__][vendor_id]" class="form-control">
                                        <option value="">{{ __('owner.trips.quick_expenses.choose_provider') }}</option>
                                        @foreach($quickExpenseVendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 col-9">
                                    <label class="form-label">{{ __('owner.trips.quick_expenses.amount') }}</label>
                                    <input type="number" step="0.01" min="0"
                                        name="quick_expenses[__INDEX__][amount]"
                                        class="form-control quick-expense-amount"
                                        placeholder="{{ __('owner.trips.quick_expenses.amount') }}">
                                </div>
                                <div class="col-md-1 col-3">
                                    <button type="button" class="btn btn-danger w-100 btn-remove-expense" title="{{ __('owner.trips.quick_expenses.remove_row') }}"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </template>
                        @endif

                        <div class="modal-footer px-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                            <button type="submit" class="btn btn-success">{{ __('owner.actions.save') }}</button>
                        </div>
                    </form>
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
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                    }
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
        $("#addTripForm").validate();
    </script>
    <script>
        $(document).ready(function () {
            $('#trip_captain_id').on('change', function () {
                let captainId = $(this).val();
                if (!captainId) {
                    $('#trip_boat_id').val('');
                    $('#trip_boat_name').val('');
                    return;
                }
                let url = "{{ route('owner.getBoatInfo', ['id' => 'CAPTAIN_ID']) }}".replace('CAPTAIN_ID', captainId);
                $.get(url, function (data) {
                    $('#trip_boat_id').val(data.boat_id);
                    $('#trip_boat_name').val(data.boat_name);
                }).fail(function () {
                    console.error('Failed to load boat info');
                });
            });
        });
    </script>
    <script>
        function tripTransition(tripId, toStatus, needsReason) {
            let cancelReason = null;

            function doTransition() {
                let postData = { _token: '{{ csrf_token() }}', to: toStatus };
                if (cancelReason) { postData.cancel_reason = cancelReason; }

                $.ajax({
                    url: "{{ route('owner.trips.transition', ['trip' => '__ID__']) }}".replace('__ID__', tripId),
                    type: 'POST',
                    data: postData,
                    success: function(response) {
                        Swal.fire('{{ __('owner.swal.success_title') }}', response.message, 'success');
                        $('#datatableDefault').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || '{{ __('owner.swal.unexpected_error') }}';
                        Swal.fire('{{ __('owner.swal.error') }}', message, 'error');
                    }
                });
            }

            if (needsReason) {
                Swal.fire({
                    title: '{{ __('owner.trips.confirm_cancel_trip_title') }}',
                    input: 'textarea',
                    inputLabel: '{{ __('trips.errors.cancel_reason_required') }}',
                    inputPlaceholder: '{{ __('trips.errors.cancel_reason_required') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: '{{ __('owner.trips.confirm_cancel_trip_yes') }}',
                    cancelButtonText: '{{ __('owner.trips.confirm_cancel_trip_cancel') }}',
                    preConfirm: (reason) => {
                        if (!reason) {
                            Swal.showValidationMessage('{{ __('trips.errors.cancel_reason_required') }}');
                        }
                        return reason;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        cancelReason = result.value;
                        doTransition();
                    }
                });
            } else {
                Swal.fire({
                    title: '{{ __('owner.swal.confirm_title') }}',
                    text: '{{ __('owner.swal.confirm_text') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __('owner.swal.confirm_proceed') }}',
                    cancelButtonText: '{{ __('owner.swal.cancel') }}'
                }).then((result) => {
                    if (result.isConfirmed) { doTransition(); }
                });
            }
        }

        // Finish a trip: prompt for the actual end date and show how it compares to the planned date.
        function finishTrip(tripId, plannedEnd) {
            const L = @json(__('owner.trips.finish'));

            function toLocalInput(d) {
                const pad = (n) => String(n).padStart(2, '0');
                return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()) +
                    'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
            }

            function humanizeDiff(ms) {
                const totalMinutes = Math.round(ms / 60000);
                const days = Math.floor(totalMinutes / 1440);
                const hours = Math.floor((totalMinutes % 1440) / 60);
                const parts = [];
                if (days > 0) { parts.push(days + ' ' + (days === 1 ? L.day : L.days)); }
                if (hours > 0) { parts.push(hours + ' ' + (hours === 1 ? L.hour : L.hours)); }
                if (parts.length === 0) { return L.less_than_hour; }
                return parts.join(' ' + L.and + ' ');
            }

            const nowVal = toLocalInput(new Date());
            const plannedRow = plannedEnd ?
                '<div class="mb-2"><strong>' + L.planned_end + ':</strong> ' + plannedEnd.replace('T', ' ') + '</div>' :
                '<div class="mb-2 text-muted">' + L.no_planned + '</div>';

            Swal.fire({
                title: L.title,
                html: '<div class="text-start">' + plannedRow +
                    '<label class="form-label d-block">' + L.actual_end_label + '</label>' +
                    '<input type="datetime-local" id="actual_end_input" class="form-control" value="' + nowVal + '">' +
                    '<div id="finish_delay" class="mt-2 fw-bold"></div></div>',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                confirmButtonText: L.confirm,
                cancelButtonText: '{{ __('owner.swal.cancel') }}',
                didOpen: () => {
                    const input = document.getElementById('actual_end_input');
                    const indicator = document.getElementById('finish_delay');
                    const update = () => {
                        if (!plannedEnd || !input.value) { indicator.textContent = ''; return; }
                        const diff = new Date(input.value) - new Date(plannedEnd);
                        if (Math.abs(diff) < 60000) {
                            indicator.style.color = '#198754';
                            indicator.textContent = L.on_time;
                        } else if (diff < 0) {
                            indicator.style.color = '#198754';
                            indicator.textContent = L.ahead.replace(':duration', humanizeDiff(-diff));
                        } else {
                            indicator.style.color = '#dc3545';
                            indicator.textContent = L.delayed.replace(':duration', humanizeDiff(diff));
                        }
                    };
                    input.addEventListener('input', update);
                    update();
                },
                preConfirm: () => {
                    const val = document.getElementById('actual_end_input').value;
                    if (!val) { Swal.showValidationMessage(L.required); return false; }
                    return val;
                }
            }).then((result) => {
                if (!result.isConfirmed) { return; }
                $.ajax({
                    url: "{{ route('owner.trips.transition', ['trip' => '__ID__']) }}".replace('__ID__', tripId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        to: {{ \App\Enums\TripStatus::Finished->value }},
                        actual_end_date: result.value
                    },
                    success: function(response) {
                        Swal.fire('{{ __('owner.swal.success_title') }}', response.message, 'success');
                        $('#datatableDefault').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || '{{ __('owner.swal.unexpected_error') }}';
                        Swal.fire('{{ __('owner.swal.error') }}', message, 'error');
                    }
                });
            });
        }

        function recalcQuickExpensesTotal() {
            let total = 0;
            $('.quick-expense-amount').each(function() {
                let value = parseFloat($(this).val());
                if (!isNaN(value) && value > 0) {
                    total += value;
                }
            });
            $('#quickExpensesTotal').text(total.toFixed(2));
        }

        $(document).on('input', '.quick-expense-amount', recalcQuickExpensesTotal);

        $(document).on('click', '#addQuickExpense', function() {
            let container = document.getElementById('quickExpensesRows');
            let index = parseInt(container.dataset.nextIndex, 10) || 0;
            let html = document.getElementById('quickExpenseRowTemplate').innerHTML.replace(/__INDEX__/g, index);
            container.insertAdjacentHTML('beforeend', html);
            container.dataset.nextIndex = index + 1;
        });

        $(document).on('click', '.btn-remove-expense', function() {
            let rows = $('#quickExpensesRows .quick-expense-row');
            if (rows.length > 1) {
                $(this).closest('.quick-expense-row').remove();
            } else {
                $(this).closest('.quick-expense-row').find('select, input').val('');
            }
            recalcQuickExpensesTotal();
        });
    </script>
@endsection
