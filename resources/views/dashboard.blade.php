@extends('layouts.app')

@section('content')
<!-- Main Content Area -->
<div class="flex-1 flex flex-col">
    <!-- Header -->
    <header class="bg-blue-800 text-white p-4 shadow-md">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Port Worker Dashboard</h1>
            <div class="flex items-center space-x-4">
                <span>Welcome, {{ auth()->user()->name }}</span>
            </div>
        </div>
    </header>
    <!-- Main Content -->
    <main class="flex-1 p-6 bg-gray-100">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <!-- Total Reports Card -->
            <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-blue-500">
                <h3 class="text-sm font-semibold text-gray-700">Total Reports</h3>
                <p class="text-2xl font-bold text-blue-500 mt-1">{{ $stats['totalReports'] }}</p>
                <p class="text-xs text-gray-500 mt-1">All submissions</p>
            </div>
            <!-- Pending Reports Card -->
            <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-yellow-500">
                <h3 class="text-sm font-semibold text-gray-700">Pending</h3>
                <p class="text-2xl font-bold text-yellow-500 mt-1">{{ $stats['pendingReports'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Awaiting review</p>
            </div>
            <!-- Under Review Card -->
            <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-blue-500">
                <h3 class="text-sm font-semibold text-gray-700">Under Review</h3>
                <p class="text-2xl font-bold text-blue-500 mt-1">{{ $stats['reviewReports'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Being reviewed</p>
            </div>
            <!-- In Progress Card -->
            <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-orange-500">
                <h3 class="text-sm font-semibold text-gray-700">Investigation</h3>
                <p class="text-2xl font-bold text-orange-500 mt-1">{{ $stats['inProgressReports'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Under investigation</p>
            </div>
            <!-- Resolved Reports Card -->
            <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-green-500">
                <h3 class="text-sm font-semibold text-gray-700">Resolved</h3>
                <p class="text-2xl font-bold text-green-500 mt-1">{{ $stats['resolvedReports'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Completed</p>
            </div>
        </div>

        <!-- Recent Resolution Updates Section -->
        @if(isset($recentResolutionUpdates) && $recentResolutionUpdates->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
                <i class="fas fa-bell text-green-600 mr-2"></i>Recent Resolution Updates
            </h2>
            <div class="space-y-4">
                @foreach($recentResolutionUpdates as $report)
                    <div class="border-l-4 border-green-500 bg-green-50 p-4 rounded-r-lg">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-gray-800">
                                Report {{ $report->display_id }} - Resolved
                            </h3>
                            <span class="text-sm text-gray-500">
                                {{ $report->resolved_at->format('M d, Y') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">
                            <strong>Department:</strong> {{ $report->handlingDepartment->name ?? 'N/A' }}
                        </p>
                        <div class="bg-white p-3 rounded border">
                            <p class="text-sm font-medium text-gray-700 mb-1">Resolution Notes:</p>
                            <p class="text-gray-600">{{ $report->resolution_notes }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Reports Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Recent Reports</h2>
                <a href="{{ route('reports.track') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View All Reports →
                </a>
            </div>
            <div class="space-y-4">
                @forelse($recentReports as $report)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-gray-800">
                                {{ $report->display_id }}
                            </h3>
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($report->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($report->status == 'review') bg-blue-100 text-blue-800
                                @elseif($report->status == 'in_progress') bg-orange-100 text-orange-800
                                @elseif($report->status == 'resolved') bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm mb-2">{{ Str::limit($report->description, 100) }}</p>
                        @if($report->handlingDepartment)
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-building mr-1"></i>
                                Assigned to: {{ $report->handlingDepartment->name }}
                            </p>
                        @endif
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            Last updated: {{ $report->updated_at->format('M d, Y g:i A') }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500">No recent reports found.</p>
                @endforelse
            </div>
        </div>
    </main>
</div>
@endsection
