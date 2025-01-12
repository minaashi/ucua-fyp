@extends('layouts.app')

@section('content')
    <h1>All Reports</h1>

    <!-- Link to create new report -->
    <a href="{{ route('admin.reports.create') }}" class="btn btn-primary">Create New Report</a>

    <br><br>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                <tr>
                    <td>{{ $report->title }}</td>
                    <td>{{ $report->description }}</td>
                    <td><a href="{{ route('admin.reports.show', $report->id) }}">View</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
