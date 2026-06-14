@php use App\Enums\TripStatus; @endphp
<div class="trip-actions d-flex flex-wrap align-items-center justify-content-center gap-1">
    {{-- Forward/cancel transition buttons (primary actions first) --}}
    @foreach ($trip->status->allowedNext() as $next)
        @php
            $isCancelAction = $next === TripStatus::Cancelled;
            // Moving a finished trip forward happens automatically once a catch is added,
            // and selling is handled by the dedicated Sell button below; hide those manual steps.
            $isAutoTransition =
                ($trip->status === TripStatus::Finished && $next === TripStatus::ReadyToSell) ||
                ($trip->status === TripStatus::ReadyToSell && $next === TripStatus::Sold);
        @endphp
        @continue($isAutoTransition)
        @php
            $label = $trip->status->transitionLabelTo($next);
            $btnClass = $isCancelAction ? 'btn-outline-danger' : 'btn-success';
            $btnIcon = $isCancelAction ? 'bi-x-circle' : 'bi-arrow-right-circle';
            $isFinishAction = $next === TripStatus::Finished;
            $plannedEnd = $trip->end_date ? \Illuminate\Support\Carbon::parse($trip->end_date)->format('Y-m-d\TH:i') : '';
        @endphp
        @if ($isFinishAction)
            <button type="button"
                    class="btn btn-sm btn-success"
                    onclick="finishTrip({{ $trip->id }}, '{{ $plannedEnd }}')">
                <i class="bi bi-flag-fill"></i>
                <span>{{ $label }}</span>
            </button>
        @else
            <button type="button"
                    class="btn btn-sm {{ $btnClass }}"
                    onclick="tripTransition({{ $trip->id }}, {{ $next->value }}, {{ $isCancelAction ? 'true' : 'false' }})">
                <i class="bi {{ $btnIcon }}"></i>
                <span>{{ $label }}</span>
            </button>
        @endif
    @endforeach

    {{-- Add Catch (Finished, no catch yet) --}}
    @if ($trip->status === TripStatus::Finished && ! $trip->catches)
        <a href="{{ route('owner.catch.create') }}?trip_id={{ $trip->id }}"
           class="btn btn-sm btn-info text-white" title="{{ __('owner.catch.add_catch') }}">
            <i class="bi bi-plus-lg"></i>
            <span>{{ __('owner.catch.add_catch') }}</span>
        </a>
    @endif

    {{-- Edit Catch (catch exists, while the trip is still open; never once sold/cancelled) --}}
    @if ($trip->catches && ! $trip->status->isTerminal())
        <a href="{{ route('owner.catch.edit', $trip->catches->id) }}"
           class="btn btn-sm btn-outline-info" title="{{ __('owner.catch.edit_catch') }}">
            <i class="bi bi-pencil"></i>
            <span>{{ __('owner.catch.edit_catch') }}</span>
        </a>
    @endif

    {{-- Sell (catch exists, trip ready to sell) --}}
    @if ($trip->catches && in_array($trip->status, [TripStatus::Counted, TripStatus::ReadyToSell], true))
        <a href="{{ route('owner.sales.create') }}?trip_id={{ $trip->id }}"
           class="btn btn-sm btn-primary" title="{{ __('owner.catch.sell') }}">
            <i class="bi bi-cash-coin"></i>
            <span>{{ __('owner.catch.sell') }}</span>
        </a>
    @endif

    {{-- Edit trip (any non-terminal trip; allows updating the end date and other details) --}}
    @if (! $trip->status->isTerminal())
        <a href="{{ route('owner.trips.edit', $trip->id) }}"
           class="btn btn-sm btn-outline-secondary" title="{{ __('owner.trips.edit_trip') }}">
            <i class="bi bi-pencil"></i>
        </a>
    @endif

    {{-- Print (always visible) --}}
    <a href="{{ route('owner.reports.print.trip', $trip->id) }}" target="_blank"
       class="btn btn-sm btn-outline-primary" title="{{ __('owner.trips.print_trip') }}">
        <i class="bi bi-printer"></i>
    </a>
</div>
