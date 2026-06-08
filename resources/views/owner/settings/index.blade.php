@extends('owner.layouts.master')

@section('title')
{{ __('owner.generated.item_1f6002') }}
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center justify-content-between">
        <h2 class="fw-bold mb-0 text-dark">{{ __('owner.settings.title') }}</h2>        
    </div>

    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
         <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('tab') == 'company' || !request('tab')  ? 'active' : '' }}"  href="?tab=company"  id="company-tab" aria-controls="company" aria-selected="{{ request('tab') == 'company' || !request('tab') ? 'true' : 'false' }}">
                <i class="bi bi-building me-1"></i> {{ __('owner.generated.company') }}</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('tab') == 'regions' ? 'active' : '' }}"  href="?tab=regions"  id="regions-tab" aria-controls="regions" aria-selected="{{ request('tab') == 'regions' ? 'true' : 'false' }}">
                <i class="bi bi-pin-map me-1"></i> {{ __('owner.generated.regions') }}</a>
        </li>
        <li class="nav-item"  role="presentation">
            <a class="nav-link {{ request('tab') == 'governorates' ? 'active' : '' }}"  href="?tab=governorates"  id="governorates-tab" aria-controls="governorates" aria-selected="{{ request('tab') == 'governorates' ? 'true' : 'false' }}">
                <i class="bi bi-bullseye me-1"></i> {{ __('owner.generated.governorates') }}</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('tab') == 'ports' ? 'active' : '' }}"  href="?tab=ports"  id="ports-tab" aria-controls="ports" aria-selected="{{ request('tab') == 'ports' ? 'true' : 'false' }}">
                <svg xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path
                        d="M19.875,21a1.174,1.174,0,0,1-.9-.466A9.338,9.338,0,0,0,22,13.5V12.438l-2-.7V7.5A3.5,3.5,0,0,0,16.5,4H15V2a2,2,0,0,0-2-2H11A2,2,0,0,0,9,2V4H7.5A3.5,3.5,0,0,0,4,7.5v4.233l-2,.705V13.5a9.34,9.34,0,0,0,3.02,7.029A1.145,1.145,0,0,1,4.125,21,1.173,1.173,0,0,1,3,20H0a4.171,4.171,0,0,0,4.125,4,4.147,4.147,0,0,0,2.63-.969,4.079,4.079,0,0,0,5.261.015,4.076,4.076,0,0,0,5.259-.015A4.084,4.084,0,0,0,24,20H21A1.158,1.158,0,0,1,19.875,21ZM7,7.5A.5.5,0,0,1,7.5,7h9a.5.5,0,0,1,.5.5v3.174L12,8.909,7,10.674ZM9.375,21A1.173,1.173,0,0,1,8.25,20l-.012-.828-.691-.443a6.147,6.147,0,0,1-2.475-4.193L10.5,12.62V20A1.158,1.158,0,0,1,9.375,21Zm5.25,0A1.173,1.173,0,0,1,13.5,20V12.62l5.428,1.916a6.161,6.161,0,0,1-2.472,4.192l-.706.434V20A1.158,1.158,0,0,1,14.625,21Z"></path>
                </svg> {{ __('owner.generated.ports') }}</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('tab') == 'boats' ? 'active' : '' }}"  href="?tab=boats"  id="boats-tab" aria-controls="boats" aria-selected="{{ request('tab') == 'boats' ? 'true' : 'false' }}">
                <i class="fas fa-ship me-1"></i> {{ __('owner.boats.title') }}</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link  {{ request('tab') == 'fish'? 'active' : '' }}" href="?tab=fish" id="fish-tab"   aria-controls="fish" aria-selected="{{ request('tab') == 'fish' ? 'true' : 'false' }}">
                <i class="fas fa-fish me-1"></i> {{ __('owner.fish.page_header') }}</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('tab') == 'categories' ? 'active' : '' }}"  href="?tab=categories"  id="categories-tab" aria-controls="categories" aria-selected="{{ request('tab') == 'categories' ? 'true' : 'false' }}">
                <i class="fas fa-database me-1"></i> {{ __('owner.categories.page_header') }}</a>
        </li>
    </ul>

    <div class="tab-content" id="settingsTabsContent">

        <div class="tab-pane fade {{ request('tab') == 'company' || !request('tab') ? 'show active' : '' }}" id="company" role="tabpanel" aria-labelledby="company-tab">
            @include('owner.settings.tabs.company')
        </div>

        <div class="tab-pane fade {{ request('tab') == 'regions' ? 'show active' : '' }}" id="regions" role="tabpanel" aria-labelledby="regions-tab">
            @include('owner.settings.tabs.regions')
        </div>

        <div class="tab-pane fade {{ request('tab') == 'governorates' ? 'show active' : '' }}" id="governorates" role="tabpanel" aria-labelledby="governorates-tab">
            @include('owner.settings.tabs.governorates')
        </div>

        <div class="tab-pane fade {{ request('tab') == 'ports' ? 'show active' : '' }}" id="ports" role="tabpanel" aria-labelledby="ports-tab">
            @include('owner.settings.tabs.ports')
        </div>

        <div class="tab-pane fade {{ request('tab') == 'boats' ? 'show active' : '' }}" id="boats" role="tabpanel" aria-labelledby="boats-tab">
            @include('owner.settings.tabs.boats')
        </div>

        <div class="tab-pane fade  {{ request('tab') == 'fish'  ? 'show active' : '' }}" id="fish" role="tabpanel" aria-labelledby="fish-tab">
            @include('owner.settings.tabs.fish')
        </div>

        <div class="tab-pane fade {{ request('tab') == 'categories' ? 'show active' : '' }}" id="categories" role="tabpanel" aria-labelledby="categories-tab">
            @include('owner.settings.tabs.categories')
        </div>        
        
    </div>
