@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.item_630f5c') }}
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
    </style>
@endsection
@section('content')

    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">{{ __('owner.generated.item_8c8d95') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.generated.item_8c8d95') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.generated.item_79b50d') }}</h1>
        </div>


    </div>




    <div class="tab-content p-4">
        <div class="tab-pane fade show active" id="allTab">
            <!-- BEGIN #datatable -->
            <!-- BEGIN #datatable -->
            <div id="datatable" class="mb-5">
                {{--                    <div class="card">--}}
                {{--                        <div class="card-body">--}}
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text">
                    <thead>
                    <tr>
                        <th>{{ __('owner.generated.item_b099b3') }}</th>
                        <th>{{ __('owner.assets.status') }}</th>
                        <th>{{ __('owner.generated.item_cc0478') }}</th>
                        <th>{{ __('owner.expenses.show.attachment') }}</th>

                    </tr>
                    </thead>
                    <tbody>

                    @foreach(auth()->user()->userRequests as $request)
                        <tr>
                            <td>{{ $request->type }}</td>
                            <td>
                                @if($request->status == 'approved')
                                    <span class="badge bg-success">{{ __('owner.assets.approved') }}</span>
                                @elseif($request->status == 'rejected')
                                    <span class="badge bg-danger">{{ __('owner.generated.item_20a971') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('owner.trips.status_pending') }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $fieldNames = [
              'name' => __('owner.generated.item_2e8b17'),
              'address' => __('owner.generated.item_6dc658'),
              'email' => __('owner.generated.item_737ee6'),
              'phone' => __('owner.generated.item_eff9cf'),
              'boat_name' => __('owner.generated.item_a77eab'),
              'boat_number' => __('owner.generated.item_c00807'),
              'nationality' => __('owner.generated.item_676e6d'),
              'crew_count' => __('owner.generated.item_2fa0c4'),
              'city_id' => __('owner.generated.item_a213cd'),
              'region_id' => __('owner.generated.item_5ebad0'),
              'governorate_id' => __('owner.generated.item_d51135'),
              'tax_number' => __('owner.generated.item_d70b52'),
              'record_number' => __('owner.generated.item_c4147d'),
              'id_number' => __('owner.generated.item_0a375e'),
          ];
          $keys = array_keys($request->data);
                $summary = implode(__('owner.generated.item_dab3a2'), array_map(fn($key) => __('owner.generated.item_759fdc') . ($fieldNames[$key] ?? $key), $keys));
                                @endphp
                                {{ $summary }}
                            </td>



                            <td>
                                @if($request->attachment !=null)
                                    <a href="{{ asset($request->attachment) }}" target="_blank" class="btn btn-outline-info btn-sm">{{ __('owner.actions.show') }}</a>
                                @else
                                    -
                                @endif
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


    {{--            </div>--}}
    {{--        </div>--}}
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
        $(document).ready(function () {
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }

            $('#datatableDefault').DataTable({
                language: {
                    url: '{{ asset("dashboard/assets/js/ar.json") }}?v={{ time() }}'
                }
            });
        });
    </script>

    <script>
        $("#createForm").validate();
        $("#editForm").validate();

    </script>


    <script>

        // Model Edit
        $('#editModel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var name = button.data('name')
            var status = button.data('status')


            // var image = button.data('image')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #name').val(name);
            modal.find('.modal-body #status').prop('checked', status == 1);


        });
        $('#deleteModel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')

            // var image = button.data('image')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);


        });

    </script>

@endsection
