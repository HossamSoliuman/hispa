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
    @php
        $activeTab = in_array(request('tab'), ['company', 'payment', 'fish', 'categories', 'regions', 'governorates', 'ports'], true)
            ? request('tab')
            : 'company';
    @endphp
    <div class="container-fluid py-4">
        <div class="row mb-4 align-items-center justify-content-between">
            <h2 class="fw-bold mb-0 text-dark">{{ __('admin.settings.page_header') }}</h2>
        </div>

        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab == 'company' ? 'active' : '' }}" href="?tab=company" id="company-tab" aria-controls="company" aria-selected="{{ $activeTab == 'company' ? 'true' : 'false' }}">
                    <i class="bi bi-building me-1"></i> {{ __('admin.settings.tabs.company') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab == 'payment' ? 'active' : '' }}" href="?tab=payment" id="payment-tab" aria-controls="payment" aria-selected="{{ $activeTab == 'payment' ? 'true' : 'false' }}">
                    <i class="bi bi-bank me-1"></i> {{ __('admin.settings.tabs.payment') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab == 'fish' ? 'active' : '' }}" href="?tab=fish" id="fish-tab" aria-controls="fish" aria-selected="{{ $activeTab == 'fish' ? 'true' : 'false' }}">
                    <i class="fas fa-fish me-1"></i> {{ __('admin.settings.tabs.fish') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab == 'categories' ? 'active' : '' }}" href="?tab=categories" id="categories-tab" aria-controls="categories" aria-selected="{{ $activeTab == 'categories' ? 'true' : 'false' }}">
                    <i class="fas fa-database me-1"></i> {{ __('admin.settings.tabs.categories') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab == 'regions' ? 'active' : '' }}" href="?tab=regions" id="regions-tab" aria-controls="regions" aria-selected="{{ $activeTab == 'regions' ? 'true' : 'false' }}">
                    <i class="bi bi-pin-map me-1"></i> {{ __('admin.settings.tabs.regions') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab == 'governorates' ? 'active' : '' }}" href="?tab=governorates" id="governorates-tab" aria-controls="governorates" aria-selected="{{ $activeTab == 'governorates' ? 'true' : 'false' }}">
                    <i class="bi bi-bullseye me-1"></i> {{ __('admin.settings.tabs.governorates') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab == 'ports' ? 'active' : '' }}" href="?tab=ports" id="ports-tab" aria-controls="ports" aria-selected="{{ $activeTab == 'ports' ? 'true' : 'false' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M19.875,21a1.174,1.174,0,0,1-.9-.466A9.338,9.338,0,0,0,22,13.5V12.438l-2-.7V7.5A3.5,3.5,0,0,0,16.5,4H15V2a2,2,0,0,0-2-2H11A2,2,0,0,0,9,2V4H7.5A3.5,3.5,0,0,0,4,7.5v4.233l-2,.705V13.5a9.34,9.34,0,0,0,3.02,7.029A1.145,1.145,0,0,1,4.125,21,1.173,1.173,0,0,1,3,20H0a4.171,4.171,0,0,0,4.125,4,4.147,4.147,0,0,0,2.63-.969,4.079,4.079,0,0,0,5.261.015,4.076,4.076,0,0,0,5.259-.015A4.084,4.084,0,0,0,24,20H21A1.158,1.158,0,0,1,19.875,21Z"/></svg>
                    {{ __('admin.settings.tabs.ports') }}
                </a>
            </li>
        </ul>

        <div class="tab-content" id="settingsTabsContent">
            <div class="tab-pane fade {{ $activeTab == 'company' ? 'show active' : '' }}" id="company" role="tabpanel" aria-labelledby="company-tab">
                @include('admin.settings.tabs.company')
            </div>
            <div class="tab-pane fade {{ $activeTab == 'payment' ? 'show active' : '' }}" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                @include('admin.settings.tabs.payment')
            </div>
            <div class="tab-pane fade {{ $activeTab == 'fish' ? 'show active' : '' }}" id="fish" role="tabpanel" aria-labelledby="fish-tab">
                @include('admin.settings.tabs.fish')
            </div>
            <div class="tab-pane fade {{ $activeTab == 'categories' ? 'show active' : '' }}" id="categories" role="tabpanel" aria-labelledby="categories-tab">
                @include('admin.settings.tabs.categories')
            </div>
            <div class="tab-pane fade {{ $activeTab == 'regions' ? 'show active' : '' }}" id="regions" role="tabpanel" aria-labelledby="regions-tab">
                @include('admin.settings.tabs.regions')
            </div>
            <div class="tab-pane fade {{ $activeTab == 'governorates' ? 'show active' : '' }}" id="governorates" role="tabpanel" aria-labelledby="governorates-tab">
                @include('admin.settings.tabs.governorates')
            </div>
            <div class="tab-pane fade {{ $activeTab == 'ports' ? 'show active' : '' }}" id="ports" role="tabpanel" aria-labelledby="ports-tab">
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
                        { data: 'name_ar', name: 'name_ar' },
                        { data: 'name_en', name: 'name_en' },
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
            $('#fish_name_ar').val(button.data('name_ar'));
            $('#fish_name_en').val(button.data('name_en'));
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
