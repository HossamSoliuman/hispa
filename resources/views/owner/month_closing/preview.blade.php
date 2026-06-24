@extends('owner.layouts.master')

@php
    $arabicMonths = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];
    $monthLabel = ($arabicMonths[$month] ?? $month) . ' ' . $year;
    $boatLabel = $boatId ? optional($boats->firstWhere('id', $boatId))->name : __('owner.profit_loss.all_boats');
@endphp

@section('title', __('owner.month_closing.preview_title').' '.$monthLabel)

@section('content')
    @php $f = $preview['financials']; @endphp

    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ __('owner.month_closing.preview_title') }} {{ $monthLabel }}</h2>
            <span class="badge bg-secondary"><i class="fa fa-ship me-1"></i>{{ $boatLabel }}</span>
        </div>
        <div class="ms-auto">
            <a href="{{ route('owner.month-closing.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-right me-1"></i>{{ __('owner.month_closing.title') }}
            </a>
        </div>
    </div>

    @foreach ($preview['warnings'] as $warning)
        <div class="alert alert-warning"><i class="fa fa-exclamation-triangle me-2"></i>{{ $warning }}</div>
    @endforeach

    @if ($preview['existing'])
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <span>{{ __('owner.month_closing.errors.already_closed') }}</span>
            <a href="{{ route('owner.month-closing.show', $preview['existing']) }}" class="btn btn-sm btn-info">
                <i class="fa fa-eye me-1"></i>{{ __('owner.month_closing.print') }}
            </a>
        </div>
    @endif

    {{-- Waterfall summary --}}
    <div class="row g-3 mb-4">
        @php
            $operatingExpenses = (float) $f['total_expenses'] - (float) $f['depreciation'];
            $cards = [
                ['owner.profit_loss.net_sales', $f['net_sales'], 'success'],
                ['owner.month_closing.expenses', $operatingExpenses, 'danger'],
                ['owner.profit_loss.depreciation', $f['depreciation'], 'secondary'],
                ['owner.profit_loss.net_profit', $f['net_profit'], $f['net_profit'] >= 0 ? 'success' : 'danger'],
                ['owner.generated.owner_ratio', $f['owner_share'], 'primary'],
                ['owner.profit_loss.crew_share', $f['crew_share'], 'warning'],
            ];
        @endphp
        @foreach ($cards as [$label, $value, $color])
            <div class="col-md">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="small text-muted mb-1">{{ __($label) }}</div>
                        <div class="h5 fw-bold text-{{ $color }} mb-0">
                            {{ number_format($value, 2) }} <x-riyal-icon size="sm" />
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @include('owner.month_closing._assets_table', [
        'assets' => $preview['asset_depreciation']['assets'],
        'total' => $preview['asset_depreciation']['total'],
    ])

    {{-- Crew dues --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('owner.month_closing.distribution') }}</h5>
            <span class="badge bg-secondary">
                {{ __('owner.month_closing.columns.share_value') }}: {{ number_format($preview['share_value'], 2) }}
            </span>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('owner.month_closing.columns.member') }}</th>
                        <th>{{ __('owner.month_closing.columns.role') }}</th>
                        <th>{{ __('owner.month_closing.columns.shares') }}</th>
                        <th>{{ __('owner.month_closing.columns.custom_percent') }}</th>
                        <th>{{ __('owner.month_closing.columns.due') }}</th>
                        <th>{{ __('owner.month_closing.columns.advances') }}</th>
                        <th>{{ __('owner.month_closing.columns.remaining') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($preview['dues'] as $due)
                        <tr>
                            <td>{{ $due['member_name'] }}</td>
                            <td>{{ $due['role'] }}</td>
                            <td>{{ $due['custom_share_percent'] !== null ? '-' : number_format($due['shares'], 2) }}</td>
                            <td>{{ $due['custom_share_percent'] !== null ? number_format($due['custom_share_percent'], 2) . '%' : '-' }}</td>
                            <td>{{ number_format($due['due_amount'], 2) }}</td>
                            <td>{{ number_format($due['advances'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($due['remaining'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">--</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="2">{{ __('owner.month_closing.columns.shares') }}: {{ number_format($preview['total_shares'], 2) }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format(collect($preview['dues'])->sum('due_amount'), 2) }}</td>
                        <td>{{ number_format(collect($preview['dues'])->sum('advances'), 2) }}</td>
                        <td>{{ number_format(collect($preview['dues'])->sum('remaining'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @unless ($preview['existing'])
        <form method="POST" action="{{ route('owner.month-closing.close') }}" id="closeMonthForm">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="boat_id" value="{{ $boatId }}">
            <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#closeMonthModal">
                <i class="fa fa-lock me-2"></i>{{ __('owner.month_closing.close_btn') }}
            </button>
        </form>

        <div class="modal fade" id="closeMonthModal" tabindex="-1" aria-labelledby="closeMonthModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="closeMonthModalLabel">
                            <i class="fa fa-lock me-2 text-success"></i>{{ __('owner.month_closing.close_btn') }} — {{ $monthLabel }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('owner.generated.btn_close_modal') }}"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">{{ __('owner.month_closing.confirm_close') }}</p>
                        <div class="alert alert-warning mb-0 py-2 small">
                            <i class="fa fa-exclamation-triangle me-1"></i>
                            {{ __('owner.month_closing.preview_title') }}: <strong>{{ $monthLabel }}</strong>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('owner.swal.cancel') }}
                        </button>
                        <button type="button" class="btn btn-success" onclick="document.getElementById('closeMonthForm').submit()">
                            <i class="fa fa-lock me-2"></i>{{ __('owner.swal.confirm_proceed') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endunless
@endsection
