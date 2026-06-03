@extends('admin.layouts.master')

@section('title')
    {{ __('admin.settings.page_header') }}
@endsection

@section('css')
    <style>
        .small-text th, .small-text td { font-size: 12px; text-align: center !important; vertical-align: middle; }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4 align-items-center justify-content-between">
            <h2 class="fw-bold mb-0 text-dark">{{ __('admin.settings.page_header') }}</h2>
        </div>

        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ (request('tab') == 'general' || !request('tab')) ? 'active' : '' }}" href="?tab=general" id="general-tab" aria-controls="general" aria-selected="{{ (request('tab') == 'general' || !request('tab')) ? 'true' : 'false' }}">
                    <i class="bi bi-gear me-1"></i> {{ __('admin.settings.tabs.general') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ request('tab') == 'company' ? 'active' : '' }}" href="?tab=company" id="company-tab" aria-controls="company" aria-selected="{{ request('tab') == 'company' ? 'true' : 'false' }}">
                    <i class="bi bi-building me-1"></i> {{ __('admin.settings.tabs.company') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ request('tab') == 'fish' ? 'active' : '' }}" href="?tab=fish" id="fish-tab" aria-controls="fish" aria-selected="{{ request('tab') == 'fish' ? 'true' : 'false' }}">
                    <i class="fas fa-fish me-1"></i> {{ __('admin.settings.tabs.fish') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ request('tab') == 'categories' ? 'active' : '' }}" href="?tab=categories" id="categories-tab" aria-controls="categories" aria-selected="{{ request('tab') == 'categories' ? 'true' : 'false' }}">
                    <i class="fas fa-database me-1"></i> {{ __('admin.settings.tabs.categories') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ request('tab') == 'regions' ? 'active' : '' }}" href="?tab=regions" id="regions-tab" aria-controls="regions" aria-selected="{{ request('tab') == 'regions' ? 'true' : 'false' }}">
                    <i class="bi bi-pin-map me-1"></i> {{ __('admin.settings.tabs.regions') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ request('tab') == 'governorates' ? 'active' : '' }}" href="?tab=governorates" id="governorates-tab" aria-controls="governorates" aria-selected="{{ request('tab') == 'governorates' ? 'true' : 'false' }}">
                    <i class="bi bi-bullseye me-1"></i> {{ __('admin.settings.tabs.governorates') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ request('tab') == 'ports' ? 'active' : '' }}" href="?tab=ports" id="ports-tab" aria-controls="ports" aria-selected="{{ request('tab') == 'ports' ? 'true' : 'false' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M19.875,21a1.174,1.174,0,0,1-.9-.466A9.338,9.338,0,0,0,22,13.5V12.438l-2-.7V7.5A3.5,3.5,0,0,0,16.5,4H15V2a2,2,0,0,0-2-2H11A2,2,0,0,0,9,2V4H7.5A3.5,3.5,0,0,0,4,7.5v4.233l-2,.705V13.5a9.34,9.34,0,0,0,3.02,7.029A1.145,1.145,0,0,1,4.125,21,1.173,1.173,0,0,1,3,20H0a4.171,4.171,0,0,0,4.125,4,4.147,4.147,0,0,0,2.63-.969,4.079,4.079,0,0,0,5.261.015,4.076,4.076,0,0,0,5.259-.015A4.084,4.084,0,0,0,24,20H21A1.158,1.158,0,0,1,19.875,21Z"/></svg>
                    {{ __('admin.settings.tabs.ports') }}
                </a>
            </li>
        </ul>

        <div class="tab-content" id="settingsTabsContent">
            <div class="tab-pane fade {{ (request('tab') == 'general' || !request('tab')) ? 'show active' : '' }}" id="general" role="tabpanel" aria-labelledby="general-tab">
                @include('admin.settings.tabs.general')
            </div>
            <div class="tab-pane fade {{ request('tab') == 'company' ? 'show active' : '' }}" id="company" role="tabpanel" aria-labelledby="company-tab">
                @include('admin.settings.tabs.company')
            </div>
            <div class="tab-pane fade {{ request('tab') == 'fish' ? 'show active' : '' }}" id="fish" role="tabpanel" aria-labelledby="fish-tab">
                @include('admin.settings.tabs.fish')
            </div>
            <div class="tab-pane fade {{ request('tab') == 'categories' ? 'show active' : '' }}" id="categories" role="tabpanel" aria-labelledby="categories-tab">
                @include('admin.settings.tabs.categories')
            </div>
            <div class="tab-pane fade {{ request('tab') == 'regions' ? 'show active' : '' }}" id="regions" role="tabpanel" aria-labelledby="regions-tab">
                @include('admin.settings.tabs.regions')
            </div>
            <div class="tab-pane fade {{ request('tab') == 'governorates' ? 'show active' : '' }}" id="governorates" role="tabpanel" aria-labelledby="governorates-tab">
                @include('admin.settings.tabs.governorates')
            </div>
            <div class="tab-pane fade {{ request('tab') == 'ports' ? 'show active' : '' }}" id="ports" role="tabpanel" aria-labelledby="ports-tab">
                @include('admin.settings.tabs.ports')
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab) {
                const triggerEl = document.querySelector('a[href="?tab=' + tab + '"]');
                if (triggerEl) {
                    const tabEl = new bootstrap.Tab(triggerEl);
                    tabEl.show();
                }
            }
        });

        // Fish tab: DataTable
        $(function () {
            if ($.fn.DataTable && $('#datatableDefaultFish').length && !$.fn.DataTable.isDataTable('#datatableDefaultFish')) {
                var appLocale = '{{ app()->getLocale() }}';
                var languageOptions = appLocale === 'ar' ? { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" } : {};
                $('#datatableDefaultFish').DataTable({
                    processing: true,
                    serverSide: true,
                    language: languageOptions,
                    ajax: { url: "{{ route('admin.getFishData') }}" },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                        { data: 'code', name: 'code' },
                        { data: 'scientific_name', name: 'scientific_name' },
                        { data: 'english_name', name: 'english_name' },
                        { data: 'red_sea_name', name: 'red_sea_name' },
                        { data: 'arabian_gulf_name', name: 'arabian_gulf_name' },
                        { data: 'status', name: 'status' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ],
                });
            }
        });

        // Fish edit modal fill
        $(document).on('show.bs.modal', '#modelEdit', function (event) {
            var button = $(event.relatedTarget);
            if (!button.length || !button.hasClass('editBtn')) return;
            $('#fish_edit_id').val(button.data('id'));
            $('#fish_code').val(button.data('code'));
            $('#fish_scientific_name').val(button.data('scientific_name'));
            $('#fish_english_name').val(button.data('english_name'));
            $('#fish_red_sea_name').val(button.data('red_sea_name'));
            $('#fish_arabian_gulf_name').val(button.data('arabian_gulf_name'));
            $('#fish_status').prop('checked', button.data('status') == 1);
        });

        // Fish delete (used by DataTable action column)
        function deleteRecord(recordId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '{{ __('admin.swal.confirm_title') }}',
                    text: '{{ __('admin.swal.confirm_text') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __('admin.swal.confirm_yes') }}'
                }).then(function (result) {
                    if (result.isConfirmed) doDeleteFish(recordId);
                });
            } else if (confirm('{{ __('admin.swal.confirm_text') }}')) {
                doDeleteFish(recordId);
            }
        }
        function doDeleteFish(recordId) {
            $.ajax({
                url: "{{ url('') }}/{{ app()->getLocale() }}/admin/fish/" + recordId,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if ($.fn.DataTable.isDataTable('#datatableDefaultFish')) {
                        $('#datatableDefaultFish').DataTable().ajax.reload();
                    }
                    if (typeof toastr !== 'undefined') toastr.success(response.message || '{{ __('admin.swal.deleted') }}');
                    else if (typeof Swal !== 'undefined') Swal.fire('{{ __('admin.swal.deleted') }}', response.message || '', 'success');
                },
                error: function (xhr) {
                    var message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __('admin.swal.error') }}';
                    if (typeof Swal !== 'undefined') Swal.fire('{{ __('admin.swal.error') }}', message, 'error');
                    else alert(message);
                }
            });
        }
    </script>
@endsection
