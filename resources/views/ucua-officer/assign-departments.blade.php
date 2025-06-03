@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-10">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-blue-800 rounded-t-lg px-8 py-6 shadow-md">
            <h1 class="text-2xl font-bold text-white mb-1">Assign Case Of Reports</h1>
            <p class="text-blue-100 text-base">Please select a department and deadline for each unassigned report below.</p>
            <p class="text-blue-200 text-sm mt-2">
                <i class="fas fa-info-circle mr-1"></i>
                Showing reports with status 'pending' or 'review' that haven't been assigned to departments yet.
            </p>
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
                <div class="text-center py-8">
                    <div class="bg-gray-50 rounded-lg p-6 max-w-md mx-auto">
                        <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">No Reports Need Assignment</h3>
                        <p class="text-gray-500 mb-4">All reports have been assigned to departments or don't meet the criteria for assignment.</p>
                        <div class="text-sm text-gray-600 bg-white p-3 rounded border">
                            <p class="font-medium mb-2">Reports shown here must:</p>
                            <ul class="text-left space-y-1">
                                <li>• Have status 'pending' or 'review'</li>
                                <li>• Not be assigned to any department yet</li>
                            </ul>
                        </div>
                        <a href="{{ route('ucua.dashboard') }}" class="inline-flex items-center mt-4 text-blue-600 hover:text-blue-800">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </div>

                <!-- Debug Information -->
                @if(isset($allReports) && $allReports->count() > 0)
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-800 mb-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        Debug: All Reports Status ({{ $allReports->count() }} total)
                    </h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-blue-100">
                                    <th class="px-3 py-2 text-left">ID</th>
                                    <th class="px-3 py-2 text-left">Employee ID</th>
                                    <th class="px-3 py-2 text-left">Status</th>
                                    <th class="px-3 py-2 text-left">Has Department?</th>
                                    <th class="px-3 py-2 text-left">Department Name</th>
                                    <th class="px-3 py-2 text-left">Can Assign?</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allReports as $debugReport)
                                <tr class="border-b border-blue-200">
                                    <td class="px-3 py-2">{{ $debugReport['id'] }}</td>
                                    <td class="px-3 py-2">{{ $debugReport['employee_id'] }}</td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-1 rounded text-xs {{ $debugReport['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($debugReport['status'] === 'review' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ $debugReport['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($debugReport['has_handling_department'])
                                            <span class="text-green-600"><i class="fas fa-check"></i> Yes</span>
                                        @else
                                            <span class="text-red-600"><i class="fas fa-times"></i> No</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">{{ $debugReport['handling_department_name'] ?? 'None' }}</td>
                                    <td class="px-3 py-2">
                                        @if(!$debugReport['has_handling_department'] && in_array($debugReport['status'], ['pending', 'review']))
                                            <span class="text-green-600 font-medium"><i class="fas fa-check"></i> Yes</span>
                                        @else
                                            <span class="text-red-600"><i class="fas fa-times"></i> No</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-blue-600 mt-2">
                        <i class="fas fa-lightbulb mr-1"></i>
                        Reports can be assigned if they have status 'pending' or 'review' AND don't have a handling department assigned yet.
                    </p>
                </div>
                @endif
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($reports as $report)
                    <div class="border border-gray-200 rounded-lg p-6 shadow-sm flex flex-col justify-between bg-gray-50 hover:shadow-lg transition-shadow">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs text-gray-400">Report ID: <span class="font-semibold text-gray-700">#{{ $report->id }}</span></span>
                                <span class="inline-block px-2 py-1 text-xs rounded 
                                    {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($report->status === 'review' ? 'bg-blue-100 text-blue-800' : 
                                        'bg-gray-100 text-gray-800') }} font-semibold">
                                    {{ ucfirst($report->status) }}
                                </span>
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
                                    <select id="department-{{ $report->id }}" name="department_id" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs text-gray-600 mb-1" for="deadline-{{ $report->id }}">Deadline</label>
                                    <input id="deadline-{{ $report->id }}" type="date" name="deadline" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="block text-xs text-gray-600 mb-1" for="assignment_remark-{{ $report->id }}">Assignment Remark (Optional)</label>
                                <textarea id="assignment_remark-{{ $report->id }}" name="assignment_remark" rows="2" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Add any notes or comments about this assignment..."></textarea>
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