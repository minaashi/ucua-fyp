@extends('layouts.admin')

@section('content')
<div class="flex-1 flex flex-col">
    <!-- Header -->
    <header class="bg-blue-800 text-white p-4 shadow-md rounded mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
            <div class="flex items-center space-x-4">
                <span>Welcome, {{ auth()->user()->name }}</span>
                <img src="{{ asset('images/profile.png') }}" alt="Profile" class="h-8 w-8 rounded-full">
            </div>
        </div>
    </header>
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h5 class="text-gray-700 mb-2">Total Reports</h5>
            <p class="text-3xl font-bold text-blue-500">{{ $totalReports }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h5 class="text-gray-700 mb-2">Pending Reports</h5>
            <p class="text-3xl font-bold text-orange-500">{{ $pendingReports }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h5 class="text-gray-700 mb-2">Resolved Cases</h5>
            <p class="text-3xl font-bold text-green-500">{{ $resolvedReports }}</p>
        </div>
    </div>
    <!-- Recent Reports -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="font-semibold text-lg mb-4">Recent Reports</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left py-2">Type</th>
                    <th class="text-left py-2">Location</th>
                    <th class="text-left py-2">Status</th>
                    <th class="text-left py-2">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentReports as $report)
                <tr>
                    <td class="py-2">{{ $report->non_compliance_type }}</td>
                    <td class="py-2">{{ $report->location }}</td>
                    <td class="py-2">
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($report->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </td>
                    <td class="py-2">{{ $report->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-gray-400 py-4">No recent reports</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
