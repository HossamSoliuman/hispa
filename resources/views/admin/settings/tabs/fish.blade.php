<div class="d-flex align-items-center mb-3">
    <div>
        <h4 class="mb-2">{{ __('admin.fish.page_header.1') ?? __('admin.fish.title.1') }}</h4>
    </div>
    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">
        <button class="btn btn-outline-theme btn-equal" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.regions.add_new') }}
        </button>
    </div>
</div>

@include('admin.settings.tabs.partials.fish_modals')

<div id="datatable" class="mb-5">
    <table id="datatableDefaultFish" class="table table-sm table-bordered table-hover text-center small-text">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('admin.fish.name_ar.1') ?? __('admin.fish.name_ar.0') }}</th>
                <th>{{ __('admin.fish.name_en.1') ?? __('admin.fish.name_en.0') }}</th>
                <th>{{ __('admin.fish.status.1') ?? __('admin.fish.status.0') }}</th>
                <th>{{ __('admin.fish.actions.1') ?? __('admin.fish.actions.0') }}</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

