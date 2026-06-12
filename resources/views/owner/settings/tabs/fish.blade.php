<div class="d-flex align-items-center mb-3">
    <div>        
        <h4 class="mb-2">{{__('owner.fish.title')}}</h4>
    </div>
    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">        
        <button class="btn btn-outline-theme btn-equal" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{__('admin.regions.add_new')}}
        </button>
    </div>
</div>

<!-- BEGIN #modalCreate -->
<div class="modal fade" id="modalCreate">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('owner.fish.add_new_title')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{route('owner.fish.store')}}" id="createForm" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4">
                            <input type="hidden" name="tab" value="{{ request('tab', 'fish') }}">
                            <div class="form-group">
                                <label for="code" class="form-label">{{__('owner.fish.code')}}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="code" value="{{old('code')}}"
                                    class="form-control" required
                                    placeholder="{{__('owner.fish.code')}}">
                                @error('code') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="scientific_name" class="form-label">{{__('owner.fish.scientific_name')}}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="scientific_name" value="{{old('scientific_name')}}"
                                    class="form-control" required
                                    placeholder="{{__('owner.fish.scientific_name')}}">
                                @error('scientific_name') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="english_name" class="form-label">{{__('owner.fish.english_name')}}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="english_name" value="{{old('english_name')}}"
                                    class="form-control" required
                                    placeholder="{{__('owner.fish.english_name')}}">
                                @error('english_name') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check form-switch " style="margin-top: 35px">
                                <input type="checkbox" name="status" checked class="form-check-input" value="1">
                                <label class="form-check-label" for="status">{{__('owner.fish.activate')}}</label>
                                @error('status') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{__('owner.actions.close')}}</button>
                        <button type="submit" class="btn btn-outline-theme">{{__('owner.actions.save')}}</button>
                    </div>
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
                <h5 class="modal-title">{{__('owner.fish.edit_title')}} </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{route('owner.fish.update','update')}}" id="editForm"
                method="post"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="tab" value="{{ request('tab', 'fish') }}">
                    <div class="row">
                        <div class="col-4">
                            <input type="hidden" name="id" id="id">
                            <div class="form-group">
                                <label for="code" class="form-label">{{__('owner.fish.code')}}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="code" id="code"
                                    value="{{old('code')}}"
                                    class="form-control" required
                                    placeholder="{{__('owner.fish.code')}}">
                                @error('code') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="scientific_name" class="form-label">{{__('owner.fish.scientific_name')}}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="scientific_name" id="scientific_name"
                                    value="{{old('scientific_name')}}"
                                    class="form-control" required
                                    placeholder="{{__('owner.fish.scientific_name')}}">
                                @error('scientific_name') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="english_name" class="form-label">{{__('owner.fish.english_name')}}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="english_name" id="english_name"
                                    value="{{old('english_name')}}"
                                    class="form-control" required
                                    placeholder="{{__('owner.fish.english_name')}}">
                                @error('english_name') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check form-switch " style="margin-top: 35px">
                                <input type="checkbox" name="status" checked class="form-check-input" value="1">
                                <label class="form-check-label" for="status">{{__('owner.fish.activate')}}</label>
                                @error('status') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{__('owner.actions.close')}}</button>
                    <button type="submit" class="btn btn-outline-theme">{{__('owner.actions.save')}}</button>
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
                <h5 class="modal-title">{{ __('owner.generated.delete_contact') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post">

                {{method_field('delete')}}
                {{csrf_field()}}
                <div class="modal-body">
                    <p class="text-center">
                    <h6 style="color:red"> {{ __('owner.generated.confirm_delete_operation') }}</h6>
                    </p>
                    <input type="hidden" name="tab" value="{{ request('tab', 'fish') }}">
                    <input type="hidden" name="id" id="id" value="">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{ __('owner.customers.modal.buttons.close') }}</button>
                    <button type="submit" class="btn btn-danger ">{{ __('owner.customers.modal.buttons.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="datatable" class="mb-5">
    <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text">
        <thead>
        <tr>
            <th>#</th>
            <th>{{__('owner.fish.code')}}</th>
            <th>{{__('owner.fish.scientific_name')}}</th>
            <th>{{__('owner.fish.english_name')}}</th>
            <th>{{__('owner.fish.status')}}</th>
            <th>{{__('owner.fish.actions')}}</th>

        </tr>
        </thead>
        <tbody>


        </tbody>
    </table>
</div>
<div class="card-arrow">
    <div class="card-arrow-top-left"></div>
    <div class="card-arrow-top-right"></div>
    <div class="card-arrow-bottom-left"></div>
    <div class="card-arrow-bottom-right"></div>
</div>