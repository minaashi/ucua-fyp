@extends('layouts.app')

@section('content')
<div class="min-h-screen flex">
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
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Report Overview</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('department.pending-reports') }}"
                       class="flex items-center px-4 py-2 {{ Request::routeIs('department.pending-reports') ? 'bg-yellow-100 text-yellow-800' : 'text-gray-600' }} hover:bg-yellow-100 hover:text-yellow-800">
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
                <h1 class="text-2xl font-bold">Pending Reports for {{ auth()->guard('department')->user()->name }}</h1>
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
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Pending Reports</h2>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Left</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($reports as $report)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->display_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $daysLeft = $report->deadline ? round(now()->diffInDays($report->deadline, false)) : null;
                                            $priority = 'Medium';
                                            $priorityColor = 'bg-blue-100 text-blue-800';

                                            if ($daysLeft !== null) {
                                                if ($daysLeft < 0) {
                                                    $priority = 'Overdue';
                                                    $priorityColor = 'bg-red-100 text-red-800';
                                                } elseif ($daysLeft <= 2) {
                                                    $priority = 'High';
                                                    $priorityColor = 'bg-red-100 text-red-800';
                                                } elseif ($daysLeft <= 5) {
                                                    $priority = 'Medium';
                                                    $priorityColor = 'bg-yellow-100 text-yellow-800';
                                                } else {
                                                    $priority = 'Low';
                                                    $priorityColor = 'bg-green-100 text-green-800';
                                                }
                                            }
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $priorityColor }}">
                                            {{ $priority }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($report->deadline)
                                            {{ $report->deadline->format('M d, Y') }}
                                        @else
                                            <span class="text-gray-400">No Deadline</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($report->deadline)
                                            @php
                                                $daysLeft = round(now()->diffInDays($report->deadline, false));
                                            @endphp
                                            @if($daysLeft < 0)
                                                <span class="text-red-600 font-semibold">{{ abs($daysLeft) }} days overdue</span>
                                            @elseif($daysLeft == 0)
                                                <span class="text-red-600 font-semibold">Due today</span>
                                            @elseif($daysLeft <= 3)
                                                <span class="text-yellow-600 font-semibold">{{ $daysLeft }} days left</span>
                                            @else
                                                <span class="text-green-600">{{ $daysLeft }} days left</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
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
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No pending reports found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     {{ $reports->links() }}
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modals -->
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

