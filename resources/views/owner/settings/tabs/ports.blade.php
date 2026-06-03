<div class="d-flex align-items-center mb-3">
    <div>
        <h4 class="mb-2">{{ __('admin.ports.page_header') }}</h4>
    </div>

    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">

        <a href="#modalCreate_port" data-bs-toggle="modal" class="btn btn-outline-theme btn-equal"><i
                class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.ports.add_new') }}</a>

    </div>
</div>


<!-- BEGIN #modalCreate -->
<div class="modal fade" id="modalCreate_port">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.ports.add_new_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('owner.ports.store') }}" id="createForm_port" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="tab" value="{{ request('tab', 'ports') }}">
                    <div class="row g-3">

                        <!-- {{ __('owner.generated.name_ar') }} -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.name_ar') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                                placeholder="{{ __('admin.ports.name_ar') }}" required>
                            @error('name')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- {{ __('owner.generated.name_en') }} -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.name_en') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control"
                                placeholder="{{ __('admin.ports.name_en') }}" required>
                            @error('name_en')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- {{ __('owner.generated.item_d51135') }} -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.governorate') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" name="governorate_id" required>
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                @foreach ($governorates as $governorate)
                                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                            @error('governorate_id')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror
                        </div>

                        @php
                            $currentLang = app()->getLocale(); // 'ar__('owner.generated.or')en'
                        @endphp

                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.category') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="categorySelect" required>
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                <option value="government" {{ old('category_ar') == 'government' ? 'selected' : '' }}>
                                    {{ $currentLang == 'ar' ? __('owner.generated.item_48b4aa') : 'Government' }}
                                </option>
                                <option value="private" {{ old('category_ar') == 'private' ? 'selected' : '' }}>
                                    {{ $currentLang == 'ar' ? __('owner.generated.item_f46186') : 'Private' }}
                                </option>
                            </select>

                            <!-- {{ __('owner.generated.item_e83f4e') }} -->
                            <input type="hidden" name="category_ar" id="category_ar" value="{{ old('category_ar') }}">
                            <input type="hidden" name="category_en" id="category_en" value="{{ old('category_en') }}">

                            @error('category_ar')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- {{ __('owner.generated.item_6210b9') }} -->
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label">{{ __('admin.ports.activate') }}</label>
                            </div>
                        </div>

                        <!-- {{ __('owner.generated.item_cc5d0e') }} -->
                        <div class="col-12">
                            <label class="form-label">{{ __('admin.ports.boat_types') }}</label>
                            <div id="boatTypesContainer">
                                @foreach ($boatTypes as $boatType)
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input boat-type-checkbox"
                                                    name="boat_types[]" value="{{ $boatType->id }}"
                                                    id="boatType{{ $boatType->id }}">
                                                <label class="form-check-label"
                                                    for="boatType{{ $boatType->id }}">{{ $boatType->name }}</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <input type="number" name="max[{{ $boatType->id }}]"
                                                class="form-control boat-type-input"
                                                placeholder="{{ __('admin.ports.max') }}" min="0" value="0"
                                                disabled>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('boat_types')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Google Maps Coordinates -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.Latitude') }}</label>
                            <input type="text" name="lat" id="lat" class="form-control"
                                placeholder="{{ __('admin.ports.Latitude') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.Longitude') }}</label>
                            <input type="text" name="lng" id="lng" class="form-control"
                                placeholder="{{ __('admin.ports.Longitude') }}">
                        </div>

                        <!-- Google Maps -->
                        {{-- <div class="col-12">
                            <div id="map" style="height: 300px; border: 1px solid #ddd;"></div>
                        </div> --}}

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
</div><!-- END #modalCreate -->

