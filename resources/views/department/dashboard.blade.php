@extends('layouts.app')

@section('content')
<div class="min-h-screen flex">
    {{-- Removed the first sidebar --}}

    <!-- Department Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4 border-b">
            <img src="{{ asset('images/ucua-logo.png') }}" alt="UCUA Logo" class="h-12 mx-auto">
            <h2 class="text-xl font-bold text-center text-gray-800 mt-2">{{ auth()->guard('department')->user()->name }} Dashboard</h2>
        </div>

        <nav class="mt-6">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('department.dashboard') }}"
                       class="flex items-center px-4 py-2 {{ Request::routeIs('department.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }} hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Report Overview</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('department.pending-reports') }}"
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-yellow-100 hover:text-yellow-800">
                        <i class="fas fa-clock w-5"></i>
                        <span>Pending Reports</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('department.resolved-reports') }}"
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-green-100 hover:text-green-800">
                        <i class="fas fa-check-circle w-5"></i>
                        <span>Resolved Reports</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <header class="bg-blue-800 text-white p-4 shadow-md">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">{{ auth()->guard('department')->user()->name }} Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span>Welcome, {{ auth()->guard('department')->user()->head_name }}</span>
                    <form action="{{ route('department.logout') }}" method="POST" class="inline">
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Reports Card -->
                <a href="{{ route('department.dashboard') }}" class="block">
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500 hover:shadow-lg transition-shadow">
                        <h3 class="text-lg font-semibold text-gray-700">Total Reports</h3>
                        <p class="text-3xl font-bold text-blue-500 mt-2">{{ $totalReports }}</p>
                        <p class="text-sm text-gray-500 mt-1">All reports assigned to department</p>
                    </div>
                </a>

                <!-- Pending Reports Card -->
                <a href="{{ route('department.pending-reports') }}" class="block">
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-yellow-500 hover:shadow-lg transition-shadow">
                        <h3 class="text-lg font-semibold text-gray-700">Pending Reports</h3>
                        <p class="text-3xl font-bold text-yellow-500 mt-2">{{ $pendingReports }}</p>
                        <p class="text-sm text-gray-500 mt-1">Awaiting resolution</p>
                    </div>
                </a>

                <!-- Resolved Reports Card -->
                <a href="{{ route('department.resolved-reports') }}" class="block">
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500 hover:shadow-lg transition-shadow">
                        <h3 class="text-lg font-semibold text-gray-700">Resolved Reports</h3>
                        <p class="text-3xl font-bold text-green-500 mt-2">{{ $resolvedReports }}</p>
                        <p class="text-sm text-gray-500 mt-1">Successfully handled</p>
                    </div>
                </a>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentReports as $report)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($report->status === 'resolved' ? 'bg-green-100 text-green-800' : 
                                                ($report->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                                'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->deadline ? $report->deadline->format('Y-m-d') : 'No Deadline' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900" onclick="viewReport({{ $report->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($report->status === 'pending')
                                        <button class="text-green-600 hover:text-green-900" onclick="resolveReport({{ $report->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        <button class="text-yellow-600 hover:text-yellow-900" onclick="addRemarks({{ $report->id }})">
                                            <i class="fas fa-comment"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No reports found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modals -->
@include('department.partials.view-report-modal')
@include('department.partials.resolve-report-modal')
@include('department.partials.add-remarks-modal')

@endsection

@push('scripts')
<script>
function viewReport(reportId) {
    // Show view report modal
    $('#viewReportModal').modal('show');
    $('#reportId').val(reportId);
}

function resolveReport(reportId) {
    // Show resolve report modal
    $('#resolveReportModal').modal('show');
    $('#reportId').val(reportId);
}

function addRemarks(reportId) {
    // Show add remarks modal
    $('#addRemarksModal').modal('show');
    $('#reportId').val(reportId);
}
</script>
@endpush 