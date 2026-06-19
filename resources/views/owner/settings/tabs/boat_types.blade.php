{{-- ================== TAB: BOAT TYPES ================== --}}
<div class="d-flex align-items-center mb-3">
    <div>
        <h4 class="mb-2">{{ __('owner.boat_type.page_header') }}</h4>
    </div>
    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">
        <a href="#boatTypeModalCreate" data-bs-toggle="modal" class="btn btn-outline-theme btn-equal">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('owner.boat_type.add_boat_type') }}
        </a>
    </div>
</div>

<!-- CREATE -->
<div class="modal fade" id="boatTypeModalCreate">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('owner.boat-types.store') }}" method="POST" id="boatTypeCreateForm">
                @csrf
                <input type="hidden" name="tab" value="boat_types">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('owner.boat_type.create.title') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.boat_type.name_ar') }} *</label>
                            <input type="text" name="name_ar" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('owner.boat_type.name_en') }} *</label>
                            <input type="text" name="name_en" class="form-control" required>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="status" value="1" checked class="form-check-input">
                                <label class="form-check-label">{{ __('admin.boat_type.activate') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('owner.actions.close') }}</button>
                    <button class="btn btn-outline-theme">{{ __('owner.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT -->
<div class="modal fade" id="boatTypeEditModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('owner.boat-types.update', 'update') }}" method="POST" id="boatTypeEditForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="boatType_id">
                <input type="hidden" name="tab" value="boat_types">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('owner.boat_type.edit.title') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.boat_type.name_ar') }}</label>
                            <input type="text" name="name_ar" id="boatType_name_ar" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('owner.boat_type.name_en') }}</label>
                            <input type="text" name="name_en" id="boatType_name_en" class="form-control" required>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="status" id="boatType_status" value="1" class="form-check-input">
                                <label class="form-check-label">{{ __('admin.boat_type.activate') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('owner.actions.close') }}</button>
                    <button class="btn btn-outline-theme">{{ __('owner.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DELETE -->
<div class="modal fade" id="boatTypeDeleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('owner.boat-types.destroy', 'delete') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tab" value="boat_types">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.swal.confirm_title') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="text-danger">{{ __('admin.swal.confirm_title') }}</h6>
                    <input type="hidden" name="id" id="boatType_delete_id">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('owner.actions.close') }}</button>
                    <button class="btn btn-danger">{{ __('admin.actions.delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TABLE -->
<table id="boatTypesTable" class="table table-sm table-bordered table-hover text-center small-text">
    <thead>
        <tr>
            <th>{{ __('owner.boat_type.name') }}</th>
            <th>{{ __('owner.boat_type.status') }}</th>
            <th>{{ __('owner.boat_type.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($boatTypes as $boatType)
            <tr>
                <td>{{ $boatType->name }}</td>
                <td>
                    @if ($boatType->status == 1)
                        <span class="badge bg-success">{{ __('owner.boat_type.status_active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('owner.boat_type.status_inactive') }}</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-outline-theme btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#boatTypeEditModal"
                        data-id="{{ $boatType->id }}"
                        data-name_ar="{{ $boatType->name_ar }}"
                        data-name_en="{{ $boatType->name_en }}"
                        data-status="{{ $boatType->status }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#boatTypeDeleteModal"
                        data-id="{{ $boatType->id }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
