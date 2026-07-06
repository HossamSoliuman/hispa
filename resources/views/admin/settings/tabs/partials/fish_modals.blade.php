{{-- Create Fish Modal --}}
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.fish.add_new_title.1') ?? __('admin.fish.add_new_title.0') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.fish.store') }}" id="createFormFish" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tab" value="fish">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label for="name_ar" class="form-label">{{ __('admin.fish.name_ar.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" value="{{ old('name_ar') }}" class="form-control" required placeholder="{{ __('admin.fish.name_ar.0') }}">
                        </div>
                        <div class="col-6">
                            <label for="name_en" class="form-label">{{ __('admin.fish.name_en.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control" required placeholder="{{ __('admin.fish.name_en.0') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label">{{ __('admin.fish.activate.0') }}</label>
                            </div>
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

{{-- Edit Fish Modal --}}
<div class="modal fade" id="modelEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.fish.edit_title.1') ?? __('admin.fish.edit_title.0') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.fish.update', ['fish' => 'update']) }}" id="editFormFish" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="tab" value="fish">
                <input type="hidden" name="id" id="fish_edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.fish.name_ar.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" id="fish_name_ar" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('admin.fish.name_en.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" id="fish_name_en" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="status" id="fish_status" class="form-check-input" value="1">
                                <label class="form-check-label">{{ __('admin.fish.activate.0') }}</label>
                            </div>
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