<!-- Modal {{ __('owner.generated.item_759fdc') }} -->
<div class="modal fade" id="editModel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.ports.edit_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm_port" method="post" action="{{ route('owner.ports.update', 'text') }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="tab" value="{{ request('tab', 'ports') }}">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.name_ar') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.name_en') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name_en" id="edit_name_en" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.governorate') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" name="governorate_id" id="edit_governorate_id"
                                required>
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                @foreach ($governorates as $governorate)
                                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.ports.category') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="editCategorySelect" required>
                                <option value="">{{ __('admin.actions.choose') }}</option>
                                <option value="government">{{ __('admin.ports.government') }}</option>
                                <option value="private">{{ __('admin.ports.private') }}</option>
                            </select>

                            <!-- {{ __('owner.generated.item_e83f4e') }} -->
                            <input type="hidden" name="category_ar" id="edit_category_ar">
                            <input type="hidden" name="category_en" id="edit_category_en">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input type="checkbox" name="status" id="edit_status" class="form-check-input"
                                    value="1">
                                <label class="form-check-label">{{ __('admin.ports.activate') }}</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('admin.ports.boat_types') }}</label>
                            <div id="editBoatTypesContainer">
                                @foreach ($boatTypes as $boatType)
                                    @php
                                        $isChecked = in_array(
                                            $boatType->id,
                                            old('boat_types', $selectedBoatTypes ?? []),
                                        );
                                        $value = old("max.$boatType->id", $maxValues[$boatType->id] ?? 0);
                                    @endphp
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input edit-boat-type"
                                                    name="boat_types[]" value="{{ $boatType->id }}"
                                                    id="editBoatType{{ $boatType->id }}"
                                                    {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="editBoatType{{ $boatType->id }}">
                                                    {{ $boatType->name }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <input type="number" name="max[{{ $boatType->id }}]"
                                                class="form-control edit-boat-max" min="0"
                                                value="{{ $value }}" {{ $isChecked ? '' : 'disabled' }}>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="lat" id="edit_lat" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="lng" id="edit_lng" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default"
                        data-bs-dismiss="modal">{{ __('admin.actions.close') }}</button>
                    <button type="submit" class="btn btn-outline-theme">{{ __('admin.actions.edit') }}</button>
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
                <h5 class="modal-title">{{ __('admin.ports.delete_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('owner.ports.destroy', 'test') }}" method="post">
                <input type="hidden" name="tab" value="{{ request('tab', 'ports') }}">
                {{ method_field('delete') }}
                {{ csrf_field() }}
                <div class="modal-body">
                    <p class="text-center">
                    <h6 style="color:red"> {{ __('admin.swal.confirm_title') }}
                        {{ __('owner.generated.item_494367') }}</h6>
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
    <table id="datatableDefault_ports" class="table table-sm table-bordered table-hover text-center small-text">
        <thead>
            <tr>
                <th>{{ __('admin.ports.name') }}</th>
                <th>{{ __('admin.ports.category') }}</th>
                <th>{{ __('admin.ports.governorate') }}</th>
                <th>{{ __('admin.ports.status') }}</th>
                <th>{{ __('admin.ports.actions') }}</th>

            </tr>
        </thead>
        <tbody>

            @foreach ($ports as $info)
                <tr>

                    <td>{{ $info->name }}</td>
                    <td>{{ $info->category_ar == 'government' ? __('admin.ports.government') : __('admin.ports.private') }}
                    </td>
                    <td>{{ $info->governorate->name ?? '' }}</td>
                    <td>
                        @if ($info->status == 1)
                            <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                        @endif
                    </td>


                    <td>

                        <button type="button" class=" modal-effect btn btn-outline-theme mb-1 btn-sm"
                            data-id="{{ $info->id }}" data-name="{{ $info->name }}"
                            data-name_en="{{ $info->name_en }}" data-category_ar="{{ $info->category_ar }}"
                            data-category_en="{{ $info->category_en }}"
                            data-governorate_id="{{ $info->governorate_id }}" data-status="{{ $info->status }}"
                            data-lat="{{ $info->lat }}" data-lng="{{ $info->lng }}"
                            data-bs-effect="effect-scale" data-bs-toggle="modal" href="#editModel"><i
                                class="bi bi-pencil btn-success fa-fw me-1"></i></button>

                        <button type="button" class=" btn btn-outline-danger mb-1 btn-sm"
                            data-id="{{ $info->id }}" data-bs-toggle="modal" data-bs-target="#deleteModel">
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

<!-- END #datatable -->
<div class="card-arrow">
    <div class="card-arrow-top-left"></div>
    <div class="card-arrow-top-right"></div>
    <div class="card-arrow-bottom-left"></div>
    <div class="card-arrow-bottom-right"></div>
</div>
