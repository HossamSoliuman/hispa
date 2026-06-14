<div class="tab-pane fade" id="analytics" role="tabpanel">
    <div class="row g-4 mt-3">

        <!-- Revenue by Species -->
        <div class="col-md-6">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">{{ __('owner.catch.charts.revenue_by_species') }}</h6>
                    <canvas id="revenueChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Weight by Species -->
        <div class="col-md-6">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">{{ __('owner.catch.charts.weight_by_species') }}</h6>
                    <canvas id="weightChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Performance -->
        <div class="col-12">
            <div class="card shadow-lg border-0 mt-4">
                <div class="card-body">
                    <h6 class="text-muted mb-3">{{ __('owner.catch.charts.monthly_performance') }}</h6>
                    <canvas id="monthlyChart" height="120"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>



    // Weight by Species - Bar
    const weightCtx = document.getElementById('weightChart').getContext('2d');
    new Chart(weightCtx, {
        type: 'bar',
        data: {
            labels: ['Hamour', 'Kan\'ad'],
            datasets: [{
                label: '{{ __('owner.generated.item_7d3c47') }}',
                data: [330, 440],
                backgroundColor: '#ffc107'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 100
                    }
                }
            }
        }
    });

    // Monthly Performance - Mixed Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: ['{{ __('owner.generated.january_2024') }}'],
            datasets: [{
                    label: '{{ __('owner.generated.catch_count') }}',
                    data: [2],
                    backgroundColor: '#0dcaf0',
                    yAxisID: 'y',
                },
                {
                    label: '{{ __('owner.generated.item_f1296c') }}',
                    data: [7920],
                    backgroundColor: '#198754',
                    yAxisID: 'y1',
                },
                {
                    label: '{{ __('owner.generated.item_7d3c47') }}',
                    data: [770],
                    type: 'line',
                    borderColor: '#dc3545',
                    backgroundColor: '#dc3545',
                    tension: 0.4,
                    yAxisID: 'y2',
                }
            ]
        },
        options: {
            responsive: false, scrollX: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            stacked: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                    title: {
                        display: true,
                        text: '{{ __('owner.generated.catch_count') }}'
                    }
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    title: {
                        display: true,
                        text: '{{ __('owner.generated.item_cd39bd') }}'
                    }
                },
                y2: {
                    type: 'linear',
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    title: {
                        display: true,
                        text: '{{ __('owner.generated.item_67e19f') }}'
                    }
                }
            }
        }
    });
</script>

@endsection
