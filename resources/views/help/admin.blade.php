@extends('help.layout')

@section('title', 'Admin Help')
@section('page-title', 'Admin Help Center')
@section('page-subtitle', 'Complete guide for system administrators')
@section('dashboard-route', route('admin.dashboard'))
@section('search-route', route('help.admin.search'))

@section('help-navigation')
    @foreach($helpSections as $key => $section)
        <a href="#{{ $key }}" 
           class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors">
            <i class="{{ $section['icon'] }} w-5 mr-3"></i>
            {{ $section['title'] }}
        </a>
    @endforeach
@endsection

@section('help-content')
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-lg p-8 text-white mb-8">
        <div class="flex items-center">
            <div class="flex-1">
                <h2 class="text-3xl font-bold mb-2">Admin Control Center</h2>
                <p class="text-purple-100 text-lg">
                    Comprehensive guide for managing users, departments, and system settings in the UCUA safety reporting system.
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-user-shield text-6xl text-purple-200"></i>
            </div>
        </div>
    </div>

    <!-- Admin Dashboard Overview -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-tachometer-alt text-purple-600 mr-3"></i>
            Admin Dashboard Overview
        </h3>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <i class="fas fa-users text-3xl text-blue-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 mb-2">User Management</h4>
                <p class="text-sm text-gray-600">Create and manage system users</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <i class="fas fa-building text-3xl text-green-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 mb-2">Departments</h4>
                <p class="text-sm text-gray-600">Manage port departments</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <i class="fas fa-envelope text-3xl text-yellow-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 mb-2">Warning Letters</h4>
                <p class="text-sm text-gray-600">Approve warning letters</p>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <i class="fas fa-cog text-3xl text-red-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 mb-2">Settings</h4>
                <p class="text-sm text-gray-600">Configure system settings</p>
            </div>
        </div>
    </div>

    <!-- Help Sections -->
    @foreach($helpSections as $key => $section)
        <div id="{{ $key }}" class="help-section bg-white rounded-lg shadow-sm border p-6 mb-8">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="{{ $section['icon'] }} text-purple-600 mr-3"></i>
                {{ $section['title'] }}
            </h3>
            
            <div class="space-y-6">
                @foreach($section['items'] as $item)
                    <div class="help-item bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-3">{{ $item['title'] }}</h4>
                        <p class="text-gray-700 mb-4">{{ $item['content'] }}</p>
                        
                        @if(isset($item['steps']) && count($item['steps']) > 0)
                            <div class="space-y-3">
                                <h5 class="font-medium text-gray-900 mb-3">Step-by-step instructions:</h5>
                                @foreach($item['steps'] as $index => $step)
                                    <div class="flex items-start space-x-3">
                                        <div class="step-number">{{ $index + 1 }}</div>
                                        <p class="text-gray-700 flex-1">{{ $step }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Best Practices Section -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-star text-yellow-500 mr-3"></i>
            Admin Best Practices
        </h3>
        
        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-gray-900">Security Guidelines</h4>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                        <span>Regularly review user access and permissions</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                        <span>Monitor system activity and login attempts</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                        <span>Keep department credentials secure</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                        <span>Regularly backup system data</span>
                    </li>
                </ul>
            </div>
            
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-gray-900">System Management</h4>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mr-2 mt-1"></i>
                        <span>Review warning letter requests promptly</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mr-2 mt-1"></i>
                        <span>Maintain up-to-date department information</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mr-2 mt-1"></i>
                        <span>Monitor system performance and usage</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mr-2 mt-1"></i>
                        <span>Provide training to new users</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- System Alerts & Notifications -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-bell text-orange-500 mr-3"></i>
            System Alerts & Notifications
        </h3>
        
        <div class="space-y-4">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-semibold text-yellow-800 mb-2">Warning Letter Escalations</h4>
                <p class="text-yellow-700">Monitor automatic escalations when users receive multiple warnings within 3 months.</p>
            </div>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="font-semibold text-red-800 mb-2">Overdue Reports</h4>
                <p class="text-red-700">Track reports that exceed their deadline and require immediate attention.</p>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-800 mb-2">New User Registrations</h4>
                <p class="text-blue-700">Review and approve new user accounts that require manual verification.</p>
            </div>
        </div>
    </div>

    <!-- Troubleshooting Section -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-tools text-orange-600 mr-3"></i>
            Admin Troubleshooting
        </h3>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50" 
                        onclick="toggleAccordion('admin-faq1')">
                    <div class="flex justify-between items-center">
                        <span>User cannot login after account creation</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="admin-faq1-icon"></i>
                    </div>
                </button>
                <div id="admin-faq1" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Check if email verification is required</li>
                            <li>Verify user role assignments are correct</li>
                            <li>Ensure account is not deactivated</li>
                            <li>Reset user password if necessary</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50" 
                        onclick="toggleAccordion('admin-faq2')">
                    <div class="flex justify-between items-center">
                        <span>Department not receiving report assignments</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="admin-faq2-icon"></i>
                    </div>
                </button>
                <div id="admin-faq2" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Verify department login credentials are working</li>
                            <li>Check if UCUA officers are assigning reports properly</li>
                            <li>Ensure department contact information is up to date</li>
                            <li>Review email notification settings</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50" 
                        onclick="toggleAccordion('admin-faq3')">
                    <div class="flex justify-between items-center">
                        <span>Warning letter emails not being sent</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="admin-faq3-icon"></i>
                    </div>
                </button>
                <div id="admin-faq3" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Check email server configuration</li>
                            <li>Verify recipient email addresses are correct</li>
                            <li>Review warning letter approval workflow</li>
                            <li>Check system logs for email delivery errors</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Support -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-3 flex items-center">
            <i class="fas fa-headset text-purple-600 mr-3"></i>
            Technical Support
        </h3>
        <p class="text-purple-800 mb-4">
            For technical issues or system administration support, contact the development team.
        </p>
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center text-purple-700">
                <i class="fas fa-envelope mr-2"></i>
                <span>admin@ucua.com</span>
            </div>
            <div class="flex items-center text-purple-700">
                <i class="fas fa-phone mr-2"></i>
                <span>+60 7-253 5888ext. 100</span>
            </div>
        </div>
    </div>
@endsection

<script>
function toggleAccordion(id) {
    const content = document.getElementById(id);
    const icon = document.getElementById(id + '-icon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>
