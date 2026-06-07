<div class="modal fade" id="inspectionModal" tabindex="-1" aria-labelledby="inspectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-l modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('owner.generated.inspect_boat') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('owner.actions.close') }}"></button>
            </div>
            <div class="modal-body">
                <form id="inspectionForm">
                    @csrf
                    <input type="hidden" id="inspection_id" name="id">

                    <div class="row g-3">
                        @if(isset($fixedBoat))
                        <input type="hidden" name="boat_id" value="{{ $fixedBoat->id }}">
                        @else
                        <div class="col-md-12">
                            <label>{{ __('owner.boats.name') }}</label>
                            <select name="boat_id" class="form-select">
                                <option selected disabled>{{ __('owner.boats.select_boat') }}</option>
                                @foreach ($boats as $boat)
                                    <option value="{{ $boat->id }}">{{ $boat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-12">
                            <label>{{ __('owner.assets.status') }}</label>
                            <select name="status" class="form-select">
                                @foreach (App\Enums\InspectionStatus::cases() as $status)
                                    <option value="{{ $status->value }}">
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('owner.generated.inspection_start_date') }}</label>
                            <input type="datetime-local" name="check_date" class="form-control"
                                value="{{ old('check_date', now()->format('Y-m-d\TH:i')) }}">
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('owner.generated.inspection_end_date') }}</label>
                            <input type="datetime-local" name="next_check" class="form-control"
                                value="{{ old('next_check', now()->addYear()->subDays(10)->format('Y-m-d\TH:i')) }}">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('owner.actions.cancel') }}</button>
                <button type="submit" form="inspectionForm" class="btn btn-primary">
                    {{ __('owner.actions.save') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="inspectionShowModal" tabindex="-1" aria-labelledby="inspectionShowModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-l modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('owner.boats.inspection_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('owner.actions.close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label>{{ __('owner.boats.name') }}</label>
                        <input type="text" class="form-control" name="boat_name" readonly>
                    </div>

                    <div class="col-md-12">
                        <label>{{ __('owner.boats.inspection_type') }}</label>
                        <input type="text" class="form-control" name="category_name" readonly>
                    </div>
                    <div class="col-md-12">
                        <label>{{ __('owner.boats.date_inspection') }}</label>
                        <input type="date" name="date" class="form-control" readonly>
                    </div>
                    <div class="col-md-12">
                        <label>{{ __('owner.boats.cost_expected') }}</label>
                        <input type="number" name="estimated_cost" class="form-control" placeholder="0" readonly>
                    </div>
                    <div class="col-md-12">
                        <label>{{ __('owner.boats.technician') }}</label>
                        <input type="text" class="form-control" name="technician"
                            placeholder="{{ __('owner.generated.placeholder_technician_name') }}" readonly>
                    </div>

                    <div class="col-md-12">
                        <label>{{ __('owner.boats.description') }}</label>
                        <textarea name="description" class="form-control" rows="3" readonly></textarea>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
