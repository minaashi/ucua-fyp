@extends('layouts.app')

@section('content')
<div class="min-h-screen flex">
    {{-- Removed the first sidebar --}}

    <!-- Department Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4 border-b">
            <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" class="h-12 mx-auto">
            <h2 class="text-xl font-bold text-center text-gray-800 mt-2">{{ auth()->guard('department')->user()->name }} Dashboard</h2>
        </div>

        <nav class="mt-6">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('department.dashboard') }}"
                       class="flex items-center px-4 py-2 {{ Request::routeIs('department.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Report Overview</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('department.pending-reports') }}"
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-yellow-100 hover:text-yellow-800">
                        <i class="fas fa-clock w-5"></i>
                        <span>Pending Reports</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('department.resolved-reports') }}"
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-green-100 hover:text-green-800">
                        <i class="fas fa-check-circle w-5"></i>
                        <span>Resolved Reports</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('department.notifications') }}"
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-red-100 hover:text-red-700 transition-colors duration-200">
                        <i class="fas fa-bell w-5 {{ $unreadNotificationsCount > 0 ? 'animate-bounce text-red-500' : '' }}"></i>
                        <span class="ml-2">Notifications</span>
                        @if($unreadNotificationsCount > 0)
                            <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $unreadNotificationsCount }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('help.department') }}"
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-question-circle w-5"></i>
                        <span>Help</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <header class="bg-blue-800 text-white p-4 shadow-md">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">{{ auth()->guard('department')->user()->name }} Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span>Welcome, {{ auth()->guard('department')->user()->head_name }}</span>
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
            

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Reports Card -->
                <a href="{{ route('department.dashboard') }}" class="block">
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500 hover:shadow-lg transition-shadow">
                        <h3 class="text-lg font-semibold text-gray-700">Total Reports</h3>
                        <p class="text-3xl font-bold text-blue-500 mt-2">{{ $totalReports }}</p>
                        <p class="text-sm text-gray-500 mt-1">All reports assigned to department</p>
                    </div>
                </a>

                <!-- Pending Reports Card -->
                <a href="{{ route('department.pending-reports') }}" class="block">
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-yellow-500 hover:shadow-lg transition-shadow">
                        <h3 class="text-lg font-semibold text-gray-700">Pending Reports</h3>
                        <p class="text-3xl font-bold text-yellow-500 mt-2">{{ $pendingReports }}</p>
                        <p class="text-sm text-gray-500 mt-1">Awaiting resolution</p>
                    </div>
                </a>

                <!-- Resolved Reports Card -->
                <a href="{{ route('department.resolved-reports') }}" class="block">
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500 hover:shadow-lg transition-shadow">
                        <h3 class="text-lg font-semibold text-gray-700">Resolved Reports</h3>
                        <p class="text-3xl font-bold text-green-500 mt-2">{{ $resolvedReports }}</p>
                        <p class="text-sm text-gray-500 mt-1">Successfully handled</p>
                    </div>
                </a>
            </div>

            <!-- Recent Notifications Section -->
            @if($notifications->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-bell mr-2 text-red-500"></i>
                        Recent Notifications
                    </h3>
                    <div class="flex space-x-2">
                        @if($unreadNotificationsCount > 0)
                        <form action="{{ route('department.notifications.mark-all-read') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition-colors">
                                Mark All Read
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('department.notifications') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            View All
                        </a>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach($notifications->take(3) as $notification)
                    <div class="border-l-4 {{ $notification->read_at ? 'border-gray-300 bg-gray-50' : 'border-red-500 bg-red-50' }} p-4 rounded-r-lg">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                @if(isset($notification->data['type']) && $notification->data['type'] === 'reminder')
                                <div class="flex items-center mb-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $notification->data['reminder_type'] === 'gentle' ? 'bg-green-100 text-green-800' :
                                           ($notification->data['reminder_type'] === 'urgent' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($notification->data['reminder_type']) }} Reminder
                                    </span>
                                    <span class="ml-2 text-sm text-gray-500">{{ $notification->data['reminder_formatted_id'] ?? '' }}</span>
                                </div>
                                <p class="text-sm font-medium text-gray-900 mb-1">
                                    Report: {{ $notification->data['report_formatted_id'] ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-600 mb-2">{{ $notification->data['report_description'] ?? '' }}</p>
                                @if(isset($notification->data['message']) && $notification->data['message'])
                                <p class="text-sm text-gray-700 italic">"{{ $notification->data['message'] }}"</p>
                                @endif
                                @else
                                <p class="text-sm font-medium text-gray-900">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                @endif
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                                @if(!$notification->read_at)
                                <button onclick="markAsRead('{{ $notification->id }}')"
                                        class="text-xs text-blue-600 hover:text-blue-800 mt-1">
                                    Mark as read
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

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
                                                ($report->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                                'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->deadline ? $report->deadline->format('Y-m-d') : 'No Deadline' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex flex-wrap gap-2">
                                            <!-- Review Button - Always Available -->
                                            <a href="{{ route('department.report.show', $report->id) }}"
                                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full hover:bg-blue-200 transition-colors duration-200"
                                               title="Review Report Details">
                                                <i class="fas fa-eye mr-1"></i>
                                                Review
                                            </a>

                                            @if(in_array($report->status, ['pending', 'in_progress', 'review']))
                                                <!-- Remark Button - Available for pending, in_progress, and review status -->
                                                <button onclick="addRemarks({{ $report->id }}, '{{ $report->status }}', '{{ $report->display_id }}')"
                                                        class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full hover:bg-green-200 transition-colors duration-200"
                                                        title="Add Department Remark">
                                                    <i class="fas fa-comment mr-1"></i>
                                                    Remark
                                                </button>
                                            @endif

                                            @if($report->status === 'review')
                                                <!-- Accept Button - Only for review status -->
                                                <button onclick="acceptReport({{ $report->id }}, '{{ $report->display_id }}')"
                                                        class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-full hover:bg-emerald-200 transition-colors duration-200"
                                                        title="Accept Report">
                                                    <i class="fas fa-thumbs-up mr-1"></i>
                                                    Accept
                                                </button>

                                                <!-- Reject Button - Only for review status -->
                                                <button onclick="rejectReport({{ $report->id }}, '{{ $report->display_id }}')"
                                                        class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full hover:bg-red-200 transition-colors duration-200"
                                                        title="Reject Report">
                                                    <i class="fas fa-thumbs-down mr-1"></i>
                                                    Reject
                                                </button>
                                            @endif

                                            @if(in_array($report->status, ['pending', 'in_progress']))
                                                <!-- Resolve Button - Only for pending and in_progress status -->
                                                <button onclick="markAsResolved({{ $report->id }}, '{{ $report->status }}', '{{ $report->display_id }}')"
                                                        class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full hover:bg-purple-200 transition-colors duration-200"
                                                        title="Mark as Resolved">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Resolve
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No reports found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modals -->
@include('department.partials.view-report-modal')
@include('department.partials.resolve-report-modal')
@include('department.partials.add-remarks-modal')

@endsection

@push('scripts')
<script>
// Department-specific modal handling
$(document).ready(function() {
    // Ensure all modal cancel buttons work
    $('.modal').on('click', '[data-dismiss="modal"]', function(e) {
        e.stopPropagation();
        const modalId = $(this).closest('.modal').attr('id');
        $('#' + modalId).modal('hide');
    });

    // Handle modal close buttons specifically
    $('.modal .close, .modal .btn-secondary').on('click', function(e) {
        e.stopPropagation();
        const modalId = $(this).closest('.modal').attr('id');
        $('#' + modalId).modal('hide');
    });

    // Auto-hide success/error messages
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 5000);

    setTimeout(function() {
        $('.alert-danger').fadeOut('slow');
    }, 7000);
});

// Mark notification as read
function markAsRead(notificationId) {
    $.ajax({
        url: `/department/notifications/${notificationId}/mark-read`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            }
        },
        error: function() {
            alert('Failed to mark notification as read');
        }
    });
}

// Accept Report Function
function acceptReport(reportId, reportCode) {
    if (confirm(`Are you sure you want to accept report ${reportCode}? This will change its status to "In Progress".`)) {
        // Show loading state
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Accepting...';
        button.disabled = true;

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/department/reports/${reportId}/accept`;

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        document.body.appendChild(form);
        form.submit();
    }
}

// Reject Report Function
function rejectReport(reportId, reportCode) {
    const reason = prompt(`Please provide a reason for rejecting report ${reportCode}:`);
    if (reason && reason.trim() !== '') {
        // Show loading state
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Rejecting...';
        button.disabled = true;

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/department/reports/${reportId}/reject`;

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add rejection reason
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'rejection_reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);

        document.body.appendChild(form);
        form.submit();
    } else if (reason !== null) {
        alert('Please provide a reason for rejection.');
    }
}
</script>
@endpush

