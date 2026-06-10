@extends('owner.layouts.master')

@section('title', __('owner.month_closing.preview_title').' '.sprintf('%02d/%d', $month, $year))

@section('content')
    @php $f = $preview['financials']; @endphp

    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ __('owner.month_closing.preview_title') }} {{ sprintf('%02d/%d', $month, $year) }}</h2>
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
            $cards = [
                ['owner.profit_loss.net_sales', $f['net_sales'], 'success'],
                ['owner.profit_loss.total_expenses', $f['total_expenses'], 'danger'],
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
                            <td>{{ number_format($due['shares'], 2) }}</td>
                            <td>{{ number_format($due['due_amount'], 2) }}</td>
                            <td>{{ number_format($due['advances'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($due['remaining'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">--</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="2">{{ __('owner.month_closing.columns.shares') }}: {{ number_format($preview['total_shares'], 2) }}</td>
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
        <form method="POST" action="{{ route('owner.month-closing.close') }}"
            onsubmit="return confirm('{{ __('owner.month_closing.confirm_close') }}')">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fa fa-lock me-2"></i>{{ __('owner.month_closing.close_btn') }}
            </button>
        </form>
    @endunless
@endsection
