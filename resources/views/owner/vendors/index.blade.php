@extends('owner.layouts.master')

@section('title')
    {{ __('owner.vendors.title') }}
@endsection


@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-2 text-dark">{{ __('owner.vendors.manage_title') }}</h2>
            <a href="{{ route('owner.vendors.create') }}" class="btn btn-outline-theme btn-equal">
                <i class="fa fa-plus-circle btn-success fa-fw me-1"></i>{{ __('owner.vendors.add_button') }}
            </a>
        </div>

        <div class="row mb-4">
            @include('owner.components.stat-card', [
                'title' => __('owner.vendors.cards.total'),
                'value' => '<span id="totalVendors">' . ($totalVendors ?? 0) . '</span>',
                'icon' => 'bi bi-people-fill',
                'gradient' => 'linear-gradient(135deg, #2980b9, #3498db);',
                'colClass' => 'col-md-3 col-sm-6 mb-3',
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.vendors.cards.pending_amount'),
                'value' =>
                    '<span id="pendingAmount">' .
                    number_format($pendingAmount ?? 0, 2) .
                    '</span> <span class="unit">' .
                    view('components.riyal-icon', ['size' => 'sm'])->render() .
                    '</span>',
                'icon' => 'bi bi-exclamation-circle',
                'gradient' => 'linear-gradient(135deg, #c0392b, #e74c3c);',
                'colClass' => 'col-md-3 col-sm-6 mb-3',
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.vendors.cards.total_expenses'),
                'value' =>
                    '<span id="totalExpenses">' .
                    number_format($totalExpenses ?? 0, 2) .
                    '</span> <span class="unit">' .
                    view('components.riyal-icon', ['size' => 'sm'])->render() .
                    '</span>',
                'icon' => 'bi bi-wallet',
                'gradient' => 'linear-gradient(135deg, #f39c12, #f1c40f);',
                'colClass' => 'col-md-3 col-sm-6 mb-3',
            ])

            @include('owner.components.stat-card', [
                'title' => __('owner.vendors.cards.total_paid'),
                'value' =>
                    '<span id="totalPaid">' .
                    number_format($totalPaid ?? 0, 2) .
                    '</span> <span class="unit">' .
                    view('components.riyal-icon', ['size' => 'sm'])->render() .
                    '</span>',
                'icon' => 'bi bi-cash-stack',
                'gradient' => 'linear-gradient(135deg, #16a085, #1abc9c);',
                'colClass' => 'col-md-3 col-sm-6 mb-3',
            ])
        </div>

        <style>
            /* Make the embedded riyal SVG smaller and aligned inside stat-cards */
            .stat-card-hover .stat-value .unit svg {
                width: 14px !important;
                height: 14px !important;
                vertical-align: middle;
                margin-left: 4px;
            }

            /* Ensure the numeric span and unit stay aligned */
            .stat-card-hover .stat-value {
                display: flex;
                align-items: center;
                gap: 6px;
            }
        </style>


        <!-- Filters -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header">
                <h5 class="card-title">{{ __('owner.expenses.filters.title') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('owner.vendors.filters.search_label') }}</label>
                        <input type="text" class="form-control" id="searchInput"
                            placeholder="{{ __('owner.vendors.filters.search_placeholder') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('owner.vendors.filters.status') }}</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">{{ __('owner.vendors.filters.options.all') }}</option>
                            <option value="1">{{ __('owner.status.active') }}</option>
                            <option value="0">{{ __('owner.status.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('owner.vendors.filters.actions') }}</label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary w-100" id="filterBtn"><i class="bi bi-search"></i>
                                {{ __('owner.vendors.filters.buttons.search') }}</button>
                            <button class="btn btn-secondary w-100" id="resetBtn"><i class="bi bi-x-circle"></i>
                                {{ __('owner.vendors.filters.buttons.reset') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs" id="vendorTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="vendors-tab" data-bs-toggle="tab" data-bs-target="#vendors"
                    type="button" role="tab" aria-controls="vendors" aria-selected="true">
                    <i class="bi bi-building me-1"></i> {{ __('owner.vendors.tabs.vendors') }}
                </button>
            </li>
        </ul>

        <div class="tab-content" id="vendorTabsContent">
            <div class="tab-pane fade show active" id="vendors" role="tabpanel" aria-labelledby="vendors-tab">
                <div class="card shadow-sm border-0">

                    <div class="">
                        <table id="vendorsTable" class="table table-sm table-bordered table-hover text-center small-text"
                            style="width:100%">
                            <thead class="">
                                <tr>
                                    <th>{{ __('owner.vendors.table.index') }}</th>
                                    <th>{{ __('owner.vendors.table.name') }}</th>
                                    <th>{{ __('owner.vendors.table.email') }}</th>
                                    <th>{{ __('owner.vendors.table.phone') }}</th>
                                    <th>{{ __('owner.vendors.table.status') }}</th>
                                    <th>{{ __('owner.vendors.table.actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        window.routes = {
            vendorsIndex: "{{ route('owner.vendors.index') }}",
            vendorsData: "{{ route('owner.vendors.data') }}",
            vendorsStore: "{{ route('owner.vendors.store') }}",
            vendorsUpdate: "{{ route('owner.vendors.update', ':id') }}",
            vendorsDestroy: "{{ route('owner.vendors.destroy', ':id') }}",
        };
    </script>
    <script src="{{ asset('dashboard/assets/js/owner/vendors.js') }}"></script>
@endsection
