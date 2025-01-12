@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Submit a New Report</h2>
        <form action="{{ route('reports.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="report_title">Title:</label>
                <input type="text" id="report_title" name="report_title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" class="form-control" required></textarea>
            </div>
            <!-- No category field here -->
            <button type="submit" class="btn btn-primary">Submit Report</button>
        </form>
    </div>
@endsection
