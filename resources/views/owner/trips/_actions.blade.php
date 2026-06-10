@php use App\Enums\TripStatus; @endphp
<div class="trip-actions d-flex flex-wrap align-items-center justify-content-center gap-1">
    {{-- Forward/cancel transition buttons (primary actions first) --}}
    @foreach ($trip->status->allowedNext() as $next)
        @php
            $isCancelAction = $next === TripStatus::Cancelled;
            // A catch must be added before a finished trip can move on to counting.
            $requiresCatch = $trip->status === TripStatus::Finished && $next === TripStatus::Counting;
            // Once counting is finished the owner sells directly; hide the "ready for sell" step.
            $hideReadyForSell = $trip->status === TripStatus::Counted;
        @endphp
        @continue(($requiresCatch && ! $trip->catches) || $hideReadyForSell)
        @php
            $label = $trip->status->transitionLabelTo($next);
            $btnClass = $isCancelAction ? 'btn-outline-danger' : 'btn-success';
            $btnIcon = $isCancelAction ? 'bi-x-circle' : 'bi-arrow-right-circle';
        @endphp
        <button type="button"
                class="btn btn-sm {{ $btnClass }}"
                onclick="tripTransition({{ $trip->id }}, {{ $next->value }}, {{ $isCancelAction ? 'true' : 'false' }})">
            <i class="bi {{ $btnIcon }}"></i>
            <span>{{ $label }}</span>
        </button>
    @endforeach

    {{-- Add Catch (Finished, no catch yet) --}}
    @if ($trip->status === TripStatus::Finished && ! $trip->catches)
        <a href="{{ route('owner.catch.create') }}?trip_id={{ $trip->id }}"
           class="btn btn-sm btn-info text-white" title="{{ __('owner.catch.add_catch') }}">
            <i class="bi bi-plus-lg"></i>
            <span>{{ __('owner.catch.add_catch') }}</span>
        </a>
    @endif

    {{-- Edit Catch (catch exists, before counting is finished; never once the trip is sold/cancelled) --}}
    @if ($trip->catches && $trip->status !== TripStatus::Counted && ! $trip->status->isTerminal())
        <a href="{{ route('owner.catch.edit', $trip->catches->id) }}"
           class="btn btn-sm btn-outline-info" title="{{ __('owner.catch.edit_catch') }}">
            <i class="bi bi-pencil"></i>
            <span>{{ __('owner.catch.edit_catch') }}</span>
        </a>
    @endif

    {{-- Sell (catch exists, only after counting is finished) --}}
    @if ($trip->catches && in_array($trip->status, [TripStatus::Counted, TripStatus::ReadyToSell], true))
        <a href="{{ route('owner.sales.create') }}?trip_id={{ $trip->id }}"
           class="btn btn-sm btn-primary" title="{{ __('owner.catch.sell') }}">
            <i class="bi bi-cash-coin"></i>
            <span>{{ __('owner.catch.sell') }}</span>
        </a>
    @endif

    {{-- Edit trip (only when New) --}}
    @if ($trip->status === TripStatus::New)
        <a href="{{ route('owner.trips.edit', $trip->id) }}"
           class="btn btn-sm btn-outline-secondary" title="{{ __('owner.actions.edit') }}">
            <i class="bi bi-pencil"></i>
        </a>
    @endif

    {{-- Print (always visible) --}}
    <a href="{{ route('owner.reports.print.trip', $trip->id) }}" target="_blank"
       class="btn btn-sm btn-outline-primary" title="{{ __('owner.trips.print_trip') }}">
        <i class="bi bi-printer"></i>
    </a>
</div>
