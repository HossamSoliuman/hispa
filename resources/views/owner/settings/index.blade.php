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
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('tab') == 'units' ? 'active' : '' }}"  href="?tab=units"  id="units-tab" aria-controls="units" aria-selected="{{ request('tab') == 'units' ? 'true' : 'false' }}">
                <i class="bi bi-rulers me-1"></i> {{ __('owner.units.title') }}</a>
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

        <div class="tab-pane fade {{ request('tab') == 'units' ? 'show active' : '' }}" id="units" role="tabpanel" aria-labelledby="units-tab">
            @include('owner.settings.tabs.units')
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
            var scientific_name = button.data('scientific_name');
            var english_name = button.data('english_name');
            var status = button.data('status');
            var region_id = button.data('region_id');
            var governorate_id = button.data('governorate_id');

            var modal = $(this);
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #code').val(code);
            modal.find('.modal-body #scientific_name').val(scientific_name);
            modal.find('.modal-body #english_name').val(english_name);
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
                responsive: false, scrollX: true
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

{{-- boat wizard --}}
<script>
    $(function () {
        const $wizard = $('#boatWizard');
        if (!$wizard.length) {
            return;
        }

        const govUrl = $wizard.data('governorates-url');
        const portUrl = $wizard.data('ports-url');
        const saudi = '{{ __('owner.generated.saudi') }}';
        const loadingText = '{{ __('owner.loading') }}';
        const choose = '{{ __('owner.actions.choose') }}';

        let boatId = null;
        let currentStep = 1;

        function notify(message) {
            if (typeof toastr !== 'undefined') {
                toastr.success(message);
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: message, timer: 1500, showConfirmButton: false });
            }
        }

        function goToStep(step) {
            currentStep = step;
            $wizard.find('.wizard-step').hide();
            $wizard.find('.wizard-step[data-step="' + step + '"]').show();
            $wizard.find('[data-step-pill]').removeClass('active');
            $wizard.find('[data-step-pill="' + step + '"]').addClass('active');
            $('html, body').animate({ scrollTop: $wizard.offset().top - 80 }, 200);
        }

        // ---- region / governorate / port cascades ----
        $wizard.on('change', '.wizard-region', function () {
            const $region = $(this);
            const $gov = $($region.data('gov-target'));
            const $port = $region.data('port-target') ? $($region.data('port-target')) : null;
            const regionId = $region.val();

            $gov.empty().append('<option value="">' + loadingText + '</option>');
            if ($port) {
                $port.empty().append('<option value="">' + choose + '</option>');
            }
            if (!regionId) {
                $gov.empty().append('<option value="">' + choose + '</option>');
                return;
            }
            $.get(govUrl.replace('REGION_ID', regionId), function (data) {
                $gov.empty().append('<option value="">' + choose + '</option>');
                $.each(data, function (i, item) {
                    $gov.append('<option value="' + item.id + '">' + item.name + '</option>');
                });
            });
        });

        $wizard.on('change', '.wizard-governorate', function () {
            const $gov = $(this);
            const $port = $($gov.data('port-target'));
            const govId = $gov.val();
            $port.empty().append('<option value="">' + loadingText + '</option>');
            if (!govId) {
                $port.empty().append('<option value="">' + choose + '</option>');
                return;
            }
            $.get(portUrl.replace('GOV_ID', govId), function (data) {
                $port.empty().append('<option value="">' + choose + '</option>');
                $.each(data, function (i, item) {
                    $port.append('<option value="' + item.id + '">' + item.name + '</option>');
                });
            });
        });

        // ---- nationality (saudi / non-saudi) toggles ----
        function applyNationality($select) {
            const prefix = $select.data('prefix');
            const $form = $select.closest('form');
            const isSaudi = $select.val() === saudi;
            const hasValue = !!$select.val();
            $form.find('.' + prefix + '-saudi-fields').toggle(isSaudi);
            $form.find('.' + prefix + '-non-saudi-fields').toggle(hasValue && !isSaudi);
        }
        $wizard.on('change', '.wizard-nationality', function () {
            applyNationality($(this));
        });

        // ---- salary type toggle ----
        function applySalaryType($select) {
            const prefix = $select.data('prefix');
            const $form = $select.closest('form');
            $form.find('.' + prefix + '-salary-value').toggle($select.val() !== 'percentage');
        }
        $wizard.on('change', '.wizard-salary-type', function () {
            applySalaryType($(this));
        });

        function resetConditionalFields($form) {
            $form.find('.wizard-nationality').each(function () { applyNationality($(this)); });
            $form.find('.wizard-salary-type').each(function () { applySalaryType($(this)); });
        }

        // initialise conditional visibility for captain & crew forms
        $wizard.find('#wizardCaptainForm, #wizardCrewForm').each(function () {
            resetConditionalFields($(this));
        });

        // ---- validation error rendering ----
        function clearErrors($form) {
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.wizard-error').remove();
        }
        function showErrors($form, errors) {
            clearErrors($form);
            $.each(errors, function (field, messages) {
                const $input = $form.find('[name="' + field + '"]');
                $input.addClass('is-invalid');
                const $target = $input.length ? $input.last() : $form;
                $('<span class="text-danger d-block wizard-error"></span>').text(messages[0]).insertAfter($target);
            });
        }

        function submitStep($form, onSuccess) {
            clearErrors($form);
            const $btn = $form.find('[type="submit"]');
            $btn.prop('disabled', true);
            $.ajax({
                url: $form.data('url') || $form.attr('action'),
                type: 'POST',
                data: new FormData($form[0]),
                processData: false,
                contentType: false,
                headers: { 'Accept': 'application/json' },
                success: function (res) {
                    onSuccess(res);
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON) {
                        showErrors($form, xhr.responseJSON.errors || {});
                    } else {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) || '{{ __('owner.swal.error') }}';
                        if (typeof Swal !== 'undefined') { Swal.fire('{{ __('owner.swal.error') }}', msg, 'error'); }
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false);
                }
            });
        }

        function setBoatId(id) {
            boatId = id;
            $wizard.find('.wizard-boat-id').val(id);
        }

        // ---- Step 1: Boat ----
        $('#wizardBoatForm').attr('action', $wizard.data('store-boat')).on('submit', function (e) {
            e.preventDefault();
            const $form = $(this);
            submitStep($form, function (res) {
                setBoatId(res.id);
                notify(res.message || '{{ __('owner.boat_wizard.success_boat') }}');
                goToStep(2);
            });
        });

        // ---- Step 2: Captain ----
        $('#wizardCaptainForm').attr('action', $wizard.data('store-captain')).on('submit', function (e) {
            e.preventDefault();
            submitStep($(this), function (res) {
                notify(res.message);
                goToStep(3);
            });
        });

        // ---- Step 3: Crew (add multiple) ----
        $('#wizardCrewForm').attr('action', $wizard.data('store-crew')).on('submit', function (e) {
            e.preventDefault();
            const $form = $(this);
            const name = $form.find('[name="name"]').val();
            const email = $form.find('[name="email"]').val();
            submitStep($form, function (res) {
                notify(res.message);
                $('#crewEmptyItem').remove();
                $('<li class="list-group-item"></li>')
                    .text(name + (email ? ' — ' + email : ''))
                    .appendTo('#crewAddedList');
                $form[0].reset();
                setBoatId(boatId);
                resetConditionalFields($form);
            });
        });

        // ---- Step 4: Maintenance ----
        $('#wizardMaintenanceForm').attr('action', $wizard.data('store-maintenance')).on('submit', function (e) {
            e.preventDefault();
            submitStep($(this), function (res) {
                notify(res.message);
                goToStep(5);
            });
        });

        // ---- Step 5: Inspection ----
        $('#wizardInspectionForm').attr('action', $wizard.data('store-inspection')).on('submit', function (e) {
            e.preventDefault();
            submitStep($(this), function (res) {
                notify(res.message);
                goToStep(6);
            });
        });

        // ---- skip / continue / restart ----
        $wizard.on('click', '[data-action="skip"], [data-action="continue"]', function () {
            goToStep(currentStep + 1);
        });
        $wizard.find('.wizard-step[data-step="3"] [data-action="next"]').on('click', function () {
            goToStep(4);
        });
        $wizard.on('click', '[data-action="restart"]', function () {
            boatId = null;
            $wizard.find('form').each(function () {
                this.reset();
                clearErrors($(this));
            });
            $wizard.find('.wizard-boat-id').val('');
            $('#crewAddedList').html('<li class="list-group-item text-muted" id="crewEmptyItem">{{ __('owner.boat_wizard.no_crew_added') }}</li>');
            $wizard.find('#wizardCaptainForm, #wizardCrewForm').each(function () {
                resetConditionalFields($(this));
            });
            goToStep(1);
        });
    });
