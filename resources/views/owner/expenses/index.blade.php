@extends('owner.layouts.master')

@section('title', __('owner.expenses.manage_title'))

@section('css')
<style>
    /* Currency icon sizing: make riyal SVG consistent and small across cards, tables and buttons */
    .unit,
    .stat-card .stat-value .unit,
    .stat-card-hover .stat-value .unit,
    table .unit,
    .btn .unit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .unit svg,
    .stat-card .stat-value .unit svg,
    table .unit svg,
    .btn .unit svg {
        width: 14px !important;
        height: 14px !important;
        max-width: 14px !important;
        max-height: 14px !important;
        display: inline-block;
        vertical-align: middle;
    }

    /* Small spacing so the icon sits next to the number without pushing layout */
    .unit {
        margin-left: 6px;
        margin-right: 0;
    }
</style>
@endsection

@section('content')

<!-- Header -->
<div class="row mb-4 align-items-center justify-content-between">
    <div class="col-md-6">
        <h2 class=" fw-bold text-dark mb-2">{{ __('owner.expenses.manage_title') }}</h2>
    </div>
    <div class="col-md-6 text-md-end text-sm-start mt-3 mt-md-0">
        <a href="{{route('owner.expenses.create')}}" class="btn btn-outline-theme btn-equal">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('owner.expenses.add_new') }}
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    @include('owner.components.stat-card', [
        'title' => __('owner.expenses.cards.total_count'),
        'value' => '<span>' . ($count ?? 0) . '</span>',
        'icon' => 'bi bi-list-check',
        'gradient' => 'linear-gradient(135deg, #34495e, #2c3e50)',
        'colClass' => 'col-md-3 col-sm-6 mb-3'
    ])

    @include('owner.components.stat-card', [
        'title' => __('owner.expenses.cards.total_amount'),
        'value' => '<span>' . number_format($totalAmount ?? 0, 2) . '</span> <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>',
        'icon' => 'bi bi-currency-dollar',
        'gradient' => 'linear-gradient(135deg, #2980b9, #3498db)',
        'colClass' => 'col-md-3 col-sm-6 mb-3'
    ])

    @include('owner.components.stat-card', [
        'title' => __('owner.expenses.cards.pending_amount'),
        'value' => '<span>' . number_format($pendingAmount ?? 0, 2) . '</span> <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>',
        'icon' => 'bi bi-clock-history',
        'gradient' => 'linear-gradient(135deg, #c0392b, #e74c3c)',
        'colClass' => 'col-md-3 col-sm-6 mb-3'
    ])

    @include('owner.components.stat-card', [
        'title' => __('owner.expenses.cards.paid_amount'),
        'value' => '<span>' . number_format($paidAmount ?? 0, 2) . '</span> <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>',
        'icon' => 'bi bi-check-circle',
        'gradient' => 'linear-gradient(135deg, #27ae60, #2ecc71)',
        'colClass' => 'col-md-3 col-sm-6 mb-3'
    ])
</div>



