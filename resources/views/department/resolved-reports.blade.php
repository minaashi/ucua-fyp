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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resolved Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resolution Time</th>
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
                                        @if($report->resolved_at)
                                            {{ $report->resolved_at->format('M d, Y') }}
                                            <div class="text-xs text-gray-500">
                                                {{ $report->resolved_at->format('h:i A') }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">Not resolved</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($report->created_at && $report->resolved_at)
                                            @php
                                                $resolutionTime = round($report->created_at->diffInDays($report->resolved_at));
                                            @endphp
                                            @if($resolutionTime == 0)
                                                <span class="text-green-600 font-semibold">Same day</span>
                                            @elseif($resolutionTime <= 3)
                                                <span class="text-green-600">{{ $resolutionTime }} {{ $resolutionTime == 1 ? 'day' : 'days' }}</span>
                                            @elseif($resolutionTime <= 7)
                                                <span class="text-yellow-600">{{ $resolutionTime }} days</span>
                                            @else
                                                <span class="text-red-600">{{ $resolutionTime }} days</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex flex-wrap gap-2">
                                            <!-- Review Button -->
                                            <a href="{{ route('department.report.show', $report->id) }}"
                                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full hover:bg-blue-200 transition-colors duration-200"
                                               title="Review Report Details">
                                                <i class="fas fa-eye mr-1"></i>
                                                Review
                                            </a>

                                            <!-- Export Button -->
                                            <button onclick="exportReport({{ $report->id }}, 'RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}')"
                                                    class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full hover:bg-gray-200 transition-colors duration-200"
                                                    title="Export Report">
                                                <i class="fas fa-download mr-1"></i>
                                                Export
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
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

@endsection

@push('scripts')
<script>
function exportReport(reportId, reportCode) {
    // Show loading state
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Exporting...';
    button.disabled = true;

    // Create a form to submit the export request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("department.report.export", ":id") }}'.replace(':id', reportId);

    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);

    // Add to body and submit
    document.body.appendChild(form);
    form.submit();

    // Reset button after a delay
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        document.body.removeChild(form);
    }, 2000);
}
</script>
@endpush