</div>


@endsection


@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">

        $(function () {
            // Check if the DataTable is already initialized and destroy it
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }
            let appLocale = '{{ app()->getLocale() }}';
            let languageOptions = {};
            if (appLocale === 'ar') {
                languageOptions = { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" };
            }

            // Initialize the DataTable
            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,

                language: languageOptions,

                ajax: {
                    url: "{{ route('owner.getFishData') }}",
                    data: function (d) {
                        // d.from_date = $('#from_date').val();
                        // d.to_date = $('#to_date').val();
                    }

                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'code', name: 'code'},
                    {data: 'scientific_name', name: 'scientific_name'},
                    {data: 'english_name', name: 'english_name'},
                    {data: 'red_sea_name', name: 'red_sea_name'},
                    {data: 'arabian_gulf_name', name: 'arabian_gulf_name'},
                    // {data: 'region', name: 'region'},
                    // {data: 'governorate', name: 'governorate'},
                    {data: 'status', name: 'status'},

                    {data: 'action', name: 'action', orderable: true, searchable: false},
                ],

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
            $('#from_date, #to_date').change(function () {
                table.draw();
            });
        });
    </script>

    <script>
        $("#createForm").validate();
        $("#editForm").validate();

    </script>
    <script>
        function deleteRecord(recordId) {
            Swal.fire({
                title: '{{__('owner.swal.confirm_title')}}',
                text: "{{__('owner.swal.confirm_text')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{__('owner.swal.confirm_yes')}}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('owner/fish') }}/" + recordId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire('{{__('owner.swal.deleted')}}', response.message, 'success');
                            $('#datatableDefault').DataTable().ajax.reload();
                        },
                        error: function (xhr) {
                            let message = '{{ __('owner.generated.item_843b15') }}';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }

                            Swal.fire(
                                '{{__('owner.swal.error')}}',
                                message,
                                'error'
                            );
                        }
                    });
                }
            });
        }



    </script>
    <script>
        // On modal open, fill in fields and load governorates
        $('#modelEdit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);

            var id = button.data('id');
            var code = button.data('code');
            var red_sea_name = button.data('red_sea_name');
            var scientific_name = button.data('scientific_name');
            var arabian_gulf_name = button.data('arabian_gulf_name');
            var english_name = button.data('english_name');
            var local_name_primary = button.data('local_name_primary');
            var local_name_secondary = button.data('local_name_secondary');
            var status = button.data('status');
            var region_id = button.data('region_id');
            var governorate_id = button.data('governorate_id');

            var modal = $(this);
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #code').val(code);
            modal.find('.modal-body #scientific_name').val(scientific_name);
            modal.find('.modal-body #red_sea_name').val(red_sea_name);
            modal.find('.modal-body #arabian_gulf_name').val(arabian_gulf_name);
            modal.find('.modal-body #english_name').val(english_name);
            modal.find('.modal-body #local_name_primary').val(local_name_primary);
            modal.find('.modal-body #local_name_secondary').val(local_name_secondary);
            modal.find('.modal-body #status').prop('checked', status == 1);

            // Set region
            modal.find('.modal-body #region_id_edit').val(region_id).trigger('change');

            // Load governorates for selected region
            if (region_id) {
                $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}", function (data) {
                    let $governorateSelect = modal.find('.modal-body #governorate_id_edit');
                    $governorateSelect.empty().append('<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');
                    $.each(data, function (i, item) {
                        let selected = (item.id == governorate_id) ? 'selected' : '';
                        $governorateSelect.append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                    });
                });
            }
        });

        // On change of region in the edit modal, load governorates dynamically
        $(document).ready(function () {
            $('#region_id_edit').on('change', function () {
                let regionId = $(this).val();
                let $governorateSelect = $('#governorate_id_edit');

                $governorateSelect.empty().append('<option value="">{{ __('owner.dalal.performance.loading') }}</option>');

                if (regionId) {
                    $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}", function (data) {
                        $governorateSelect.empty().append('<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');
                        $.each(data, function (i, item) {
                            $governorateSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                    });
                } else {
                    $governorateSelect.empty().append('<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            let oldRegionId = '{{ old('region_id') }}';
            let oldGovernorateId = '{{ old('governorate_id') }}';


            // تحميل المحافظات عند اختيار المنطقة
            $('#region_id').on('change', function () {
                let regionId = $(this).val();
                $('#governorate_id').empty().append('<option value="">{{ __('owner.dalal.performance.loading') }}</option>');
                $('#port_id').empty().append('<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');

                if (regionId) {
                    $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}", function (data) {
                        $('#governorate_id').empty().append('<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');
                        $.each(data, function (i, item) {
                            $('#governorate_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                    });
                }
            });

            // عند تحميل الصفحة إذا في old value للمنطقة والمحافظة والمدينة
            if (oldRegionId && !$('#governorate_id option:selected').val()) {
                $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}", function (governorates) {
                    $('#governorate_id').empty().append('<option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>');
                    $.each(governorates, function (i, item) {
                        let selected = (item.id == oldGovernorateId) ? 'selected' : '';
                        $('#governorate_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                    });

                });
            }
        });
    </script>














<script>
        window.routes = {
            categoriesData: "{{ route('owner.getCategoriesData') }}",
            categoriesStore: "{{ route('owner.categories.store') }}",
            categoriesUpdate: "{{ route('owner.categories.update', ':id') }}",
            categoriesDestroy: "{{ route('owner.categories.destroy', ':id') }}",
        };
    </script>
    <script>
        $(document).ready(function() {
            let categoriesTable;
            let isEditMode = false;
            let currentEditId = null;
            let appLocale = '{{ app()->getLocale() }}';
            let languageOptions = {};
            if (appLocale === 'ar') {
                languageOptions = {
                    url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                };
            }
            categoriesTable = $('#categoriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: window.routes.categoriesData,
                language: languageOptions,
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'parent_name'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                responsive: true
            });

            $(document).on('click', '.addSubBtn', function() {
                resetCategoryForm();
                $('#modalTitle').text('{{ __('owner.categories.add_new_title') }}');
                $('#addCategoryModal').modal('show');
            });

            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                const url = isEditMode ? window.routes.categoriesUpdate.replace(':id', currentEditId) :
                    window.routes.categoriesStore;
                $('#formMethod').val(isEditMode ? 'PUT' : 'POST');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: form.serialize(),
                    success: function() {
                        $('#addCategoryModal').modal('hide');
                        categoriesTable.ajax.reload();
                        toastr.success(isEditMode ? '{{ __('owner.generated.item_fe2368') }}' :
                            '{{ __('owner.generated.item_26a187') }}');
                        resetCategoryForm();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                let input = form.find('[name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.after('<span class="text-danger">' + messages[0] +
                                    '</span>');
                            });
                        } else {
                            Swal.fire('{{ __('owner.generated.item_e4c800') }}');
                        }
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                const data = $(this).data();
                isEditMode = true;
                currentEditId = data.id;
                $('#modalTitle').text('{{ __('owner.categories.edit_title') }}');
                $('#categoryId').val(data.id);
                $('#nameAr').val(data.name_ar);
                $('#nameEn').val(data.name_en);
                $('#parent_id').val(data.parent_id);
                $('#status').val(data.status);

                $('#addCategoryModal').modal('show');
            });

            $(document).on('click', '.deleteBtn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: '{{ __('owner.swal.confirm_title') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __('owner.swal.confirm_yes') }}',
                    cancelButtonText: '{{ __('owner.swal.cancel') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: window.routes.categoriesDestroy.replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function() {
                                categoriesTable.ajax.reload();
                                toastr.success('{{ __('owner.categories.deleted') }}');
                            },
                            error: function() {
                                toastr.error('{{ __('owner.swal.error') }}');
                            }
                        });
                    }
                });
            });

            function resetCategoryForm() {
                clearValidationErrors($('#categoryForm'));
                $('#categoryForm')[0].reset();
                $('#categoryId').val('');
                $('#formMethod').val('POST');
                $('#modalTitle').text('{{ __('owner.categories.add_new_title') }}');
                isEditMode = false;
                currentEditId = null;
            }

            function clearValidationErrors(form) {
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.text-danger').remove();
            }
        });
    </script>


