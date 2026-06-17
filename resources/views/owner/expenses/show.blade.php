@extends('owner.layouts.master')

@section('title', __('owner.expenses.show.title'))

@section('content')
<style>
    .unit svg { width: 14px; height: 14px; }
    @media print {
        .no-print { display: none !important; }
        .page-header { font-size: 1.5rem; }
    }
</style>

<div class="container-fluid py-4">

    {{-- header --}}
    <div class="d-flex align-items-center mb-4 no-print">
        <div class="me-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">
                    <li class="breadcrumb-item"><a href="{{ route('owner.expenses.index') }}">{{ __('owner.expenses.manage_title') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('owner.expenses.show.breadcrumb') }}</li>
                </ol>
            </nav>
            <h1 class="page-header mb-0 fw-bold text-dark">{{ __('owner.expenses.show.page_header') }}</h1>
        </div>
        <div>
            <a href="{{ route('owner.expenses.index') }}" class="btn btn-outline-dark me-2 rounded-pill shadow-sm">
                <i class="bi bi-arrow-right"></i> {{ __('owner.actions.back') }}
            </a>
            <a href="{{ route('owner.expenses.print', $expense->id) }}" target="_blank" class="btn btn-success rounded-pill shadow-sm">
                <i class="bi bi-printer"></i> {{ __('owner.actions.print') }}
            </a>
        </div>
    </div>

    {{-- Main Info Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body text-center py-3">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="bi bi-hash fs-3 text-primary"></i>
                    </div>
                    <h6 class="text-muted mb-1 small">{{ __('owner.expenses.table.expense_number') }}</h6>
                    <p class="fw-bold fs-5 mb-0 text-dark">{{ $expense->number }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body text-center py-3">
                    <i class="bi bi-calendar-event fs-3 text-info"></i>
                    <h6 class="mt-2 mb-1 text-muted small">{{ __('owner.expenses.table.date') }}</h6>
                    <p class="fw-bold mb-0">{{ $expense->date }}</p>
                    <small class="text-muted">@hijri($expense->date)</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body text-center py-3">
                    <i class="bi {{ $expense->status === 'paid' ? 'bi-check-circle-fill text-success' : 'bi-hourglass-split text-warning' }} fs-3"></i>
                    <h6 class="mt-2 mb-1 text-muted small">{{ __('owner.expenses.table.status') }}</h6>
                    <span class="badge {{ $expense->status === 'paid' ? 'bg-success' : 'bg-warning' }} px-3 py-1 rounded-pill">
                        {{ $expense->status === 'paid' ? __('owner.paid') : __('owner.pending') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body text-center py-3">
                    <i class="bi bi-currency-exchange fs-3 text-success"></i>
                    <h6 class="mt-2 mb-1 text-muted small">{{ __('owner.expenses.show.totals.final_total') }}</h6>
                    <p class="fw-bold fs-5 mb-0 text-success">{!! number_format($expense->final_price, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Details & Classification --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-info-circle text-primary"></i> {{ __('owner.expenses.show.details_title') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small mb-1"><i class="bi bi-tag"></i> {{ __('owner.expenses.table.category') }}</label>
                        <p class="fw-semibold mb-0">{{ optional($expense->category->parent)->name ?? '-' }} / {{ $expense->category->name ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1"><i class="bi bi-sailboat"></i> {{ __('owner.expenses.table.boat') }}</label>
                        <p class="fw-semibold mb-0">{{ $expense->boat->name ?? __('owner.general') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1"><i class="bi bi-people"></i> {{ __('owner.expenses.table.vendor') }}</label>
                        <p class="fw-semibold mb-0">{{ $expense->vendor->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-muted small mb-1"><i class="bi bi-credit-card"></i> {{ __('owner.expenses.show.payment_method') }}</label>
                        <p class="fw-semibold mb-0">{{ $expense->paymentMethod->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-receipt text-success"></i> {{ __('owner.expenses.show.invoice_summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span class="text-muted">{{ __('owner.expenses.show.totals.before_discount') }}</span>
                        <span class="fw-semibold">{!! number_format($expense->total_price, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</span>
                    </div>

                    @if($expense->discount_value > 0)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom text-danger">
                        <span>{{ __('owner.expenses.show.totals.discount_value') }}
                            @if($expense->discount_type === 'percentage')
                                <small>({{ $expense->discount_value }}%)</small>
                            @endif
                        </span>
                        <span>- {!! number_format($expense->calculated_discount, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</span>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                        <span class="fw-bold fs-5">{{ __('owner.expenses.show.totals.final_total') }}</span>
                        <span class="fw-bold fs-5 text-success">{!! number_format($expense->final_price, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notes Section --}}
    @if($expense->notes)
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <h6 class="fw-bold text-muted mb-2"><i class="bi bi-journal-text"></i> {{ __('owner.expenses.show.notes') }}</h6>
                    <p class="mb-0">{{ $expense->notes }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Equipment/Maintenance Details --}}
    @if(optional($expense->category->parent)->type === 'operating' && $expense->category->type === 'operating-equipments')
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-tools text-primary"></i> {{ __('owner.expenses.show.equipment_details_title') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th>{{ __('owner.expenses.show.equipment_name') }}</th>
                                <th class="text-center">{{ __('owner.expenses.show.quantity') }}</th>
                                <th class="text-center">{{ __('owner.expenses.show.unit_price') }}</th>
                                <th class="text-center">{{ __('owner.expenses.show.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expense->details as $detail)
                            @php $item = $detail->expenseable; @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $item->fishingEquipment->name ?? '-' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-center">{!! number_format($item->unit_price, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</td>
                                <td class="text-center fw-bold text-success">{!! number_format($item->total_price, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif(optional($expense->category->parent)->type === 'maintenance')
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-wrench-adjustable text-warning"></i> {{ __('owner.expenses.show.maintenance_details_title') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">{{ __('owner.expenses.table.date') }}</th>
                                <th>{{ __('owner.expenses.show.boat') }}</th>
                                <th>{{ __('owner.expenses.show.description') }}</th>
                                <th>{{ __('owner.expenses.show.technician') }}</th>
                                <th class="text-center">{{ __('owner.expenses.show.estimated_cost') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expense->details as $detail)
                            @php $maintenance = $detail->expenseable; @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    <div>{{ $maintenance->date }}</div>
                                    <small class="text-muted">@hijri($maintenance->date)</small>
                                </td>
                                <td class="fw-semibold">{{ $maintenance->boat->name ?? '-' }}</td>
                                <td>{{ $maintenance->description }}</td>
                                <td>{{ $maintenance->technician }}</td>
                                <td class="text-center fw-bold text-success">{!! number_format($maintenance->estimated_cost, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Attachment --}}
    @if($expense->attachment)
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-0 pt-3">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-paperclip text-info"></i> {{ __('owner.expenses.show.attachment') }}</h5>
        </div>
        <div class="card-body">
            <a href="{{ $expense->attachment_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-download"></i> {{ __('owner.expenses.show.view_attachment') }}
            </a>
        </div>
    </div>
    @endif

</div>
@endsection
