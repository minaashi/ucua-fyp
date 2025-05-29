@extends('layouts.app')

@section('content')
<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4 border-b">
            <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" class="h-12 mx-auto">
            <h2 class="text-xl font-bold text-center text-gray-800 mt-2">UCUA Officer Dashboard</h2>
        </div>
        
        <nav class="mt-6">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('ucua.dashboard') }}" 
                       class="flex items-center px-4 py-2 {{ Request::routeIs('ucua.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Report Overview</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('ucua.assign-departments-page') }}" 
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-tasks w-5"></i>
                        <span>Assign Departments</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('ucua.warnings') }}" 
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-yellow-100 hover:text-yellow-800 transition-colors duration-200">
                        <i class="fas fa-exclamation-triangle w-5 animate-pulse text-yellow-500"></i>
                        <span class="ml-2 font-semibold">Warning Letters</span>
                        <span class="ml-2 bg-yellow-200 text-yellow-800 text-xs px-2 py-1 rounded-full">NEW</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('ucua.reminders') }}" 
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-red-100 hover:text-red-700 transition-colors duration-200">
                        <i class="fas fa-bell w-5 animate-bounce text-red-500"></i>
                        <span class="ml-2 font-semibold">Reminders</span>
                        <span class="ml-2 bg-red-200 text-red-700 text-xs px-2 py-1 rounded-full">ALERTS</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <header class="bg-blue-800 text-white p-4 shadow-md">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">UCUA Officer Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span>Welcome, {{ auth()->user()->name }}</span>
                    <form action="{{ route('ucua.logout') }}" method="POST" class="inline">
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
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Reports Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500">
                    <h3 class="text-lg font-semibold text-gray-700">Total Reports</h3>
                    <p class="text-3xl font-bold text-blue-500 mt-2">{{ $totalReports }}</p>
                    <p class="text-sm text-gray-500 mt-1">All reports</p>
                </div>

                <!-- Pending Reports Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-yellow-500">
                    <h3 class="text-lg font-semibold text-gray-700">Pending Reports</h3>
                    <p class="text-3xl font-bold text-yellow-500 mt-2">{{ $pendingReports }}</p>
                    <p class="text-sm text-gray-500 mt-1">Awaiting assignment</p>
                </div>

                <!-- Resolved Cases Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500">
                    <h3 class="text-lg font-semibold text-gray-700">Resolved Cases</h3>
                    <p class="text-3xl font-bold text-green-500 mt-2">{{ $resolvedReports }}</p>
                    <p class="text-sm text-gray-500 mt-1">Successfully handled</p>
                </div>

                <!-- Deadline Reminders Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-500">
                    <h3 class="text-lg font-semibold text-gray-700">Deadline Reminders</h3>
                    <p class="text-3xl font-bold text-red-500 mt-2">{{ $deadlineReports->count() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Reports nearing deadline</p>
                </div>
            </div>

            <!-- Recent Reports Table -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Recent Reports</h2>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentReports as $report)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($report->status === 'resolved' ? 'bg-green-100 text-green-800' : 
                                                'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->handling_department ?? $report->department ?? 'Not Assigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->deadline ? $report->deadline->format('Y-m-d') : 'No Deadline' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900" onclick="assignDepartment({{ $report->id }})">
                                            <i class="fas fa-building"></i>
                                        </button>
                                        <button class="text-green-600 hover:text-green-900" onclick="addRemarks({{ $report->id }})">
                                            <i class="fas fa-comment"></i>
                                        </button>
                                        <button class="text-yellow-600 hover:text-yellow-900" onclick="suggestWarning({{ $report->id }})">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" onclick="sendReminder({{ $report->id }})">
                                            <i class="fas fa-bell"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No reports found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Deadline Reminders Section -->
            <div class="mt-6 bg-white rounded-lg shadow-md">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Deadline Reminders</h2>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        @forelse($deadlineReports as $report)
                        <div class="flex items-center justify-between bg-red-50 p-4 rounded-lg">
                            <div>
                                <h3 class="font-semibold text-red-800">Report #{{ $report->id }}</h3>
                                <p class="text-sm text-red-600">Deadline: {{ $report->deadline->format('Y-m-d') }}</p>
                            </div>
                            <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                    onclick="sendReminder({{ $report->id }})">
                                Send Reminder
                            </button>
                        </div>
                        @empty
                        <p class="text-center text-gray-500">No reports nearing deadline</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modals for actions -->
@include('ucua-officer.partials.assign-department-modal')
@include('ucua-officer.partials.add-remarks-modal')
@include('ucua-officer.partials.suggest-warning-modal')
@include('ucua-officer.partials.send-reminder-modal')

@endsection

@push('scripts')
<script>
function assignDepartment(reportId) {
    // Show assign department modal
    $('#assignDepartmentModal').modal('show');
    $('#reportId').val(reportId);
}

function addRemarks(reportId) {
    // Show add remarks modal
    $('#addRemarksModal').modal('show');
    $('#reportId').val(reportId);
}

function suggestWarning(reportId) {
    // Show suggest warning modal
    $('#suggestWarningModal').modal('show');
    $('#reportId').val(reportId);
}

function sendReminder(reportId) {
    // Show send reminder modal
    $('#sendReminderModal').modal('show');
    $('#reportId').val(reportId);
}
</script>
@endpush 