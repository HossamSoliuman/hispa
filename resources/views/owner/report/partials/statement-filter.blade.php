{{-- Entity + optional date range filter for account statement reports.
     Expects: $action, $printAction (route name), $entityParam, $entityLabel,
     $placeholder, $selectedId, $from, $to, and $groups — an array of
     ['label' => ?string, 'items' => iterable of objects with id & name]. --}}
<div class="card shadow-sm border-0 mb-3 no-print">
    <div class="card-body">
        <form method="GET" action="{{ $action }}">
            <div class="row align-items-end gy-2">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">{{ $entityLabel }}</label>
                    <select name="{{ $entityParam }}" class="form-select" required>
                        <option value="">{{ $placeholder }}</option>
                        @foreach ($groups as $group)
                            @if (!empty($group['label']))
                                <optgroup label="{{ $group['label'] }}">
                                    @foreach ($group['items'] as $item)
                                        <option value="{{ $item->id }}" @selected($selectedId == $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </optgroup>
                            @else
                                @foreach ($group['items'] as $item)
                                    <option value="{{ $item->id }}" @selected($selectedId == $item->id)>{{ $item->name }}</option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">{{ __('owner.analysis_reports.from_date') }}</label>
                    <input type="date" name="from" class="form-control" value="{{ $from }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">{{ __('owner.analysis_reports.to_date') }}</label>
                    <input type="date" name="to" class="form-control" value="{{ $to }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-search me-1"></i>{{ __('owner.analysis_reports.update') }}
                    </button>
                    @if ($selectedId)
                        <a href="{{ route($printAction, request()->all()) }}" target="_blank" class="btn btn-outline-info">
                            <i class="bi bi-printer me-1"></i>{{ __('owner.analysis_reports.print') }}
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
