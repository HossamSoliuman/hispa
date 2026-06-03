<!-- BEGIN #modalCreate -->
<div class="modal fade" id="modalCreate">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.customers.modal.create_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.customers.store') }}" id="createForm" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="name" class="form-label">{{ __('admin.customers.modal.labels.name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required placeholder="{{ __('admin.customers.modal.labels.name') }}">
                                @error('name')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="phone" class="form-label">{{ __('admin.customers.modal.labels.phone') }}<span class="text-danger">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" required placeholder="{{ __('admin.customers.modal.labels.phone') }}">
                                @error('phone')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="email" class="form-label">{{ __('admin.customers.modal.labels.email') }}<span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required placeholder="{{ __('admin.customers.modal.labels.email') }}">
                                @error('email')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="type" class="form-label">{{ __('admin.customers.modal.labels.type') }}<span class="text-danger">*</span></label>
                                <input type="text" name="type" value="{{ old('type') }}" class="form-control" placeholder="{{ __('admin.customers.modal.labels.type') }}">
                                @error('type')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="notes" class="form-label">{{ __('admin.customers.modal.labels.notes') }}</label>
                                <textarea class="form-control" name="notes" placeholder="{{ __('admin.customers.modal.labels.notes') }}">{{ old('notes') }}</textarea>
                                @error('notes')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check form-switch" style="margin-top: 35px">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label" for="status">{{ __('admin.customers.modal.labels.status') }}</label>
                                @error('status')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('admin.customers.modal.buttons.close') }}</button>
                    <button type="submit" class="btn btn-outline-theme">{{ __('admin.customers.modal.buttons.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END #modalCreate-->

<div class="modal fade" id="modelEdit">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.customers.modal.edit_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.customers.update', 'update') }}" id="editForm" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <input type="hidden" id="id" name="id">
                                <label for="name" class="form-label">{{ __('admin.customers.modal.labels.name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required placeholder="{{ __('admin.customers.modal.labels.name') }}">
                                @error('name')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="phone" class="form-label">{{ __('admin.customers.modal.labels.phone') }}<span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control" required placeholder="{{ __('admin.customers.modal.labels.phone') }}">
                                @error('phone')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="email" class="form-label">{{ __('admin.customers.modal.labels.email') }}<span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" required placeholder="{{ __('admin.customers.modal.labels.email') }}">
                                @error('email')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="type" class="form-label">{{ __('admin.customers.modal.labels.type') }}<span class="text-danger">*</span></label>
                                <input type="text" id="type" name="type" value="{{ old('type') }}" class="form-control" placeholder="{{ __('admin.customers.modal.labels.type') }}">
                                @error('type')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="notes" class="form-label">{{ __('admin.customers.modal.labels.notes') }}</label>
                                <textarea class="form-control" id="notes" name="notes" placeholder="{{ __('admin.customers.modal.labels.notes') }}">{{ old('notes') }}</textarea>
                                @error('notes')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check form-switch" style="margin-top: 35px">
                                <input type="checkbox" name="status" id="status" class="form-check-input" value="1">
                                <label class="form-check-label" for="status">{{ __('admin.customers.modal.labels.status') }}</label>
                                @error('status')<span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('admin.customers.modal.buttons.close') }}</button>
                    <button type="submit" class="btn btn-outline-theme">{{ __('admin.customers.modal.buttons.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
