@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Report Details</h1>
    
    <p><strong>Title:</strong> {{ $report->title }}</p>
    <p><strong>Description:</strong> {{ $report->description }}</p>
    <p><strong>Category:</strong> {{ $report->category }}</p>
    <p><strong>Status:</strong> {{ $report->status }}</p>
    
    <form action="{{ route('reports.categorize', $report->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="category">Change Category</label>
            <select id="category" name="category" class="form-control">
                <option value="unsafe_act" {{ $report->category == 'unsafe_act' ? 'selected' : '' }}>Unsafe Act</option>
                <option value="unsafe_condition" {{ $report->category == 'unsafe_condition' ? 'selected' : '' }}>Unsafe Condition</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-secondary">Change Category</button>
    </form>
    
    <!-- You can add more functionalities like sending notifications here -->
    
</div>
@endsection
