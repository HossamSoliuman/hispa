<div class="d-flex align-items-center mb-3">
    <div>
        <h4 class="mb-2">{{ __('owner.units.title') }}</h4>
        <p class="text-muted mb-0">{{ __('owner.units.subtitle') }}</p>
    </div>
    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">
        <button class="btn btn-outline-theme btn-equal addUnitBtn" data-bs-toggle="modal" data-bs-target="#addUnitModal">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('owner.units.add_new') }}
        </button>
    </div>
</div>

<div class="card border-0 mb-3">
    <table id="unitsTable" class="table table-sm table-bordered table-hover text-center small-text"
        style="width: 100% !important;">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('owner.units.name_ar') }}</th>
                <th>{{ __('owner.units.name_en') }}</th>
                <th>{{ __('owner.units.default') }}</th>
                <th>{{ __('owner.units.status') }}</th>
                <th>{{ __('owner.units.actions') }}</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1">
    <div class="modal-dialog modal-l">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="unitModalTitle">{{ __('owner.units.add_new_title') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="unitForm">
                @csrf
                <input type="hidden" id="unitId">
                <input type="hidden" name="_method" id="unitFormMethod" value="POST">
                <input type="hidden" name="tab" value="units">
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="unitNameAr" class="form-label">{{ __('owner.units.name_ar') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unitNameAr" name="name_ar">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="unitNameEn" class="form-label">{{ __('owner.units.name_en') }}</label>
                            <input type="text" class="form-control" id="unitNameEn" name="name_en">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="unitStatus" class="form-label">{{ __('owner.units.status') }}</label>
                            <select class="form-select" id="unitStatus" name="status">
                                <option value="1">{{ __('owner.status.active') }}</option>
                                <option value="0">{{ __('owner.status.inactive') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="unitIsDefault" name="is_default"
                                value="1">
                            <label class="form-check-label"
                                for="unitIsDefault">{{ __('owner.units.set_default') }}</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('owner.actions.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('owner.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
