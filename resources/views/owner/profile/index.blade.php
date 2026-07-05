@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.item_e9c893') }}
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

        .profile-avatar-wrap {
            position: relative;
            width: 116px;
            height: 116px;
        }

        .profile-avatar {
            width: 116px;
            height: 116px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--bs-primary);
            box-shadow: 0 4px 14px rgba(0, 0, 0, .12);
        }

        .profile-status-dot {
            position: absolute;
            inset-inline-end: 6px;
            bottom: 6px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 3px solid var(--bs-card-bg, #fff);
        }

        .info-icon {
            flex: 0 0 auto;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        .min-w-0 {
            min-width: 0;
        }
    </style>
@endsection
@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">{{ __('owner.menu.profile') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.menu.profile') }}</li>
            </ul>
            <h1 class="page-header mb-0"> {{ __('owner.generated.edit_profile') }}</h1>
        </div>

    </div>

    @php
        $subscription = $user->activeSubscription;
        $fields = [
            ['icon' => 'bi-person-vcard', 'tone' => 'info', 'label' => __('owner.profile_details.id_number'), 'value' => $user->id_number],
            ['icon' => 'bi-briefcase', 'tone' => 'secondary', 'label' => __('owner.profile_details.job_title'), 'value' => $user->job_title ?: $user->role],
            ['icon' => 'bi-card-heading', 'tone' => 'success', 'label' => __('owner.profile_details.fishing_license_number'), 'value' => $user->fishing_license_number],
            ['icon' => 'bi-calendar-x', 'tone' => 'warning', 'label' => __('owner.profile_details.fishing_license_expiry'), 'value' => $user->fishing_license_expiry],
            ['icon' => 'bi-telephone', 'tone' => 'success', 'label' => __('owner.profile_details.phone'), 'value' => $user->phone],
            ['icon' => 'bi-envelope', 'tone' => 'primary', 'label' => __('owner.profile_details.email'), 'value' => $user->email],
            ['icon' => 'bi-pin-map', 'tone' => 'warning', 'label' => __('owner.profile_details.region'), 'value' => $user->region?->name],
            ['icon' => 'bi-geo-alt', 'tone' => 'info', 'label' => __('owner.profile_details.governorate'), 'value' => $user->governorate?->name],
            ['icon' => 'bi-buildings', 'tone' => 'secondary', 'label' => __('owner.profile_details.port'), 'value' => $user->port?->name],
            ['icon' => 'bi-water', 'tone' => 'primary', 'label' => __('owner.profile_details.boats_count'), 'value' => $user->boats_count],
        ];
        $subscriptionFields = [
            ['icon' => 'bi-hash', 'tone' => 'info', 'label' => __('owner.profile_details.subscription_number'), 'value' => $subscription?->id],
            ['icon' => 'bi-calendar-check', 'tone' => 'success', 'label' => __('owner.profile_details.subscription_start'), 'value' => $subscription?->start_date?->format('Y-m-d')],
            ['icon' => 'bi-calendar-x', 'tone' => 'danger', 'label' => __('owner.profile_details.subscription_end'), 'value' => $subscription?->end_date?->format('Y-m-d')],
        ];
    @endphp

    <div class="row g-3">
        {{-- Identity card --}}
        <div class="col-lg-4">
            <div class="card h-100">
                @include('owner.partials._card_arrow')
                <div class="card-body text-center p-4 d-flex flex-column">
                    <div class="profile-avatar-wrap mx-auto mb-3">
                        <img src="{{ $user->logo ? asset($user->logo) : asset('default-avatar.png') }}"
                            class="profile-avatar" alt="{{ $user->name }}">
                        <span class="profile-status-dot bg-{{ $user->status ? 'success' : 'danger' }}"
                            title="{{ $user->status ? __('owner.status.active') : __('owner.status.inactive') }}"></span>
                    </div>

                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted text-capitalize mb-3">{{ $user->job_title ?: $user->role }}</p>

                    @if ($user->status)
                        <span class="badge rounded-pill bg-success-subtle text-success-emphasis px-3 py-2 mx-auto">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ __('owner.status.active') }}
                        </span>
                    @else
                        <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis px-3 py-2 mx-auto">
                            <i class="bi bi-x-circle-fill me-1"></i>{{ __('owner.status.inactive') }}
                        </span>
                    @endif

                    <div class="mt-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#editProfileModal">
                            <i class="bi bi-pencil me-1"></i>{{ __('owner.generated.edit_profile') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="col-lg-8">
            <div class="card h-100">
                @include('owner.partials._card_arrow')
                <div class="card-body p-4">
                    <h6 class="text-muted text-uppercase fw-bold small mb-4">
                        <i class="bi bi-person-vcard me-1"></i>{{ __('owner.profile_details.contact_information') }}
                    </h6>

                    <div class="row g-4">
                        @foreach ($fields as $field)
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="info-icon bg-{{ $field['tone'] }} bg-opacity-10 text-{{ $field['tone'] }}">
                                        <i class="bi {{ $field['icon'] }}"></i>
                                    </span>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="text-muted small mb-1">{{ $field['label'] }}</div>
                                        <div class="fw-semibold text-truncate">{{ filled($field['value']) ? $field['value'] : '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-4">

                    <h6 class="text-muted text-uppercase fw-bold small mb-4">
                        <i class="bi bi-patch-check me-1"></i>{{ __('owner.profile_details.subscription_information') }}
                    </h6>

                    <div class="row g-4">
                        @foreach ($subscriptionFields as $field)
                            <div class="col-sm-4">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="info-icon bg-{{ $field['tone'] }} bg-opacity-10 text-{{ $field['tone'] }}">
                                        <i class="bi {{ $field['icon'] }}"></i>
                                    </span>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="text-muted small mb-1">{{ $field['label'] }}</div>
                                        <div class="fw-semibold text-truncate">{{ filled($field['value']) ? $field['value'] : '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal: Edit Profile -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4">
                <form action="{{ route('owner.profile.update', auth()->user()->id) }}" id="createForm" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title text-white" id="editProfileModalLabel">
                            {{ __('owner.generated.edit_profile') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>{{ __('owner.assets.name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ auth()->user()->name }}" required>
                            </div>

                            <div class="col-md-6">
                                <label>{{ __('owner.generated.phone_number') }}<span class="text-danger">*</span></label>
                                <input type="text" required name="phone" class="form-control"
                                    value="{{ auth()->user()->phone }}">
                            </div>
                            <div class="col-md-6">
                                <label>{{ __('owner.generated.logo') }}({{ __('owner.generated.new_image') }})</label>
                                <input type="file" name="logo" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>{{ __('owner.generated.new_password') }}</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="{{ __('owner.generated.placeholder_password') }}">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="submit"
                            class="btn btn-success px-5">{{ __('owner.generated.save_changes') }}</button>
                    </div>
                </form>
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
        $(document).ready(function() {
            let oldRegionId = '{{ old('region_id') }}';
            let oldGovernorateId = '{{ old('governorate_id') }}';
            let oldCityId = '{{ old('city_id') }}';
            let oldPortId = '{{ old('port_id') }}';

            // تحميل المحافظات عند اختيار المنطقة
            $('#region_id').on('change', function() {
                let regionId = $(this).val();
                $('#governorate_id').empty().append(
                    '<option value="">{{ __('owner.dalal.performance.loading') }}</option>');
                $('#city_id').empty().append(
                    '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');

                if (regionId) {
                    $.get('/get-governorates/' + regionId, function(data) {
                        $('#governorate_id').empty().append(
                            '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>'
                        );
                        $.each(data, function(i, item) {
                            $('#governorate_id').append('<option value="' + item.id + '">' +
                                item.name + '</option>');
                        });
                    });
                }
            });

            // تحميل المدن عند اختيار المحافظة
            $('#governorate_id').on('change', function() {
                let govId = $(this).val();
                $('#city_id').empty().append(
                    '<option value="">{{ __('owner.dalal.performance.loading') }}</option>');

                if (govId) {
                    $.get('/get-cities/' + govId, function(data) {
                        $('#city_id').empty().append(
                            '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>'
                        );
                        $.each(data, function(i, item) {
                            $('#city_id').append('<option value="' + item.id + '">' + item
                                .name + '</option>');
                        });
                    });
                }
            });
            // تحميل المنافذ عند اختيار المدينة
            $('#city_id').on('change', function() {
                let cityId = $(this).val();
                $('#port_id').empty().append(
                    '<option value="">{{ __('owner.dalal.performance.loading') }}</option>');

                if (cityId) {
                    $.get('/get-ports/' + cityId, function(data) {
                        $('#port_id').empty().append(
                            '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>'
                        );
                        $.each(data, function(i, item) {
                            $('#port_id').append('<option value="' + item.id + '">' + item
                                .name + '</option>');
                        });
                    });
                }
            });

            // عند تحميل الصفحة إذا في old value للمنطقة والمحافظة والمدينة
            if (oldRegionId && !$('#governorate_id option:selected').val()) {
                $.get('/get-governorates/' + oldRegionId, function(governorates) {
                    $('#governorate_id').empty().append(
                        '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');
                    $.each(governorates, function(i, item) {
                        let selected = (item.id == oldGovernorateId) ? 'selected' : '';
                        $('#governorate_id').append('<option value="' + item.id + '" ' + selected +
                            '>' + item.name + '</option>');
                    });

                    if (oldGovernorateId) {
                        $.get('/get-cities/' + oldGovernorateId, function(cities) {
                            $('#city_id').empty().append(
                                '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>'
                            );
                            $.each(cities, function(i, item) {
                                let selected = (item.id == oldCityId) ? 'selected' : '';
                                $('#city_id').append('<option value="' + item.id + '" ' +
                                    selected + '>' + item.name + '</option>');
                            });

                            // ✅ تحميل المنافذ من المدينة المختارة
                            if (oldCityId) {
                                $.get('/get-ports/' + oldCityId, function(ports) {
                                    $('#port_id').empty().append(
                                        '<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>'
                                    );
                                    $.each(ports, function(i, item) {
                                        let selected = (item.id == oldPortId) ?
                                            'selected' : '';
                                        $('#port_id').append('<option value="' +
                                            item.id + '" ' + selected + '>' +
                                            item.name + '</option>');
                                    });
                                });
                            }
                        });
                    }
                });
            }
        });
    </script>
@endsection
