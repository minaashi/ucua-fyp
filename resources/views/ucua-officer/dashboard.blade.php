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
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 {{ $deadlineReports->count() > 0 ? 'border-red-500 ring-2 ring-red-200' : 'border-green-500' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                            @if($deadlineReports->count() > 0)
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2 animate-pulse"></i>
                            @else
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            @endif
                            Urgent Reports
                        </h3>
                        <p class="text-3xl font-bold {{ $deadlineReports->count() > 0 ? 'text-red-500' : 'text-green-500' }} mt-2">{{ $deadlineReports->count() }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $deadlineReports->count() > 0 ? 'Need immediate attention' : 'All on track' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Status Info -->
        @php
            $unassignedReports = $recentReports->where('handlingDepartment', null);
            $assignedReports = $recentReports->where('handlingDepartment', '!=', null);
        @endphp

        @if($unassignedReports->count() > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>{{ $unassignedReports->count() }} report(s)</strong> need department assignment.
                        <a href="{{ route('ucua.assign-departments-page') }}" class="font-medium underline hover:text-yellow-800">
                            Assign departments now
                        </a>
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Reports Table -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Recent Reports</h2>
                    <div class="text-sm text-gray-600">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $unassignedReports->count() }} Unassigned
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            {{ $assignedReports->count() }} Assigned
                        </span>
                    </div>
                </div>
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
                                    {{ $report->display_id }}
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
                                    @if($report->handlingDepartment)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            {{ $report->handlingDepartment->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Not Assigned
                                        </span>
                                    @endif
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
                                        @else
                                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full"
                                              title="Already assigned to {{ $report->handlingDepartment->name }}">
                                            <i class="fas fa-check mr-1"></i>
                                            Assigned
                                        </span>
                                        @endif



                                        <!-- Suggest Warning Button (only for internal violators) -->
                                        @php
                                            $violator = $report->getViolatorForWarning();
                                            $isInternalViolator = $violator && isset($violator->id) && !empty($violator->email);
                                        @endphp
                                        @if($isInternalViolator)
                                            <button onclick="suggestWarning({{ $report->id }}, '{{ $report->status }}', 'RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')"
                                                    class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full hover:bg-yellow-200 transition-colors duration-200"
                                                    title="Suggest Warning Letter">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Warning
                                            </button>
                                        @elseif($violator && !$isInternalViolator)
                                            <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full"
                                                  title="Warning letters are only available for internal employees">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                External Violator
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full"
                                                  title="Violator must be identified before warning can be suggested">
                                                <i class="fas fa-search mr-1"></i>
                                                ID Required
                                            </span>
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
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    Deadline Reminders
                </h2>
                <p class="text-sm text-gray-600 mt-1">Reports with deadlines within 3 days or overdue ({{ $deadlineReports->count() }} found)</p>
            </div>
            <div class="p-4">
                <div class="space-y-4">
                    @forelse($deadlineReports as $report)
                    @php
                        $daysLeft = $report->deadline ? (int) now()->diffInDays($report->deadline, false) : null;
                        $isOverdue = $report->deadline && $report->deadline->isPast();
                        $urgencyClass = $isOverdue ? 'bg-red-100 border-red-300' : ($daysLeft <= 1 ? 'bg-orange-100 border-orange-300' : 'bg-yellow-100 border-yellow-300');
                        $textClass = $isOverdue ? 'text-red-800' : ($daysLeft <= 1 ? 'text-orange-800' : 'text-yellow-800');
                        $reportId = $report->display_id ?? 'RPT-' . str_pad($report->id, 3, '0', STR_PAD_LEFT);
                    @endphp
                    <div class="flex items-center justify-between {{ $urgencyClass }} p-4 rounded-lg border-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold {{ $textClass }} text-lg">{{ $reportId }}</h3>
                                @if($isOverdue)
                                    <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full animate-pulse">OVERDUE</span>
                                @elseif($daysLeft <= 1)
                                    <span class="px-3 py-1 bg-orange-500 text-white text-xs font-bold rounded-full">URGENT</span>
                                @else
                                    <span class="px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full">DUE SOON</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                <div>
                                    <p class="font-medium {{ $textClass }}">
                                        <i class="fas fa-calendar mr-1"></i>
                                        Deadline: {{ $report->deadline->format('d/m/Y') }}
                                    </p>
                                    <p class="text-xs {{ $textClass }}">
                                        @if($isOverdue)
                                            <i class="fas fa-clock text-red-500 mr-1"></i>
                                            {{ abs($daysLeft) }} day{{ abs($daysLeft) != 1 ? 's' : '' }} overdue
                                        @else
                                            <i class="fas fa-hourglass-half text-orange-500 mr-1"></i>
                                            {{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }} remaining
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    @if($report->handlingDepartment)
                                        <p class="text-xs text-gray-700">
                                            <i class="fas fa-building mr-1"></i>
                                            Assigned to: <span class="font-medium">{{ $report->handlingDepartment->name }}</span>
                                        </p>
                                    @else
                                        <p class="text-xs text-red-600">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Not assigned to department
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-flag mr-1"></i>
                                        Status: <span class="capitalize font-medium">{{ str_replace('_', ' ', $report->status) }}</span>
                                    </p>
                                    @php
                                        $reminderCount = $report->reminders ? $report->reminders->count() : 0;
                                        $lastReminder = $report->reminders ? $report->reminders->first() : null;
                                    @endphp
                                    @if($reminderCount > 0)
                                    <p class="text-xs text-blue-600 mt-1">
                                        <i class="fas fa-bell mr-1"></i>
                                        {{ $reminderCount }} reminder{{ $reminderCount != 1 ? 's' : '' }} sent
                                        @if($lastReminder)
                                            <span class="ml-1 px-2 py-0.5 rounded text-xs font-medium
                                                {{ $lastReminder->type === 'gentle' ? 'bg-green-100 text-green-700' :
                                                   ($lastReminder->type === 'urgent' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700') }}">
                                                Last: {{ ucfirst($lastReminder->type) }}
                                            </span>
                                        @endif
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 ml-4">
                            <a href="{{ route('ucua.report.show', $report->id) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors duration-200">
                                <i class="fas fa-eye mr-2"></i>
                                View Details
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <div class="mb-4">
                            <i class="fas fa-calendar-check text-green-400 text-6xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">All Clear!</h3>
                        <p class="text-gray-500 text-lg mb-1">No urgent reports found</p>
                        <p class="text-gray-400 text-sm">All reports are on track with their deadlines</p>
                    </div>
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
    $('#new_deadline').val(''); // Clear the new deadline value
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