</script>
{{-- end boat wizard --}}

{{-- boat type inline quick add --}}
<script>
    $(function () {
        const $wrap = $('#boatTypeQuickAdd');
        if (!$wrap.length) {
            return;
        }

        const $select = $('#boat_type_id');
        const $nameAr = $('#quickBoatTypeNameAr');
        const $nameEn = $('#quickBoatTypeNameEn');
        const $error = $('#quickBoatTypeError');
        const $save = $('#quickBoatTypeSave');

        function resetQuickAdd() {
            $nameAr.val('');
            $nameEn.val('');
            $error.addClass('d-none').text('');
        }

        $('#boatTypeQuickAddToggle').on('click', function (e) {
            e.preventDefault();
            $wrap.toggleClass('d-none');
            if (!$wrap.hasClass('d-none')) {
                $nameAr.trigger('focus');
            }
        });

        $('#quickBoatTypeCancel').on('click', function () {
            resetQuickAdd();
            $wrap.addClass('d-none');
        });

        $save.on('click', function () {
            const nameAr = $.trim($nameAr.val());
            const nameEn = $.trim($nameEn.val());
            $error.addClass('d-none').text('');

            if (!nameAr || !nameEn) {
                $error.removeClass('d-none').text('{{ __('owner.boat_wizard.quick_type_required') }}');
                return;
            }

            $save.prop('disabled', true);
            $.ajax({
                url: $wrap.data('store-url'),
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                data: {
                    _token: '{{ csrf_token() }}',
                    name_ar: nameAr,
                    name_en: nameEn,
                    status: 1
                },
                success: function (res) {
                    $('<option></option>').val(res.id).text(res.name).prop('selected', true).appendTo($select);
                    $select.trigger('change');
                    resetQuickAdd();
                    $wrap.addClass('d-none');
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.message);
                    }
                },
                error: function (xhr) {
                    let msg = '{{ __('owner.swal.error') }}';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors)[0][0];
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    $error.removeClass('d-none').text(msg);
                },
                complete: function () {
                    $save.prop('disabled', false);
                }
            });
        });
    });
