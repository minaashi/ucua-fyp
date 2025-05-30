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
                       class="flex items-center px-4 py-2 {{ Request::routeIs('department.pending-reports') ? 'bg-yellow-100 text-yellow-800' : 'text-gray-600' }} hover:bg-yellow-100 hover:text-yellow-800">
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
                <h1 class="text-2xl font-bold">Pending Reports for {{ auth()->guard('department')->user()->name }}</h1>
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
                    <h2 class="text-xl font-semibold text-gray-800">Pending Reports</h2>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                                        {{ $report->deadline ? $report->deadline->format('Y-m-d') : 'No Deadline' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="relative inline-block text-left">
                                            <button type="button" 
                                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                    onclick="toggleActionsMenu({{ $report->id }})">
                                                Actions
                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <div id="actions-menu-{{ $report->id }}" 
                                                 class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1" role="menu" aria-orientation="vertical">
                                                    <button onclick="viewReport({{ $report->id }})"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                            role="menuitem">
                                                        <i class="fas fa-eye mr-2"></i> View Details
                                                    </button>
                                                    @if($report->status === 'pending')
                                                    <button onclick="resolveReport({{ $report->id }})"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                            role="menuitem">
                                                        <i class="fas fa-check mr-2"></i> Resolve Report
                                                    </button>
                                                    @endif
                                                    <button onclick="addRemarks({{ $report->id }})"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                            role="menuitem">
                                                        <i class="fas fa-comment mr-2"></i> Add Remarks
                                                    </button>
                                                    <button onclick="requestExtension({{ $report->id }})"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                            role="menuitem">
                                                        <i class="fas fa-clock mr-2"></i> Request Extension
                                                    </button>
                                                    <button onclick="assignToTeam({{ $report->id }})"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                            role="menuitem">
                                                        <i class="fas fa-users mr-2"></i> Assign to Team
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No pending reports found
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
function toggleActionsMenu(reportId) {
    const menu = document.getElementById(`actions-menu-${reportId}`);
    menu.classList.toggle('hidden');
    
    // Close other open menus
    document.querySelectorAll('[id^="actions-menu-"]').forEach(otherMenu => {
        if (otherMenu.id !== `actions-menu-${reportId}`) {
            otherMenu.classList.add('hidden');
        }
    });
}

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[id^="actions-menu-"]') && !event.target.closest('button')) {
        document.querySelectorAll('[id^="actions-menu-"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});

function viewReport(reportId) {
    $('#viewReportModal').modal('show');
    $('#reportId').val(reportId);
}

function resolveReport(reportId) {
    $('#resolveReportModal').modal('show');
    $('#reportId').val(reportId);
}

function addRemarks(reportId) {
    $('#addRemarksModal').modal('show');
    $('#reportId').val(reportId);
}

function requestExtension(reportId) {
    // TODO: Implement request extension functionality
    alert('Request extension functionality to be implemented');
}

function assignToTeam(reportId) {
    // TODO: Implement team assignment functionality
    alert('Team assignment functionality to be implemented');
}
</script>
@endpush 