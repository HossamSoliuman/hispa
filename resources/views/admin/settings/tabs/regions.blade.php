<div class="d-flex align-items-center mb-3">
    <div>
        <h4 class="mb-2">{{ __('admin.regions.page_header') }}</h4>
    </div>
    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">
        <a href="#regionModalCreate" data-bs-toggle="modal" class="btn btn-outline-theme btn-equal">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.regions.add_new') }}
        </a>
    </div>
</div>

{{-- Create --}}
<div class="modal fade" id="regionModalCreate">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.regions.store') }}" id="regionCreateForm" method="POST">
                @csrf
                <input type="hidden" name="tab" value="regions">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.regions.add_new_title') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.regions.name_ar') }} *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.regions.name_en') }} *</label>
                            <input type="text" name="name_en" class="form-control" required>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="status" value="1" checked class="form-check-input">
                                <label class="form-check-label">{{ __('admin.regions.activate') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                    <button class="btn btn-outline-theme" type="submit">{{ __('admin.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit --}}
<div class="modal fade" id="regionEditModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.regions.update', ['region' => 'update']) }}" method="POST" id="regionEditForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="tab" value="regions">
                <input type="hidden" name="id" id="region_id">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.regions.edit_title') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.regions.name_ar') }}</label>
                            <input type="text" name="name" id="region_name" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.regions.name_en') }}</label>
                            <input type="text" name="name_en" id="region_name_en" class="form-control" required>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="status" id="region_status" value="1" class="form-check-input">
                                <label class="form-check-label">{{ __('admin.regions.activate') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                    <button class="btn btn-outline-theme" type="submit">{{ __('admin.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete --}}
<div class="modal fade" id="regionDeleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.regions.destroy', ['region' => 'delete']) }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tab" value="regions">
                <input type="hidden" name="id" id="region_delete_id">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.regions.delete_title') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="text-danger">{{ __('admin.swal.confirm_title') }}</h6>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                    <button class="btn btn-danger" type="submit">{{ __('admin.actions.delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<table id="regionTable" class="table table-sm table-bordered table-hover text-center small-text">
    <thead>
        <tr>
            <th>{{ __('admin.regions.name') }}</th>
            <th>{{ __('admin.regions.status') }}</th>
            <th>{{ __('admin.regions.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($regions as $info)
        <tr>
            <td>{{ $info->name }}</td>
            <td>
                @if($info->status == 1)
                    <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                @else
                    <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                @endif
            </td>
            <td>
                <button class="btn btn-outline-theme btn-sm" data-bs-toggle="modal" data-bs-target="#regionEditModal"
                    data-id="{{ $info->id }}" data-name="{{ $info->name }}" data-name_en="{{ $info->name_en }}" data-status="{{ $info->status }}">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#regionDeleteModal" data-id="{{ $info->id }}">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
$(function() {
    $(document).on('click', '[data-bs-target="#regionEditModal"]', function() {
        var btn = $(this);
        $('#region_id').val(btn.data('id'));
        $('#region_name').val(btn.data('name'));
        $('#region_name_en').val(btn.data('name_en'));
        $('#region_status').prop('checked', btn.data('status') == 1);
    });
    $(document).on('click', '[data-bs-target="#regionDeleteModal"]', function() {
        $('#region_delete_id').val($(this).data('id'));
    });
});
</script>