</script>
{{-- end boat type inline quick add --}}


{{-- units --}}
<script>
    window.unitsRoutes = {
        unitsData: "{{ route('owner.getUnitsData') }}",
        unitsStore: "{{ route('owner.units.store') }}",
        unitsUpdate: "{{ route('owner.units.update', ':id') }}",
        unitsDestroy: "{{ route('owner.units.destroy', ':id') }}",
    };
</script>
<script>
    $(document).ready(function() {
        let unitsTable;
        let unitIsEditMode = false;
        let unitCurrentEditId = null;
        let appLocale = '{{ app()->getLocale() }}';
        let unitsLanguageOptions = {};
        if (appLocale === 'ar') {
            unitsLanguageOptions = {
                url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
            };
        }
        unitsTable = $('#unitsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: window.unitsRoutes.unitsData,
            language: unitsLanguageOptions,
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name' },
                { data: 'name_en' },
                { data: 'is_default' },
                { data: 'status' },
                { data: 'action', orderable: false, searchable: false },
            ],
            responsive: false, scrollX: true
        });

        $(document).on('click', '.addUnitBtn', function() {
            resetUnitForm();
            $('#unitModalTitle').text('{{ __('owner.units.add_new_title') }}');
            $('#addUnitModal').modal('show');
        });

        $('#unitForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            const url = unitIsEditMode ? window.unitsRoutes.unitsUpdate.replace(':id', unitCurrentEditId) :
                window.unitsRoutes.unitsStore;
            $('#unitFormMethod').val(unitIsEditMode ? 'PUT' : 'POST');

            $.ajax({
                url: url,
                method: 'POST',
                data: form.serialize(),
                success: function() {
                    $('#addUnitModal').modal('hide');
                    unitsTable.ajax.reload();
                    toastr.success(unitIsEditMode ? '{{ __('owner.generated.item_fe2368') }}' :
                        '{{ __('owner.generated.item_26a187') }}');
                    resetUnitForm();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors || {};
                        $.each(errors, function(field, messages) {
                            let input = form.find('[name="' + field + '"]');
                            input.addClass('is-invalid');
                            input.after('<span class="text-danger">' + messages[0] + '</span>');
                        });
                    } else {
                        Swal.fire('{{ __('owner.generated.item_e4c800') }}');
                    }
                }
            });
        });

        $(document).on('click', '.unitEditBtn', function() {
            const data = $(this).data();
            unitIsEditMode = true;
            unitCurrentEditId = data.id;
            $('#unitModalTitle').text('{{ __('owner.units.edit_title') }}');
            $('#unitId').val(data.id);
            $('#unitNameAr').val(data.name_ar);
            $('#unitNameEn').val(data.name_en);
            $('#unitStatus').val(data.status);
            $('#unitIsDefault').prop('checked', data.is_default == 1);

            $('#addUnitModal').modal('show');
        });

        $(document).on('click', '.unitDeleteBtn', function() {
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
                        url: window.unitsRoutes.unitsDestroy.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            unitsTable.ajax.reload();
                            toastr.success('{{ __('owner.units.deleted') }}');
                        },
                        error: function(xhr) {
                            let msg = (xhr.responseJSON && xhr.responseJSON.error) ?
                                xhr.responseJSON.error : '{{ __('owner.swal.error') }}';
                            toastr.error(msg);
                        }
                    });
                }
            });
        });

        function resetUnitForm() {
            $('#unitForm').find('.is-invalid').removeClass('is-invalid');
            $('#unitForm').find('.text-danger').remove();
            $('#unitForm')[0].reset();
            $('#unitId').val('');
            $('#unitFormMethod').val('POST');
            $('#unitIsDefault').prop('checked', false);
            $('#unitModalTitle').text('{{ __('owner.units.add_new_title') }}');
            unitIsEditMode = false;
            unitCurrentEditId = null;
        }
    });
</script>
{{-- end units --}}

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
