@extends('owner.layouts.master')

@section('title')
    {{ __('owner.boats.maintenance') }}
@endsection

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.boats.index') }}">{{ __('owner.boats.title') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.boats.maintenance') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.boats.maintenance') }}</h1>
            <p class="text-muted mb-0">{{ __('owner.boats.maintenance_subtitle') }}</p>
        </div>

        <div class="ms-auto">
            <button class="btn btn-outline-theme" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                <i class="bi bi-tools me-1"></i>{{ __('owner.boats.maintenance_schedule') }}
            </button>
        </div>
    </div>

    <div class="card border-0 mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="filterBoat" class="form-label">{{ __('owner.boats.name') }}</label>
                    <select id="filterBoat" class="form-select">
                        <option value="">{{ __('owner.boats.all_boats') }}</option>
                        @foreach ($boats as $boat)
                            <option value="{{ $boat->id }}">{{ $boat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        @include('owner.boats.maintenance.table')
    </div>

    @include('owner.boats.maintenance.modal', ['boats' => $boats, 'categories' => $categories])
@endsection

@section('script')
    <script>
        window.currentBoatId = null;
        window.routes = {
            maintenanceData: "{{ route('owner.maintenance.data') }}",
            maintenanceStore: "{{ route('owner.maintenance.store') }}",
            maintenanceUpdate: "{{ route('owner.maintenance.update', ':id') }}",
            maintenanceDestroy: "{{ route('owner.maintenance.destroy', ':id') }}",
            maintenanceEdit: "{{ route('owner.maintenance.edit', ':id') }}",
            maintenanceShow: "{{ route('owner.maintenance.show', ':id') }}",
        };
        let appLocale = '{{ app()->getLocale() }}';
        let languageOptions = {};
        if (appLocale === 'ar') {
            languageOptions = { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" };
        }
        let swalOptions = {
            title: '{{ __('owner.swal.confirm_title') }}',
            text: '{{ __('owner.swal.confirm_text') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __('owner.swal.confirm_yes') }}',
            cancelButtonText: '{{ __('owner.swal.cancel') }}',
            deleted_title: '{{ __('owner.swal.deleted') }}',
            error_title: '{{ __('owner.swal.error') }}'
        };
    </script>
    <script src="{{ asset('dashboard/assets/js/owner/maintenance.js') }}?v={{ filemtime(public_path('dashboard/assets/js/owner/maintenance.js')) }}"></script>
    <script>
        $('#filterBoat').on('change', function () {
            window.currentBoatId = $(this).val() || null;
            $('#datatableMaintenance').DataTable().ajax.reload();
        });
    </script>
@endsection
