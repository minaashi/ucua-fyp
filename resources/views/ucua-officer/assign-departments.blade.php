@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-800 px-6 py-4">
                <h1 class="text-xl font-bold text-white">Assign Departments to Reports</h1>
                <p class="text-blue-100 text-sm mt-1">Select a department for each pending report.</p>
            </div>
            <div class="p-6">
                @if($reports->isEmpty())
                    <p class="text-center text-gray-500">No pending reports to assign.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Employee ID</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Assign To</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reports as $report)
                            <tr>
                                <td class="px-4 py-2">{{ $report->id }}</td>
                                <td class="px-4 py-2">{{ $report->employee_id }}</td>
                                <td class="px-4 py-2">{{ $report->department }}</td>
                                <td class="px-4 py-2">{{ $report->description }}</td>
                                <td class="px-4 py-2">
                                    <form method="POST" action="{{ route('ucua.assign-department') }}" class="flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="report_id" value="{{ $report->id }}">
                                        <select name="department" class="form-control rounded border-gray-300" required>
                                            <option value="">Select Department</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->name }}">{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="date" name="deadline" class="form-control rounded border-gray-300" required>
                                        <button type="submit" class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Assign</button>
                                    </form>
                                </td>
                                <td class="px-4 py-2">
                                    <!-- Optionally, add more actions here -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 