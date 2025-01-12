<!-- resources/views/admin/dashboard.blade.php -->

@extends('layouts.app') 

@section('content')
    <div class="container">
        <h1>Admin Dashboard</h1>
         <!-- Link to the Reports Index Page -->
    <a href="{{ route('admin.reports.index') }}" class="btn btn-primary">View All Reports</a>
        <!-- List of reports to send warning letters -->
        <h3>Reports to send warning letters</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Report Title</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through reports -->
                @foreach($reports as $report)
                    <tr>
                        <td>{{ $report->title }}</td>
                        <td>{{ $report->description }}</td>
                        <td>{{ $report->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Form to send warning letters -->
        <form action="{{ route('admin.sendWarningLetters') }}" method="POST">
            @csrf  <!-- CSRF protection for POST requests -->
            <button type="submit" class="btn btn-danger">Send Warning Letters</button>
        </form>
    </div>
@endsection
