@php
    $selectedCaptains = array_map('strval', (array) ($row['captain_ids'] ?? []));
    $selectedCaptainNames = collect($captains)
        ->filter(fn ($c) => in_array((string) $c->id, $selectedCaptains, true))
        ->pluck('name')
        ->implode('، ');
@endphp
<div class="row g-2 align-items-start mb-3 boat-row border rounded p-2">
    <div class="col-xl-5 col-md-5">
        <label class="form-label">{{ __('owner.trips.boats.boat') }} <span class="text-danger">*</span></label>
        <select name="boats[{{ $index }}][boat_id]" class="form-select boat-select" required>
            <option value="">{{ __('owner.actions.choose') }}</option>
            @foreach($boats as $boat)
                <option value="{{ $boat->id }}" {{ (string) ($row['boat_id'] ?? '') === (string) $boat->id ? 'selected' : '' }}>
                    {{ $boat->name }}@if($boat->number) ({{ $boat->number }})@endif
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-xl-6 col-md-6">
        <label class="form-label">{{ __('owner.trips.boats.captains') }} <span class="text-danger">*</span></label>
        <div class="dropdown captain-dropdown" data-placeholder="{{ __('owner.actions.choose') }}">
            <button class="form-select text-start text-truncate captain-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <span class="captain-dropdown-label">{{ $selectedCaptainNames ?: __('owner.actions.choose') }}</span>
            </button>
            <div class="dropdown-menu w-100 p-2" style="max-height: 220px; overflow-y: auto;">
                @forelse($captains as $captain)
                    <label class="dropdown-item d-flex align-items-center gap-2 mb-1" style="cursor: pointer;">
                        <input type="checkbox" class="form-check-input m-0 captain-checkbox"
                            name="boats[{{ $index }}][captain_ids][]" value="{{ $captain->id }}"
                            data-name="{{ $captain->name }}"
                            {{ in_array((string) $captain->id, $selectedCaptains, true) ? 'checked' : '' }}>
                        <span>{{ $captain->name }}</span>
                    </label>
                @empty
                    <span class="dropdown-item-text text-muted small">{{ __('owner.trips.no_captain') }}</span>
                @endforelse
            </div>
        </div>
        <small class="text-muted">{{ __('owner.trips.boats.captains_hint') }}</small>
    </div>
    <div class="col-xl-1 col-md-1 d-flex align-items-end">
        <button type="button" class="btn btn-danger btn-sm w-100 btn-remove-boat" title="{{ __('owner.trips.boats.remove') }}">
            <i class="fa fa-times"></i>
        </button>
    </div>
</div>
