@extends('layouts.app')

@section('content')
<div class="flex-1 flex flex-col">
    <!-- Header -->
    <header class="bg-blue-800 text-white p-4 shadow-md">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('department.dashboard') }}" class="text-white hover:text-gray-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <h1 class="text-2xl font-bold">Report Details - RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span>Welcome, {{ $department->head_name }}</span>
                <form action="{{ route('department.logout') }}" method="POST" class="inline">
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

                <!-- Department Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Department Actions</h3>
                    <div class="flex flex-wrap gap-3">
                        <button onclick="addRemarks({{ $report->id }}, '{{ $report->status }}', 'RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                            <i class="fas fa-comment mr-2"></i>
                            Add Department Remark
                        </button>

                        @if($report->status !== 'resolved')
                        <button onclick="markAsResolved({{ $report->id }}, '{{ $report->status }}', 'RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')" 
                                class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                            <i class="fas fa-check mr-2"></i>
                            Mark as Resolved
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="space-y-6">
                <!-- Assignment Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Assignment Details</h3>
                    
                    <div class="mb-4">
                        <span class="text-sm text-gray-500">Assigned to:</span>
                        <div class="text-base font-medium text-gray-800">{{ $department->name }}</div>
                    </div>
                    
                    @if($report->deadline)
                    <div class="mb-4">
                        <span class="text-sm text-gray-500">Deadline:</span>
                        <div class="text-base font-medium text-gray-800">
                            {{ $report->deadline->format('M d, Y') }}
                            @if($report->deadline < now())
                                <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Overdue</span>
                            @elseif($report->deadline->diffInDays(now()) <= 3)
                                <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Due Soon</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($report->assignment_remark)
                    <div>
                        <span class="text-sm text-gray-500">Assignment Notes:</span>
                        <div class="text-sm text-gray-800 bg-gray-50 p-2 rounded mt-1">{{ $report->assignment_remark }}</div>
                    </div>
                    @endif
                </div>

                <!-- Discussion Comments -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Discussion Comments</h3>
                        <button type="button"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                onclick="document.getElementById('main-comment-form').classList.toggle('hidden')">
                            <i class="fas fa-plus mr-1"></i>
                            Add Department Remark
                        </button>
                    </div>

                    <!-- Main Comment Form -->
                    <div id="main-comment-form" class="hidden mb-6 p-4 bg-gray-50 rounded-lg border">
                        <form method="POST" action="{{ route('department.add-remarks') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="report_id" value="{{ $report->id }}">

                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Department Remark</label>
                                <textarea name="remarks"
                                          rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                                          placeholder="Add your department remark (confidential)..."
                                          required></textarea>
                                <p class="text-xs text-yellow-600 mt-1">
                                    <i class="fas fa-lock mr-1"></i>
                                    This remark will be confidential and visible only to your department, UCUA officers, and administrators.
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Attachment (optional)</label>
                                <input type="file"
                                       name="attachment"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                                       accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                                <p class="text-xs text-gray-500 mt-1">
                                    Max 10MB. Allowed: JPG, PNG, PDF, DOC, XLS, TXT
                                </p>
                            </div>

                            <div class="flex items-center justify-end space-x-2">
                                <button type="button"
                                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                        onclick="document.getElementById('main-comment-form').classList.add('hidden')">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <i class="fas fa-comment mr-1"></i>
                                    Post Remark
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Threaded Comments Display -->
                    @if(isset($threadedRemarks) && $threadedRemarks->count() > 0)
                        <div class="space-y-4">
                            @foreach($threadedRemarks as $comment)
                                <x-department-threaded-comment :comment="$comment" :report="$report" />
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-comments text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500 text-sm">No discussion comments yet.</p>
                            <p class="text-gray-400 text-xs">Be the first to start the conversation!</p>
                        </div>
                    @endif
                </div>

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
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                            <div>
                                <div class="font-medium">Assigned to {{ $department->name }}</div>
                                <div class="text-gray-500">{{ $report->updated_at->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>
                        @if($report->status === 'resolved')
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            <div>
                                <div class="font-medium">Report Resolved</div>
                                <div class="text-gray-500">{{ $report->resolved_at ? $report->resolved_at->format('M d, Y g:i A') : 'Recently' }}</div>
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
@include('department.partials.add-remarks-modal')
@include('department.partials.resolve-report-modal')

@endsection

@push('scripts')
<script>
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
