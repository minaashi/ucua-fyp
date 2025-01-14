@extends('layouts.app')

@section('content')
<!-- Main dashboard layout -->
<div class="flex h-screen">

    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 text-white">
        <div class="h-16 bg-gray-900 flex items-center justify-center">
            <h1 class="text-2xl font-bold text-white">Admin Panel</h1>
        </div>
        <nav class="mt-4">
            <ul>
                <!-- Sidebar menu -->
                <li><a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 text-lg text-gray-300 hover:bg-gray-700">Dashboard</a></li>
                <li><a href="{{ route('admin.reports.index') }}" class="block py-2 px-4 text-lg text-gray-300 hover:bg-gray-700">Report Management</a></li>
                <li><a href="{{ route('admin.sendWarningLetters') }}" class="block py-2 px-4 text-lg text-gray-300 hover:bg-gray-700">Send Warning Letters</a></li>
                <li><a href="{{ route('admin.settings') }}" class="block py-2 px-4 text-lg text-gray-300 hover:bg-gray-700">Admin Settings</a></li>
            </ul>
        </nav>
    </div>

    <!-- Main content -->
    <div class="flex-1 bg-gray-100">
        <!-- Header -->
        <header class="bg-white shadow-md px-4 py-2">
            <div class="flex justify-between items-center">
                <div class="text-xl font-semibold text-gray-800">Admin Dashboard</div>
                <div>
                    <!-- User Info / Logout button -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-blue-500 hover:underline">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main content section -->
        <div class="p-8">
            <h2 class="text-2xl font-semibold mb-6">Reports Overview (Dummy Page)</h2>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-medium text-gray-800">Pending Reports (Dummy Data)</h3>
                <div class="mt-4">
                    <!-- Dummy Report List (static content for now) -->
                    <div class="border-b border-gray-300 py-2">
                        <p><strong>Unsafe Condition Report #1</strong></p>
                        <p>Status: Pending</p>
                    </div>
                    <div class="border-b border-gray-300 py-2">
                        <p><strong>Unsafe Act Report #2</strong></p>
                        <p>Status: Pending</p>
                    </div>
                    <div class="border-b border-gray-300 py-2">
                        <p><strong>Unsafe Act Report #3</strong></p>
                        <p>Status: Pending</p>
                    </div>
                </div>
            </div>

            <!-- Send Warning Letter Section -->
            <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                <h3 class="text-xl font-medium text-gray-800">Send Warning Letters (Dummy Functionality)</h3>
                <p class="mt-4">Select reports to send warning letters (this is just a dummy functionality for now).</p>
                <button class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Send All Warning Letters</button>
            </div>

            <!-- Admin Settings Section -->
            <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                <h3 class="text-xl font-medium text-gray-800">Admin Settings (Dummy Page)</h3>
                <p class="mt-4">Manage your admin settings here (this is just a dummy functionality for now).</p>
                <button class="mt-4 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Manage Settings</button>
            </div>
        </div>
        
    </div>
</div>

<!-- Footer (optional) -->
<footer class="bg-gray-800 text-white py-4">
    <div class="text-center">
        &copy; {{ date('Y') }} Admin Panel. All rights reserved.
    </div>
</footer>
@endsection
