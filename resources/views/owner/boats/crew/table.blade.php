@php
    $captain = $boat->captain ?? null;
@endphp

<div class="card shadow-sm border-0 mb-3">
    <div class="p-3">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-person-badge-fill text-success"></i>
            <h5 class="mb-0">{{ __('owner.boats.captain') }}</h5>
        </div>

        @if ($captain)
            <div class="row g-3">
                <div class="col-md-4 col-sm-6">
                    <small class="text-muted d-block">{{ __('owner.crew.table.name') }}</small>
                    @include('owner.partials._avatar', ['user' => $captain])
                </div>
                <div class="col-md-2 col-sm-6">
                    <small class="text-muted d-block">{{ __('owner.crew.table.job_title') }}</small>
                    <span class="fw-semibold">{{ $captain->job_title ?? __('owner.boats.captain') }}</span>
                </div>
                <div class="col-md-2 col-sm-6">
                    <small class="text-muted d-block">{{ __('owner.crew.table.phone') }}</small>
                    <span class="fw-semibold">{{ $captain->phone ?? '--' }}</span>
                </div>
                <div class="col-md-2 col-sm-6">
                    <small class="text-muted d-block">{{ __('owner.crew.table.id_number') }}</small>
                    <span class="fw-semibold">{{ $captain->id_number ?? ($captain->passport_number ?? '--') }}</span>
                </div>
                <div class="col-md-2 col-sm-6">
                    <small class="text-muted d-block">{{ __('owner.crew.table.status') }}</small>
                    @if ($captain->status)
                        <span class="badge bg-success">{{ __('owner.status.active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('owner.status.inactive') }}</span>
                    @endif
                </div>
            </div>
        @else
            <p class="text-muted mb-0">
                <i class="bi bi-info-circle me-1"></i>{{ __('owner.boats.captain_not_found') }}
            </p>
        @endif
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="p-3">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <h5 class="mb-0">{{ __('owner.boats.crew') }}</h5>
        </div>
        <table id="datatableCrew" class="table table-sm table-bordered table-hover text-center small-text"
            style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('owner.crew.table.name') }}</th>
                    <th>{{ __('owner.crew.table.job_title') }}</th>
                    <th>{{ __('owner.crew.table.phone') }}</th>
                    <th>{{ __('owner.crew.table.id_number') }}</th>
                    <th>{{ __('owner.crew.table.status') }}</th>
                    <th>{{ __('owner.crew.table.actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