<!-- Filters -->
<div class="card shadow-sm border-0 mt-4">
    <div class="card-header">
        <h5 class="card-title">{{ __('owner.expenses.filters.title') }}</h5>
    </div>
    <div class="card-body">
        <div class="row align-items-end gy-2">
            <div class="col-md-2">
                <label class="form-label">{{ __('owner.expenses.filters.category') }}</label>
                <select id="filterCategory" class="form-select">
                    <option value="">{{ __('owner.expenses.filters.all_categories') }}</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('owner.expenses.filters.boat') }}</label>
                <select id="filterBoat" class="form-select">
                    <option value="">{{ __('owner.expenses.filters.all_boats') }}</option>
                    @foreach($boats as $boat)
                    <option value="{{ $boat->id }}">{{ $boat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('owner.expenses.filters.status') }}</label>
                <select id="filterStatus" class="form-select">
                    <option value="">{{ __('owner.expenses.filters.all_statuses') }}</option>
                    <option value="paid">{{ __('owner.paid') }}</option>
                    <option value="pending">{{ __('owner.pending') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('owner.expenses.filters.from_date') }}</label>
                <input type="date" id="filterFromDate" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('owner.expenses.filters.to_date') }}</label>
                <input type="date" id="filterToDate" class="form-control">
            </div>
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-end align-items-center gap-2">
                    <button id="applyFilters" class="btn btn-primary btn-sm">
                        <i class="bi bi-search"></i> {{ __('owner.expenses.filters.search') }}
                    </button>
                    <button id="clearFilters" class="btn btn-light btn-sm">
                        <i class="bi bi-x-circle"></i> {{ __('owner.expenses.filters.clear') }}
                    </button>
                    <a href="#" id="printReportBtn" target="_blank" class="btn btn-dark btn-sm">
                        <i class="bi bi-printer"></i> {{ __('owner.expenses.filters.print_report') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mt-4 d-flex" id="expenseTabs" role="tablist">
    <li class="nav-item flex-fill text-center" role="presentation">
        <button class="nav-link w-100 active" id="records-tab" data-bs-toggle="tab" data-bs-target="#records"
            type="button" role="tab">
            {{ __('owner.expenses.tabs.records') }}
        </button>
    </li>
    <li class="nav-item flex-fill text-center" role="presentation">
        <button class="nav-link w-100" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics"
            type="button" role="tab">
            {{ __('owner.expenses.tabs.analytics') }}
        </button>
    </li>
    <li class="nav-item flex-fill text-center" role="presentation">
        <button class="nav-link w-100" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories"
            type="button" role="tab">
            {{ __('owner.expenses.tabs.categories') }}
        </button>
    </li>
    <li class="nav-item flex-fill text-center" role="presentation">
        <button class="nav-link w-100" id="trends-tab" data-bs-toggle="tab" data-bs-target="#trends" type="button"
            role="tab">
            {{ __('owner.expenses.tabs.trends') }}
        </button>
    </li>
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="expenseTabsContent">
    <!-- Tab: Records -->
    <div class="tab-pane fade show active" id="records" role="tabpanel">
        @include('owner.expenses.sections.records')
    </div>

    <!-- Tab: Analytics -->
    <div class="tab-pane fade" id="analytics" role="tabpanel">
     @include('owner.expenses.sections.analytics', compact('analytics'))
    </div>

    <!-- Tab: Categories -->
    <div class="tab-pane fade" id="categories" role="tabpanel">
        @include('owner.expenses.sections.categories')
    </div>

    <!-- Tab: Trends -->
    <div class="tab-pane fade" id="trends" role="tabpanel">
        @include('owner.expenses.sections.trends')
    </div>
</div>

@endsection

@section('script')
<script>
    window.routes = {
        expensesIndex: "{{ route('owner.expenses.index') }}",
        expensesData: "{{ route('owner.expenses.data') }}",
        expensesStore: "{{ route('owner.expenses.store') }}",
        expensesUpdate: "{{ route('owner.expenses.update', ':id') }}",
        expensesDestroy: "{{ route('owner.expenses.destroy', ':id') }}",
        expensesStatus: "{{ route('owner.expenses.status', ':id') }}",
        expensesPrint: "{{ route('owner.expenses.print', ':id') }}",
        expensesReportPrint: "{{ route('owner.expenses.report.print') }}",
    };
</script>
<script>
    // If DataTables is loaded, apply the language defaults so the table UI is translated
    if (typeof window.datatablesLang !== 'undefined' && typeof $.fn.dataTable !== 'undefined') {
        $.extend(true, $.fn.dataTable.defaults, { language: window.datatablesLang });
    }
</script>
<script>
    // DataTables language strings (used by JS table init)
    window.datatablesLang = {
        lengthMenu: "{{ __('owner.expenses.datatable.lengthMenu') }}",
        search: "{{ __('owner.expenses.datatable.search') }}",
        info: "{{ __('owner.expenses.datatable.info') }}",
        paginate: {
            next: "{{ __('owner.expenses.datatable.paginate.next') }}",
            previous: "{{ __('owner.expenses.datatable.paginate.previous') }}"
        }
    };
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="{{ asset('dashboard/assets/js/owner/expenses/index.js') }}"></script>
<script>
    // طباعة المصاريف المعروضة حسب الفلاتر المطبقة
    $(function () {
        $('#printReportBtn').on('click', function (e) {
            e.preventDefault();
            const params = new URLSearchParams();
            const categoryId = $('#filterCategory').val();
            const boatId = $('#filterBoat').val();
            const status = $('#filterStatus').val();
            const fromDate = $('#filterFromDate').val();
            const toDate = $('#filterToDate').val();
            if (categoryId) params.append('category_id', categoryId);
            if (boatId) params.append('boat_id', boatId);
            if (status) params.append('status', status);
            if (fromDate) params.append('from_date', fromDate);
            if (toDate) params.append('to_date', toDate);
            const query = params.toString();
            const url = window.routes.expensesReportPrint + (query ? ('?' + query) : '');
            window.open(url, '_blank');
        });
    });
</script>


@endsection
