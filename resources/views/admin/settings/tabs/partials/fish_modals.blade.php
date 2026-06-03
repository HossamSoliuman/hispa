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
                        <div class="col-4">
                            <label for="code" class="form-label">{{ __('admin.fish.code.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="code" value="{{ old('code') }}" class="form-control" required placeholder="{{ __('admin.fish.code.0') }}">
                        </div>
                        <div class="col-4">
                            <label for="scientific_name" class="form-label">{{ __('admin.fish.scientific_name.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="scientific_name" value="{{ old('scientific_name') }}" class="form-control" required>
                        </div>
                        <div class="col-4">
                            <label for="english_name" class="form-label">{{ __('admin.fish.english_name.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="english_name" value="{{ old('english_name') }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4">
                            <label for="red_sea_name" class="form-label">{{ __('admin.fish.red_sea_name.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="red_sea_name" value="{{ old('red_sea_name') }}" class="form-control" required>
                        </div>
                        <div class="col-4">
                            <label for="arabian_gulf_name" class="form-label">{{ __('admin.fish.arabian_gulf_name.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="arabian_gulf_name" value="{{ old('arabian_gulf_name') }}" class="form-control" required>
                        </div>
                        <div class="col-4 pt-4">
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
                        <div class="col-4">
                            <label class="form-label">{{ __('admin.fish.code.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="fish_code" class="form-control" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label">{{ __('admin.fish.scientific_name.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="scientific_name" id="fish_scientific_name" class="form-control" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label">{{ __('admin.fish.english_name.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="english_name" id="fish_english_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4">
                            <label class="form-label">{{ __('admin.fish.red_sea_name.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="red_sea_name" id="fish_red_sea_name" class="form-control" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label">{{ __('admin.fish.arabian_gulf_name.0') }} <span class="text-danger">*</span></label>
                            <input type="text" name="arabian_gulf_name" id="fish_arabian_gulf_name" class="form-control" required>
                        </div>
                        <div class="col-4 pt-4">
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
