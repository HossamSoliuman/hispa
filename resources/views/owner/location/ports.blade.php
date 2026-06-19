@extends('owner.layouts.master')
@section('title')
{{__('admin.ports.title')}}
@endsection
@section('css')

<link href="{{asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}"
    rel="stylesheet">
<link href="{{asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css')}}"
    rel="stylesheet">
<link href="{{asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}"
    rel="stylesheet">
<link href="{{asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css')}}" rel="stylesheet">
<style>
    #datatableDefault th,
    #datatableDefault td {
        text-align: center !important;
        vertical-align: middle;
    }

    /* {{ __('owner.generated.item_ed06b0') }} */
    .small-text th,
    .small-text td {
        font-size: 12px;
        /* {{ __('owner.generated.or') }} 13px {{ __('owner.generated.item_4cc9e8') }} */
        text-align: center !important;
        vertical-align: middle;
        font-weight: bold;

    }


    label.error {
        color: red;
        font-weight: bold;
        margin-top: 5px;
        display: block;
    }
</style>
@endsection
@section('content')

<div class="d-flex align-items-center mb-3">
    <div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{__('admin.ports.title')}}</a></li>
            <li class="breadcrumb-item active">{{__('admin.ports.title')}}</li>
        </ul>
        <h1 class="page-header mb-0">{{__('admin.ports.page_header')}}</h1>
    </div>

    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">
        <a href="{{ route('owner.ports.print') }}" class="btn btn-outline-theme btn-equal"><i
                class="bi bi-file-earmark-pdf me-1"></i> {{ __('owner.list_reports.download') }}</a>
        <a href="#modalCreate" data-bs-toggle="modal" class="btn btn-outline-theme btn-equal"><i
                class="fa fa-plus-circle btn-success fa-fw me-1"></i> {{__('admin.ports.add_new')}}</a>

    </div>
