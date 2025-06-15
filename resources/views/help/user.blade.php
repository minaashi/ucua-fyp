@extends('help.layout')

@section('title', 'User Help')
@section('page-title', 'User Help Center')
@section('page-subtitle', 'Complete guide for port workers')
@section('dashboard-route', route('dashboard'))
@section('search-route', route('help.search'))

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
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg p-8 text-white mb-8">
        <div class="flex items-center">
            <div class="flex-1">
                <h2 class="text-3xl font-bold mb-2">Welcome to the UCUA Safety Reporting System</h2>
                <p class="text-blue-100 text-lg">
                    This guide will help you navigate the system, submit safety reports, and track their progress effectively.
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-life-ring text-6xl text-blue-200"></i>
            </div>
        </div>
    </div>

    <!-- Quick Start Guide -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-rocket text-blue-600 mr-3"></i>
            Quick Start Guide
        </h3>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="font-bold">1</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Register & Login</h4>
                <p class="text-sm text-gray-600">Create your account and verify your email to get started</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="font-bold">2</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Submit Reports</h4>
                <p class="text-sm text-gray-600">Report safety incidents and unsafe conditions you observe</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <div class="w-12 h-12 bg-purple-600 text-white rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="font-bold">3</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Track Progress</h4>
                <p class="text-sm text-gray-600">Monitor your reports and view department responses</p>
            </div>
        </div>
    </div>

    <!-- Help Sections -->
    @foreach($helpSections as $key => $section)
        <div id="{{ $key }}" class="help-section bg-white rounded-lg shadow-sm border p-6 mb-8">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="{{ $section['icon'] }} text-blue-600 mr-3"></i>
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

    <!-- Troubleshooting Section -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-tools text-orange-600 mr-3"></i>
            Common Issues & Troubleshooting
        </h3>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50" 
                        onclick="toggleAccordion('faq1')">
                    <div class="flex justify-between items-center">
                        <span>I'm not receiving OTP emails</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="faq1-icon"></i>
                    </div>
                </button>
                <div id="faq1" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Check your spam/junk folder</li>
                            <li>Ensure your email address is correct</li>
                            <li>Wait a few minutes and try resending the OTP</li>
                            <li>Contact system administrator if issue persists</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50" 
                        onclick="toggleAccordion('faq2')">
                    <div class="flex justify-between items-center">
                        <span>My report submission failed</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="faq2-icon"></i>
                    </div>
                </button>
                <div id="faq2" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Check that all required fields are filled</li>
                            <li>Ensure your internet connection is stable</li>
                            <li>Try refreshing the page and submitting again</li>
                            <li>Save your report content before submitting</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50" 
                        onclick="toggleAccordion('faq3')">
                    <div class="flex justify-between items-center">
                        <span>I can't see my report status</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="faq3-icon"></i>
                    </div>
                </button>
                <div id="faq3" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Use the "Track Report" feature with your report ID</li>
                            <li>Check "My Reports" section for all your submissions</li>
                            <li>Reports may take time to be assigned to departments</li>
                            <li>Contact UCUA officers for status updates</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Support -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
            <i class="fas fa-headset text-blue-600 mr-3"></i>
            Need Additional Help?
        </h3>
        <p class="text-blue-800 mb-4">
            If you can't find the answer to your question in this help guide, please contact our support team.
        </p>
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center text-blue-700">
                <i class="fas fa-envelope mr-2"></i>
                <span>support@ucua.com</span>
            </div>
            <div class="flex items-center text-blue-700">
                <i class="fas fa-phone mr-2"></i>
                <span>+60 7-253 5888</span>
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
