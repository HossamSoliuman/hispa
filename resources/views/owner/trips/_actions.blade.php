@php use App\Enums\TripStatus; @endphp
<div class="d-flex flex-wrap gap-1 justify-content-center">
    {{-- Print (always visible) --}}
    <a href="{{ route('owner.reports.print.trip', $trip->id) }}" target="_blank"
       class="btn btn-sm btn-outline-primary" title="{{ __('owner.trips.print_trip') }}">
        <i class="bi bi-printer"></i>
    </a>

    {{-- Edit trip (only when New) --}}
    @if ($trip->status === TripStatus::New)
        <a href="{{ route('owner.trips.edit', $trip->id) }}"
           class="btn btn-sm btn-outline-secondary" title="{{ __('owner.actions.edit') }}">
            <i class="bi bi-pencil"></i>
        </a>
    @endif

    {{-- Add Catch (Finished, no catch yet) --}}
    @if ($trip->status === TripStatus::Finished && ! $trip->catches)
        <a href="{{ route('owner.catch.create') }}?trip_id={{ $trip->id }}"
           class="btn btn-sm btn-outline-info" title="{{ __('owner.catch.add_catch') }}">
            <i class="bi bi-plus"></i> {{ __('owner.catch.add_catch') }}
        </a>
    @endif

    {{-- Edit Catch (Sold, catch exists) --}}
    @if ($trip->status === TripStatus::Sold && $trip->catches)
        <a href="{{ route('owner.catch.edit', $trip->catches->id) }}"
           class="btn btn-sm btn-outline-info" title="{{ __('owner.catch.edit_catch') }}">
            <i class="bi bi-pencil"></i> {{ __('owner.catch.edit_catch') }}
        </a>
    @endif

    {{-- Forward/cancel transition buttons --}}
    @foreach ($trip->status->allowedNext() as $next)
        @php
            $isCancelAction = $next === TripStatus::Cancelled;
            $label = $trip->status->transitionLabelTo($next);
            $btnClass = $isCancelAction ? 'btn-outline-danger' : 'btn-outline-success';
        @endphp
        <button type="button"
                class="btn btn-sm {{ $btnClass }}"
                onclick="tripTransition({{ $trip->id }}, {{ $next->value }}, {{ $isCancelAction ? 'true' : 'false' }})">
            {{ $label }}
        </button>
    @endforeach
</div>
