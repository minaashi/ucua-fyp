@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-red-50 py-10">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-red-400 rounded-t-lg px-8 py-6 shadow-md flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-red-900 mb-1 flex items-center">
                    <i class="fas fa-bell mr-2 animate-bounce"></i> Reminder Management
                </h1>
                <p class="text-red-900 text-base">Track and manage all sent reminders for reports with approaching deadlines.</p>
            </div>
            <div class="text-right">
                <div class="bg-red-500 text-white px-4 py-2 rounded-lg">
                    <div class="text-2xl font-bold">{{ $totalReminders }}</div>
                    <div class="text-sm">Total Reminders</div>
                </div>
            </div>
           
        </div>
        <div class="bg-white rounded-b-lg shadow-md p-8 mt-2">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                    <h3 class="text-lg font-semibold text-red-800">Total Reminders</h3>
                    <p class="text-3xl font-bold text-red-600">{{ $totalReminders }}</p>
                    <p class="text-sm text-red-500">All time</p>
                </div>
                <div class="bg-orange-50 rounded-lg p-6 border border-orange-200">
                    <h3 class="text-lg font-semibold text-orange-800">Recent Reminders</h3>
                    <p class="text-3xl font-bold text-orange-600">{{ $recentReminders }}</p>
                    <p class="text-sm text-orange-500">Last 7 days</p>
                </div>
                <div class="bg-red-100 rounded-lg p-6 border border-red-300 {{ $overdueReports->count() > 0 ? 'ring-2 ring-red-400' : '' }}">
                    <h3 class="text-lg font-semibold text-red-800 flex items-center">
                        @if($overdueReports->count() > 0)
                            <i class="fas fa-exclamation-triangle mr-2 animate-pulse"></i>
                        @endif
                        Overdue Reports
                    </h3>
                    <p class="text-3xl font-bold text-red-700">{{ $overdueReports->count() }}</p>
                    <p class="text-sm text-red-600">Need immediate action</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                    <h3 class="text-lg font-semibold text-yellow-800">Due Soon</h3>
                    <p class="text-3xl font-bold text-yellow-600">{{ $upcomingReports->count() }}</p>
                    <p class="text-sm text-yellow-500">Within 3 days</p>
                </div>
            </div>

            <!-- Reports Needing Reminders Section -->
            @if($reportsNeedingReminders->count() > 0)
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-red-800 mb-6 flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3 animate-pulse"></i>
                    Reports Requiring Immediate Attention
                    <span class="ml-3 bg-red-500 text-white text-sm px-3 py-1 rounded-full">{{ $reportsNeedingReminders->count() }}</span>
                </h2>

                <!-- Overdue Reports -->
                @if($overdueReports->count() > 0)
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-red-700 mb-4 flex items-center">
                        <i class="fas fa-clock text-red-600 mr-2"></i>
                        Overdue Reports ({{ $overdueReports->count() }})
                    </h3>
                    <div class="space-y-4">
                        @foreach($overdueReports as $report)
                        @php
                            $daysOverdue = (int) abs(now()->diffInDays($report->deadline, false));
                            $lastReminder = $report->reminders->first();
                        @endphp
                        <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="text-lg font-bold text-red-800">{{ $report->display_id ?? 'RPT-' . str_pad($report->id, 3, '0', STR_PAD_LEFT) }}</h4>
                                        <span class="px-3 py-1 bg-red-600 text-white text-xs font-bold rounded-full animate-pulse">
                                            OVERDUE {{ $daysOverdue }} DAY{{ $daysOverdue != 1 ? 'S' : '' }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-red-700"><strong>Deadline:</strong> {{ $report->deadline->format('d/m/Y') }}</p>
                                            <p class="text-gray-700"><strong>Status:</strong> <span class="capitalize">{{ str_replace('_', ' ', $report->status) }}</span></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-700"><strong>Department:</strong> {{ $report->handlingDepartment ? $report->handlingDepartment->name : 'Not assigned' }}</p>
                                            @if($lastReminder)
                                                <p class="text-gray-600"><strong>Last Reminder:</strong> {{ $lastReminder->created_at->format('d/m/Y') }} ({{ ucfirst($lastReminder->type) }})</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 ml-4">
                                    <a href="{{ route('ucua.report.show', $report->id) }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-eye mr-2"></i>View Report
                                    </a>
                                    @if($report->handlingDepartment)
                                    <button onclick="sendReminder({{ $report->id }}, '{{ $report->status }}', '{{ $report->display_id ?? 'RPT-' . str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                        <i class="fas fa-bell mr-2"></i>Send Reminder
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Upcoming Reports -->
                @if($upcomingReports->count() > 0)
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-yellow-700 mb-4 flex items-center">
                        <i class="fas fa-hourglass-half text-yellow-600 mr-2"></i>
                        Due Soon ({{ $upcomingReports->count() }})
                    </h3>
                    <div class="space-y-4">
                        @foreach($upcomingReports as $report)
                        @php
                            $daysLeft = (int) now()->diffInDays($report->deadline, false);
                            $lastReminder = $report->reminders->first();
                            $urgencyClass = $daysLeft <= 1 ? 'border-orange-400 bg-orange-50' : 'border-yellow-400 bg-yellow-50';
                            $urgencyText = $daysLeft <= 1 ? 'URGENT' : 'DUE SOON';
                            $urgencyColor = $daysLeft <= 1 ? 'bg-orange-500' : 'bg-yellow-500';
                        @endphp
                        <div class="border-l-4 {{ $urgencyClass }} p-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="text-lg font-bold text-gray-800">{{ $report->display_id ?? 'RPT-' . str_pad($report->id, 3, '0', STR_PAD_LEFT) }}</h4>
                                        <span class="px-3 py-1 {{ $urgencyColor }} text-white text-xs font-bold rounded-full">
                                            {{ $urgencyText }} - {{ $daysLeft }} DAY{{ $daysLeft != 1 ? 'S' : '' }} LEFT
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-700"><strong>Deadline:</strong> {{ $report->deadline->format('d/m/Y') }}</p>
                                            <p class="text-gray-700"><strong>Status:</strong> <span class="capitalize">{{ str_replace('_', ' ', $report->status) }}</span></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-700"><strong>Department:</strong> {{ $report->handlingDepartment ? $report->handlingDepartment->name : 'Not assigned' }}</p>
                                            @if($lastReminder)
                                                <p class="text-gray-600"><strong>Last Reminder:</strong> {{ $lastReminder->created_at->format('d/m/Y') }} ({{ ucfirst($lastReminder->type) }})</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 ml-4">
                                    <a href="{{ route('ucua.report.show', $report->id) }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-eye mr-2"></i>View Report
                                    </a>
                                    @if($report->handlingDepartment)
                                    <button onclick="sendReminder({{ $report->id }}, '{{ $report->status }}', '{{ $report->display_id ?? 'RPT-' . str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')"
                                            class="inline-flex items-center px-4 py-2 {{ $daysLeft <= 1 ? 'bg-orange-600 hover:bg-orange-700' : 'bg-yellow-600 hover:bg-yellow-700' }} text-white text-sm font-medium rounded-lg transition-colors">
                                        <i class="fas fa-bell mr-2"></i>Send Reminder
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="mb-8 text-center py-12 bg-green-50 rounded-lg border border-green-200">
                <i class="fas fa-check-circle text-green-500 text-6xl mb-4"></i>
                <h3 class="text-2xl font-semibold text-green-700 mb-2">All Reports On Track!</h3>
                <p class="text-green-600">No reports require immediate attention at this time.</p>
            </div>
            @endif

            <!-- Sent Reminders History -->
            <h2 class="text-xl font-semibold text-red-800 mb-4 flex items-center">
                <i class="fas fa-history mr-2"></i>
                Reminder History
                <span class="ml-2 text-xs text-gray-500">(Last updated: {{ now()->format('H:i:s') }})</span>
            </h2>
            <table class="min-w-full divide-y divide-red-200">
                <thead class="bg-red-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Report</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Sent By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-red-100">
                    @forelse($reminders as $reminder)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-900 font-bold">{{ $reminder->formatted_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-900">
                            @if($reminder->report)
                                <a href="{{ route('ucua.report.show', $reminder->report->id) }}" class="hover:text-red-700">
                                    {{ $reminder->report->display_id }}
                                </a>
                            @else
                                <span class="text-gray-500">No Report</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $reminder->type === 'gentle' ? 'bg-green-100 text-green-800' : 
                                   ($reminder->type === 'urgent' ? 'bg-orange-100 text-orange-800' : 
                                    'bg-red-100 text-red-800') }}">
                                {{ ucfirst($reminder->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-red-900">{{ Str::limit($reminder->message ?? 'No message', 50) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-900">{{ $reminder->sentBy->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-900">{{ $reminder->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No reminders found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $reminders->links() }}
            </div>
        </div>
    </div>
</div>

@include('ucua-officer.partials.send-reminder-modal')

@push('scripts')
<script>
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
    // Check if there's a success message (indicating a reminder was just sent)
    if ($('.alert-success').length > 0) {
        // Refresh the page after 2 seconds to show updated reminder history
        setTimeout(function() {
            window.location.reload();
        }, 2000);
    }

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

@endsection