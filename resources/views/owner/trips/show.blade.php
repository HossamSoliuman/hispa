@extends('owner.layouts.master')
@section('title')
    {{ __('owner.trips.show.title') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/tag-it/css/jquery.tagit.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/summernote/dist/summernote-lite.css') }}" rel="stylesheet">

    <style>
        label.error {
            color: red;
            font-weight: bold;
            margin-top: 5px;
            display: block;
        }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">{{ __('owner.trips.show.breadcrumb_manage') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.trips.show.breadcrumb_show') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.trips.show.page_header', ['number' => $data->number]) }}</h1>
        </div>

        <div class="col-md-6 col-sm-12 text-md-end text-sm-start justify-content-lg-end">
            @if (filled($data->start_date) && blank($data->end_date))
                <a href="#" id="end_trip_button" onclick="endTrip({{ $data->id }})" class="btn btn-red btn-sm">
                    <i class="bi bi-clock"></i> {{ __('owner.trips.end_trip') }}
                </a>
            @endif
        </div>

    </div>

    <div class="row gx-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-fish me-2"></i>
                        {{ __('owner.trips.show.catch_title', ['count' => $data->fishQuantityStocks->count()]) }}
                    </div>
                </div>

                <div class="card-body px-4 py-3">
                    @forelse($data->fishQuantityStocks as $stock)
                        <div class="row align-items-center py-2 border-bottom">
                            <div class="col-md-6 d-flex align-items-start">
                                <div class="position-relative">
                                    <div class="bg-light border rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="fas fa-fish text-success fs-4"></i>
                                    </div>
                                </div>

                                <div class="ms-3">
                                    <div class="fw-bold fs-6 text-dark">
                                        {{ $stock->fish->scientific_name ?? ($stock->fish_name ?? '---') }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ number_format($stock->quantity, 2) }} {{ __('owner.units.kg') }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="alert alert-secondary text-center mb-0">
                            {{ __('owner.trips.show.no_catch_data') }}
                        </div>
                    @endforelse
                </div>

                <div class="card-footer text-muted text-center small">
                    {{ __('owner.trips.show.total_items') }}: {{ $data->fishQuantityStocks->count() }} |
                    {{ __('owner.trips.show.total_weight') }}:
                    {{ number_format($data->fishQuantityStocks->sum('quantity'), 2) }} {{ __('owner.units.kg') }}
                </div>

                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

            {{-- بطاقة تفاصيل الرحلة --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-ship me-2"></i> {{ __('owner.trips.show.trip_details') }}
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm m-0 text-center">
                        <tbody>
                            <tr>
                                <th class="w-25">{{ __('owner.trips.show.name') }}</th>
                                <td>{{ $data->name ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('owner.trips.show.license_number') }}</th>
                                <td>{{ $data->license_number ?? '---' }}</td>
                            </tr>

                            @php
                                $colors = [
                                    'new' => 'primary',
                                    'in_progress' => 'info',
                                    'cancelled' => 'danger',
                                    'captain_done' => 'secondary',
                                    'progress_count' => 'warning',
                                    'counter_done' => 'warning',
                                    'ready_to_sell' => 'success',
                                    'completed' => 'success',
                                ];
                                $statusKey = \App\Models\Trip::statuses()[$data->status] ?? 'unknown';
                                $color = $colors[$statusKey] ?? 'dark';
                                $label = $data->status_name;
                            @endphp

                            <tr>
                                <th>{{ __('owner.trips.show.status') }}</th>
                                <td>
                                    <span class="badge bg-{{ $color }} fs-6"><i class="fas fa-info-circle me-1"></i>
                                        {{ $label }}</span>
                                </td>
                            </tr>

                            @php
                                use Illuminate\Support\Carbon;
                                $departure = $data->start_date
                                    ? Carbon::parse($data->start_date)->format('h:i A')
                                    : '--';
                                $return = $data->end_date ? Carbon::parse($data->end_date)->format('h:i A') : '--';
                                $departure = str_replace(
                                    ['AM', 'PM'],
                                    [__('owner.dates.am'), __('owner.dates.pm')],
                                    $departure,
                                );
                                $return = str_replace(
                                    ['AM', 'PM'],
                                    [__('owner.dates.am'), __('owner.dates.pm')],
                                    $return,
                                );
                                $startDate = $data->start_date
                                    ? Carbon::parse($data->start_date)->format('Y/m/d')
                                    : '--';
                                $endDate = $data->end_date ? Carbon::parse($data->end_date)->format('Y/m/d') : '--';
                            @endphp

                            <tr>
                                <th>{{ __('owner.trips.show.time_depart_return') }}</th>
                                <td>
                                    <span class="badge bg-warning text-dark"><i
                                            class="far fa-clock me-1"></i>{{ $departure }} - {{ $return }}</span>
                                </td>
                            </tr>

                            <tr>
                                <th>{{ __('owner.trips.show.date_depart_return') }}</th>
                                <td>
                                    <span class="badge bg-danger"><i
                                            class="far fa-calendar-alt me-1"></i>{{ $startDate }} -
                                        {{ $endDate }}</span>
                                </td>
                            </tr>

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

            {{-- بطاقة معلومات القارب --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="fas fa-anchor me-2"></i> {{ __('owner.trips.show.boat_info') }}
                </div>
                <table class="table table-bordered table-sm m-0 text-center">
                    <tbody>
                        <tr>
                            <th class="w-25">{{ __('owner.trips.boat_name') }}</th>
                            <td>{{ $data->boat_name ?? '---' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('owner.trips.show.boat_number') }}</th>
                            <td>{{ $data->boat_number ?? '---' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('owner.trips.show.boat_color') }}</th>
                            <td>{{ $data->boat_color ?? '---' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('owner.trips.show.boat_length') }}</th>
                            <td>{{ $data->boat_length ?? '---' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('owner.trips.show.boat_width') }}</th>
                            <td>{{ $data->boat_width ?? '---' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('owner.trips.show.crew_count') }}</th>
                            <td>{{ $data->crew_count ?? '---' }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

        </div>
        <div class="col-lg-4">

            {{-- الملاحظات --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="fas fa-sticky-note me-2"></i> {{ __('owner.trips.show.notes') }}
                </div>
                <div class="card-body text-muted">
                    {!! nl2br(e($data->notes ?? __('owner.trips.show.no_notes'))) !!}
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>

            </div>

            {{-- الصيّاد --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="fas fa-user-tie me-2"></i> {{ __('owner.trips.show.owner') }}
                </div>
                <div class="card-body d-flex align-items-center">
                    <img src="{{ asset($data->owner->logo ?? 'assets/img/avatar.png') }}"
                        alt="{{ __('owner.generated.item_113da9') }}" class="rounded-circle border" width="50"
                        height="50">
                    <div class="ms-3">
                        <div class="fw-bold">{{ $data->owner?->name ?? '---' }}</div>
                        <div class="text-muted small">{{ $data->owner?->phone ?? '---' }}</div>
                    </div>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

            {{-- الكابتن --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-user-ninja me-2"></i> {{ __('owner.trips.show.captain') }}
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-2">
                        <img src="{{ asset($data->captain->logo ?? 'assets/img/avatar.png') }}"
                            alt="{{ __('owner.generated.item_0a9699') }}" class="rounded-circle border" width="50"
                            height="50">
                        <div class="ms-3">
                            <div class="fw-bold">{{ $data->captain?->name ?? '---' }}</div>
                            <div class="text-muted small">{{ $data->captain?->phone ?? '---' }}</div>

                            @php

                                $actualStart = $data->start_date
                                    ? Carbon::parse($data->start_date)->format('Y/m/d - h:i A')
                                    : '---';
                                $actualEnd = $data->end_date
                                    ? Carbon::parse($data->end_date)->format('Y/m/d - h:i A')
                                    : '---';

                                // استبدال AM/PM بالعربية
                                $actualStart = str_replace(
                                    ['AM', 'PM'],
                                    [__('owner.generated.item_8cebea'), __('owner.generated.item_e1ac99')],
                                    $actualStart,
                                );
                                $actualEnd = str_replace(
                                    ['AM', 'PM'],
                                    [__('owner.generated.item_8cebea'), __('owner.generated.item_e1ac99')],
                                    $actualEnd,
                                );
                            @endphp

                            <div class="mt-2">
                                <span class="badge bg-info text-dark me-1">
                                    <i class="far fa-play-circle me-1"></i> {{ __('owner.trips.show.actual_start') }}:
                                    {{ $actualStart }}
                                </span>
                                <span class="badge bg-secondary text-white mt-1 d-inline-block">
                                    <i class="far fa-stop-circle me-1"></i> {{ __('owner.trips.show.actual_end') }}:
                                    {{ $actualEnd }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>


        </div>

    </div>
@endsection
@section('script')
    <script src="{{ asset('dashboard/assets/plugins/jquery-migrate/dist/jquery-migrate.min.js') }}"></script>

    <script src="{{ asset('dashboard/assets/plugins/@highlightjs/cdn-assets/highlight.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/highlightjs.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/jquery.validate.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>
    <script src="{{ asset('dashboard/assets/plugins/summernote/dist/summernote-lite.min.js') }}"></script>


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
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('trip-status');
            const reasonWrapper = document.getElementById('cancel-reason-wrapper');
            const reasonInput = document.getElementById('cancel_reason');

            function toggleReasonField() {
                if (statusSelect.value == '3') {
                    reasonWrapper.style.display = 'block';
                    reasonInput.setAttribute('required', 'required');
                } else {
                    reasonWrapper.style.display = 'none';
                    reasonInput.removeAttribute('required');
                    reasonInput.value = '';
                }
            }

            // Trigger on load and on change
            toggleReasonField();
            statusSelect.addEventListener('change', toggleReasonField);
        });
    </script>
@endsection
