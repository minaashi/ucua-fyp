@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Back to Dashboard Button -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6">Report Tracking</h1>

            <!-- Report Card -->
            @forelse($reports as $report)
            <div class="bg-white border rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <div class="flex items-center space-x-2 mb-1">
                            <h2 class="text-xl font-semibold">Report {{ $report->display_id }}</h2>
                            @if($report->is_anonymous)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-user-secret mr-1"></i>
                                    Anonymous
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-600">{{ Str::limit($report->description, 100) }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($report->status == 'pending')
                                bg-yellow-100 text-yellow-800
                            @elseif($report->status == 'review')
                                bg-blue-100 text-blue-800
                            @elseif($report->status == 'in_progress')
                                bg-orange-100 text-orange-800
                            @elseif($report->status == 'resolved')
                                bg-green-100 text-green-800
                            @elseif($report->status == 'rejected')
                                bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                        </span>
                        <a href="{{ route('reports.details', $report) }}"
                           class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-eye mr-1"></i>
                            View Details
                        </a>
                    </div>
                </div>

                <!-- Progress Tracker -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <!-- Submitted -->
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                            <p class="text-sm mt-2 font-medium">Submitted</p>
                            <p class="text-xs text-gray-500">{{ $report->created_at->format('M d') }}</p>
                        </div>
                        <div class="flex-1 h-1 {{ $report->status != 'pending' ? 'bg-green-500' : 'bg-gray-300' }} mx-2"></div>

                        <!-- Under Review -->
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-12 h-12 {{ ($report->status == 'review' || $report->status == 'in_progress' || $report->status == 'resolved' || $report->status == 'rejected') ? 'bg-blue-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center">
                                <i class="fas fa-search text-white"></i>
                            </div>
                            <p class="text-sm mt-2 font-medium">Under Review</p>
                            @if($report->status == 'review' || $report->status == 'in_progress' || $report->status == 'resolved')
                                <p class="text-xs text-gray-500">In progress</p>
                            @else
                                <p class="text-xs text-gray-500">Pending</p>
                            @endif
                        </div>
                        <div class="flex-1 h-1 {{ ($report->status == 'in_progress' || $report->status == 'resolved' || $report->status == 'rejected') ? 'bg-orange-500' : 'bg-gray-300' }} mx-2"></div>

                        <!-- Investigation -->
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-12 h-12 {{ ($report->status == 'in_progress' || $report->status == 'resolved') ? 'bg-orange-500' : ($report->status == 'rejected' ? 'bg-red-500' : 'bg-gray-300') }} rounded-full flex items-center justify-center">
                                @if($report->status == 'rejected')
                                    <i class="fas fa-times text-white"></i>
                                @else
                                    <i class="fas fa-clipboard-check text-white"></i>
                                @endif
                            </div>
                            <p class="text-sm mt-2 font-medium">
                                {{ $report->status == 'rejected' ? 'Rejected' : 'Investigation' }}
                            </p>
                            @if($report->handlingDepartment)
                                <p class="text-xs text-gray-500">{{ $report->handlingDepartment->name }}</p>
                            @else
                                <p class="text-xs text-gray-500">Pending assignment</p>
                            @endif
                        </div>
                        <div class="flex-1 h-1 {{ $report->status == 'resolved' ? 'bg-green-500' : 'bg-gray-300' }} mx-2"></div>

                        <!-- Resolved -->
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-12 h-12 {{ $report->status == 'resolved' ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <p class="text-sm mt-2 font-medium">Resolved</p>
                            @if($report->status == 'resolved' && $report->resolved_at)
                                <p class="text-xs text-gray-500">{{ $report->resolved_at->format('M d') }}</p>
                            @else
                                <p class="text-xs text-gray-500">Pending</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Report Details -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold mb-2">Location</h3>
                        <p class="text-gray-600">{{ $report->location }}</p>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-2">Last Updated</h3>
                        <p class="text-gray-600">{{ $report->updated_at->format('Y-m-d') }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <h3 class="font-semibold mb-2">Description</h3>
                    <p class="text-gray-600">{{ $report->description }}</p>
                </div>

                <!-- Status History -->
                @if($report->statusHistory && $report->statusHistory->count() > 0)
                <div class="mt-6">
                    <h3 class="font-semibold mb-3 text-gray-800">
                        <i class="fas fa-history mr-2 text-blue-600"></i>Progress Updates
                    </h3>
                    <div class="space-y-3">
                        @foreach($report->statusHistory->take(5) as $history)
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                @if($history->new_status == 'resolved')
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-white text-sm"></i>
                                    </div>
                                @elseif($history->new_status == 'in_progress')
                                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-cog text-white text-sm"></i>
                                    </div>
                                @elseif($history->new_status == 'review')
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-search text-white text-sm"></i>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-circle text-white text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">
                                    Status changed to: <span class="capitalize">{{ str_replace('_', ' ', $history->new_status) }}</span>
                                </p>
                                @if($history->department)
                                    <p class="text-xs text-gray-600">
                                        by {{ $history->department->name }}
                                    </p>
                                @elseif($history->changedBy)
                                    <p class="text-xs text-gray-600">
                                        by {{ $history->changedBy->name }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-500">
                                    {{ $history->created_at->format('M d, Y g:i A') }}
                                </p>
                                @if($history->reason)
                                    <p class="text-sm text-gray-600 mt-1">{{ $history->reason }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Resolution Notes -->
                @if($report->status == 'resolved' && $report->resolution_notes)
                <div class="mt-6">
                    <h3 class="font-semibold mb-3 text-green-700">
                        <i class="fas fa-check-circle mr-2 text-green-600"></i>Final Resolution
                    </h3>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm font-medium text-green-800">
                                Resolution by: {{ $report->handlingDepartment->name ?? 'Department' }}
                            </p>
                            <span class="text-sm text-green-600">
                                {{ $report->resolved_at->format('M d, Y g:i A') }}
                            </span>
                        </div>
                        <div class="bg-white p-3 rounded border border-green-100">
                            <p class="text-gray-700">{{ $report->resolution_notes }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($report->status == 'rejected' && $report->remarks)
                <div class="mt-6">
                    <h3 class="font-semibold mb-3 text-red-700">
                        <i class="fas fa-times-circle mr-2 text-red-600"></i>Rejection Details
                    </h3>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-red-700">{{ $report->remarks }}</p>
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-8">
                <p class="text-gray-500">No reports found.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
