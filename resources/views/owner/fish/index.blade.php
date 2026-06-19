@extends('owner.layouts.master')
@section('title')
    {{__('owner.fish.title')}}
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
            font-size: 12px; /* {{ __('owner.generated.or') }} 13px {{ __('owner.generated.item_4cc9e8') }} */
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
        /* make Add/Print buttons equal height */
        .btn-equal {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }
    </style>
@endsection
@section('content')

    <div class="row">
        <div class="row mb-4 align-items-center justify-content-between">
            <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
                <h1 class="h3 fw-bold text-dark mb-1">{{__('owner.fish.title')}}</h1>
                <p class="text-muted mb-0"></p>
            </div>

            <div class="col-md-6 col-sm-12 text-md-end text-sm-start d-flex justify-content-md-end gap-2">
                <a href="{{ route('owner.fish.print') }}" class="btn btn-outline-theme btn-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i>{{ __('owner.list_reports.download') }}
                </a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalCreate" class="btn btn-black btn-sm">
                    <i class="bi bi-plus"></i>{{__('owner.fish.add_new')}}
                </button>
            </div>

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

                            <div class="col-4 ">

                                <div class="form-group ">
                                    <label for="code" class="form-label">{{__('owner.fish.code')}}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="code" value="{{old('code')}}"
                                           class="form-control  " required
                                           placeholder="{{__('owner.fish.code')}}">


                                    @error('code') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                            <div class="col-4">

                                <div class="form-group ">
                                    <label for="scientific_name" class="form-label">{{__('owner.fish.scientific_name')}}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="scientific_name" value="{{old('scientific_name')}}"
                                           class="form-control  " required
                                           placeholder="{{__('owner.fish.scientific_name')}}">


                                    @error('scientific_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group ">
                                    <label for="english_name" class="form-label">{{__('owner.fish.english_name')}}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="english_name" value="{{old('english_name')}}"
                                           class="form-control  " required
                                           placeholder="{{__('owner.fish.english_name')}}">


                                    @error('english_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                            </div>

                            <div class="row">
                            <div class="col-4">
                                <div class="form-group ">
                                    <label for="red_sea_name" class="form-label">{{__('owner.fish.red_sea_name')}}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="red_sea_name" value="{{old('red_sea_name')}}"
                                           class="form-control  " required
                                           placeholder="{{__('owner.fish.red_sea_name')}}">


                                    @error('red_sea_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>


                            <div class="col-4">
                                <div class="form-group ">
                                    <label for="arabian_gulf_name" class="form-label">{{__('owner.fish.arabian_gulf_name')}}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="arabian_gulf_name" required
                                           value="{{old('arabian_gulf_name')}}"
                                           class="form-control  "
                                           placeholder="{{__('owner.fish.arabian_gulf_name')}}">


                                    @error('arabian_gulf_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                                <!-- <div class="col-4">
                                    <div class="form-group ">
                                        <label for="region_id" class="form-label">{{ __('owner.generated.region_name') }}</label>

                                        <select name="region_id" class="form-control  "  id="region_id">
                                            <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                            @foreach($regions as $region)
                                                <option
                                                    value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                                {{$region->name}}
                                            @endforeach
                                        </select>


                                        @error('region_id') <span class="text-danger error">{{ $message }}</span>@enderror

                                    </div>

                                </div> -->
                            </div>
                            <div class="row">
                                <!-- <div class="col-4">
                                    <div class="form-group">
                                        <label for="governorate_id" class="form-label">{{ __('owner.generated.governorate_name') }}</label>

                                        <select name="governorate_id" class="form-control"  id="governorate_id">
                                            <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>

                                        </select>


                                        @error('governorate_id') <span class="text-danger error">{{ $message }}</span>@enderror

                                    </div>

                                </div> -->
                            <div class="col-4 ">
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

                        <div class="row">

                            <div class="col-4 ">
                                <div class="form-group ">
                                    <label for="code" class="form-label">{{__('owner.fish.code')}}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code"
                                           value="{{old('code')}}"
                                           class="form-control" required
                                           placeholder="{{__('owner.fish.code')}}">


                                    @error('code') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                            <div class="col-4 ">
                                <input type="hidden" name="id" id="id">
                                <div class="form-group ">
                                    <label for="scientific_name" class="form-label">{{__('owner.fish.scientific_name')}}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="scientific_name" id="scientific_name"
                                           value="{{old('scientific_name')}}"
                                           class="form-control  " required
                                           placeholder="{{__('owner.fish.scientific_name')}}">


                                    @error('scientific_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                            <div class="col-4 ">
                                <div class="form-group ">
                                    <label for="english_name" class="form-label">{{__('owner.fish.english_name')}}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="english_name" id="english_name"
                                           value="{{old('english_name')}}"
                                           class="form-control  " required
                                           placeholder="{{__('owner.fish.english_name')}}">


                                    @error('english_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group ">
                                    <label for="red_sea_name" class="form-label">{{__('owner.fish.red_sea_name')}}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="red_sea_name" id="red_sea_name" value="{{old('red_sea_name')}}"
                                           class="form-control  " required
                                           placeholder="{{__('owner.fish.red_sea_name')}}">


                                    @error('red_sea_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>


                            <div class="col-4">
                                <div class="form-group ">
                                    <label for="arabian_gulf_name" class="form-label">{{__('owner.fish.arabian_gulf_name')}}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="arabian_gulf_name" id="arabian_gulf_name" required
                                           value="{{old('arabian_gulf_name')}}"
                                           class="form-control  "
                                           placeholder="{{__('owner.fish.arabian_gulf_name')}}">


                                    @error('arabian_gulf_name') <span
                                        class="text-danger error">{{ $message }}</span>@enderror

                                </div>
                            </div>
                            <!-- <div class="col-4">
                                <div class="form-group ">
                                    <label for="region_id" class="form-label">{{ __('owner.generated.region_name') }}</label>

                                    <select name="region_id" class="form-control"  id="region_id_edit">
                                        <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                        @foreach($regions as $region)
                                            <option
                                                value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                            {{$region->name}}
                                        @endforeach
                                    </select>


                                    @error('region_id') <span class="text-danger error">{{ $message }}</span>@enderror

                                </div>

                            </div> -->
                        </div>
                        <div class="row">
                            <!-- <div class="col-4">
                                <div class="form-group">
                                    <label for="governorate_id" class="form-label">{{ __('owner.generated.governorate_name') }}</label>

                                    <select name="governorate_id" class="form-control"  id="governorate_id_edit">
                                        <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>

                                    </select>


                                    @error('governorate_id') <span class="text-danger error">{{ $message }}</span>@enderror

                                </div>

                            </div> -->
                            <div class="col-4 ">
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


    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <!-- BEGIN #datatable -->
            <!-- BEGIN #datatable -->
            <div id="datatable" class="mb-5">
                {{--                    <div class="card">--}}
                {{--                        <div class="card-body">--}}
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{__('owner.fish.code')}}</th>
                        <th>{{__('owner.fish.scientific_name')}}</th>
                        <th>{{__('owner.fish.english_name')}}</th>
                        <th>{{__('owner.fish.red_sea_name')}}</th>
                        <th>{{__('owner.fish.arabian_gulf_name')}}</th>
                        <!-- <th>{{ __('owner.crew.edit.region') }}</th>
                        <th>{{ __('owner.crew.edit.governorate') }}</th> -->
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

        </div>
    </div>
    <div class="card-arrow">
        <div class="card-arrow-top-left"></div>
        <div class="card-arrow-top-right"></div>
        <div class="card-arrow-bottom-left"></div>
        <div class="card-arrow-bottom-right"></div>
    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
@endsection
