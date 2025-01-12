@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Track Report Status</h3>
    
    <div class="row">
        <!-- Pending Reports -->
        <div class="col-md-6">
            <h5>Pending Reports</h5>
            @if($pendingReports->isEmpty())
                <p>No pending reports.</p>
            @else
                @foreach($pendingReports as $report)
                    <div class="card mb-3">
                        <div class="card-header">{{ $report->title }}</div>
                        <div class="card-body">
                            <p>Status: Pending</p>
                            <p>{{ $report->description }}</p>
                            <p>Created at: {{ $report->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        
        <!-- Solved Reports -->
        <div class="col-md-6">
            <h5>Solved Reports</h5>
            @if($solvedReports->isEmpty())
                <p>No solved reports.</p>
            @else
                @foreach($solvedReports as $report)
                    <div class="card mb-3">
                        <div class="card-header">{{ $report->title }}</div>
                        <div class="card-body">
                            <p>Status: Solved</p>
                            <p>{{ $report->description }}</p>
                            <p>Created at: {{ $report->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
