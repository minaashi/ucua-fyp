@extends('layouts.app')

@php
use Illuminate\Support\Facades\Request;
@endphp

@section('content')
<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4 border-b">
            <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" class="h-12 mx-auto">
            <h2 class="text-xl font-bold text-center text-gray-800 mt-2">User Dashboard</h2>
        </div>
        
        <nav class="mt-6">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center px-4 py-2 {{ Request::routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Report Overview</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.create') }}" 
                       class="flex items-center px-4 py-2 {{ Request::routeIs('reports.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-file-alt w-5"></i>
                        <span>Submit Report</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.track') }}" 
                       class="flex items-center px-4 py-2 {{ Request::routeIs('reports.track') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-search w-5"></i>
                        <span>Track Report</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.history') }}" 
                       class="flex items-center px-4 py-2 {{ Request::routeIs('reports.history') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-history w-5"></i>
                        <span>Report History</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('profile.show') }}" 
                       class="flex items-center px-4 py-2 {{ Request::routeIs('profile.show') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-cog w-5"></i>
                        <span>Settings</span>
                    </a>
                </li>
                
                <!-- Logout Button -->
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="px-4">
                        @csrf
                        <button type="submit" 
                                class="flex items-center w-full px-4 py-2 text-red-600 hover:bg-red-50 hover:text-red-700">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <header class="bg-blue-800 text-white p-4 shadow-md">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">User Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span>Welcome, {{ auth()->user()->name }}</span>
                    <img src="{{ asset('images/profile.png') }}" alt="Profile" class="h-8 w-8 rounded-full">
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

        <!-- Footer -->
        <footer class="bg-blue-800 text-white p-4">
            <p class="text-center text-sm">Copyright © 2025 Nursyahmina Mosdy, Dr Cik.Feresa Mohd Foozy</p>
        </footer>
    </div>
</div>
@endsection
