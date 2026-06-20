@php
    $selectedRows = ! empty($selected) ? $selected : [['boat_id' => '', 'captain_ids' => []]];
@endphp
<hr>
<div class="row mb-2">
    <div class="col-12">
        <h5 class="mb-1"><i class="fas fa-anchor me-1"></i>{{ __('owner.trips.boats.title') }}</h5>
        <small class="text-muted">{{ __('owner.trips.boats.hint') }}</small>
    </div>
</div>
@error('boats') <span class="text-danger error d-block mb-2">{{ $message }}</span>@enderror

<div id="boatsWrapper" data-next-index="{{ count($selectedRows) }}">
    @foreach($selectedRows as $index => $row)
        @include('owner.trips._boat_row', ['index' => $index, 'row' => $row, 'boats' => $boats, 'captains' => $captains])
    @endforeach
</div>

<div class="row mb-3">
    <div class="col-12">
        <button type="button" id="addBoatRow" class="btn btn-outline-primary btn-sm">
            <i class="fa fa-plus me-1"></i>{{ __('owner.trips.boats.add_boat') }}
        </button>
    </div>
</div>

<template id="boatRowTemplate">
    @include('owner.trips._boat_row', ['index' => '__INDEX__', 'row' => ['boat_id' => '', 'captain_ids' => []], 'boats' => $boats, 'captains' => $captains])
</template>
