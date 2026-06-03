@extends('admin.layouts.master')
@section('title')
    {{ __('admin.menu.admins') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <style>
        #datatableDefault th, #datatableDefault td { text-align: center !important; vertical-align: middle; }
        .small-text th, .small-text td { font-size: 12px; text-align: center !important; vertical-align: middle; font-weight: bold; }
    </style>
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.menu.admins') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.admins.create') }}" class="btn btn-outline-theme btn-equal">
                <i class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{ __('admin.menu.add_new_admin') }}
            </a>
        </div>
    </div>

    @php
        $adminStatusOptions = [
            ['value' => '', 'label' => __('admin.filters.all')],
            ['value' => '1', 'label' => __('admin.status.active')],
            ['value' => '0', 'label' => __('admin.status.inactive')],
        ];
    @endphp
    <x-admin.components.datatable-filters
        formId="adminsFilters"
        formAction="{{ route('admin.admins.index') }}"
        formMethod="get"
        :showSearchButton="true"
        :showResetButton="true"
        :filters="[
            ['type' => 'text', 'id' => 'search', 'name' => 'search', 'label' => __('admin.filters.search'), 'placeholder' => __('admin.owner.name'), 'value' => request('search')],
            ['type' => 'select-static', 'id' => 'status', 'name' => 'status', 'label' => __('admin.boat_types.status'), 'options' => $adminStatusOptions, 'selected' => request('status')],
        ]"
        :showArrow="false"
    />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center small-text" id="datatableDefault">
                    <thead>
                        <tr>
                            <th>{{ __('admin.table.id') }}</th>
                            <th>{{ __('admin.owner.name') }}</th>
                            <th>{{ __('admin.owner.email') ?? 'Email' }}</th>
                            <th>{{ __('admin.owner.phone') ?? 'Phone' }}</th>
                            <th>{{ __('admin.boat_types.status') }}</th>
                            <th>{{ __('admin.boat_types.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $admin)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->phone ?? '—' }}</td>
                            <td>
                                @if($admin->status ?? 1)
                                    <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($admin->id != auth('admin')->id())
                                <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.swal.confirm_text') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('admin.subscriptions.no_subscriptions') ?? __('admin.no_data.0') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
