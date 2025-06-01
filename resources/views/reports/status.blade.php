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
                        <h2 class="text-xl font-semibold">{{ $report->non_compliance_type }}</h2>
                        <p class="text-gray-600">Report ID: RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm
                        @if($report->status == 'pending')
                            bg-yellow-100 text-yellow-800
                        @elseif($report->status == 'review')
                            bg-blue-100 text-blue-800
                        @elseif($report->status == 'resolved')
                            bg-green-100 text-green-800
                        @elseif($report->status == 'rejected')
                            bg-red-100 text-red-800
                        @endif">
                        @if($report->status == 'review')
                            Under Review
                        @else
                            {{ ucfirst($report->status) }}
                        @endif
                    </span>
                </div>

                <!-- Progress Tracker -->
                <div class="flex justify-between items-center mb-8">
                    <div class="flex-1 flex items-center">
                        <!-- Submitted -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                            <p class="text-sm mt-2">Submitted</p>
                        </div>
                        <div class="flex-1 h-1 {{ $report->status != 'pending' ? 'bg-green-500' : 'bg-gray-300' }} mx-2"></div>

                        <!-- Under Review -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 {{ ($report->status == 'review' || $report->status == 'in_progress' || $report->status == 'resolved' || $report->status == 'rejected') ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center">
                                <i class="fas fa-search text-white"></i>
                            </div>
                            <p class="text-sm mt-2">Under Review</p>
                        </div>
                        <div class="flex-1 h-1 {{ ($report->status == 'in_progress' || $report->status == 'resolved' || $report->status == 'rejected') ? 'bg-green-500' : 'bg-gray-300' }} mx-2"></div>

                        <!-- Investigation/Resolved/Rejected -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 {{ ($report->status == 'in_progress' || $report->status == 'resolved') ? 'bg-blue-500' : ($report->status == 'rejected' ? 'bg-red-500' : 'bg-gray-300') }} rounded-full flex items-center justify-center">
                                @if($report->status == 'resolved')
                                    <i class="fas fa-check text-white"></i>
                                @elseif($report->status == 'rejected')
                                    <i class="fas fa-times text-white"></i>
                                @else
                                    <i class="fas fa-clipboard-check text-white"></i>
                                @endif
                            </div>
                            <p class="text-sm mt-2">{{ ($report->status == 'resolved') ? 'Resolved' : (($report->status == 'rejected') ? 'Rejected' : 'Investigation') }}</p>
                        </div>

                         {{-- No line after the last step --}}
                         <div class="flex-1 h-1 bg-gray-300 mx-2 invisible"></div>

                        <!-- Action Taken (Placeholder for potential future steps) -->
                         <div class="flex flex-col items-center invisible">
                             <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                 <i class="fas fa-check text-white"></i>
                             </div>
                             <p class="text-sm mt-2">Action Taken</p>
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

                @if($report->status == 'rejected' && $report->remarks)
                <div class="mt-4">
                    <h3 class="font-semibold mb-2 text-red-700">Rejection Remarks</h3>
                    <p class="text-red-600">{{ $report->remarks }}</p>
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
