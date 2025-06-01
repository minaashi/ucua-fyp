@extends('layouts.app')

@section('content')
<div class="flex-1 flex flex-col">
    <!-- Header -->
    <header class="bg-blue-800 text-white p-4 shadow-md">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('ucua.dashboard') }}" class="text-white hover:text-gray-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <h1 class="text-2xl font-bold">Report Details - RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}</h1>
            </div>
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
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Report Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Report Details Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Report Information</h2>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full 
                            {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($report->status === 'resolved' ? 'bg-green-100 text-green-800' : 
                                ($report->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Report ID</label>
                            <p class="text-gray-900">RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                            <p class="text-gray-900">{{ $report->employee_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reporter</label>
                            <p class="text-gray-900">{{ $report->is_anonymous ? 'Anonymous' : $report->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <p class="text-gray-900">{{ $report->department }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <p class="text-gray-900">{{ $report->phone }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <p class="text-gray-900">{{ $report->category ? ucfirst(str_replace('_', ' ', $report->category)) : 'Not categorized' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <p class="text-gray-900">{{ $report->location }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Incident Date</label>
                            <p class="text-gray-900">{{ $report->incident_date->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <div class="bg-gray-50 p-3 rounded border">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $report->description }}</p>
                        </div>
                    </div>

                    <!-- Safety Details -->
                    @if($report->unsafe_condition || $report->unsafe_act)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Safety Issue Details</label>
                        @if($report->unsafe_condition)
                        <div class="mb-2">
                            <span class="text-sm font-medium text-red-600">Unsafe Condition:</span>
                            <span class="text-gray-900">{{ $report->unsafe_condition }}</span>
                        </div>
                        @endif
                        @if($report->unsafe_act)
                        <div>
                            <span class="text-sm font-medium text-orange-600">Unsafe Act:</span>
                            <span class="text-gray-900">{{ $report->unsafe_act }}</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($report->attachment)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Attachment</label>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-paperclip text-gray-500"></i>
                            <a href="{{ asset('storage/' . $report->attachment) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">
                                View Attachment
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Quick Actions</h3>
                    <div class="flex flex-wrap gap-3">
                        @if(!$report->handlingDepartment)
                        <button onclick="assignDepartment({{ $report->id }}, '{{ $report->status }}', 'RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')" 
                                class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                            <i class="fas fa-building mr-2"></i>
                            Assign to Department
                        </button>
                        @endif

                        <button onclick="addRemarks({{ $report->id }}, '{{ $report->status }}', 'RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                            <i class="fas fa-comment mr-2"></i>
                            Add Discussion Comment
                        </button>

                        <button onclick="suggestWarning({{ $report->id }}, '{{ $report->status }}', 'RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')" 
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Suggest Warning
                        </button>

                        @if($report->handlingDepartment && $report->deadline)
                        <button onclick="sendReminder({{ $report->id }}, '{{ $report->status }}', 'RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <i class="fas fa-bell mr-2"></i>
                            Send Reminder
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="space-y-6">
                <!-- Assignment Information -->
                @if($report->handlingDepartment || $report->assignment_remark || $report->deadline)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Assignment Details</h3>
                    
                    @if($report->handlingDepartment)
                    <div class="mb-4">
                        <span class="text-sm text-gray-500">Assigned to:</span>
                        <div class="text-base font-medium text-gray-800">{{ $report->handlingDepartment->name }}</div>
                    </div>
                    @endif
                    
                    @if($report->deadline)
                    <div class="mb-4">
                        <span class="text-sm text-gray-500">Deadline:</span>
                        <div class="text-base font-medium text-gray-800">
                            {{ $report->deadline->format('M d, Y') }}
                            @if($report->isOverdue())
                                <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Overdue</span>
                            @elseif($report->daysUntilDeadline() !== null && $report->daysUntilDeadline() <= 3)
                                <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Due Soon</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if($report->assignment_remark)
                    <div>
                        <span class="text-sm text-gray-500">Assignment Notes:</span>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded mt-2">
                            <p class="text-sm text-gray-800">{{ $report->assignment_remark }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Activity Summary -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Activity Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Discussion Comments:</span>
                            <span class="font-semibold text-blue-600">{{ $report->remarks ? $report->remarks->count() : 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Warning Suggestions:</span>
                            <span class="font-semibold text-yellow-600">{{ $report->warnings ? $report->warnings->count() : 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Follow-up Reminders:</span>
                            <span class="font-semibold text-purple-600">{{ $report->reminders ? $report->reminders->count() : 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Comments -->
                @if($report->remarks && $report->remarks->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Recent Discussion Comments</h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($report->remarks->take(5) as $remark)
                        <div class="border-l-4 border-blue-500 pl-3 py-2">
                            <p class="text-sm text-gray-600">{{ $remark->user ? $remark->user->name : 'Unknown User' }}</p>
                            <p class="text-sm text-gray-800">{{ $remark->content }}</p>
                            <p class="text-xs text-gray-500">{{ $remark->created_at->diffForHumans() }}</p>
                        </div>
                        @endforeach
                    </div>
                    @if($report->remarks->count() > 5)
                    <div class="mt-3 text-center">
                        <span class="text-sm text-gray-500">Showing latest 5 of {{ $report->remarks->count() }} comments</span>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Timeline -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Report Timeline</h3>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                            <div>
                                <div class="font-medium">Report Created</div>
                                <div class="text-gray-500">{{ $report->created_at->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>
                        @if($report->handlingDepartment)
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                            <div>
                                <div class="font-medium">Assigned to {{ $report->handlingDepartment->name }}</div>
                                <div class="text-gray-500">{{ $report->updated_at->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Include Modals -->
@include('ucua-officer.partials.assign-department-modal')
@include('ucua-officer.partials.add-remarks-modal')
@include('ucua-officer.partials.suggest-warning-modal')
@include('ucua-officer.partials.send-reminder-modal')

@endsection

@push('scripts')
<script>
function assignDepartment(reportId, status, reportCode) {
    $('#assignReportId').val(reportId);
    $('#displayReportId').text(reportCode);
    $('#displayReportStatus').text(status.charAt(0).toUpperCase() + status.slice(1));
    $('#assignDepartmentModal').modal('show');
}

function addRemarks(reportId, status, reportCode) {
    $('#remarksReportId').val(reportId);
    $('#remarksDisplayReportId').text(reportCode);
    $('#remarksDisplayReportStatus').text(status.charAt(0).toUpperCase() + status.slice(1));
    $('#content').val('');
    $('#addRemarksModal').modal('show');
}

function suggestWarning(reportId, status, reportCode) {
    $('#warningReportId').val(reportId);
    $('#warningDisplayReportId').text(reportCode);
    $('#warningDisplayReportStatus').text(status.charAt(0).toUpperCase() + status.slice(1));
    $('#warning_type').val('');
    $('#warning_reason').val('');
    $('#suggested_action').val('');
    $('#suggestWarningModal').modal('show');
}

function sendReminder(reportId, status, reportCode) {
    $('#reminderReportId').val(reportId);
    $('#reminderDisplayReportId').text(reportCode);
    $('#reminderDisplayReportStatus').text(status.charAt(0).toUpperCase() + status.slice(1));
    $('#reminder_type').val('');
    $('#reminder_message').val('');
    $('#extend_deadline').prop('checked', false);
    $('#new_deadline_group').hide();
    $('#new_deadline').prop('required', false);
    $('#sendReminderModal').modal('show');
}

$(document).ready(function() {
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 5000);
    
    setTimeout(function() {
        $('.alert-danger').fadeOut('slow');
    }, 7000);
});
</script>
@endpush
