@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-red-50 py-10">
    <div class="max-w-5xl mx-auto px-4">
        <div class="bg-red-400 rounded-t-lg px-8 py-6 shadow-md flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-red-900 mb-1 flex items-center">
                    <i class="fas fa-bell mr-2 animate-bounce"></i> Reminders
                </h1>
                <p class="text-red-900 text-base">Send and track reminders for urgent and overdue reports.</p>
            </div>
            <div>
                <button onclick="sendReminder()" class="bg-red-700 hover:bg-red-800 text-white font-semibold px-4 py-2 rounded shadow transition">+ Send New Reminder</button>
            </div>
        </div>
        <div class="bg-white rounded-b-lg shadow-md p-8 mt-2">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                    <h3 class="text-lg font-semibold text-red-800">Total Reminders</h3>
                    <p class="text-3xl font-bold text-red-600">{{ $totalReminders }}</p>
                </div>
                <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                    <h3 class="text-lg font-semibold text-red-800">Recent Reminders</h3>
                    <p class="text-3xl font-bold text-red-600">{{ $recentReminders }}</p>
                    <p class="text-sm text-red-500">Last 7 days</p>
                </div>
            </div>

            <h2 class="text-xl font-semibold text-red-800 mb-4">Recent Reminders</h2>
            <table class="min-w-full divide-y divide-red-200">
                <thead class="bg-red-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Report</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Sent By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-red-100">
                    @forelse($reminders as $reminder)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-900 font-bold">#{{ $reminder->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-900">
                            @if($reminder->report)
                                <a href="{{ route('ucua.report.show', $reminder->report->id) }}" class="hover:text-red-700">
                                    Report #{{ $reminder->report->id }}
                                </a>
                            @else
                                <span class="text-gray-500">No Report</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $reminder->type === 'gentle' ? 'bg-green-100 text-green-800' : 
                                   ($reminder->type === 'urgent' ? 'bg-orange-100 text-orange-800' : 
                                    'bg-red-100 text-red-800') }}">
                                {{ ucfirst($reminder->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-red-900">{{ Str::limit($reminder->message ?? 'No message', 50) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-900">{{ $reminder->sentBy->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-900">{{ $reminder->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No reminders found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $reminders->links() }}
            </div>
        </div>
    </div>
</div>

@include('ucua-officer.partials.send-reminder-modal')
@endsection 