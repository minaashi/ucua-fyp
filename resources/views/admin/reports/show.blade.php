@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <header class="bg-blue-800 text-white p-4 shadow-md rounded mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Report Review</h1>
                <p class="text-blue-200">Report ID: RPT-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.reports.index') }}" class="bg-white text-blue-800 px-4 py-2 rounded shadow hover:bg-gray-100 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Reports
                </a>
                <button type="button" onclick="window.print()" class="bg-white text-blue-800 px-4 py-2 rounded shadow hover:bg-gray-100 flex items-center">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
            </div>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Report Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Report Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Report Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Report ID</label>
                        <p class="text-gray-900 font-semibold">RPT-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                            {{ $report->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($report->status == 'resolved' ? 'bg-green-100 text-green-800' : 
                               ($report->status == 'review' ? 'bg-blue-100 text-blue-800' : 
                               ($report->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <p class="text-gray-900">{{ $report->category ? ucfirst(str_replace('_', ' ', $report->category)) : 'Not categorized' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Submission Date</label>
                        <p class="text-gray-900">{{ $report->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Incident Date</label>
                        <p class="text-gray-900">{{ $report->incident_date ? $report->incident_date->format('F d, Y \a\t g:i A') : 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <p class="text-gray-900">{{ $report->location ?: 'Not specified' }}</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <div class="bg-gray-50 p-3 rounded border">
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $report->description ?: 'No description provided' }}</p>
                    </div>
                </div>

                @if($report->unsafe_condition || $report->unsafe_act)
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($report->unsafe_condition)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unsafe Condition</label>
                        <p class="text-gray-900">{{ $report->unsafe_condition }}</p>
                        @if($report->other_unsafe_condition)
                        <p class="text-gray-600 text-sm mt-1">Details: {{ $report->other_unsafe_condition }}</p>
                        @endif
                    </div>
                    @endif
                    
                    @if($report->unsafe_act)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unsafe Act</label>
                        <p class="text-gray-900">{{ $report->unsafe_act }}</p>
                        @if($report->other_unsafe_act)
                        <p class="text-gray-600 text-sm mt-1">Details: {{ $report->other_unsafe_act }}</p>
                        @endif
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

            <!-- Reporter Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Reporter Information</h2>
                @if($report->is_anonymous)
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                        <div class="flex items-center">
                            <i class="fas fa-user-secret text-yellow-600 mr-2"></i>
                            <span class="text-yellow-800 font-medium">Anonymous Report</span>
                        </div>
                        <p class="text-yellow-700 text-sm mt-1">Reporter details are not available for anonymous reports.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <p class="text-gray-900">{{ $report->user ? $report->user->name : 'Not available' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <p class="text-gray-900">{{ $report->user ? $report->user->email : 'Not available' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                            <p class="text-gray-900">{{ $report->employee_id ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <p class="text-gray-900">{{ $report->phone ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <p class="text-gray-900">{{ $report->department ?: ($report->user && $report->user->department ? $report->user->department : 'Not specified') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Handling Information -->
            @if($report->handlingDepartment || $report->handlingStaff || $report->deadline)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Handling Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($report->handlingDepartment)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Department</label>
                        <p class="text-gray-900">{{ $report->handlingDepartment ? $report->handlingDepartment->name : 'Not assigned' }}</p>
                    </div>
                    @endif

                    @if($report->handlingStaff)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Staff</label>
                        <p class="text-gray-900">{{ $report->handlingStaff ? $report->handlingStaff->name : 'Not assigned' }}</p>
                    </div>
                    @endif
                    
                    @if($report->deadline)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                        <p class="text-gray-900 {{ $report->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                            {{ $report->deadline->format('F d, Y') }}
                            @if($report->isOverdue())
                                <span class="text-red-600 text-sm">(Overdue)</span>
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar - Actions and History -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Quick Actions</h3>
                <div class="space-y-3">
                    @if($report->status === 'pending')
                    <form action="{{ route('admin.reports.accept', $report->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 flex items-center justify-center" onclick="return confirm('Are you sure you want to accept this report?')">
                            <i class="fas fa-check mr-2"></i>
                            Accept Report
                        </button>
                    </form>
                    @endif
                    
                    @if($report->status !== 'resolved')
                    <button type="button" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center justify-center" data-toggle="modal" data-target="#updateStatusModal">
                        <i class="fas fa-edit mr-2"></i>
                        Update Status
                    </button>
                    @endif
                    
                    @if($report->status !== 'rejected')
                    <button type="button" class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 flex items-center justify-center" data-toggle="modal" data-target="#rejectModal">
                        <i class="fas fa-times mr-2"></i>
                        Reject Report
                    </button>
                    @endif
                </div>
            </div>

            <!-- Report Statistics -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Report Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Remarks:</span>
                        <span class="font-semibold">{{ $report->getRemarksCount() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Warnings:</span>
                        <span class="font-semibold">{{ $report->getWarningsCount() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Reminders:</span>
                        <span class="font-semibold">{{ $report->getRemindersCount() }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            @if($report->hasActivity())
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Recent Activity</h3>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @if($report->remarks && $report->remarks->count() > 0)
                        @foreach($report->remarks->take(3) as $remark)
                        <div class="border-l-4 border-blue-500 pl-3">
                            <p class="text-sm text-gray-600">Remark by {{ $remark->user ? $remark->user->name : 'Unknown User' }}</p>
                            <p class="text-sm text-gray-800">{{ Str::limit($remark->content, 100) }}</p>
                            <p class="text-xs text-gray-500">{{ $remark->created_at->diffForHumans() }}</p>
                        </div>
                        @endforeach
                    @endif

                    @if($report->warnings && $report->warnings->count() > 0)
                        @foreach($report->warnings->take(2) as $warning)
                        <div class="border-l-4 border-yellow-500 pl-3">
                            <p class="text-sm text-gray-600">Warning ({{ ucfirst($warning->type) }}) by {{ $warning->suggestedBy ? $warning->suggestedBy->name : 'Unknown User' }}</p>
                            <p class="text-sm text-gray-800">{{ Str::limit($warning->reason, 100) }}</p>
                            <p class="text-xs text-gray-500">{{ $warning->created_at->diffForHumans() }}</p>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Detailed History Sections -->
    @if($report->remarks && $report->remarks->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Remarks History</h2>
        <div class="space-y-4">
            @foreach($report->remarks as $remark)
            <div class="border border-gray-200 rounded p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $remark->user ? $remark->user->name : 'Unknown User' }}</p>
                        <p class="text-sm text-gray-600">{{ $remark->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
                <p class="text-gray-900 whitespace-pre-wrap">{{ $remark->content }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($report->warnings && $report->warnings->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Warnings History</h2>
        <div class="space-y-4">
            @foreach($report->warnings as $warning)
            <div class="border border-yellow-200 rounded p-4 bg-yellow-50">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $warning->suggestedBy ? $warning->suggestedBy->name : 'Unknown User' }}</p>
                        <p class="text-sm text-gray-600">{{ $warning->created_at->format('F d, Y \a\t g:i A') }}</p>
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800 mt-1">
                            {{ ucfirst($warning->type) }} Warning
                        </span>
                    </div>
                </div>
                <div class="mt-2">
                    <p class="text-sm font-medium text-gray-700">Reason:</p>
                    <p class="text-gray-900">{{ $warning->reason }}</p>
                </div>
                <div class="mt-2">
                    <p class="text-sm font-medium text-gray-700">Suggested Action:</p>
                    <p class="text-gray-900">{{ $warning->suggested_action }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($report->reminders && $report->reminders->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Reminders History</h2>
        <div class="space-y-4">
            @foreach($report->reminders as $reminder)
            <div class="border border-blue-200 rounded p-4 bg-blue-50">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $reminder->sentBy ? $reminder->sentBy->name : 'Unknown User' }}</p>
                        <p class="text-sm text-gray-600">{{ $reminder->created_at->format('F d, Y \a\t g:i A') }}</p>
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-200 text-blue-800 mt-1">
                            {{ ucfirst($reminder->type) }} Reminder
                        </span>
                    </div>
                </div>
                @if($reminder->message)
                <div class="mt-2">
                    <p class="text-gray-900">{{ $reminder->message }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Report Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.reports.update-status', $report->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="review" {{ $report->status == 'review' ? 'selected' : '' }}>Under Review</option>
                                <option value="in_progress" {{ $report->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="unsafe_act" {{ $report->category == 'unsafe_act' ? 'selected' : '' }}>Unsafe Act</option>
                                <option value="unsafe_condition" {{ $report->category == 'unsafe_condition' ? 'selected' : '' }}>Unsafe Condition</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.reports.reject', $report->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Rejection Reason</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="4" required placeholder="Please provide a reason for rejecting this report..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($report->remarks)
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Admin Remarks</h2>
        <div class="bg-gray-50 p-4 rounded border">
            <p class="text-gray-900 whitespace-pre-wrap">{{ $report->remarks ?: 'No remarks available' }}</p>
        </div>
    </div>
    @endif

@endsection