{{-- boat types --}}
<script>
    $(function () {
        $('#boatTypesTable').DataTable({
            language: '{{ app()->getLocale() }}' === 'ar'
                ? { url: "{{ asset('dashboard/assets/js/ar.json') }}" }
                : {}
        });

        $(document).on('click', '[data-bs-target="#boatTypeEditModal"]', function () {
            let btn = $(this);
            $('#boatTypeEditModal').one('shown.bs.modal', function () {
                $('#boatType_id').val(btn.data('id'));
                $('#boatType_name_ar').val(btn.data('name_ar'));
                $('#boatType_name_en').val(btn.data('name_en'));
                $('#boatType_status').prop('checked', btn.data('status') == 1);
            });
        });

        $(document).on('click', '[data-bs-target="#boatTypeDeleteModal"]', function () {
            $('#boatTypeDeleteModal').one('shown.bs.modal', () => {
                $('#boatType_delete_id').val($(this).data('id'));
            });
        });
    });
</script>
{{-- end boat types --}}

{{-- region --}}
<script>
    $(function () {

        $('#regionTable').DataTable({
            language: '{{ app()->getLocale() }}' === 'ar'
                ? { url: "{{ asset('dashboard/assets/js/ar.json') }}" }
                : {}
        });

        
        $(document).on('click', '[data-bs-target="#regionEditModal"]', function () {
            let btn = $(this);

            $('#regionEditModal').one('shown.bs.modal', function () {
                $('#region_id').val(btn.data('id'));
                $('#region_name').val(btn.data('name')).trigger('change');
                $('#region_name_en').val(btn.data('name_en'));
                $('#region_status').prop('checked', btn.data('status') == 1);
            });
        });
        $(document).on('click', '[data-bs-target="#regionDeleteModal"]', function () {
            $('#regionDeleteModal').one('shown.bs.modal', () => {
                $('#region_delete_id').val($(this).data('id'));
            });
        });

    });
