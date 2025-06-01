@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-yellow-50 py-10">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-yellow-400 rounded-t-lg px-8 py-6 shadow-md">
            <div>
                <h1 class="text-3xl font-bold text-yellow-900 mb-1 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Warning Letter Suggestions
                </h1>
                <p class="text-yellow-900 text-base">View and track your warning letter suggestions. To create new suggestions, go to the dashboard and select a report.</p>
            </div>
        </div>
        <div class="bg-white rounded-b-lg shadow-md p-8 mt-2">
            <!-- Basic Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-200 mr-4">
                            <i class="fas fa-exclamation-triangle text-yellow-700 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-800">Total Suggestions</h3>
                            <p class="text-3xl font-bold text-yellow-600">{{ $totalWarnings }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-orange-50 rounded-lg p-6 border border-orange-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-200 mr-4">
                            <i class="fas fa-clock text-orange-700 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-orange-800">Pending Review</h3>
                            <p class="text-3xl font-bold text-orange-600">{{ $pendingWarnings }}</p>
                        </div>
                    </div>
                </div>
            </div>



            <h2 class="text-xl font-semibold text-yellow-800 mb-4">Your Warning Letter Suggestions</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-yellow-200">
                    <thead class="bg-yellow-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Report</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-yellow-100">
                        @forelse($warnings as $warning)
                        <tr class="{{ $warning->status === 'pending' ? 'bg-yellow-25' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-900 font-bold">
                                {{ $warning->formatted_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-900">
                                <a href="{{ route('ucua.report.show', $warning->report->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    RPT-{{ str_pad($warning->report->id, 3, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $warning->type === 'minor' ? 'bg-yellow-100 text-yellow-800' :
                                       ($warning->type === 'moderate' ? 'bg-orange-100 text-orange-800' :
                                        'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($warning->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-yellow-900 max-w-xs">
                                <div class="truncate" title="{{ $warning->reason }}">
                                    {{ Str::limit($warning->reason, 40) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $warning->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                       ($warning->status === 'approved' ? 'bg-green-100 text-green-800' :
                                        ($warning->status === 'rejected' ? 'bg-red-100 text-red-800' :
                                         'bg-purple-100 text-purple-800')) }}">
                                    {{ ucfirst($warning->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-900">
                                {{ $warning->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="viewWarningDetails({{ $warning->id }})"
                                        class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full hover:bg-blue-200 transition-colors duration-200"
                                        title="View Warning Details">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-lg font-medium">No warning suggestions found</p>
                                    <p class="text-sm mt-1">Go to the dashboard to create warning suggestions for reports</p>
                                    <a href="{{ route('ucua.dashboard') }}" class="mt-3 bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                                        Go to Dashboard
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $warnings->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Warning Details Modal -->
<div class="modal fade" id="warningDetailsModal" tabindex="-1" role="dialog" aria-labelledby="warningDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-yellow-600 text-white">
                <h5 class="modal-title" id="warningDetailsModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Warning Letter Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" onclick="closeWarningModal()"></button>
            </div>
            <div class="modal-body" id="warningDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" onclick="closeWarningModal()">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// View warning details
function viewWarningDetails(warningId) {
    fetch(`/ucua/warnings/${warningId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('warningDetailsContent').innerHTML = data.html;
                $('#warningDetailsModal').modal('show');
            } else {
                alert('Error loading warning details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading warning details');
        });
}

// Close warning modal function
function closeWarningModal() {
    // Try Bootstrap 5 method first
    const modal = bootstrap?.Modal?.getInstance(document.getElementById('warningDetailsModal'));
    if (modal) {
        modal.hide();
    } else {
        // Fallback to Bootstrap 4 method
        $('#warningDetailsModal').modal('hide');
    }
}

// Also handle clicking outside the modal
$(document).ready(function() {
    $('#warningDetailsModal').on('click', function(e) {
        if (e.target === this) {
            closeWarningModal();
        }
    });
});
</script>
@endpush

@endsection