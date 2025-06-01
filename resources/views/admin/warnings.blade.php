@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <header class="bg-blue-800 text-white p-4 shadow-md rounded mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Warning Letters Management</h1>
            <div class="text-sm text-blue-100">
                <i class="fas fa-info-circle mr-1"></i>
                All warnings must be suggested by UCUA Officers and approved by Admin
            </div>
        </div>
    </header>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('admin.warnings.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text"
                       name="search"
                       id="search"
                       value="{{ request('search') }}"
                       placeholder="Search by report ID, officer name, reason, or type..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="min-w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
                <a href="{{ route('admin.warnings.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>
    <!-- Warning Letters Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <h3 class="text-sm font-semibold text-gray-700">Total</h3>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalWarnings }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <h3 class="text-sm font-semibold text-gray-700">Pending</h3>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $pendingWarnings }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <h3 class="text-sm font-semibold text-gray-700">Approved</h3>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $approvedWarnings }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <h3 class="text-sm font-semibold text-gray-700">Rejected</h3>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $rejectedWarnings }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <h3 class="text-sm font-semibold text-gray-700">Sent</h3>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $sentWarnings }}</p>
        </div>
    </div>
    <!-- Warning Letters Table -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Warning Letter Suggestions</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suggested By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($warnings as $warning)
                        <tr class="{{ $warning->status === 'pending' ? 'bg-yellow-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                WL-{{ str_pad($warning->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a href="{{ route('admin.reports.show', $warning->report->id) }}" class="text-blue-600 hover:text-blue-800">
                                    RPT-{{ str_pad($warning->report->id, 4, '0', STR_PAD_LEFT) }}
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $warning->suggestedBy ? $warning->suggestedBy->name : 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate" title="{{ $warning->reason }}">
                                {{ Str::limit($warning->reason, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $warning->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                       ($warning->status == 'approved' ? 'bg-green-100 text-green-800' :
                                        ($warning->status == 'rejected' ? 'bg-red-100 text-red-800' :
                                         'bg-purple-100 text-purple-800')) }}">
                                    {{ ucfirst($warning->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $warning->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <!-- View Details Button -->
                                    <button onclick="viewWarningDetails({{ $warning->id }})"
                                            class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full hover:bg-blue-200 transition-colors duration-200"
                                            title="View Warning Details">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </button>

                                    @if($warning->status === 'pending')
                                        <!-- Approve Button -->
                                        <button onclick="approveWarning({{ $warning->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full hover:bg-green-200 transition-colors duration-200"
                                                title="Approve Warning">
                                            <i class="fas fa-check mr-1"></i>Approve
                                        </button>
                                        <!-- Reject Button -->
                                        <button onclick="rejectWarning({{ $warning->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full hover:bg-red-200 transition-colors duration-200"
                                                title="Reject Warning">
                                            <i class="fas fa-times mr-1"></i>Reject
                                        </button>
                                    @elseif($warning->status === 'approved')
                                        <!-- Send Warning Button -->
                                        <form action="{{ route('admin.warnings.send', $warning) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full hover:bg-purple-200 transition-colors duration-200"
                                                    title="Send Warning Letter"
                                                    onclick="return confirm('Are you sure you want to send this warning letter?')">
                                                <i class="fas fa-paper-plane mr-1"></i>Send
                                            </button>
                                        </form>
                                    @elseif($warning->status === 'sent')
                                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                            <i class="fas fa-check-circle mr-1"></i>Sent
                                        </span>
                                    @elseif($warning->status === 'rejected')
                                        <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">
                                            <i class="fas fa-ban mr-1"></i>Rejected
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                <p>No warning letter suggestions found</p>
                                @if(request('search') || request('status'))
                                    <p class="text-sm mt-1">Try adjusting your search criteria</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $warnings->links() }}
        </div>
    </div>

    <!-- View Warning Details Modal -->
    <div class="modal fade" id="viewWarningModal" tabindex="-1" role="dialog" aria-labelledby="viewWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-blue-600 text-white">
                    <h5 class="modal-title" id="viewWarningModalLabel">
                        <i class="fas fa-eye mr-2"></i>Warning Letter Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" onclick="closeModal('viewWarningModal')"></button>
                </div>
                <div class="modal-body" id="warningDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" onclick="closeModal('viewWarningModal')">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Warning Modal -->
    <div class="modal fade" id="approveWarningModal" tabindex="-1" role="dialog" aria-labelledby="approveWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white">
                    <h5 class="modal-title" id="approveWarningModalLabel">
                        <i class="fas fa-check mr-2"></i>Approve Warning Letter
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" onclick="closeModal('approveWarningModal')"></button>
                </div>
                <form id="approveWarningForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Review the warning suggestion and customize the message before approval.</strong>
                        </div>

                        <div id="approveWarningContent">
                            <!-- Content will be loaded dynamically -->
                        </div>

                        <div class="form-group mt-4">
                            <label for="warning_message" class="font-weight-bold">Final Warning Message <span class="text-danger">*</span></label>
                            <textarea name="warning_message" id="warning_message" class="form-control" rows="4" required
                                      placeholder="Enter the final warning message that will be sent to the employee..."></textarea>
                            <small class="form-text text-muted">This message will be included in the official warning letter</small>
                        </div>

                        <div class="form-group">
                            <label for="admin_notes" class="font-weight-bold">Admin Notes (Optional)</label>
                            <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3"
                                      placeholder="Add any internal notes about this approval..."></textarea>
                            <small class="form-text text-muted">These notes are for internal use only</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" onclick="closeModal('approveWarningModal')">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check mr-2"></i>Approve Warning
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Warning Modal -->
    <div class="modal fade" id="rejectWarningModal" tabindex="-1" role="dialog" aria-labelledby="rejectWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-red-600 text-white">
                    <h5 class="modal-title" id="rejectWarningModalLabel">
                        <i class="fas fa-times mr-2"></i>Reject Warning Letter
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" onclick="closeModal('rejectWarningModal')"></button>
                </div>
                <form id="rejectWarningForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Please provide a reason for rejecting this warning suggestion.</strong>
                        </div>

                        <div id="rejectWarningContent">
                            <!-- Content will be loaded dynamically -->
                        </div>

                        <div class="form-group mt-4">
                            <label for="reject_admin_notes" class="font-weight-bold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="admin_notes" id="reject_admin_notes" class="form-control" rows="4" required
                                      placeholder="Explain why this warning suggestion is being rejected..."></textarea>
                            <small class="form-text text-muted">This will be visible to the UCUA officer who made the suggestion</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" onclick="closeModal('rejectWarningModal')">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-ban mr-2"></i>Reject Warning
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
<script>
// View warning details
function viewWarningDetails(warningId) {
    fetch(`/admin/warnings/${warningId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('warningDetailsContent').innerHTML = data.html;
                $('#viewWarningModal').modal('show');
            } else {
                alert('Error loading warning details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading warning details');
        });
}

// Approve warning
function approveWarning(warningId) {
    fetch(`/admin/warnings/${warningId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate the approve modal with warning details
                document.getElementById('approveWarningContent').innerHTML = data.html;

                // Pre-fill the warning message with the suggested content
                const suggestedMessage = data.warning.reason + '\n\nSuggested Action: ' + data.warning.suggested_action;
                document.getElementById('warning_message').value = suggestedMessage;

                // Set the form action
                document.getElementById('approveWarningForm').action = `/admin/warnings/${warningId}/approve`;

                $('#approveWarningModal').modal('show');
            } else {
                alert('Error loading warning details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading warning details');
        });
}

// Reject warning
function rejectWarning(warningId) {
    fetch(`/admin/warnings/${warningId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate the reject modal with warning details
                document.getElementById('rejectWarningContent').innerHTML = data.html;

                // Set the form action
                document.getElementById('rejectWarningForm').action = `/admin/warnings/${warningId}/reject`;

                $('#rejectWarningModal').modal('show');
            } else {
                alert('Error loading warning details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading warning details');
        });
}

// Auto-submit search form when status changes
document.getElementById('status').addEventListener('change', function() {
    this.form.submit();
});

// Universal close modal function
function closeModal(modalId) {
    // Try Bootstrap 5 method first
    const modal = bootstrap?.Modal?.getInstance(document.getElementById(modalId));
    if (modal) {
        modal.hide();
    } else {
        // Fallback to Bootstrap 4 method
        $('#' + modalId).modal('hide');
    }
}

// Handle clicking outside modals
$(document).ready(function() {
    $('.modal').on('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});
</script>
@endpush

@endsection