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
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
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
                            <td class="px-6 py-4 whitespace-nowrap">RPT-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $report->non_compliance_type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $report->location }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $report->category == 'unsafe_act' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $report->category)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $report->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($report->status == 'resolved' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $report->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <button type="button" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600" data-bs-toggle="modal" data-bs-target="#viewReportModal{{ $report->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600" data-bs-toggle="modal" data-bs-target="#updateStatusModal{{ $report->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" onclick="return confirm('Are you sure you want to delete this report?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <!-- Modals for view and update status can be refactored similarly if needed -->
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">No reports found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection 