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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Reports Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500">
                <h3 class="text-lg font-semibold text-gray-700">Total Reports</h3>
                <p class="text-3xl font-bold text-blue-500 mt-2">{{ $stats['totalReports'] }}</p>
                <p class="text-sm text-gray-500 mt-1">All time submissions</p>
            </div>
            <!-- Pending Reports Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-orange-500">
                <h3 class="text-lg font-semibold text-gray-700">Pending Reports</h3>
                <p class="text-3xl font-bold text-orange-500 mt-2">{{ $stats['pendingReports'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Awaiting response</p>
            </div>
            <!-- Solved Reports Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500">
                <h3 class="text-lg font-semibold text-gray-700">Solved Reports</h3>
                <p class="text-3xl font-bold text-green-500 mt-2">{{ $stats['solvedReports'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Successfully resolved</p>
            </div>
        </div>
        <!-- Recent Reports Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Recent Reports</h2>
            <div class="space-y-4">
                @forelse($recentReports as $report)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <h3 class="font-semibold text-gray-800">{{ $report->title }}</h3>
                        <p class="text-gray-600">Status: {{ ucfirst($report->status) }}</p>
                        <p class="text-sm text-gray-500">Submitted on: {{ $report->created_at->format('d M Y, H:i') }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">No recent reports found.</p>
                @endforelse
            </div>
        </div>
    </main>
</div>
@endsection