</div>
<!-- BEGIN #modalCreate -->
<div class="modal fade" id="modalCreate">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('admin.ports.add_new_title')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('owner.ports.store') }}" id="createForm" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="row g-3">

                        <!-- {{ __('owner.generated.name_ar') }} -->
                        <div class="col-md-6">
                            <label class="form-label">{{__('admin.ports.name_ar')}}<span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="{{__('admin.ports.name_ar')}}" required>
                            @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>

                        <!-- {{ __('owner.generated.name_en') }} -->
                        <div class="col-md-6">
                            <label class="form-label">{{__('admin.ports.name_en')}}<span class="text-danger">*</span></label>
                            <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control" placeholder="{{__('admin.ports.name_en')}}" required>
                            @error('name_en') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>

                        <!-- {{ __('owner.generated.item_d51135') }} -->
                        <div class="col-md-6">
                            <label class="form-label">{{__('admin.ports.governorate')}}<span class="text-danger">*</span></label>
                            <select class="form-select select2" name="governorate_id" required>
                                <option value="">{{__('admin.actions.choose')}}</option>
                                @foreach($governorates as $governorate)
                                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                            @error('governorate_id') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>

                        @php
                            $currentLang = app()->getLocale(); // 'ar__('owner.generated.or')en'
                        @endphp

                        <div class="col-md-6">
                            <label class="form-label">{{__('admin.ports.category')}}<span class="text-danger">*</span></label>
                            <select class="form-select" id="categorySelect" required>
                                <option value="">{{__('admin.actions.choose')}}</option>
                                <option value="government" {{ (old('category_ar') == 'government') ? 'selected' : '' }}>
                                    {{ $currentLang == 'ar' ? __('owner.generated.item_48b4aa') : 'Government' }}
                                </option>
                                <option value="private" {{ (old('category_ar') == 'private') ? 'selected' : '' }}>
                                    {{ $currentLang == 'ar' ? __('owner.generated.item_f46186') : 'Private' }}
                                </option>
                            </select>

                            <!-- {{ __('owner.generated.item_e83f4e') }} -->
                            <input type="hidden" name="category_ar" id="category_ar" value="{{ old('category_ar') }}">
                            <input type="hidden" name="category_en" id="category_en" value="{{ old('category_en') }}">

                            @error('category_ar') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>

                        <!-- {{ __('owner.generated.item_6210b9') }} -->
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label">{{__('admin.ports.activate')}}</label>
                            </div>
                        </div>

                        <!-- {{ __('owner.generated.item_cc5d0e') }} -->
                        <div class="col-12">
                            <label class="form-label">{{__('admin.ports.boat_types')}}</label>
                            <div id="boatTypesContainer">
                                @foreach($boatTypes as $boatType)
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input boat-type-checkbox"
                                                       name="boat_types[]"
                                                       value="{{ $boatType->id }}"
                                                       id="boatType{{ $boatType->id }}">
                                                <label class="form-check-label" for="boatType{{ $boatType->id }}">{{ $boatType->name }}</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <input type="number"
                                                   name="max[{{ $boatType->id }}]"
                                                   class="form-control boat-type-input"
                                                   placeholder="{{__('admin.ports.max')}}"
                                                   min="0"
                                                   value="0"
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
                            <label class="form-label">{{__('admin.ports.Latitude')}}</label>
                            <input type="text" name="lat" id="lat" class="form-control" placeholder="{{__('admin.ports.Latitude')}}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{__('admin.ports.Longitude')}}</label>
                            <input type="text" name="lng" id="lng" class="form-control" placeholder="{{__('admin.ports.Longitude')}}">
                        </div>

                        <!-- Google Maps -->
                        {{-- <div class="col-12">
                            <div id="map" style="height: 300px; border: 1px solid #ddd;"></div>
                        </div> --}}

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{__('admin.actions.close')}}</button>
                    <button type="submit" class="btn btn-outline-theme">{{__('admin.actions.save')}}</button>
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
                <h5 class="modal-title">{{__('admin.ports.edit_title')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="post" action="{{route('owner.ports.update','text')}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{__('admin.ports.name_ar')}}<span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{__('admin.ports.name_en')}}<span class="text-danger">*</span></label>
                            <input type="text" name="name_en" id="edit_name_en" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{__('admin.ports.governorate')}}<span class="text-danger">*</span></label>
                            <select class="form-select select2" name="governorate_id" id="edit_governorate_id" required>
                                <option value="">{{__('admin.actions.choose')}}</option>
                                @foreach($governorates as $governorate)
                                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{__('admin.ports.category')}}<span class="text-danger">*</span></label>
                            <select class="form-select" id="editCategorySelect" required>
                                <option value="">{{__('admin.actions.choose')}}</option>
                                <option value="government">{{__('admin.ports.government')}}</option>
                                <option value="private">{{__('admin.ports.private')}}</option>
                            </select>

                            <!-- {{ __('owner.generated.item_e83f4e') }} -->
                            <input type="hidden" name="category_ar" id="edit_category_ar">
                            <input type="hidden" name="category_en" id="edit_category_en">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input type="checkbox" name="status" id="edit_status" class="form-check-input" value="1">
                                <label class="form-check-label">{{__('admin.ports.activate')}}</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{__('admin.ports.boat_types')}}</label>
                            <div id="editBoatTypesContainer">
                                @foreach($boatTypes as $boatType)
                                    @php
                                        $isChecked = in_array($boatType->id, old('boat_types', $selectedBoatTypes ?? []));
                                        $value = old("max.$boatType->id", $maxValues[$boatType->id] ?? 0);
                                    @endphp
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                       class="form-check-input edit-boat-type"
                                                       name="boat_types[]"
                                                       value="{{ $boatType->id }}"
                                                       id="editBoatType{{ $boatType->id }}"
                                                    {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label" for="editBoatType{{ $boatType->id }}">
                                                    {{ $boatType->name }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <input type="number"
                                                   name="max[{{ $boatType->id }}]"
                                                   class="form-control edit-boat-max"
                                                   min="0"
                                                   value="{{ $value }}"
                                                {{ $isChecked ? '' : 'disabled' }}>
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
{{-- 
                        <div class="col-12">
                            <div id="editMap" style="height: 300px; border: 1px solid #ddd;"></div>
                        </div> --}}
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{__('admin.actions.close')}}</button>
                    <button type="submit" class="btn btn-outline-theme">{{__('admin.actions.edit')}}</button>
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
                <h5 class="modal-title">{{__('admin.ports.delete_title')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('owner.ports.destroy','test') }}" method="post">

                {{method_field('delete')}}
                {{csrf_field()}}
                <div class="modal-body">
                    <p class="text-center">
                    <h6 style="color:red"> {{__('admin.swal.confirm_title')}} {{ __('owner.generated.item_494367') }}</h6>
                    </p>

                    <input type="hidden" name="id" id="id" value="">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{__('admin.actions.close')}}</button>
                    <button type="submit" class="btn btn-danger ">{{__('admin.actions.delete')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="tab-content py-4">
    <div class="tab-pane fade show active" id="allTab">
        <!-- BEGIN #datatable -->
        <!-- BEGIN #datatable -->
        <div id="datatable" class="mb-5">
            {{-- <div class="card">--}}
            {{-- <div class="card-body">--}}
            <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text">
                <thead>
                    <tr>
                        <th>{{__('admin.ports.name')}}</th>
                        <th>{{__('admin.ports.category')}}</th>
                        <th>{{__('admin.ports.governorate')}}</th>
                        <th>{{__('admin.ports.capacity')}}</th>
                        <th>{{__('admin.ports.status')}}</th>
                        <th>{{__('admin.ports.actions')}}</th>

                    </tr>
                </thead>
                <tbody>

                    @foreach ($data as $info)

                    <tr>

                        <td>{{ $info->name }}</td>
                        <td>{{ $info->category_ar == 'government' ? __('admin.ports.government') : __('admin.ports.private') }}</td>
                        <td>{{ $info->governorate->name ??"" }}</td>
                        <td>{{ $info->boatTypes()->sum('max') ??"" }}</td>
                        <td>
                            @if($info->status ==1 )
                            <span class="badge bg-success">{{__('admin.status.active')}}</span>
                            @else
                            <span class="badge bg-danger">{{__('admin.status.inactive')}}</span>

                            @endif
                        </td>


                        <td>
                            
                            <button type="button" class=" modal-effect btn btn-outline-theme mb-1 btn-sm"
                                data-id="{{ $info->id }}"
                                    data-name="{{ $info->name }}"
                                    data-name_en="{{ $info->name_en }}"
                                    data-category_ar="{{ $info->category_ar }}"
                                    data-category_en="{{ $info->category_en }}"
                                    data-governorate_id="{{ $info->governorate_id }}"
                                    data-status="{{ $info->status }}"
                                    data-lat="{{ $info->lat }}"
                                    data-lng="{{ $info->lng }}"
                                    data-boat_types='@json($info->boatTypes->pluck("id")->toArray())'
                                    data-boat_max='@json($info->boatTypes->pluck("pivot.max","id"))'

                                data-bs-effect="effect-scale"
                                data-bs-toggle="modal" href="#editModel"><i class="bi bi-pencil btn-success fa-fw me-1"></i></button>
         
                            <button type="button" class=" btn btn-outline-danger mb-1 btn-sm"
                                data-id="{{ $info->id }}"
                                data-bs-toggle="modal" data-bs-target="#deleteModel">
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

    </div>
</div>
<!-- END #datatable -->


{{-- </div>--}}
{{-- </div>--}}
<!-- END #datatable -->

<div class="card-arrow">
    <div class="card-arrow-top-left"></div>
    <div class="card-arrow-top-right"></div>
    <div class="card-arrow-bottom-left"></div>
    <div class="card-arrow-bottom-right"></div>
</div>


@endsection
@section('script')

<script src="{{asset('dashboard/assets/plugins/@highlightjs/cdn-assets/highlight.min.js')}}"></script>
<script src="{{asset('dashboard/assets/js/demo/highlightjs.demo.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.colVis.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
<script
    src="{{asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
<script
    src="{{asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script
    src="{{asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
<script src="{{asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.js')}}"></script>
<script src="{{asset('dashboard/assets/js/demo/table-plugins.demo.js')}}"></script>
<script src="{{asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js')}}"></script>
<script src="{{asset('dashboard/assets/js/jquery.validate.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#datatableDefault')) {
            $('#datatableDefault').DataTable().destroy();
        }
        let appLocale = '{{ app()->getLocale() }}';
        let languageOptions = {};
        if (appLocale === 'ar') {
            languageOptions = { url: "{{ asset('dashboard/assets/js/ar.json') }}?v={{ time() }}" };
        }

        $('#datatableDefault').DataTable({
            language: languageOptions
        });
    });
</script>

<script>
    $("#createForm").validate();
    $("#editForm").validate();
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


@endsection