</script>
{{-- region --}}


{{-- governorates --}}
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#datatableDefault_governorates')) {
            $('#datatableDefault_governorates').DataTable().destroy();
        }
        let appLocale = '{{ app()->getLocale() }}';
        let languageOptions = {};
        if (appLocale === 'ar') {
            languageOptions = { url: "{{ asset('dashboard/assets/js/ar.json') }}?v={{ time() }}" };
        }
        $('#datatableDefault_governorates').DataTable({
            language: languageOptions
        });
    });
</script>
<script>
    $("#createForm_governorate").validate();
    $("#editForm_governorate").validate();
</script>
<script>
    // Model Edit
    $('#editModel_governorate').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var name = button.data('name')
        var name_en = button.data('name_en')
        var region_id = button.data('region_id')
        var status = button.data('status')


        // var image = button.data('image')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name').val(name);
        modal.find('.modal-body #name_en').val(name_en);
        modal.find('.modal-body #region_id').val(region_id);
        modal.find('.modal-body #status').prop('checked', status == 1);


    });
    $('#deleteModel').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')

        // var image = button.data('image')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);


    });
</script>
{{-- end governorates --}}

{{-- ports --}}
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#datatableDefault_ports')) {
            $('#datatableDefault_ports').DataTable().destroy();
        }
        let appLocale = '{{ app()->getLocale() }}';
        let languageOptions = {};
        if (appLocale === 'ar') {
            languageOptions = { url: "{{ asset('dashboard/assets/js/ar.json') }}?v={{ time() }}" };
        }

        $('#datatableDefault_ports').DataTable({
            language: languageOptions
        });
    });
</script>

