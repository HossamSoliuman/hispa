@extends('owner.layouts.master')
@section('title')
    {{ __('owner.payrolls.show.title') }}
@endsection
@section('css')

@endsection
@section('content')

        <div class="container py-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    {{ __('owner.payrolls.show.header', ['boat' => $payroll->boat->name ?? '']) }}
                </div>
                <div class="card-body">

                    {{-- معلومات أساسية --}}
                    <div class="row mb-4">
                        <div class="col-md-4"><strong>{{ __('owner.payrolls.show.period') }}:</strong> {{ $payroll->period_from }} {{ __('owner.generated.item_0985d2') }} {{ $payroll->period_to }}</div>
                        <div class="col-md-4"><strong>{{ __('owner.payrolls.show.total_revenues') }}:</strong> {{ number_format($payroll->total_revenues,2) }}</div>
                        <div class="col-md-4"><strong>{{ __('owner.payrolls.show.total_expenses') }}:</strong> {{ number_format($payroll->total_expenses,2) }}</div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4"><strong>{{ __('owner.payrolls.show.owner_percentage') }}:</strong> {{ $payroll->owner_percentage }}%</div>
                        <div class="col-md-4"><strong>{{ __('owner.payrolls.show.owner_profit') }}:</strong> {{ number_format($payroll->owner_profit,2) }}</div>
                        <div class="col-md-4"><strong>{{ __('owner.payrolls.show.crew_total') }}:</strong> {{ number_format($payroll->crew_total,2) }}</div>
                    </div>

                    {{-- الرسم البياني --}}
                    <div class="mb-4">
                        <canvas id="payrollChart" style="; height: 250px;"></canvas>
                    </div>

                    {{-- جدول الرواتب --}}
                    <h5 class="mb-3">{{ __('owner.payrolls.show.details_title') }}</h5>
                    <input type="text" id="searchPayrollDetails" class="form-control mb-3" placeholder="{{ __('owner.payrolls.create.search_placeholder') }}">

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="payrollDetailsTable">
                            <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>{{ __('owner.payrolls.show.employee') }}</th>
                                <th>{{ __('owner.payrolls.show.salary_type') }}</th>
                                <th>{{ __('owner.payrolls.show.fixed_salary') }}</th>
                                <th>{{ __('owner.payrolls.show.percentage') }}</th>
                                <th>{{ __('owner.payrolls.show.calculated_salary') }}</th>
                                <th>{{ __('owner.payrolls.show.captain') }}</th>
                                <th>{{ __('owner.payrolls.show.crew') }}</th>
                                <th>{{ __('owner.payrolls.show.remarks') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payroll->details as $detail)
                                <tr class="{{ $detail->is_captain ? 'table-primary' : ($detail->is_crew ? 'table-success' : '') }}">
                                    <td>{{ $detail->iteration   }}</td>
                                    <td>{{ $detail->user->name ?? '---' }}</td>
                                    <td>{{ $detail->salary_type }}</td>
                                    <td>{{ number_format($detail->fixed_amount,2) }}</td>
                                    <td>{{ $detail->percentage ?? 0 }}%</td>
                                    <td>{{ number_format($detail->calculated_salary,2) }}</td>
                                    <td>{!! $detail->is_captain ? '<span class="badge bg-primary">'.__('owner.payrolls.show.captain').'</span>' : '-' !!}</td>
                                    <td>{!! $detail->is_crew ? '<span class="badge bg-success">'.__('owner.payrolls.show.crew').'</span>' : '-' !!}</td>
                                    <td>{{ $detail->notes }}</td>
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
        </div>
    @endsection

    @section('script')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Search filter
                $('#searchPayrollDetails').on('keyup', function() {
                    let value = $(this).val().toLowerCase();
                    $("#payrollDetailsTable tbody tr").filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });

                const ctx = document.getElementById('payrollChart').getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['{{ __('owner.generated.item_83f142') }}', '{{ __('owner.generated.item_301286') }}'],
                        datasets: [{
                            label: '{{ __('owner.generated.item_6fccef') }}',
                            data: [
                                {{ $payroll->owner_profit ?? 0 }},
                                {{ $payroll->crew_total ?? 0 }}
                            ],
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(75, 192, 192, 0.7)'
                            ],
                            borderRadius: 5, // makes bars rounded
                            barPercentage: 0.6 // thinner bars
                        }]
                    },
                    options: {
                        responsive: false, scrollX: true,
                        maintainAspectRatio: false, // fits smaller containers
                        plugins: {
                            legend: { display: false },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 12 } }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { font: { size: 12 } }
                            }
                        }
                    }
                });

            });
        </script>
    @endsection
