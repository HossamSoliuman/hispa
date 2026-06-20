<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-labelledby="maintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-l modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('owner.boats.maintenance_schedule') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('owner.actions.close') }}"></button>
            </div>
            <div class="modal-body">
                <form id="maintenanceForm">
                    @csrf
                    <input type="hidden" id="maintenance_id" name="id">

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
                            <label>{{ __('owner.boats.maintenance_type') }}</label>
                            <select name="category_id" class="form-select">
                                <option selected disabled>{{ __('owner.actions.choose') }}</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.boats.date_maintenance') }}</label>
                            <input type="date" name="date" class="form-control"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('owner.boats.next_maintenance_date') }}</label>
                            <input type="date" name="next_maintenance_date" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('owner.boats.cost_expected') }}</label>
                            <input type="number" name="estimated_cost" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('owner.boats.technician') }}</label>
                            <input type="text" name="technician" class="form-control"
                                placeholder="{{ __('owner.generated.placeholder_technician_name') }}">
                        </div>

                        <div class="col-md-12">
                            <label>{{ __('owner.boats.description') }}</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('owner.actions.cancel') }}</button>
                <button type="submit" form="maintenanceForm" class="btn btn-primary">
                    {{ __('owner.actions.save') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="maintenanceShowModal" tabindex="-1" aria-labelledby="maintenanceShowModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-l modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('owner.boats.maintenance_details') }}</h5>
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
                        <label>{{ __('owner.boats.maintenance_type') }}</label>
                        <input type="text" class="form-control" name="category_name" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('owner.boats.date_maintenance') }}</label>
                        <input type="date" name="date" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('owner.boats.next_maintenance_date') }}</label>
                        <input type="date" name="next_maintenance_date" class="form-control" readonly>
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
