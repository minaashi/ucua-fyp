@extends('layouts.app')

@section('content')
<div class="min-h-screen flex">
    <!-- HOD Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4 border-b">
            <img src="{{ asset('images/logo.png') }}" alt="Port Logo" class="h-12 mx-auto">
            <h2 class="text-xl font-bold text-center text-gray-800 mt-2">HOD Dashboard</h2>
        </div>

        <nav class="mt-6">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('hod.dashboard') }}"
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Report Overview</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('hod.pending-reports') }}"
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-yellow-100 hover:text-yellow-800">
                        <i class="fas fa-clock w-5"></i>
                        <span>Pending Reports</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('hod.resolved-reports') }}"
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-green-100 hover:text-green-800">
                        <i class="fas fa-check-circle w-5"></i>
                        <span>Resolved Reports</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('hod.notifications') }}"
                       class="flex items-center px-4 py-2 {{ Request::routeIs('hod.notifications') ? 'bg-red-50 text-red-600' : 'text-gray-600' }} hover:bg-red-100 hover:text-red-700 transition-colors duration-200">
                        <i class="fas fa-bell w-5"></i>
                        <span class="ml-2">Notifications</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('help.hod') }}"
                       class="flex items-center px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-question-circle w-5"></i>
                        <span>Help</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <header class="bg-blue-800 text-white p-4 shadow-md">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Notifications</h1>
                <div class="flex items-center space-x-4">
                    <span>Welcome, {{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-white hover:text-gray-200">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6 bg-gray-100">
            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <!-- Notifications Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-bell mr-3 text-red-500"></i>
                            All Notifications
                        </h2>
                        <p class="text-gray-600 mt-1">Stay updated with reminders and important messages</p>
                    </div>
                    <div class="flex space-x-3">
                        @if($notifications->where('read_at', null)->count() > 0)
                        <form action="{{ route('hod.notifications.mark-all-read') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                                <i class="fas fa-check-double mr-2"></i>
                                Mark All Read
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('hod.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6">
                    @if($notifications->count() > 0)
                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                        <div class="border-l-4 {{ $notification->read_at ? 'border-gray-300 bg-gray-50' : 'border-red-500 bg-red-50' }} p-6 rounded-r-lg transition-all duration-200 hover:shadow-md">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    @if(isset($notification->data['type']) && $notification->data['type'] === 'reminder')
                                    <!-- Reminder Notification -->
                                    <div class="flex items-center mb-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            {{ $notification->data['reminder_type'] === 'gentle' ? 'bg-green-100 text-green-800' : 
                                               ($notification->data['reminder_type'] === 'urgent' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                            <i class="fas fa-bell mr-2"></i>
                                            {{ ucfirst($notification->data['reminder_type']) }} Reminder
                                        </span>
                                        <span class="ml-3 text-sm text-gray-500 font-mono">{{ $notification->data['reminder_formatted_id'] ?? '' }}</span>
                                        @if(!$notification->read_at)
                                        <span class="ml-3 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-circle mr-1 text-blue-500" style="font-size: 6px;"></i>
                                            New
                                        </span>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            Report: {{ $notification->data['report_formatted_id'] ?? 'N/A' }}
                                        </h3>
                                        <p class="text-gray-700 mb-2">{{ $notification->data['report_description'] ?? '' }}</p>
                                        
                                        @if(isset($notification->data['report_location']))
                                        <p class="text-sm text-gray-600 mb-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            Location: {{ $notification->data['report_location'] }}
                                        </p>
                                        @endif
                                        
                                        @if(isset($notification->data['report_deadline']))
                                        <p class="text-sm text-gray-600 mb-2">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            Deadline: {{ $notification->data['report_deadline'] }}
                                        </p>
                                        @endif
                                    </div>

                                    @if(isset($notification->data['message']) && $notification->data['message'])
                                    <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mb-3">
                                        <p class="text-sm text-blue-800 italic">
                                            <i class="fas fa-quote-left mr-1"></i>
                                            "{{ $notification->data['message'] }}"
                                        </p>
                                        <p class="text-xs text-blue-600 mt-1">
                                            - {{ $notification->data['sent_by'] ?? 'UCUA Officer' }}
                                        </p>
                                    </div>
                                    @endif

                                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                                        <p class="text-sm text-yellow-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            {{ $notification->data['action_required'] ?? 'Please review and take appropriate action on this safety report.' }}
                                        </p>
                                    </div>
                                    @else
                                    <!-- Other Notification Types -->
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                        <span class="font-medium text-gray-900">General Notification</span>
                                        @if(!$notification->read_at)
                                        <span class="ml-3 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-circle mr-1 text-blue-500" style="font-size: 6px;"></i>
                                            New
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-700">{{ $notification->data['message'] ?? 'New notification received' }}</p>
                                    @endif
                                </div>
                                
                                <div class="text-right ml-6 flex-shrink-0">
                                    <p class="text-sm text-gray-500 mb-2">{{ $notification->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-400 mb-3">{{ $notification->created_at->format('h:i A') }}</p>
                                    
                                    <div class="flex flex-col space-y-2">
                                        @if(!$notification->read_at)
                                        <button onclick="markAsRead('{{ $notification->id }}')" 
                                                class="text-xs bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition-colors">
                                            Mark as Read
                                        </button>
                                        @else
                                        <span class="text-xs text-green-600 px-3 py-1 bg-green-100 rounded">
                                            <i class="fas fa-check mr-1"></i>
                                            Read
                                        </span>
                                        @endif
                                        
                                        @if(isset($notification->data['link']))
                                        <a href="{{ $notification->data['link'] }}" 
                                           class="text-xs bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600 transition-colors text-center">
                                            View Report
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                    @else
                    <div class="text-center py-12">
                        <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-500 mb-2">No Notifications</h3>
                        <p class="text-gray-400">You don't have any notifications yet.</p>
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-hide success/error messages
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 5000);

    setTimeout(function() {
        $('.alert-danger').fadeOut('slow');
    }, 7000);
});

// Mark notification as read
function markAsRead(notificationId) {
    $.ajax({
        url: `/hod/notifications/${notificationId}/mark-read`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            }
        },
        error: function() {
            alert('Failed to mark notification as read');
        }
    });
}
</script>
@endpush
