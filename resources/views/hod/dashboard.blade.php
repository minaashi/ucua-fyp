@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-red-50 py-10">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-red-400 rounded-t-lg px-8 py-6 shadow-md">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-red-900 mb-1 flex items-center">
                        <i class="fas fa-user-tie mr-2"></i> HOD Dashboard
                    </h1>
                    <p class="text-red-900 text-base">{{ $department->name }} Department</p>
                </div>
                <div class="text-right">
                    <div class="bg-red-500 text-white px-4 py-2 rounded-lg">
                        <div class="text-2xl font-bold">{{ $totalReports }}</div>
                        <div class="text-sm">Total Reports</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="bg-white rounded-b-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Pending Reports -->
                <div class="bg-orange-100 border-l-4 border-orange-500 p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-orange-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Pending Reports</h3>
                            <p class="text-3xl font-bold text-orange-600">{{ $pendingReports }}</p>
                        </div>
                    </div>
                </div>

                <!-- Resolved Reports -->
                <div class="bg-green-100 border-l-4 border-green-500 p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Resolved Reports</h3>
                            <p class="text-3xl font-bold text-green-600">{{ $resolvedReports }}</p>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="bg-blue-100 border-l-4 border-blue-500 p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-bell text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Unread Notifications</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ $unreadNotificationsCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notifications Section -->
        @if($notifications->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-bell mr-2 text-red-500"></i>
                    Recent Notifications
                </h3>
                <div class="flex space-x-2">
                    @if($unreadNotificationsCount > 0)
                    <form action="{{ route('hod.notifications.mark-all-read') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition-colors">
                            Mark All Read
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('hod.notifications') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        View All
                    </a>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($notifications->take(3) as $notification)
                <div class="border-l-4 {{ $notification->read_at ? 'border-gray-300 bg-gray-50' : 'border-red-500 bg-red-50' }} p-4 rounded-r-lg">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            @if(isset($notification->data['type']) && $notification->data['type'] === 'reminder')
                            <div class="flex items-center mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $notification->data['reminder_type'] === 'gentle' ? 'bg-green-100 text-green-800' :
                                       ($notification->data['reminder_type'] === 'urgent' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($notification->data['reminder_type']) }} Reminder
                                </span>
                                <span class="ml-2 text-sm text-gray-500">{{ $notification->data['reminder_formatted_id'] ?? '' }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-900 mb-1">
                                Report: {{ $notification->data['report_formatted_id'] ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600 mb-2">{{ $notification->data['report_description'] ?? '' }}</p>
                            @if(isset($notification->data['message']) && $notification->data['message'])
                            <p class="text-sm text-gray-700 italic">"{{ $notification->data['message'] }}"</p>
                            @endif
                            @else
                            <p class="text-sm font-medium text-gray-900">{{ $notification->data['message'] ?? 'New notification' }}</p>
                            @endif
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                            @if(!$notification->read_at)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                New
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Reports -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-file-alt mr-2 text-red-500"></i>
                Recent Reports
            </h3>
            
            @if($recentReports->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentReports as $report)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $report->display_id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ Str::limit($report->description, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $report->status === 'resolved' ? 'bg-green-100 text-green-800' : 
                                       ($report->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $report->deadline ? $report->deadline->format('d/m/Y') : 'Not set' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('hod.report.show', $report) }}" class="text-blue-600 hover:text-blue-900">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 text-center py-8">No reports assigned to your department yet.</p>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ route('hod.pending-reports') }}" class="bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition-colors">
                <i class="fas fa-clock mr-2"></i>View Pending Reports
            </a>
            <a href="{{ route('hod.notifications') }}" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors">
                <i class="fas fa-bell mr-2"></i>View All Notifications
            </a>
        </div>
    </div>
</div>
@endsection
