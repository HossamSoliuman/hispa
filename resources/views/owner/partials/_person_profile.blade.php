{{--
    Shared person profile hero (captain / crew): avatar + identity card beside a
    contact-details grid. Theme-aware (light/dark) and built from Bootstrap
    utilities so it matches the rest of the owner dashboard.

    Params:
      $user           — the person being shown
      $editRoute      — URL for the edit button
      $statementRoute — (optional) URL for the payroll-statement PDF; button hidden when omitted
--}}
<div class="row g-3 mb-3">
    {{-- Identity card --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            @include('owner.partials._card_arrow')
            <div class="card-body text-center p-4 d-flex flex-column">
                <div class="person-avatar-wrap mx-auto mb-3">
                    <img src="{{ $user->logo ? asset($user->logo) : asset('default-avatar.png') }}"
                        class="person-avatar" alt="{{ $user->name }}">
                    <span class="person-status-dot bg-{{ $user->status ? 'success' : 'danger' }}"
                        title="{{ $user->status ? __('owner.status.active') : __('owner.status.inactive') }}"></span>
                </div>

                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted text-capitalize mb-3">{{ $user->role }}</p>

                @if ($user->status)
                    <span class="badge rounded-pill bg-success-subtle text-success-emphasis px-3 py-2 mx-auto">
                        <i class="bi bi-check-circle-fill me-1"></i>{{ __('owner.status.active') }}
                    </span>
                @else
                    <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis px-3 py-2 mx-auto">
                        <i class="bi bi-x-circle-fill me-1"></i>{{ __('owner.status.inactive') }}
                    </span>
                @endif

                <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
                    <a href="{{ $editRoute }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>{{ __('owner.actions.edit') }}
                    </a>
                    @if (! empty($statementRoute))
                        <a href="{{ $statementRoute }}" target="_blank" class="btn btn-outline-primary">
                            <i class="bi bi-receipt-cutoff me-1"></i>{{ __('owner.payrolls.statement.button') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Contact details --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            @include('owner.partials._card_arrow')
            <div class="card-body p-4">
                <h6 class="text-muted text-uppercase fw-bold small mb-4">
                    <i class="bi bi-person-vcard me-1"></i>{{ __('owner.generated.contact_information') }}
                </h6>

                @php
                    $fields = [
                        ['icon' => 'bi-envelope', 'tone' => 'primary', 'label' => __('owner.generated.email_address'), 'value' => $user->email],
                        ['icon' => 'bi-telephone', 'tone' => 'success', 'label' => __('owner.generated.phone_number_1'), 'value' => $user->phone],
                        ['icon' => 'bi-pin-map', 'tone' => 'warning', 'label' => __('owner.generated.region'), 'value' => $user->region?->name],
                        ['icon' => 'bi-geo-alt', 'tone' => 'info', 'label' => __('owner.generated.governorate'), 'value' => $user->governorate?->name],
                        ['icon' => 'bi-water', 'tone' => 'primary', 'label' => __('owner.sales.boat'), 'value' => $user->boat?->name],
                        ['icon' => 'bi-buildings', 'tone' => 'secondary', 'label' => __('owner.crew.table.port'), 'value' => $user->port?->name],
                    ];
                @endphp

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
            </div>
        </div>
    </div>
</div>

<style>
    .person-avatar-wrap {
        position: relative;
        width: 116px;
        height: 116px;
    }

    .person-avatar {
        width: 116px;
        height: 116px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--bs-primary);
        box-shadow: 0 4px 14px rgba(0, 0, 0, .12);
    }

    .person-status-dot {
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
