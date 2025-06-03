@extends('layouts.app')

@section('content')
<div class="flex-1 flex flex-col">
    <!-- Header -->
    <header class="bg-blue-800 text-white p-4 shadow-md">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">UCUA Officer Dashboard</h1>
            <div class="flex items-center space-x-4">
                <span>Welcome, {{ auth()->user()->name }}</span>
                <form action="{{ route('ucua.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-white hover:text-gray-200">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 p-6 bg-gray-100">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Total Reports Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500">
                <h3 class="text-lg font-semibold text-gray-700">Total Reports</h3>
                <p class="text-3xl font-bold text-blue-500 mt-2">{{ $totalReports }}</p>
                <p class="text-sm text-gray-500 mt-1">All reports</p>
            </div>

            <!-- Pending Reports Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-yellow-500">
                <h3 class="text-lg font-semibold text-gray-700">Pending Reports</h3>
                <p class="text-3xl font-bold text-yellow-500 mt-2">{{ $pendingReports }}</p>
                <p class="text-sm text-gray-500 mt-1">Awaiting assignment</p>
            </div>

            <!-- Resolved Cases Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500">
                <h3 class="text-lg font-semibold text-gray-700">Resolved Cases</h3>
                <p class="text-3xl font-bold text-green-500 mt-2">{{ $resolvedReports }}</p>
                <p class="text-sm text-gray-500 mt-1">Successfully handled</p>
            </div>

            <!-- Deadline Reminders Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-500">
                <h3 class="text-lg font-semibold text-gray-700">Deadline Reminders</h3>
                <p class="text-3xl font-bold text-red-500 mt-2">{{ $deadlineReports->count() }}</p>
                <p class="text-sm text-gray-500 mt-1">Reports nearing deadline</p>
            </div>
        </div>

        <!-- Recent Reports Table -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Recent Reports</h2>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Violator Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentReports as $report)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                           ($report->status === 'resolved' ? 'bg-green-100 text-green-800' :
                                            'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($report->hasViolatorIdentified())
                                        <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            IDENTIFIED
                                        </span>
                                        <div class="text-xs text-gray-600 mt-1">
                                            {{ $report->violator_name }}
                                        </div>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-search mr-1"></i>
                                            INVESTIGATION
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Pending
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $report->handlingDepartment->name ?? 'Not Assigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $report->deadline ? $report->deadline->format('Y-m-d') : 'No Deadline' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-wrap gap-2">
                                        <!-- Review Button -->
                                        <a href="{{ route('ucua.report.show', $report->id) }}"
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full hover:bg-blue-200 transition-colors duration-200"
                                           title="Review Report Details">
                                            <i class="fas fa-eye mr-1"></i>
                                            Review
                                        </a>

                                        <!-- Assign Department Button (only if not assigned) -->
                                        @if(!$report->handlingDepartment)
                                        <button onclick="assignDepartment({{ $report->id }}, '{{ $report->status }}', 'RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')"
                                                class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full hover:bg-purple-200 transition-colors duration-200"
                                                title="Assign to Department">
                                            <i class="fas fa-building mr-1"></i>
                                            Assign
                                        </button>
                                        @endif



                                        <!-- Suggest Warning Button -->
                                        <button onclick="suggestWarning({{ $report->id }}, '{{ $report->status }}', 'RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')"
                                                class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full hover:bg-yellow-200 transition-colors duration-200"
                                                title="Suggest Warning Letter">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Warning
                                        </button>

                                        <!-- Send Reminder Button (only if assigned and has deadline) -->
                                        @if($report->handlingDepartment && $report->deadline)
                                        <button onclick="sendReminder({{ $report->id }}, '{{ $report->status }}', 'RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')"
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full hover:bg-red-200 transition-colors duration-200"
                                                title="Send Follow-up Reminder">
                                            <i class="fas fa-bell mr-1"></i>
                                            Reminder
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No reports found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Deadline Reminders Section -->
        <div class="mt-6 bg-white rounded-lg shadow-md">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Deadline Reminders</h2>
            </div>
            <div class="p-4">
                <div class="space-y-4">
                    @forelse($deadlineReports as $report)
                    <div class="flex items-center justify-between bg-red-50 p-4 rounded-lg">
                        <div>
                            <h3 class="font-semibold text-red-800">Report #{{ $report->id }}</h3>
                            <p class="text-sm text-red-600">Deadline: {{ $report->deadline->format('Y-m-d') }}</p>
                        </div>
                        
                    </div>
                    @empty
                    <p class="text-center text-gray-500">No reports nearing deadline</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modals for actions -->
@include('ucua-officer.partials.assign-department-modal')
@include('ucua-officer.partials.suggest-warning-modal')
@include('ucua-officer.partials.send-reminder-modal')

@endsection

@push('scripts')
<script>
function assignDepartment(reportId, status, reportCode) {
    // Populate report information
    $('#assignReportId').val(reportId);
    $('#displayReportId').text(reportCode);
    $('#displayReportStatus').text(status.charAt(0).toUpperCase() + status.slice(1));

    // Show assign department modal
    $('#assignDepartmentModal').modal('show');
}



function suggestWarning(reportId, status, reportCode) {
    // Populate report information
    $('#warningReportId').val(reportId);
    $('#warningDisplayReportId').text(reportCode);
    $('#warningDisplayReportStatus').text(status.charAt(0).toUpperCase() + status.slice(1));

    // Clear previous content
    $('#warning_type').val('');
    $('#warning_reason').val('');
    $('#suggested_action').val('');

    // Show suggest warning modal
    $('#suggestWarningModal').modal('show');
}

function sendReminder(reportId, status, reportCode) {
    // Populate report information
    $('#reminderReportId').val(reportId);
    $('#reminderDisplayReportId').text(reportCode);
    $('#reminderDisplayReportStatus').text(status.charAt(0).toUpperCase() + status.slice(1));

    // Clear previous content
    $('#reminder_type').val('');
    $('#reminder_message').val('');
    $('#extend_deadline').prop('checked', false);
    $('#new_deadline_group').hide();
    $('#new_deadline').prop('required', false);

    // Show send reminder modal
    $('#sendReminderModal').modal('show');
}

// Universal close modal function - Bootstrap 4 compatible
function closeModal(modalId) {
    $('#' + modalId).modal('hide');
}

// Success message auto-hide
$(document).ready(function() {
    // Auto-hide success messages after 5 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 5000);

    // Auto-hide error messages after 7 seconds
    setTimeout(function() {
        $('.alert-danger').fadeOut('slow');
    }, 7000);

    // Handle clicking outside modals
    $('.modal').on('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});
</script>
@endpush