<script>
    $("#createForm_port").validate();
    $("#editForm_port").validate();
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
<script>
    let map;
    let marker;

    function initMap() {
        const defaultLocation = { lat: 31.5, lng: 34.5 };
        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 8,
        });

        map.addListener('click', function(e) {
            placeMarker(e.latLng);
        });
    }

    function placeMarker(location) {
        if (marker) {
            marker.setPosition(location);
        } else {
            marker = new google.maps.Marker({
                position: location,
                map: map
            });
        }
        document.getElementById('lat').value = location.lat();
        document.getElementById('lng').value = location.lng();
    }

    window.initMap = initMap;
</script>
<script>
    $('#categorySelect').on('change', function() {
        var value = $(this).val();
        var enText = value === 'government' ? 'Government' : (value === 'private' ? 'Private' : '');
        var arText = value;
        $('#category_ar').val(value);
        $('#category_en').val(enText);
    });


    $(document).ready(function() {
        var selected = $('#categorySelect').val();
        if(selected){
            var enText = selected === 'government' ? 'Government' : (selected === 'private' ? 'Private' : '');
            $('#category_ar').val(selected);
            $('#category_en').val(enText);
        }
    });
</script>
<script>
    $(document).on('click', '.modal-effect', function() {
        var button = $(this);

        // بيانات القارب الأساسية
        var id = button.data('id');
        var name = button.data('name');
        var name_en = button.data('name_en');
        var governorate_id = button.data('governorate_id');
        var status = button.data('status');
        var lat = button.data('lat');
        var lng = button.data('lng');
        var boat_types = button.data('boat_types') || [];
        var boat_max = button.data('boat_max') || {};
        var category_ar = button.data('category_ar');
        var category_en = button.data('category_en');

        // تحديث الحقول الأساسية
        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_name_en').val(name_en);
        $('#edit_governorate_id').val(governorate_id).trigger('change');
        $('#edit_status').prop('checked', status == 1);
        $('#edit_lat').val(lat);
        $('#edit_lng').val(lng);

        // الفئات
        $('#editCategorySelect').val(category_ar);
        $('#edit_category_ar').val(category_ar);
        $('#edit_category_en').val(category_en);

        $('#editCategorySelect').off('change').on('change', function() {
            var value = $(this).val();
            var enText = value === 'government' ? 'Government' : (value === 'private' ? 'Private' : '');
            $('#edit_category_ar').val(value);
            $('#edit_category_en').val(enText);
        });

        // إعادة تعيين القوارب
        $('.edit-boat-type').prop('checked', false);
        $('.edit-boat-max').val(0).prop('disabled', true);

        // تعليم القوارب حسب البيانات
        $('.edit-boat-type').each(function() {
            var boatId = parseInt($(this).val());
            if (boat_types.includes(boatId)) {
                $(this).prop('checked', true);
                var input = $(this).closest('.row').find('.edit-boat-max');
                input.prop('disabled', false).val(boat_max[boatId] ?? 0);
            }
        });

        // عرض المودال
        $('#editModel').modal('show');
    });

    // التعامل مع تفعيل/إلغاء تفعيل إدخال القوارب بشكل موحد
    document.addEventListener("change", function(e) {
        if (e.target.matches(".edit-boat-type, .boat-type-checkbox")) {
            let inputClass = e.target.classList.contains("edit-boat-type")
                ? ".edit-boat-max"
                : ".boat-type-input";

            let input = e.target.closest(".row").querySelector(inputClass);

            if (e.target.checked) {
                input.disabled = false;
                input.focus();
            } else {
                input.disabled = true;
                input.value = 0; // reset value
            }
        }
    });

    $('#deleteModel').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')

        // var image = button.data('image')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);


    });
</script>

