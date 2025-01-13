@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <h2 class="text-2xl font-bold mb-6">Track Report Status</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Pending Reports -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Pending Reports</h3>
            @if($pendingReports->isEmpty())
                <p class="text-gray-500">No pending reports.</p>
            @else
                <div class="space-y-4">
                    @foreach($pendingReports as $report)
                        <div class="border rounded-lg p-4 hover:bg-gray-50">
                            <h4 class="font-semibold text-gray-800">{{ $report->non_compliance_type }}</h4>
                            <p class="text-sm text-gray-600">Location: {{ $report->location }}</p>
                            <p class="text-sm text-gray-600">Status: Pending</p>
                            <p class="text-xs text-gray-500 mt-2">Submitted: {{ $report->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <!-- Solved Reports -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Solved Reports</h3>
            @if($solvedReports->isEmpty())
                <p class="text-gray-500">No solved reports.</p>
            @else
                <div class="space-y-4">
                    @foreach($solvedReports as $report)
                        <div class="border rounded-lg p-4 hover:bg-gray-50">
                            <h4 class="font-semibold text-gray-800">{{ $report->non_compliance_type }}</h4>
                            <p class="text-sm text-gray-600">Location: {{ $report->location }}</p>
                            <p class="text-sm text-green-600">Status: Solved</p>
                            <p class="text-xs text-gray-500 mt-2">Solved on: {{ $report->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
