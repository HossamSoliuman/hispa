@extends('owner.layouts.master')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h4 class="fw-bold"><i class="bi bi-person-workspace me-2"></i>{{ __('owner.generated.item_419582') }}: {{ $boat->name }}</h4>
        <p class="text-muted small">{{ __('owner.generated.captain_crew_details_linked_to_boat') }}</p>
    </div>
    @php
    $captain = $boat->captain;
    $crew = $boat->crews;
    @endphp
    <!-- {{ __('owner.generated.captain_data') }} -->
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0 text-white"><i class="bi bi-person-badge-fill me-2"></i>{{ __('owner.generated.captain_data') }}</h6>
        </div>
        <div class="card-body">
            @if($captain)
            <div class="row g-3">
                <div class="col-md-4"><strong>{{ __('owner.generated.name') }}</strong> {{ $captain->name }}</div>
                <div class="col-md-4"><strong>{{ __('owner.generated.email') }}</strong> {{ $captain->email }}</div>
                <div class="col-md-4"><strong>{{ __('owner.generated.nationality') }}</strong> {{ $captain->nationality }}</div>

                @if($captain->nationality == '{{ __('owner.generated.saudi') }}')
                <div class="col-md-4"><strong>{{ __('owner.generated.id_number') }}</strong> {{ $captain->id_number }}</div>
                @else
                <div class="col-md-4"><strong>{{ __('owner.generated.residence_number') }}</strong> {{ $captain->residence_number }}</div>
                <div class="col-md-4"><strong>{{ __('owner.generated.passport_number') }}</strong> {{ $captain->passport_number }}</div>
                <div class="col-md-4"><strong>{{ __('owner.generated.residence_start_date') }}</strong> {{ $captain->residence_start_date }}</div>
                <div class="col-md-4"><strong>{{ __('owner.generated.residence_end_date') }}</strong> {{ $captain->residence_end_date }}</div>
                @endif

                <div class="col-md-4"><strong>{{ __('owner.generated.appointment_date') }}</strong> {{ $captain->date_appointment }}</div>
                <div class="col-md-4"><strong>{{ __('owner.generated.phone') }}</strong> {{ $captain->phone }}</div>

                <div class="col-md-4"><strong>{{ __('owner.dalal.filters.status') }}</strong>
                    @if($captain->status) <span class="badge bg-success">{{ __('owner.fish.activate') }}</span>
                    @else <span class="badge bg-danger">{{ __('owner.generated.inactive') }}</span>
                    @endif
                </div>
            </div>
            @else
            <div class="row g-3">
                <div class="col-md-12"><strong>{{ __('owner.trips.no_captain') }}</strong></div>
            </div>
            @endif
        </div>
    </div>

    <!-- {{ __('owner.generated.item_7f651d') }} -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-white"><i class="bi bi-people-fill me-2"></i>{{ __('owner.generated.crew_data') }}</h6>
            <span class="small">{{ __('owner.generated.item_ca2dbd') }}: {{ $crew->count() }}</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="tab-content py-4">
                    <div class="tab-pane fade show active" id="allTab">
                        <div id="datatable">
                            <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('owner.assets.name') }}</th>
                                        <th>{{ __('owner.customers.modal.labels.email') }}</th>
                                        <th>{{ __('owner.dalal.modal.form.phone') }}</th>
                                        <th>{{ __('owner.employee.table.nationality') }}</th>
                                        <th> {{ __('owner.generated.identity') }}/{{ __('owner.generated.passport') }}</th>
                                        <th> {{ __('owner.crew.table.job_title') }}</th>
                                        <th>{{ __('owner.assets.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
@endsection

@section('script')
<script type="text/javascript">
    $(function() {
        // Check if the DataTable is already initialized and destroy it
        if ($.fn.DataTable.isDataTable('#datatableDefault')) {
            $('#datatableDefault').DataTable().destroy();
        }

        var table = $('#datatableDefault').DataTable({
            processing: true,
            serverSide: true,

            language: {
                url: "{{asset('dashboard/assets/js/ar.json')}}?v={{ time() }}"
            },

            ajax: {
                url: "{{ route('owner.getCrewData') }}",
                data: function(d) {
                    d.boat_id = '{{ $boat->id }}';
                }
            },

            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: 'text-center',
                },
                {
                    data: 'name',
                    name: 'name',
                    className: 'text-center',
                },
                {
                    data: 'email',
                    name: 'email',
                    className: 'text-center',
                },
                {
                    data: 'phone',
                    name: 'phone',
                    className: 'text-center',
                },
                {
                    data: 'nationality',
                    name: 'nationality',
                    className: 'text-center',
                },
                {
                    data: 'id_number',
                    name: 'id_number',
                    className: 'text-center',
                },
                {
                    data: 'job_title',
                    name: 'job_title',
                    className: 'text-center',
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'text-center',
                },
            ],
            responsive: false, scrollX: true,

            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
        });
    });
</script>

@endsection