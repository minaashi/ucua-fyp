@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-yellow-50 py-10">
    <div class="max-w-5xl mx-auto px-4">
        <div class="bg-yellow-400 rounded-t-lg px-8 py-6 shadow-md flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-yellow-900 mb-1 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2 animate-pulse"></i> Warning Letters
                </h1>
                <p class="text-yellow-900 text-base">Review and manage all warning letters issued to port workers.</p>
            </div>
            <div>
                <button onclick="suggestWarning()" class="bg-yellow-700 hover:bg-yellow-800 text-white font-semibold px-4 py-2 rounded shadow transition">+ Issue New Warning</button>
            </div>
        </div>
        <div class="bg-white rounded-b-lg shadow-md p-8 mt-2">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                    <h3 class="text-lg font-semibold text-yellow-800">Total Warnings</h3>
                    <p class="text-3xl font-bold text-yellow-600">{{ $totalWarnings }}</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                    <h3 class="text-lg font-semibold text-yellow-800">Pending Warnings</h3>
                    <p class="text-3xl font-bold text-yellow-600">{{ $pendingWarnings }}</p>
                </div>
                <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                    <h3 class="text-lg font-semibold text-red-800">Active Escalations</h3>
                    <p class="text-3xl font-bold text-red-600" id="activeEscalations">-</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-800">This Month</h3>
                    <p class="text-3xl font-bold text-blue-600" id="monthlyWarnings">-</p>
                </div>
            </div>

            <!-- Analytics Dashboard -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-yellow-800">Warning Analytics Dashboard</h2>
                    <div class="flex space-x-2">
                        <select id="analyticsFilter" class="form-select text-sm border-yellow-300 rounded">
                            <option value="last_30_days">Last 30 Days</option>
                            <option value="last_3_months">Last 3 Months</option>
                            <option value="last_6_months">Last 6 Months</option>
                            <option value="last_12_months" selected>Last 12 Months</option>
                        </select>
                        <button onclick="exportAnalytics()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded text-sm">
                            <i class="fas fa-download mr-1"></i> Export
                        </button>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Warning Trends Chart -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-3">Warning Trends</h3>
                        <canvas id="warningTrendsChart" height="200"></canvas>
                    </div>

                    <!-- Violation Types Chart -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-3">Violation Types Distribution</h3>
                        <canvas id="violationTypesChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Additional Analytics Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Repeat Offenders -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-3">Top Repeat Offenders</h3>
                        <div id="repeatOffendersList" class="space-y-2">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Department Stats -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-3">Department Statistics</h3>
                        <canvas id="departmentChart" height="200"></canvas>
                    </div>

                    <!-- Effectiveness Metrics -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-3">Effectiveness Metrics</h3>
                        <div id="effectivenessMetrics" class="space-y-3">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="text-xl font-semibold text-yellow-800 mb-4">Recent Warning Letters</h2>
            <table class="min-w-full divide-y divide-yellow-200">
                <thead class="bg-yellow-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Report</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Suggested By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-yellow-100">
                    @forelse($warnings as $warning)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-900 font-bold">#{{ $warning->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-900">
                            <a href="{{ route('reports.show', $warning->report->id) }}" class="hover:text-yellow-700">
                                Report #{{ $warning->report->id }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $warning->type === 'minor' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($warning->type === 'moderate' ? 'bg-orange-100 text-orange-800' : 
                                    'bg-red-100 text-red-800') }}">
                                {{ ucfirst($warning->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-yellow-900">{{ Str::limit($warning->reason, 50) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-900">{{ $warning->suggestedBy->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-900">{{ $warning->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No warning letters found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $warnings->links() }}
            </div>
        </div>
    </div>
</div>

@include('ucua-officer.partials.suggest-warning-modal')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Global variables for charts
let warningTrendsChart, violationTypesChart, departmentChart;

// Initialize analytics dashboard
document.addEventListener('DOMContentLoaded', function() {
    loadAnalyticsData();

    // Add event listener for filter change
    document.getElementById('analyticsFilter').addEventListener('change', function() {
        loadAnalyticsData();
    });
});

// Load analytics data
function loadAnalyticsData() {
    const period = document.getElementById('analyticsFilter').value;

    fetch(`/ucua/analytics/data?period=${period}`)
        .then(response => response.json())
        .then(data => {
            updateOverviewStats(data.overview);
            updateWarningTrendsChart(data.trends);
            updateViolationTypesChart(data.violation_types);
            updateDepartmentChart(data.department_stats);
            updateRepeatOffenders(data.repeat_offenders);
            updateEffectivenessMetrics(data.effectiveness_metrics);
        })
        .catch(error => {
            console.error('Error loading analytics data:', error);
        });
}

// Update overview statistics
function updateOverviewStats(overview) {
    document.getElementById('activeEscalations').textContent = overview.active_escalations || 0;
    document.getElementById('monthlyWarnings').textContent = overview.total_warnings || 0;
}

// Update warning trends chart
function updateWarningTrendsChart(trends) {
    const ctx = document.getElementById('warningTrendsChart').getContext('2d');

    if (warningTrendsChart) {
        warningTrendsChart.destroy();
    }

    const dates = trends.dates.slice(-30); // Last 30 days
    const minorData = dates.map(date => trends.data[date]?.minor || 0);
    const moderateData = dates.map(date => trends.data[date]?.moderate || 0);
    const severeData = dates.map(date => trends.data[date]?.severe || 0);

    warningTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates.map(date => new Date(date).toLocaleDateString()),
            datasets: [
                {
                    label: 'Minor',
                    data: minorData,
                    borderColor: '#fbbf24',
                    backgroundColor: 'rgba(251, 191, 36, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Moderate',
                    data: moderateData,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Severe',
                    data: severeData,
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
}

// Update violation types chart
function updateViolationTypesChart(violationTypes) {
    const ctx = document.getElementById('violationTypesChart').getContext('2d');

    if (violationTypesChart) {
        violationTypesChart.destroy();
    }

    const labels = Object.keys(violationTypes);
    const data = Object.values(violationTypes);

    violationTypesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#fbbf24',
                    '#f97316',
                    '#dc2626'
                ],
                borderWidth: 2,
                borderColor: '#fff'
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
}

// Update department chart
function updateDepartmentChart(departmentStats) {
    const ctx = document.getElementById('departmentChart').getContext('2d');

    if (departmentChart) {
        departmentChart.destroy();
    }

    const labels = departmentStats.map(dept => dept.department_name || 'Unknown');
    const data = departmentStats.map(dept => dept.warning_count);

    departmentChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Warnings',
                data: data,
                backgroundColor: '#fbbf24',
                borderColor: '#f59e0b',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Update repeat offenders list
function updateRepeatOffenders(repeatOffenders) {
    const container = document.getElementById('repeatOffendersList');

    if (!repeatOffenders || repeatOffenders.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-sm">No repeat offenders found</p>';
        return;
    }

    container.innerHTML = repeatOffenders.slice(0, 5).map(offender => `
        <div class="flex justify-between items-center p-2 bg-white rounded border">
            <div>
                <p class="font-medium text-sm">${offender.user.name}</p>
                <p class="text-xs text-gray-500">ID: ${offender.user.worker_id || 'N/A'}</p>
            </div>
            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                ${offender.warning_count} warnings
            </span>
        </div>
    `).join('');
}

// Update effectiveness metrics
function updateEffectivenessMetrics(metrics) {
    const container = document.getElementById('effectivenessMetrics');

    container.innerHTML = `
        <div class="flex justify-between items-center">
            <span class="text-sm font-medium">Repeat Violation Rate</span>
            <span class="text-sm font-bold ${metrics.repeat_violation_rate > 20 ? 'text-red-600' : 'text-green-600'}">
                ${metrics.repeat_violation_rate}%
            </span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm font-medium">Warning Effectiveness</span>
            <span class="text-sm font-bold ${metrics.warning_effectiveness_score > 70 ? 'text-green-600' : 'text-yellow-600'}">
                ${metrics.warning_effectiveness_score}/100
            </span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm font-medium">Avg. Time to Resolution</span>
            <span class="text-sm font-bold text-blue-600">
                ${metrics.average_time_to_resolution || 'N/A'} days
            </span>
        </div>
    `;
}

// Export analytics function
function exportAnalytics() {
    const period = document.getElementById('analyticsFilter').value;
    window.open(`/ucua/analytics/export?period=${period}&format=pdf`, '_blank');
}

// Existing suggest warning function
function suggestWarning() {
    // Your existing suggest warning modal code
    $('#suggestWarningModal').modal('show');
}
</script>
@endpush

@endsection