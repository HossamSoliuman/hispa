<div class="row g-4 mt-3">
    <!-- Expenses by Category -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-3">{{ __('owner.expenses.sections.analytics.by_category') }}</h6>
                <div style="height: 280px;">
                    <canvas id="expenseCategoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses by Vessel -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted mb-3">{{ __('owner.expenses.sections.analytics.by_boat') }}</h6>
                <div style="height: 280px;">
                    <canvas id="expenseVesselChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Expense Trends -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted mb-3">{{ __('owner.expenses.sections.analytics.monthly_trends') }}</h6>
                <div style="height: 380px;">
                    <canvas id="monthlyExpenseChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    const expensesByCategory = @json($analytics['expensesByCategory']);
    const expensesByBoat = @json($analytics['expensesByBoat']);
    const monthlyTrends = @json($analytics['monthlyTrends']);
    const chartLocale = '{{ app()->getLocale() === 'ar' ? 'ar-SA' : 'en-US' }}';
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    new Chart(document.getElementById('expenseCategoryChart'), {
        type: 'doughnut',
        data: {
            labels: expensesByCategory.map(c => c.category),
            datasets: [{
                data: expensesByCategory.map(c => c.total),
                backgroundColor: ['#0d6efd', '#ffc107', '#20c997', '#dc3545'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    new Chart(document.getElementById('expenseVesselChart'), {
        type: 'bar',
        data: {
            labels: expensesByBoat.map(b => b.boat),
            datasets: [{
                label: '{{ __('owner.expenses.sections.analytics.amount_label') }}',
                data: expensesByBoat.map(b => b.total),
                backgroundColor: ['#20c997', '#dc3545', '#ffc107', '#0d6efd']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    new Chart(document.getElementById('monthlyExpenseChart'), {
        type: 'line',
        data: {
            labels: monthlyTrends.map(m => {
                const [year, month] = m.month.split('-');
                return new Intl.DateTimeFormat(chartLocale, { year: 'numeric', month: 'long' })
                    .format(new Date(year, month - 1, 1));
            }),
            datasets: [{
                    label: '{{ __('owner.expenses.sections.analytics.amount_label') }}',
                    data: monthlyTrends.map(m => m.total),
                    backgroundColor: 'rgba(111, 102, 255, 0.3)',
                    borderColor: 'rgba(111, 102, 255, 1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                },
                {
                    label: '{{ __('owner.expenses.sections.analytics.count_label') }}',
                    data: monthlyTrends.map(m => m.count),
                    borderColor: 'rgba(75, 192, 192, 0.8)',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    pointRadius: 0,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