{{-- boats tab --}}
<script>
    $(document).ready(function () {
        let oldRegionId = '{{ old('region_id') }}';
        let oldGovernorateId = '{{ old('governorate_id') }}';
        let oldPortId = '{{ old('port_id') }}';

        $('#addBoat_region_id').on('change', function () {
            let regionId = $(this).val();
            $('#addBoat_governorate_id').empty().append('<option value="">{{ __('owner.loading') }}</option>');
            $('#addBoat_port_id').empty().append('<option value="">{{ __('owner.actions.choose') }}</option>');

            if (regionId) {
                $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID', regionId), function (data) {
                    $('#addBoat_governorate_id').empty().append('<option value="">{{ __('owner.actions.choose') }}</option>');
                    $.each(data, function (i, item) {
                        $('#addBoat_governorate_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                });
            }
        });

        $('#addBoat_governorate_id').on('change', function () {
            let govId = $(this).val();
            $('#addBoat_port_id').empty().append('<option value="">{{ __('owner.loading') }}</option>');

            if (govId) {
                $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', govId), function (data) {
                    $('#addBoat_port_id').empty().append('<option value="">{{ __('owner.actions.choose') }}</option>');
                    $.each(data, function (i, item) {
                        $('#addBoat_port_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                });
            }
        });

        if (oldRegionId && !$('#addBoat_governorate_id option:selected').val()) {
            $.get("{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}".replace('REGION_ID', oldRegionId), function (governorates) {
                $('#addBoat_governorate_id').empty().append('<option value="">{{ __('owner.actions.choose') }}</option>');
                $.each(governorates, function (i, item) {
                    let selected = (item.id == oldGovernorateId) ? 'selected' : '';
                    $('#addBoat_governorate_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                });

                if (oldGovernorateId) {
                    $.get("{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}".replace('GOV_ID', oldGovernorateId), function (ports) {
                        $('#addBoat_port_id').empty().append('<option value="">{{ __('owner.actions.choose') }}</option>');
                        $.each(ports, function (i, item) {
                            let selected = (item.id == oldPortId) ? 'selected' : '';
                            $('#addBoat_port_id').append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                        });
                    });
                }
            });
        }

        $('#addBoatForm').validate();

        // Boats DataTable
        let appLocale = '{{ app()->getLocale() }}';
        let boatLangOptions = appLocale === 'ar' ? { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json" } : {};

        if ($.fn.DataTable.isDataTable('#boatsSettingsTable')) {
            $('#boatsSettingsTable').DataTable().destroy();
        }

        $('#boatsSettingsTable').DataTable({
            processing: true,
            serverSide: true,
            language: boatLangOptions,
            ajax: {
                url: "{{ route('owner.getBoatData') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'category', name: 'category' },
                { data: 'type', name: 'type' },
                { data: 'captain', name: 'captain' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            responsive: true,
        });

        // Crew DataTable (management moved here from the boat profile page)
        if ($.fn.DataTable.isDataTable('#crewSettingsTable')) {
            $('#crewSettingsTable').DataTable().destroy();
        }

        $('#crewSettingsTable').DataTable({
            processing: true,
            serverSide: true,
            language: boatLangOptions,
            ajax: {
                url: "{{ route('owner.getCrewData') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'nationality', name: 'nationality' },
                { data: 'id_number', name: 'id_number' },
                { data: 'job_title', name: 'job_title' },
                { data: 'boat', name: 'boat' },
                { data: 'region', name: 'region' },
                { data: 'governorate', name: 'governorate' },
                { data: 'port', name: 'port' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            responsive: true,
        });
    });

    function deleteCrewRecord(recordId) {
        Swal.fire({
            title: '{{__('owner.swal.confirm_title')}}',
            text: "{{__('owner.swal.confirm_text')}}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{__('owner.swal.confirm_yes')}}',
            cancelButtonText: '{{__('owner.swal.cancel')}}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('owner/crew') }}/" + recordId,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (response) {
                        Swal.fire('{{__('owner.swal.deleted')}}', response.message, 'success');
                        $('#crewSettingsTable').DataTable().ajax.reload();
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message || '{{ __('owner.generated.item_843b15') }}';
                        Swal.fire('{{__('owner.swal.error')}}', message, 'error');
                    }
                });
            }
        });
    }

    function deleteBoatRecord(recordId) {
        Swal.fire({
            title: '{{__('owner.swal.confirm_title')}}',
            text: "{{__('owner.swal.confirm_text')}}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{__('owner.swal.confirm_yes')}}',
            cancelButtonText: '{{__('owner.swal.cancel')}}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('owner/boats') }}/" + recordId,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (response) {
                        Swal.fire('{{__('owner.swal.deleted')}}', response.message, 'success');
                        $('#boatsSettingsTable').DataTable().ajax.reload();
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message || '{{ __('owner.generated.item_843b15') }}';
                        Swal.fire('{{__('owner.swal.error')}}', message, 'error');
                    }
                });
            }
        });
    }
</script>
{{-- end boats tab --}}


<script>
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');

    if (tab) {
        const triggerEl = document.querySelector(
            'a[data-bs-target="#' + tab + '"]'
        );

        if (triggerEl) {
            new bootstrap.Tab(triggerEl).show();
        }
    }
});
</script>

@endsection
