<div class="d-flex align-items-center mb-3">
    <div>
        <h4 class="mb-2">{{ __('admin.governorates.page_header') }}</h4>
    </div>
    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">
        <a href="#modalCreate_governorate" data-bs-toggle="modal" class="btn btn-outline-theme btn-equal">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i>{{ __('admin.governorates.add_new') }}
        </a>
    </div>
</div>
<!-- BEGIN #modalCreate -->
<div class="modal fade" id="modalCreate_governorate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.governorates.add_new_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('owner.governorates.store') }}" id="createForm_governorate" method="post"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tab" value="{{ request('tab', 'governorates') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 ">
                            <div class="form-group ">
                                <label for="name" class="form-label">{{ __('admin.governorates.name_ar') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control  "
                                    required placeholder="{{ __('admin.governorates.name_ar') }}">


                                @error('name')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>

                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name_en" class="form-label">{{ __('admin.governorates.name_en') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control"
                                    required placeholder="{{ __('admin.governorates.name_en') }}">
                                @error('name_en')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-6 ">
                            <div class="form-check form-switch " style="margin-top: 35px">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label"
                                    for="status">{{ __('admin.governorates.activate') }}</label>
                                @error('status')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-6 ">
                            <label for="name" class="form-label">{{ __('admin.governorates.region') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" required id="" name="region_id">
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                            @error('region_id')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror

                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default"
                        data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                    <button type="submit" class="btn btn-outline-theme">{{ __('admin.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END #modalCreate-->
<div class="modal fade" id="editModel_governorate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.governorates.edit_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('owner.governorates.update', 'update') }}" id="editForm_governorate" method="post"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body justify-content-center">
                    <input type="hidden" name="tab" value="{{ request('tab', 'governorates') }}">

                    <div class="row">
                        <input type="hidden" name="id" id="id" value="">
                        <div class="col-6 ">

                            <div class="form-group ">
                                <label for="name" class="form-label">{{ __('admin.governorates.name_ar') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    class="form-control  " required
                                    placeholder="{{ __('admin.governorates.name_ar') }}">


                                @error('name')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>

                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('admin.governorates.name_en') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name_en" id="name_en" class="form-control" required
                                    placeholder="{{ __('admin.governorates.name_en') }}">
                                @error('name_en')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-6 ">
                            <div class="form-check form-switch " style="margin-top: 35px">
                                <input type="checkbox" name="status" id="status" class="form-check-input"
                                    value="1">
                                <label class="form-check-label"
                                    for="status">{{ __('admin.governorates.activate') }}</label>
                                @error('status')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-6 ">
                            <label for="name" class="form-label">{{ __('admin.governorates.region') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" required id="region_id" name="region_id">
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                            @error('region_id')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror

                        </div>

                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default"
                        data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                    <button type="submit" class="btn btn-outline-theme">{{ __('admin.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- delete -->
<div class="modal fade" id="deleteModel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.governorates.delete_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('owner.governorates.destroy', 'test') }}" method="post">

                {{ method_field('delete') }}
                {{ csrf_field() }}
                <input type="hidden" name="tab" value="{{ request('tab', 'governorates') }}">
                <div class="modal-body">
                    <p class="text-center">
                    <h6 style="color:red"> {{ __('admin.swal.confirm_title') }}</h6>
                    </p>

                    <input type="hidden" name="id" id="id" value="">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default"
                        data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                    <button type="submit" class="btn btn-danger ">{{ __('admin.actions.delete') }}</button>
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
                            <button type="button" class=" modal-effect btn btn-outline-theme mb-1 btn-sm"
                                data-id="{{ $info->id }}" data-region_id="{{ $info->region_id }}"
                                data-name="{{ $info->name_ar }}" data-name_en="{{ $info->name_en }}"
                                data-status="{{ $info->status }}" data-bs-effect="effect-scale"
                                data-bs-toggle="modal" href="#editModel_governorate"><i
                                    class="bi bi-pencil btn-success fa-fw me-1"></i> </button>

                            <button type="button" class=" modal-effect btn btn-outline-danger mb-1 btn-sm"
                                data-id="{{ $info->id }}" data-bs-effect="effect-scale"
                                data-bs-toggle="modal" href="#deleteModel">
                                <i class="bi bi-trash btn-danger fa-fw me-1"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-arrow">
        <div class="card-arrow-top-left"></div>
        <div class="card-arrow-top-right"></div>
        <div class="card-arrow-bottom-left"></div>
        <div class="card-arrow-bottom-right"></div>
    </div>