@extends('layouts.app')

@php
use Illuminate\Support\Facades\Request;
@endphp

@section('content')
<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white text-gray-700 min-h-screen border-r">
        <div class="p-6 border-b">
            <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" class="mx-auto mb-2 h-12">
            <h2 class="text-xl font-bold text-center text-gray-800">User Dashboard</h2>
        </div>
        
        <nav class="mt-6">
            <ul class="space-y-2">
                <li class="hover:bg-blue-50 transition-colors">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:text-blue-600 {{ Request::routeIs('dashboard') ? 'text-blue-600 bg-blue-50' : '' }}">
                        <i class="fas fa-chart-line mr-2"></i>
                        Report Overview
                    </a>
                </li>
                <li class="hover:bg-blue-50 transition-colors">
                    <a href="{{ route('reports.submit') }}" class="block px-4 py-2 hover:text-blue-600 {{ Request::routeIs('reports.submit') ? 'text-blue-600 bg-blue-50' : '' }}">
                        <i class="fas fa-file-alt mr-2"></i>
                        Submit Report
                    </a>
                </li>
                <li class="hover:bg-blue-50 transition-colors">
                    <a href="{{ route('reports.track') }}" class="block px-4 py-2 hover:text-blue-600 {{ Request::routeIs('reports.track') ? 'text-blue-600 bg-blue-50' : '' }}">
                        <i class="fas fa-search mr-2"></i>
                        Track Report
                    </a>
                </li>
                <li class="hover:bg-blue-50 transition-colors">
                    <a href="{{ route('reports.history') }}" class="block px-4 py-2 hover:text-blue-600 {{ Request::routeIs('reports.history') ? 'text-blue-600 bg-blue-50' : '' }}">
                        <i class="fas fa-history mr-2"></i>
                        Report History
                    </a>
                </li>
                <li class="hover:bg-blue-50 transition-colors">
                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 hover:text-blue-600 {{ Request::routeIs('profile.show') ? 'text-blue-600 bg-blue-50' : '' }}">
                        <i class="fas fa-cog mr-2"></i>
                        Settings
                    </a>
                </li>

                <!-- Logout Button -->
                <li class="hover:bg-blue-50 transition-colors">
                    <form method="POST" action="{{ route('logout') }}" class="block px-4 py-2">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold w-full text-left">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Header -->
        <header class="bg-blue-800 text-white p-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">User Dashboard</h1>
                <div class="flex items-center space-x-4">
                <span>Welcome, {{ $user->name }}</span>
                    <img src="{{ asset('images/profile.png') }}" alt="Profile" class="rounded-full w-8 h-8">
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6 bg-gray-100">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Reports -->
                <div class="bg-white rounded-lg shadow p-6 border-t-4 border-blue-500">
                    <h3 class="text-lg font-semibold text-gray-600">Total Reports Submitted</h3>
                    <p class="text-3xl font-bold text-blue-500 mt-2">{{ $stats['totalReports'] }}</p>
                    <div class="mt-2 text-sm text-gray-500">All time submissions</div>
                </div>

                <!-- Pending Reports -->
                <div class="bg-white rounded-lg shadow p-6 border-t-4 border-orange-500">
                    <h3 class="text-lg font-semibold text-gray-600">Pending Reports</h3>
                    <p class="text-3xl font-bold text-orange-500 mt-2">{{ $stats['pendingReports'] }}</p>
                    <div class="mt-2 text-sm text-gray-500">Awaiting response</div>
                </div>

                <!-- Solved Reports -->
                <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
                    <h3 class="text-lg font-semibold text-gray-600">Solved Reports</h3>
                    <p class="text-3xl font-bold text-green-500 mt-2">{{ $stats['solvedReports'] }}</p>
                    <div class="mt-2 text-sm text-gray-500">Successfully resolved</div>
                </div>
            </div>

            <!-- Recent Reports Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Recent Reports</h2>
                <div class="space-y-4">
                    @forelse($recentReports as $report)
                        <div class="border rounded p-4 hover:bg-gray-50 transition-colors">
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
        <footer class="bg-blue-800 text-white p-4 mt-auto">
            <p class="text-center">Copyright © 2025 Nursyahmina Mosdy, Dr Cik.Feresa Mohd Foozy</p>
        </footer>
    </div>
</div>
@endsection
