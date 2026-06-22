@extends('owner.layouts.master')

@section('title', __('owner.month_closing.title'))

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ __('owner.month_closing.title') }}</h2>
            <p class="text-muted mb-0">{{ __('owner.month_closing.subtitle') }}</p>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Select month to preview --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('owner.month_closing.select_month') }}</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('owner.month-closing.preview') }}">
                <div class="row align-items-end gy-2">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('owner.month_closing.year') }}</label>
                        <input type="number" name="year" class="form-control" value="{{ $year }}" min="2000" max="2100" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('owner.month_closing.month') }}</label>
                        <select name="month" class="form-select" required>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('owner.profit_loss.boat') }}</label>
                        <select name="boat_id" class="form-select">
                            <option value="">{{ __('owner.profit_loss.all_boats') }}</option>
                            @foreach ($boats as $boat)
                                <option value="{{ $boat->id }}" {{ $boatId == $boat->id ? 'selected' : '' }}>
                                    {{ $boat->name ?? $boat->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-search me-2"></i>{{ __('owner.month_closing.preview_btn') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Closed months --}}
    <div class="card shadow-sm border-0">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('owner.month_closing.closings_list') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('owner.month_closing.columns.period') }}</th>
                            <th>{{ __('owner.profit_loss.boat') }}</th>
                            <th>{{ __('owner.month_closing.columns.net_profit') }}</th>
                            <th>{{ __('owner.profit_loss.crew_share') }}</th>
                            <th>{{ __('owner.month_closing.closed_at') }}</th>
                            <th>{{ __('owner.month_closing.columns.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($closings as $closing)
                            <tr>
                                <td>{{ sprintf('%02d/%d', $closing->month, $closing->year) }}</td>
                                <td>{{ $closing->boat?->name ?? __('owner.profit_loss.all_boats') }}</td>
                                <td>{{ number_format($closing->net_profit, 2) }}</td>
                                <td>{{ number_format($closing->crew_share, 2) }}</td>
                                <td>{{ optional($closing->closed_at)->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="d-flex gap-1 align-items-center justify-content-center">
                                        <a href="{{ route('owner.month-closing.show', $closing) }}" class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('owner.month-closing.print', $closing) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa fa-print"></i>
                                        </a>
                                        <form method="POST" action="{{ route('owner.month-closing.destroy', $closing) }}"
                                            class="d-flex m-0"
                                            onsubmit="return confirm('{{ __('owner.month_closing.messages.delete_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">{{ __('owner.month_closing.no_closings') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
