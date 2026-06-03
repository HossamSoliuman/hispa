<div class="d-flex align-items-center mb-3">
    <div>
        <h4 class="mb-2">{{ __('owner.categories.title') }}</h4>
    </div>
    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">
        <button class="btn btn-outline-theme btn-equal addSubBtn" data-bs-toggle="modal"
            data-bs-target="#addCategoryModal">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.regions.add_new') }}
        </button>
    </div>
</div>

<div class="card border-0 mb-3">
    {{-- <div class="card-body">
        <div class="table-responsive"> --}}
    <table id="categoriesTable" class="table table-sm table-bordered table-hover text-center small-text"
        style="width: 100% !important;">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('owner.categories.name') }}</th>
                <th>{{ __('owner.categories.status') }}</th>
                <th>{{ __('owner.categories.parent') }}</th>
                <th>{{ __('owner.categories.actions') }}</th>
            </tr>
        </thead>
    </table>
    {{-- </div>
</div> --}}
</div>

<!-- Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-l">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="modalTitle">{{ __('owner.categories.add_new_title') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId">
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="type" value="child"><!-- {{ __('owner.generated.item_e73629') }} -->
                <input type="hidden" name="tab" value="{{ request('tab', 'categories') }}">
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="nameAr" class="form-label">{{ __('owner.categories.name_ar') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nameAr" name="name_ar">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="nameEn" class="form-label">{{ __('owner.categories.name_en') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nameEn" name="name_en">
                        </div>
                    </div>

                    <!-- {{ __('owner.generated.item_7c7254') }} -->
                    <div class="col-md-12 parent-field">
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">{{ __('owner.categories.parent') }}</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">{{ __('owner.actions.choose') }}</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('owner.categories.status') }}</label>
                            <select class="form-select" id="status" name="status">
                                <option value="1">{{ __('owner.status.active') }}</option>
                                <option value="0">{{ __('owner.status.inactive') }}</option>
                            </select>
                        </div>
                    </div>
                </div><!-- modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('owner.actions.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('owner.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
