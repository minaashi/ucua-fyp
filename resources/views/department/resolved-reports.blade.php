@extends('layouts.app')

@section('content')
<div class="min-h-screen flex">
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
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600">
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
                       class="flex items-center px-4 py-2 {{ Request::routeIs('department.resolved-reports') ? 'bg-green-100 text-green-800' : 'text-gray-600' }} hover:bg-green-100 hover:text-green-800">
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
                <h1 class="text-2xl font-bold">Resolved Reports for {{ auth()->guard('department')->user()->name }}</h1>
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
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Resolved Reports</h2>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resolution Notes</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resolved At</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($reports as $report)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($report->status === 'resolved' ? 'bg-green-100 text-green-800' : 
                                                'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->resolution_notes ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->resolved_at ? $report->resolved_at->format('Y-m-d H:i') : 'N/A' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No resolved reports found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     {{ $reports->links() }}
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
    // In a real application, you'd fetch report details via AJAX here
    // and populate the modal fields.
}

// You might not need resolveReport and addRemarks functions on the resolved reports page
// unless you want to allow re-opening or adding post-resolution remarks.
// Keeping them commented out or removing them might be appropriate.
/*
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
*/
</script>
@endpush 