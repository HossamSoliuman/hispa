<div class="d-flex align-items-center mb-3">
    <div>
        <h4 class="mb-2">{{ __('admin.ports.page_header') }}</h4>
    </div>
    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">
        <a href="#modalCreate_port" data-bs-toggle="modal" class="btn btn-outline-theme btn-equal">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.ports.add_new') }}
        </a>
    </div>
</div>

{{-- Create Port Modal --}}
<div class="modal fade" id="modalCreate_port">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.ports.add_new_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.ports.store') }}" id="createForm_port" method="post">
                @csrf
                <input type="hidden" name="tab" value="ports">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.name_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.name_en') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.governorate') }} <span class="text-danger">*</span></label>
                            <select class="form-select" name="governorate_id" required>
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                @foreach($governorates as $governorate)
                                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.category') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="categorySelect" name="category_ar" required>
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                <option value="government">{{ __('admin.ports.government') }}</option>
                                <option value="private">{{ __('admin.ports.private') }}</option>
                            </select>
                            <input type="hidden" name="category_en" id="category_en" value="">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label">{{ __('admin.ports.activate') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.Latitude') }}</label>
                            <input type="text" name="lat" class="form-control" placeholder="{{ __('admin.ports.Latitude') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.Longitude') }}</label>
                            <input type="text" name="lng" class="form-control" placeholder="{{ __('admin.ports.Longitude') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('admin.ports.boat_types') }}</label>
                            @foreach($boatTypes as $boatType)
                                <div class="row mb-2 align-items-center">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input boat-type-checkbox" name="boat_types[]" value="{{ $boatType->id }}" id="boatType{{ $boatType->id }}">
                                            <label class="form-check-label" for="boatType{{ $boatType->id }}">{{ $boatType->name }}</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="max[{{ $boatType->id }}]" class="form-control boat-type-input" placeholder="{{ __('admin.ports.max') }}" min="0" value="0" disabled>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                    <button type="submit" class="btn btn-outline-theme">{{ __('admin.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Port Modal --}}
<div class="modal fade" id="editModel_port" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.ports.edit_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm_port" method="post" action="{{ route('admin.ports.update', ['port' => 'text']) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="tab" value="ports">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.name_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.name_en') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" id="edit_name_en" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.governorate') }} <span class="text-danger">*</span></label>
                            <select class="form-select" name="governorate_id" id="edit_governorate_id" required>
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                @foreach($governorates as $governorate)
                                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.category') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="editCategorySelect" required>
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                <option value="government">{{ __('admin.ports.government') }}</option>
                                <option value="private">{{ __('admin.ports.private') }}</option>
                            </select>
                            <input type="hidden" name="category_ar" id="edit_category_ar">
                            <input type="hidden" name="category_en" id="edit_category_en">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input type="checkbox" name="status" id="edit_status" class="form-check-input" value="1">
                                <label class="form-check-label">{{ __('admin.ports.activate') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.Latitude') }}</label>
                            <input type="text" name="lat" id="edit_lat" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.Longitude') }}</label>
                            <input type="text" name="lng" id="edit_lng" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('admin.ports.boat_types') }}</label>
                            @foreach($boatTypes as $boatType)
                                <div class="row mb-2 align-items-center">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input edit-boat-type" name="boat_types[]" value="{{ $boatType->id }}" id="editBoatType{{ $boatType->id }}">
                                            <label class="form-check-label" for="editBoatType{{ $boatType->id }}">{{ $boatType->name }}</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="max[{{ $boatType->id }}]" class="form-control edit-boat-max" min="0" value="0" disabled>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                    <button type="submit" class="btn btn-outline-theme">{{ __('admin.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Port Modal --}}
<div class="modal fade" id="deleteModel_port">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.ports.delete_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.ports.destroy', ['port' => 'test']) }}" method="post">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tab" value="ports">
                <input type="hidden" name="id" id="port_delete_id">
                <div class="modal-body">
                    <h6 class="text-center text-danger">{{ __('admin.swal.confirm_title') }}</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('admin.actions.delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<table id="datatableDefault_ports" class="table table-sm table-bordered table-hover text-center small-text">
    <thead>
        <tr>
            <th>{{ __('admin.ports.name') }}</th>
            <th>{{ __('admin.ports.category') }}</th>
            <th>{{ __('admin.ports.governorate') }}</th>
            <th>{{ __('admin.ports.capacity') }}</th>
            <th>{{ __('admin.ports.status') }}</th>
            <th>{{ __('admin.ports.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ports as $info)
        <tr>
            <td>{{ $info->name }}</td>
            <td>{{ $info->category_ar == 'government' ? __('admin.ports.government') : __('admin.ports.private') }}</td>
            <td>{{ $info->governorate->name ?? '' }}</td>
            <td>{{ $info->boatTypes->sum('pivot.max') ?? '' }}</td>
            <td>
                @if($info->status == 1)
                    <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                @else
                    <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                @endif
            </td>
            <td>
                <button type="button" class="btn btn-outline-theme btn-sm modal-effect" data-id="{{ $info->id }}" data-name="{{ $info->getRawOriginal('name') }}" data-name_en="{{ $info->name_en }}"
                    data-governorate_id="{{ $info->governorate_id }}" data-status="{{ $info->status }}" data-lat="{{ $info->lat }}" data-lng="{{ $info->getRawOriginal('long') ?? $info->lat ?? '' }}"
                    data-category_ar="{{ $info->category_ar }}" data-category_en="{{ $info->category_en }}"
                    data-boat_types='@json($info->boatTypes->pluck("id")->toArray())' data-boat_max='@json($info->boatTypes->pluck("pivot.max","id"))'
                    data-bs-toggle="modal" data-bs-target="#editModel_port"><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-outline-danger btn-sm" data-id="{{ $info->id }}" data-bs-toggle="modal" data-bs-target="#deleteModel_port"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#categorySelect').on('change', function() {
        var v = $(this).val();
        $('#category_en').val(v === 'government' ? 'Government' : (v === 'private' ? 'Private' : ''));
    });
    $(document).on('click', '[data-bs-target="#editModel_port"]', function() {
        var btn = $(this);
        $('#edit_id').val(btn.data('id'));
        $('#edit_name').val(btn.data('name'));
        $('#edit_name_en').val(btn.data('name_en'));
        $('#edit_governorate_id').val(btn.data('governorate_id'));
        $('#edit_status').prop('checked', btn.data('status') == 1);
        $('#edit_lat').val(btn.data('lat'));
        $('#edit_lng').val(btn.data('lng'));
        $('#edit_category_ar').val(btn.data('category_ar'));
        $('#edit_category_en').val(btn.data('category_en'));
        $('#editCategorySelect').val(btn.data('category_ar'));
        $('.edit-boat-type').prop('checked', false);
        $('.edit-boat-max').val(0).prop('disabled', true);
        var boat_types = btn.data('boat_types') || [];
        var boat_max = btn.data('boat_max') || {};
        $('.edit-boat-type').each(function() {
            var boatId = parseInt($(this).val(), 10);
            if (boat_types.indexOf(boatId) !== -1) {
                $(this).prop('checked', true);
                $(this).closest('.row').find('.edit-boat-max').prop('disabled', false).val(boat_max[boatId] || 0);
            }
        });
    });
    $(document).on('change', '.edit-boat-type, .boat-type-checkbox', function() {
        var isEdit = $(this).hasClass('edit-boat-type');
        var input = $(this).closest('.row').find(isEdit ? '.edit-boat-max' : '.boat-type-input');
        input.prop('disabled', !$(this).is(':checked')).val($(this).is(':checked') ? input.val() : 0);
    });
    $(document).on('click', '[data-bs-target="#deleteModel_port"]', function() {
        $('#port_delete_id').val($(this).data('id'));
    });
});
</script>
