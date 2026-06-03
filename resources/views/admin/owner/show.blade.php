@extends('admin.layouts.master')

@section('title')
    {{ __('admin.owner.show_title') }} - {{ $owner->name }}
@endsection

@section('css')
    <style>
        .owner-avatar { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; }
        .nav-owner-tabs .nav-link { font-weight: 500; }
        .owner-stat { font-size: 1.1rem; }
        .table-owner th { white-space: nowrap; }
    </style>
@endsection

@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0 d-flex align-items-center gap-3">
            <img src="{{ $owner->logo }}" alt="{{ $owner->name }}" class="owner-avatar">
            <div>
                <h2 class="fw-bold text-dark mb-1">{{ $owner->name }}</h2>
                <p class="text-muted mb-0 small">
                    {{ $owner->phone ?? '—' }} · {{ $owner->email ?? '—' }}
                </p>
                <div class="mt-1">
                    @if($owner->status == 1)
                        <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                    @endif
                    @if($owner->activeSubscription)
                        @if($owner->activeSubscription->is_suspended)
                            <span class="badge bg-secondary">{{ __('admin.subscriptions.suspended') }}</span>
                        @elseif($owner->activeSubscription->end_date >= now()->toDateString())
                            <span class="badge bg-info">{{ __('admin.owner.subscription_active') }}</span>
                        @else
                            <span class="badge bg-warning">{{ __('admin.owner.subscription_expired') }}</span>
                        @endif
                    @else
                        <span class="badge bg-secondary">{{ __('admin.owner.subscription_none') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            @if(auth('admin')->user()->can('update_owner'))
                <a href="{{ route('admin.owner.edit', $owner->id) }}" class="btn btn-primary btn-equal">
                    <i class="bi bi-pencil"></i> {{ __('admin.actions.edit') }}
                </a>
            @endif
            <a href="{{ route('admin.owner.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.menu.owners') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-tabs nav-owner-tabs mb-3" id="ownerTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-pane" type="button" role="tab">
                <i class="bi bi-person me-1"></i> {{ __('admin.owner.profile_tab') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="boats-tab" data-bs-toggle="tab" data-bs-target="#boats-pane" type="button" role="tab">
                <i class="bi bi-signpost-split me-1"></i> {{ __('admin.owner.boats_tab') }}
                <span class="badge bg-primary bg-opacity-75 ms-1">{{ $owner->boats_count ?? $owner->boats->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="subscriptions-tab" data-bs-toggle="tab" data-bs-target="#subscriptions-pane" type="button" role="tab">
                <i class="bi bi-box-seam me-1"></i> {{ __('admin.owner.subscriptions_tab') }}
                <span class="badge bg-primary bg-opacity-75 ms-1">{{ $owner->subscriptions_count ?? $owner->subscriptions->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="trips-tab" data-bs-toggle="tab" data-bs-target="#trips-pane" type="button" role="tab">
                <i class="bi bi-geo-alt me-1"></i> {{ __('admin.owner.trips_tab') }}
                <span class="badge bg-primary bg-opacity-75 ms-1">{{ $owner->trips_count ?? $owner->trips->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales-pane" type="button" role="tab">
                <i class="bi bi-receipt me-1"></i> {{ __('admin.owner.sales_tab') }}
                <span class="badge bg-primary bg-opacity-75 ms-1">{{ $owner->sales_as_seller_count ?? $owner->salesAsSeller->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="customers-tab" data-bs-toggle="tab" data-bs-target="#customers-pane" type="button" role="tab">
                <i class="bi bi-people me-1"></i> {{ __('admin.owner.customers_tab') }}
                <span class="badge bg-primary bg-opacity-75 ms-1">{{ $owner->customers_count ?? $owner->customers->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="ownerTabsContent">
        {{-- Profile --}}
        <div class="tab-pane fade show active" id="profile-pane" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-owner table-borderless">
                                <tr><th class="text-muted w-40">{{ __('admin.owner.name') }}</th><td>{{ $owner->name }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.phone') }}</th><td>{{ $owner->phone ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.email') }}</th><td>{{ $owner->email ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.region') }}</th><td>{{ $owner->region->name ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.governorate') }}</th><td>{{ $owner->governorate->name ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.port') }}</th><td>{{ $owner->port->name ?? '—' }}</td></tr>
                                <tr><th class="text-muted">{{ __('admin.owner.registered_at') }}</th><td>{{ $owner->created_at?->format('Y-m-d H:i') ?? '—' }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Boats --}}
        <div class="tab-pane fade" id="boats-pane" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($owner->boats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-owner">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin.table.id') }}</th>
                                        <th>{{ __('admin.boats.name') }}</th>
                                        <th>{{ __('admin.boats.number') }}</th>
                                        <th>{{ __('admin.owner.status') }}</th>
                                        <th>{{ __('admin.actions.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($owner->boats as $boat)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $boat->name ?? $boat->name_ar ?? $boat->name_en ?? '—' }}</td>
                                            <td>{{ $boat->number ?? '—' }}</td>
                                            <td>
                                                @if($boat->status == 1)
                                                    <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.boats.edit', $boat->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('admin.owner.no_boats') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Subscriptions --}}
        <div class="tab-pane fade" id="subscriptions-pane" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($owner->subscriptions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-owner">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin.table.id') }}</th>
                                        <th>{{ __('admin.subscriptions.package') }}</th>
                                        <th>{{ __('admin.subscriptions.start_date') }}</th>
                                        <th>{{ __('admin.subscriptions.end_date') }}</th>
                                        <th>{{ __('admin.subscriptions.status') }}</th>
                                        <th>{{ __('admin.actions.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($owner->subscriptions as $sub)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sub->package->name ?? '—' }}</td>
                                            <td>{{ $sub->start_date?->format('Y-m-d') ?? '—' }}</td>
                                            <td>{{ $sub->end_date?->format('Y-m-d') ?? '—' }}</td>
                                            <td>
                                                @if($sub->is_suspended)
                                                    <span class="badge bg-secondary">{{ __('admin.subscriptions.suspended') }}</span>
                                                @elseif($sub->status == 'active' && $sub->end_date >= now()->toDateString())
                                                    <span class="badge bg-success">{{ __('admin.subscriptions.active') }}</span>
                                                @elseif($sub->status == 'expired' || $sub->end_date < now()->toDateString())
                                                    <span class="badge bg-danger">{{ __('admin.subscriptions.expired') }}</span>
                                                @elseif($sub->status == 'trial')
                                                    <span class="badge bg-warning">{{ __('admin.subscriptions.trial') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $sub->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.subscriptions.show', $sub->id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                                <a href="{{ route('admin.subscriptions.edit', $sub->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('admin.owner.no_subscriptions') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Trips --}}
        <div class="tab-pane fade" id="trips-pane" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($owner->trips->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-owner">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin.table.id') }}</th>
                                        <th>{{ __('admin.trips.table.name') }}</th>
                                        <th>{{ __('admin.trips.table.status') }}</th>
                                        <th>{{ __('admin.trips.departure_date') }}</th>
                                        <th>{{ __('admin.actions.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($owner->trips as $trip)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $trip->name ?? $trip->number ?? '—' }}</td>
                                            <td>
                                                @php $st = $trip->status ?? 0; @endphp
                                                @if($st == 2) <span class="badge bg-success">{{ __('admin.trips.status_completed') }}</span>
                                                @elseif($st == 1) <span class="badge bg-info">{{ __('admin.trips.status_in_progress') }}</span>
                                                @else <span class="badge bg-secondary">{{ __('admin.trips.status_pending') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $trip->start_date?->format('Y-m-d') ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('admin.trips.show', $trip->id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($owner->trips->count() >= 50)
                            <p class="text-muted small mt-2">{{ __('admin.filters.showing_last') }}</p>
                        @endif
                    @else
                        <p class="text-muted mb-0">{{ __('admin.owner.no_trips') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sales --}}
        <div class="tab-pane fade" id="sales-pane" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($owner->salesAsSeller->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-owner">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin.table.id') }}</th>
                                        <th>{{ __('admin.sales.number') }}</th>
                                        <th>{{ __('admin.sales.trip_id') }}</th>
                                        <th>{{ __('admin.sales.customer_id') }}</th>
                                        <th>{{ __('admin.sales.total_price') }}</th>
                                        <th>{{ __('admin.sales.datetime') }}</th>
                                        <th>{{ __('admin.actions.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($owner->salesAsSeller as $sale)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sale->number ?? '—' }}</td>
                                            <td>{{ $sale->trip->name ?? $sale->trip->number ?? '—' }}</td>
                                            <td>{{ $sale->customer_name ?? $sale->customer->name ?? '—' }}</td>
                                            <td>{{ number_format($sale->total_price ?? 0, 2) }}</td>
                                            <td>{{ $sale->sale_datetime?->format('Y-m-d H:i') ?? $sale->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('admin.sales.show', $sale->id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($owner->salesAsSeller->count() >= 50)
                            <p class="text-muted small mt-2">{{ __('admin.filters.showing_last') }}</p>
                        @endif
                    @else
                        <p class="text-muted mb-0">{{ __('admin.owner.no_sales') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Customers --}}
        <div class="tab-pane fade" id="customers-pane" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    @if($owner->customers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-owner">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin.table.id') }}</th>
                                        <th>{{ __('admin.customers.table.name') }}</th>
                                        <th>{{ __('admin.owner.phone') }}</th>
                                        <th>{{ __('admin.owner.email') }}</th>
                                        <th>{{ __('admin.owner.status') }}</th>
                                        <th>{{ __('admin.actions.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($owner->customers as $cust)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $cust->name ?? '—' }}</td>
                                            <td>{{ $cust->phone ?? '—' }}</td>
                                            <td>{{ $cust->email ?? '—' }}</td>
                                            <td>
                                                @if($cust->status == 1)
                                                    <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.customers.show', $cust->id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                                <a href="{{ route('admin.customers.edit', $cust->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($owner->customers->count() >= 50)
                            <p class="text-muted small mt-2">{{ __('admin.filters.showing_last') }}</p>
                        @endif
                    @else
                        <p class="text-muted mb-0">{{ __('admin.owner.no_customers') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    (function() {
        function activateTabFromHash() {
            var hash = window.location.hash;
            if (!hash || hash.indexOf('-pane') === -1) return;
            var tabId = hash.replace('#', '').replace('-pane', '-tab');
            var tabEl = document.getElementById(tabId);
            if (!tabEl) return;
            if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                try {
                    var tab = bootstrap.Tab.getOrCreateInstance(tabEl);
                    tab.show();
                } catch (e) {
                    tabEl.click();
                }
            } else {
                tabEl.click();
            }
        }
        function runWhenReady() {
            activateTabFromHash();
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(runWhenReady, 50);
            });
        } else {
            setTimeout(runWhenReady, 50);
        }
        window.addEventListener('hashchange', activateTabFromHash);
    })();
</script>
@endsection
