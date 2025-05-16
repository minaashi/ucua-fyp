@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-10">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-blue-800 rounded-t-lg px-8 py-6 shadow-md">
            <h1 class="text-2xl font-bold text-white mb-1">Assign Departments to Reports</h1>
            <p class="text-blue-100 text-base">Select a department and deadline for each pending report below.</p>
        </div>
        <div class="bg-white rounded-b-lg shadow-md p-8">
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if($reports->isEmpty())
                <p class="text-center text-gray-500 text-lg">No pending reports to assign.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($reports as $report)
                    <div class="border border-gray-200 rounded-lg p-6 shadow-sm flex flex-col justify-between bg-gray-50 hover:shadow-lg transition-shadow">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs text-gray-400">Report ID: <span class="font-semibold text-gray-700">#{{ $report->id }}</span></span>
                                <span class="inline-block px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800 font-semibold">Pending</span>
                            </div>
                            <div class="mb-2">
                                <span class="block text-sm text-gray-500">Employee ID:</span>
                                <span class="font-semibold text-gray-800">{{ $report->employee_id }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="block text-sm text-gray-500">Current Department:</span>
                                <span class="font-semibold text-blue-700">{{ $report->department }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="block text-sm text-gray-500">Description:</span>
                                <span class="text-gray-700">{{ $report->description }}</span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('ucua.assign-department') }}" class="mt-4 flex flex-col gap-3">
                            @csrf
                            <input type="hidden" name="report_id" value="{{ $report->id }}">
                            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                                <div class="flex-1">
                                    <label class="block text-xs text-gray-600 mb-1" for="department-{{ $report->id }}">Assign Department</label>
                                    <select id="department-{{ $report->id }}" name="department" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->name }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs text-gray-600 mb-1" for="deadline-{{ $report->id }}">Deadline</label>
                                    <input id="deadline-{{ $report->id }}" type="date" name="deadline" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                </div>
                            </div>
                            <button type="submit" class="mt-2 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition-colors shadow">Assign Department</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 