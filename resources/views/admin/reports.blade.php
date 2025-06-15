@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <header class="bg-blue-800 text-white p-4 shadow-md rounded mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Report Management</h1>
            <button type="button" onclick="window.print()" class="bg-white text-blue-800 px-4 py-2 rounded shadow hover:bg-gray-100 flex items-center">
                <i class="fas fa-print mr-2"></i> Print
            </button>
        </div>
    </header>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700">Total Reports</h3>
            <p class="text-3xl font-bold text-blue-500 mt-2">{{ $totalReports }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700">Pending Reports</h3>
            <p class="text-3xl font-bold text-orange-500 mt-2">{{ $pendingReports }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700">Resolved Cases</h3>
            <p class="text-3xl font-bold text-green-500 mt-2">{{ $resolvedReports }}</p>
        </div>
    </div>
    <!-- Report Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('admin.reports.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <select name="category" class="border rounded px-3 py-2">
                    <option value="All Categories" {{ request('category') == 'All Categories' ? 'selected' : '' }}>All Categories</option>
                    <option value="unsafe_act" {{ request('category') == 'unsafe_act' ? 'selected' : '' }}>Unsafe Act</option>
                    <option value="unsafe_condition" {{ request('category') == 'unsafe_condition' ? 'selected' : '' }}>Unsafe Condition</option>
                </select>
                <select name="status" class="border rounded px-3 py-2">
                    <option value="All Status" {{ request('status') == 'All Status' ? 'selected' : '' }}>All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>Review</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <input type="text" name="search" class="border rounded px-3 py-2" placeholder="Search reports..." value="{{ request('search') }}">
                <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">Filter</button>
            </div>
        </form>
    </div>
    <!-- Reports Table -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Reports List</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports as $report)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $report->display_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $report->location }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="{{ route('admin.reports.update', $report->id) }}" method="POST" class="flex items-center">
                                    @csrf
                                    @method('PUT')
                                    <select name="category" class="border rounded px-2 py-1 text-xs focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                                        <option value="unsafe_act" {{ $report->category == 'unsafe_act' ? 'selected' : '' }}>Unsafe Act</option>
                                        <option value="unsafe_condition" {{ $report->category == 'unsafe_condition' ? 'selected' : '' }}>Unsafe Condition</option>
                                    </select>
                                    <input type="hidden" name="status" value="{{ $report->status }}">
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($report->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($report->status == 'review') bg-blue-100 text-blue-800
                                    @elseif($report->status == 'in_progress') bg-purple-100 text-purple-800
                                    @elseif($report->status == 'resolved') bg-green-100 text-green-800
                                    @elseif($report->status == 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $report->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <!-- Review Button - Always Available -->
                                    <a href="{{ route('admin.reports.show', $report->id) }}" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 inline-flex items-center" title="Review Report">
                                        <i class="fas fa-eye mr-1"></i>
                                        Review
                                    </a>

                                    @if($report->status === 'pending')
                                        <!-- Accept Button - Only for pending status -->
                                        <form action="{{ route('admin.reports.accept', $report->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600" onclick="return confirm('Are you sure you want to accept this report?')" title="Accept Report">
                                                <i class="fas fa-check mr-1"></i>
                                                Accept
                                            </button>
                                        </form>

                                        <!-- Reject Button - Only for pending status -->
                                        <button type="button" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" data-toggle="modal" data-target="#rejectReportModal{{ $report->id }}" title="Reject Report">
                                            <i class="fas fa-times mr-1"></i>
                                            Reject
                                        </button>
                                    @endif

                                    @if(in_array($report->status, ['pending', 'review', 'in_progress']))
                                        <!-- Delete Button - Available for non-resolved reports -->
                                        <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600" onclick="return confirm('Are you sure you want to delete this report?')" title="Delete Report">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif


                                </div>
                            </td>
                        </tr>
                        <!-- Modals for view and update status can be refactored similarly if needed -->

                        @if($report->status === 'pending')
                            <!-- Reject Report Modal - Only for pending reports -->
                            <div class="modal fade" id="rejectReportModal{{ $report->id }}" tabindex="-1" aria-labelledby="rejectReportModalLabel{{ $report->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="rejectReportModalLabel{{ $report->id }}">Reject Report {{ $report->display_id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.reports.reject', $report->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="remarks{{ $report->id }}" class="form-label">Rejection Reason</label>
                                                    <textarea class="form-control" id="remarks{{ $report->id }}" name="remarks" rows="4" placeholder="Please provide a reason for rejecting this report..." required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject Report</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">No reports found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection 