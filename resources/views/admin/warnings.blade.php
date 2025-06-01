@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <header class="bg-blue-800 text-white p-4 shadow-md rounded mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Warning Letters Management</h1>
            <button class="bg-yellow-500 text-white px-4 py-2 rounded shadow hover:bg-yellow-600 flex items-center" data-bs-toggle="modal" data-bs-target="#sendWarningModal">
                <i class="fas fa-envelope mr-2"></i> Send New Warning
            </button>
        </div>
    </header>
    <!-- Warning Letters Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700">Total Warnings</h3>
            <p class="text-3xl font-bold text-yellow-500 mt-2">{{ $totalWarnings }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700">Pending</h3>
            <p class="text-3xl font-bold text-red-500 mt-2">{{ $pendingWarnings }}</p>
        </div>
    </div>
    <!-- Warning Letters Table -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Recent Warning Letters</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Sent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($warnings as $warning)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">WL-{{ str_pad($warning->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $warning->suggestedBy ? $warning->suggestedBy->name : 'Unknown' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">RPT-{{ str_pad($warning->report->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $warning->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                       ($warning->status == 'approved' ? 'bg-green-100 text-green-800' :
                                        ($warning->status == 'rejected' ? 'bg-red-100 text-red-800' :
                                         'bg-blue-100 text-blue-800')) }}">
                                    {{ ucfirst($warning->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $warning->sent_at ? $warning->sent_at->format('M d, Y') : 'Not sent' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <button class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600" data-bs-toggle="modal" data-bs-target="#viewWarningModal{{ $warning->id }}">
                                        View
                                    </button>
                                    @if($warning->status != 'sent')
                                        <form action="{{ route('admin.warnings.resend', $warning) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Resend</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <!-- View Warning Modal can be refactored similarly if needed -->
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">No warning letters found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $warnings->links() }}
        </div>
    </div>
    <!-- Send Warning Modal can be refactored similarly if needed -->
@endsection 