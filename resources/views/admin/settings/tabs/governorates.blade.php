<div class="d-flex align-items-center mb-3">
    <div>
        <h4 class="mb-2">{{ __('admin.governorates.page_header') }}</h4>
    </div>
    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">
        <a href="#modalCreate_governorate" data-bs-toggle="modal" class="btn btn-outline-theme btn-equal">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.governorates.add_new') }}
        </a>
    </div>
</div>

{{-- Create --}}
<div class="modal fade" id="modalCreate_governorate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.governorates.add_new_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.governorates.store') }}" id="createForm_governorate" method="post">
                @csrf
                <input type="hidden" name="tab" value="governorates">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.governorates.name_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.governorates.name_en') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control" required>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label">{{ __('admin.governorates.activate') }}</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.governorates.region') }} <span class="text-danger">*</span></label>
                            <select class="form-select" name="region_id" required>
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
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

{{-- Edit --}}
<div class="modal fade" id="editModel_governorate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.governorates.edit_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.governorates.update', ['governorate' => 'update']) }}" id="editForm_governorate" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="tab" value="governorates">
                <input type="hidden" name="id" id="gov_edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.governorates.name_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="gov_name" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.governorates.name_en') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" id="gov_name_en" class="form-control" required>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="status" id="gov_status" class="form-check-input" value="1">
                                <label class="form-check-label">{{ __('admin.governorates.activate') }}</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.governorates.region') }} <span class="text-danger">*</span></label>
                            <select class="form-select" name="region_id" id="gov_region_id" required>
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
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

{{-- Delete --}}
<div class="modal fade" id="deleteModel_governorate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.governorates.delete_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.governorates.destroy', ['governorate' => 'test']) }}" method="post">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tab" value="governorates">
                <input type="hidden" name="id" id="gov_delete_id">
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

<div id="datatable" class="mb-5">
    <table id="datatableDefault_governorates" class="table table-sm table-bordered table-hover text-center small-text">
        <thead>
            <tr>
                <th>{{ __('admin.governorates.name') }}</th>
                <th>{{ __('admin.governorates.region') }}</th>
                <th>{{ __('admin.governorates.status') }}</th>
                <th>{{ __('admin.governorates.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($governorates as $info)
            <tr>
                <td>{{ $info->name }}</td>
                <td>{{ $info->region->name ?? '' }}</td>
                <td>
                    @if ($info->status == 1)
                        <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                    @endif
                </td>
                <td>
                    <button type="button" class="btn btn-outline-theme btn-sm modal-effect" data-id="{{ $info->id }}" data-region_id="{{ $info->region_id }}"
                        data-name="{{ $info->name_ar ?? $info->getRawOriginal('name') }}" data-name_en="{{ $info->name_en }}" data-status="{{ $info->status }}"
                        data-bs-toggle="modal" data-bs-target="#editModel_governorate"><i class="bi bi-pencil"></i></button>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-id="{{ $info->id }}" data-bs-toggle="modal" data-bs-target="#deleteModel_governorate"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $(document).on('click', '[data-bs-target="#editModel_governorate"]', function() {
        var btn = $(this);
        $('#gov_edit_id').val(btn.data('id'));
        $('#gov_name').val(btn.data('name'));
        $('#gov_name_en').val(btn.data('name_en'));
        $('#gov_region_id').val(btn.data('region_id'));
        $('#gov_status').prop('checked', btn.data('status') == 1);
    });
    $(document).on('click', '[data-bs-target="#deleteModel_governorate"]', function() {
        $('#gov_delete_id').val($(this).data('id'));
    });
});
</script>
