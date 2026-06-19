@extends('owner.layouts.master')
@section('title')
{{__('admin.governorates.title')}}
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
            <li class="breadcrumb-item"><a href="#">{{__('admin.governorates.title')}}</a></li>
            <li class="breadcrumb-item active">{{__('admin.governorates.title')}}</li>
        </ul>
        <h1 class="page-header mb-0">{{__('admin.governorates.page_header')}}</h1>
    </div>

    <div class="ms-auto d-flex flex-nowrap align-items-center gap-2">
        <a href="{{ route('owner.governorates.print') }}" class="btn btn-outline-theme btn-equal"><i
                class="bi bi-file-earmark-pdf me-1"></i>{{ __('owner.list_reports.download') }}</a>
        <a href="#modalCreate" data-bs-toggle="modal" class="btn btn-outline-theme btn-equal"><i
                class="fa fa-plus-circle btn-success fa-fw me-1"></i>{{__('admin.governorates.add_new')}}</a>

    </div>
</div>
<!-- BEGIN #modalCreate -->
<div class="modal fade" id="modalCreate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('admin.governorates.add_new_title')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{route('owner.governorates.store')}}" id="createForm" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="row">

                        <div class="col-6 ">

                            <div class="form-group ">
                                <label for="name" class="form-label">{{__('admin.governorates.name_ar')}}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{old('name')}}"
                                    class="form-control  " required
                                    placeholder="{{__('admin.governorates.name_ar')}}">


                                @error('name') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>

                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name_en" class="form-label">{{__('admin.governorates.name_en')}}<span class="text-danger">*</span></label>
                                <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control" required placeholder="{{__('admin.governorates.name_en')}}">
                                @error('name_en') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="col-6 ">
                            <div class="form-check form-switch " style="margin-top: 35px">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label" for="status">{{__('admin.governorates.activate')}}</label>
                                @error('status') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>
                        </div>
                        <div class="col-6 ">
                            <label for="name" class="form-label">{{__('admin.governorates.region')}}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" required id="" name="region_id">
                                <option value="">{{__('admin.actions.choose')}}</option>
                                @foreach($regions as $region)
                                <option value="{{$region->id}}">{{$region->name}}</option>

                                @endforeach
                            </select>
                            @error('region_id') <span class="text-danger error">{{ $message }}</span>@enderror

                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{__('admin.actions.close')}}</button>
                    <button type="submit" class="btn btn-outline-theme">{{__('admin.actions.save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END #modalCreate-->
<div class="modal fade" id="editModel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('admin.governorates.edit_title')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{route('owner.governorates.update','update')}}" id="editForm"
                method="post"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body justify-content-center">


                    <div class="row">
                        <input type="hidden" name="id" id="id" value="">
                        <div class="col-6 ">

                            <div class="form-group ">
                                <label for="name" class="form-label">{{__('admin.governorates.name_ar')}}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{old('name')}}"
                                    class="form-control  " required
                                    placeholder="{{__('admin.governorates.name_ar')}}">


                                @error('name') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>

                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">{{__('admin.governorates.name_en')}}<span class="text-danger">*</span></label>
                                <input type="text" name="name_en" id="name_en" class="form-control" required placeholder="{{__('admin.governorates.name_en')}}">
                                @error('name_en') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="col-6 ">
                            <div class="form-check form-switch " style="margin-top: 35px">
                                <input type="checkbox" name="status" id="status" class="form-check-input" value="1">
                                <label class="form-check-label" for="status">{{__('admin.governorates.activate')}}</label>
                                @error('status') <span class="text-danger error">{{ $message }}</span>@enderror

                            </div>
                        </div>
                        <div class="col-6 ">
                            <label for="name" class="form-label">{{__('admin.governorates.region')}}<span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" required id="region_id" name="region_id">
                                <option value="">{{__('admin.actions.choose')}}</option>
                                @foreach($regions as $region)
                                <option value="{{$region->id}}">{{$region->name}}</option>

                                @endforeach
                            </select>
                            @error('region_id') <span class="text-danger error">{{ $message }}</span>@enderror

                        </div>

                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default" data-bs-dismiss="modal">{{__('admin.actions.close')}}</button>
                    <button type="submit" class="btn btn-outline-theme">{{__('admin.actions.save')}}</button>
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
                <h5 class="modal-title">{{__('admin.governorates.delete_title')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('owner.governorates.destroy','test') }}" method="post">

                {{method_field('delete')}}
                {{csrf_field()}}
                <div class="modal-body">
                    <p class="text-center">
                    <h6 style="color:red"> {{__('admin.swal.confirm_title')}}</h6>
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
                        <th>{{__('admin.governorates.name')}}</th>
                        <th>{{__('admin.governorates.region')}}</th>
                        <th>{{__('admin.governorates.status')}}</th>
                        <th>{{__('admin.governorates.actions')}}</th>

                    </tr>
                </thead>
                <tbody>

                    @foreach ($data as $info)

                    <tr>

                        <td>{{ $info->name }}</td>
                        <td>{{ $info->region->name ??"" }}</td>
                        <td>
                            @if($info->status ==1 )
                            <span class="badge bg-success">{{__('admin.status.active')}}</span>
                            @else
                            <span class="badge bg-danger">{{__('admin.status.inactive')}}</span>

                            @endif
                        </td>


                        <td>
                            
                            <button type="button" class=" modal-effect btn btn-outline-theme mb-1 btn-sm"
                                data-id="{{ $info->id }}" data-region_id="{{ $info->region_id }}"
                                data-name="{{$info->name_ar}}" data-name_en="{{$info->name_en}}" data-status="{{ $info->status }}"
                                data-bs-effect="effect-scale"
                                data-bs-toggle="modal" href="#editModel"><i class="bi bi-pencil btn-success fa-fw me-1"></i> </button>
       
                            <button type="button" class=" modal-effect btn btn-outline-danger mb-1 btn-sm"
                                data-id="{{ $info->id }}"
                                data-bs-effect="effect-scale"
                                data-bs-toggle="modal" href="#deleteModel">
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


<script>
    // Model Edit
    $('#editModel').on('show.bs.modal', function(event) {
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

@endsection
