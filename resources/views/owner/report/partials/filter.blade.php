{{-- Shared month/boat filter for analysis reports.
     Expects: $action, $from, $to, $printAction (route name string),
     optional $boats + $boatId, $showBoat (bool, default true). --}}
@php($showBoat = $showBoat ?? true)
<div class="card shadow-sm border-0 mb-3 no-print">
    <div class="card-body">
        <form method="GET" action="{{ $action }}">
            <div class="row align-items-end gy-2">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">{{ __('owner.analysis_reports.from_date') }}</label>
                    <input type="date" name="from" class="form-control" value="{{ $from }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">{{ __('owner.analysis_reports.to_date') }}</label>
                    <input type="date" name="to" class="form-control" value="{{ $to }}" required>
                </div>
                @if ($showBoat && isset($boats))
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">{{ __('owner.analysis_reports.boat') }}</label>
                        <select name="boat_id" class="form-select">
                            <option value="">{{ __('owner.analysis_reports.all_boats') }}</option>
                            @foreach ($boats as $boat)
                                <option value="{{ $boat->id }}" {{ ($boatId ?? null) == $boat->id ? 'selected' : '' }}>
                                    {{ $boat->name ?? $boat->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-search me-1"></i>{{ __('owner.analysis_reports.update') }}
                    </button>
                    <a href="{{ route($printAction, request()->all()) }}" target="_blank" class="btn btn-outline-info">
                        <i class="fa fa-print me-1"></i>{{ __('owner.analysis_reports.print') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
