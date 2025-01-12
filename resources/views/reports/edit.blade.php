@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Report</h1>
    
    <form action="{{ route('reports.update', $report->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ $report->title }}" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" required>{{ $report->description }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" class="form-control" required>
                <option value="unsafe_act" {{ $report->category == 'unsafe_act' ? 'selected' : '' }}>Unsafe Act</option>
                <option value="unsafe_condition" {{ $report->category == 'unsafe_condition' ? 'selected' : '' }}>Unsafe Condition</option>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
@endsection
