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
                <h1 class="text-2xl font-bold">Report Details - {{ $report->display_id }}</h1>
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
                <!-- Report Overview -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Report Overview</h2>
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
                            <p class="text-gray-900">{{ $report->display_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Submission Date</label>
                            <p class="text-gray-900">{{ $report->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Reporter Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-user mr-2 text-blue-600"></i>
                        Reporter Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reporter Name</label>
                            <p class="text-gray-900">{{ $report->is_anonymous ? 'Anonymous Report' : $report->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                            <p class="text-gray-900">{{ $report->employee_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <p class="text-gray-900">{{ $report->department }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <p class="text-gray-900">{{ $report->phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Incident Details -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-exclamation-triangle mr-2 text-orange-600"></i>
                        Incident Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <p class="text-gray-900">{{ $report->category ? ucfirst(str_replace('_', ' ', $report->category)) : 'Not categorized' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <p class="text-gray-900">{{ $report->location }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Incident Date & Time</label>
                            <p class="text-gray-900">{{ $report->incident_date->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <div class="bg-gray-50 p-3 rounded border">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $report->description }}</p>
                        </div>
                    </div>

                    <!-- Safety Details -->
                    @if($report->unsafe_condition || $report->unsafe_act)
                    <div class="mb-4">
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Attachment</label>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-paperclip text-gray-500"></i>
                            <a href="{{ route('attachment.view', ['filename' => basename($report->attachment)]) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">
                                View Attachment
                            </a>
                            <span class="text-xs text-gray-500">({{ basename($report->attachment) }})</span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- VIOLATOR IDENTIFICATION STATUS - PROMINENT SECTION -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 {{ $report->hasViolatorIdentified() ? 'border-green-500' : 'border-red-500' }}">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-user-check mr-2"></i>
                            Violator Identification Status
                        </h2>
                        @if($report->hasViolatorIdentified())
                            <span class="px-3 py-1 text-sm font-bold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                IDENTIFIED
                            </span>
                        @else
                            <span class="px-3 py-1 text-sm font-bold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                INVESTIGATION NEEDED
                            </span>
                        @endif
                    </div>

                    @if($report->hasViolatorIdentified())
                        <!-- Violator Details -->
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <h3 class="font-semibold text-green-800 mb-3">
                                <i class="fas fa-user-times mr-1"></i>
                                Identified Violator
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-green-700 mb-1">Employee ID</label>
                                    <p class="text-green-900 font-bold">{{ $report->violator_employee_id ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-green-700 mb-1">Full Name</label>
                                    <p class="text-green-900 font-bold">{{ $report->violator_name ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-green-700 mb-1">Department</label>
                                    <p class="text-green-900 font-bold">{{ $report->violator_department ?: 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="mt-3 p-3 bg-green-100 border border-green-300 rounded">
                                <p class="text-sm text-green-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <strong>Ready for Warning:</strong> You can now suggest warning letters for this violator. The system will send warnings to the correct person.
                                </p>
                            </div>
                        </div>
                    @else
                        <!-- Investigation Needed -->
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <h3 class="font-semibold text-red-800 mb-3">
                                <i class="fas fa-search mr-1"></i>
                                Investigation Required
                            </h3>
                            <p class="text-red-700 mb-3">
                                The violator for this safety incident has not been identified yet. The handling department needs to complete their investigation.
                            </p>
                            <div class="p-3 bg-red-100 border border-red-300 rounded">
                                <p class="text-sm text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <strong>Cannot Issue Warning:</strong> Warning letters cannot be suggested until the violator is identified through department investigation.
                                </p>
                            </div>
                            @if($report->handlingDepartment)
                                <div class="mt-3">
                                    <p class="text-sm text-red-700">
                                        <i class="fas fa-building mr-1"></i>
                                        <strong>Assigned to:</strong> {{ $report->handlingDepartment->name }} for investigation
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Discussion Comments -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Discussion Comments</h3>
                        <button onclick="addUCUAComment({{ $report->id }}, '{{ $report->status }}', 'RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Add Comment
                        </button>
                    </div>

                    <!-- Threaded Comments Display -->
                    @if(isset($threadedRemarks) && $threadedRemarks->count() > 0)
                        <div class="space-y-4">
                            @foreach($threadedRemarks as $comment)
                                <x-threaded-comment :comment="$comment" :report="$report" />
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-comments text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500 text-sm">No discussion comments yet.</p>
                            <p class="text-gray-400 text-xs">Start the conversation by adding the first comment!</p>
                        </div>
                    @endif
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

                <!-- Reminders History -->
                @if($report->reminders && $report->reminders->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-bell mr-2 text-red-500"></i>Reminders History
                    </h3>
                    <div class="space-y-4">
                        @foreach($report->reminders as $reminder)
                        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="font-semibold text-gray-800">{{ $reminder->sentBy ? $reminder->sentBy->name : 'Unknown User' }}</span>
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $reminder->type === 'gentle' ? 'bg-green-100 text-green-800' :
                                               ($reminder->type === 'urgent' ? 'bg-orange-100 text-orange-800' :
                                                'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($reminder->type) }} Reminder
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $reminder->created_at->format('F d, Y \a\t g:i A') }}</p>
                                    @if($reminder->message)
                                    <div class="bg-white p-3 rounded border border-red-200">
                                        <p class="text-sm text-gray-800">{{ $reminder->message }}</p>
                                    </div>
                                    @else
                                    <p class="text-sm text-gray-500 italic">No additional message provided</p>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <span class="text-xs text-gray-500">{{ $reminder->formatted_id }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Department Remarks Display -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">
                        <i class="fas fa-building mr-2 text-blue-600"></i>
                        Department Investigation Updates
                    </h3>

                    <!-- Department Remarks Display -->
                    @if(isset($threadedRemarks) && $threadedRemarks->where('user_type', 'department')->count() > 0)
                        <div class="space-y-4">
                            @foreach($threadedRemarks->where('user_type', 'department') as $remark)
                                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center">
                                            <i class="fas fa-building text-blue-600 mr-2"></i>
                                            <span class="font-medium text-blue-800">
                                                {{ $remark->department ? $remark->department->name : 'Department' }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-blue-600">
                                            {{ $remark->created_at->format('M d, Y g:i A') }}
                                        </span>
                                    </div>
                                    <div class="text-gray-700">
                                        {{ $remark->content }}
                                    </div>
                                    @if(str_contains($remark->content, '[INVESTIGATION UPDATE]'))
                                        <div class="mt-2 p-2 bg-green-100 border border-green-300 rounded">
                                            <i class="fas fa-check-circle text-green-600 mr-1"></i>
                                            <span class="text-sm font-medium text-green-800">Investigation Complete - Violator Identified</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-search text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500 text-sm">No department investigation updates yet.</p>
                            <p class="text-gray-400 text-xs">Waiting for department to provide investigation results.</p>
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
        </div>
    </main>
</div>

<!-- Include Modals -->
@include('ucua-officer.partials.assign-department-modal')
@include('ucua-officer.partials.suggest-warning-modal')
@include('ucua-officer.partials.send-reminder-modal')
@include('ucua-officer.partials.add-comment-modal')

@endsection

@push('scripts')
<script>
function assignDepartment(reportId, status, reportCode) {
    $('#assignReportId').val(reportId);
    $('#displayReportId').text(reportCode);
    $('#displayReportStatus').text(status.charAt(0).toUpperCase() + status.slice(1));
    $('#assignDepartmentModal').modal('show');
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

function addUCUAComment(reportId, status, reportCode) {
    // Populate report information
    $('#commentReportId').val(reportId);
    $('#commentDisplayReportId').text(reportCode);
    $('#commentDisplayReportStatus').text(status.charAt(0).toUpperCase() + status.slice(1));

    // Clear previous content
    $('#content').val('');
    $('#attachment').val('');

    // Show add comment modal
    $('#addCommentModal').modal('show');
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
