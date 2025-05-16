@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-yellow-50 py-10">
    <div class="max-w-5xl mx-auto px-4">
        <div class="bg-yellow-400 rounded-t-lg px-8 py-6 shadow-md flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-yellow-900 mb-1 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2 animate-pulse"></i> Warning Letters
                </h1>
                <p class="text-yellow-900 text-base">Review and manage all warning letters issued to port workers.</p>
            </div>
            <div>
                <button onclick="suggestWarning()" class="bg-yellow-700 hover:bg-yellow-800 text-white font-semibold px-4 py-2 rounded shadow transition">+ Issue New Warning</button>
            </div>
        </div>
        <div class="bg-white rounded-b-lg shadow-md p-8 mt-2">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                    <h3 class="text-lg font-semibold text-yellow-800">Total Warnings</h3>
                    <p class="text-3xl font-bold text-yellow-600">{{ $totalWarnings }}</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                    <h3 class="text-lg font-semibold text-yellow-800">Pending Warnings</h3>
                    <p class="text-3xl font-bold text-yellow-600">{{ $pendingWarnings }}</p>
                </div>
            </div>

            <h2 class="text-xl font-semibold text-yellow-800 mb-4">Recent Warning Letters</h2>
            <table class="min-w-full divide-y divide-yellow-200">
                <thead class="bg-yellow-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Report</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Suggested By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-700 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-yellow-100">
                    @forelse($warnings as $warning)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-900 font-bold">#{{ $warning->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-900">
                            <a href="{{ route('reports.show', $warning->report->id) }}" class="hover:text-yellow-700">
                                Report #{{ $warning->report->id }}
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
                        <td class="px-6 py-4 text-sm text-yellow-900">{{ Str::limit($warning->reason, 50) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-900">{{ $warning->suggestedBy->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-900">{{ $warning->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No warning letters found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $warnings->links() }}
            </div>
        </div>
    </div>
</div>

@include('ucua-officer.partials.suggest-warning-modal')
@endsection 