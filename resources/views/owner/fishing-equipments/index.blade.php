@extends('owner.layouts.master')

@section('title', '{{ __('owner.generated.manage_fishing_gear') }}')

@section('content')
<div class="d-flex align-items-center mb-3">
    <div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{ __('owner.generated.manage_fishing_gear') }}</a></li>
            <li class="breadcrumb-item active"> {{ __('owner.assets.fishing_equipment') }}</li>
        </ul>
        <h1 class="page-header mb-0"> {{ __('owner.assets.fishing_equipment') }}</h1>
    </div>

    <div class="ms-auto">
        <button class="btn btn-outline-theme" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fa fa-plus-circle fa-fw me-1"></i>{{ __('owner.generated.add') }}</button>
    </div>
</div>

<div class="card border-0 mb-3">
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-sm table-bordered table-hover text-center small-text">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.generated.name_ar') }}</th>
                        <th>{{ __('owner.generated.name_en') }}</th>
                        <th>{{ __('owner.expenses.print.quantity') }}</th>
                        <th>{{ __('owner.assets.status') }}</th>
                        <th>{{ __('owner.dalal.table.actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-l">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="modalTitle">{{ __('owner.generated.add') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="modalForm" method="POST">
                @csrf
                <input type="hidden" id="id">
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('owner.generated.name_ar') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nameEn" class="form-label">{{ __('owner.generated.name_en') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nameEn" name="name_en">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('owner.assets.status') }}</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="1">{{ __('owner.fish.activate') }}</option>
                                    <option value="0">{{ __('owner.generated.inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('owner.customers.modal.buttons.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

@endsection

@section('script')
<script>
    window.routes = {
        fishingEquipmentsData: "{{ route('owner.fishing-equipments.data') }}",
        fishingEquipmentsStore: "{{ route('owner.fishing-equipments.store') }}",
        fishingEquipmentsUpdate: "{{ route('owner.fishing-equipments.update', ':id') }}",
        fishingEquipmentsDestroy: "{{ route('owner.fishing-equipments.destroy', ':id') }}",
    };
</script>
<script src="{{ asset('dashboard/assets/js/owner/fishing-equipments.js') }}"></script>
@endsection