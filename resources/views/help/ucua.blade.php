@extends('help.layout')

@section('title', 'UCUA Officer Help')
@section('page-title', 'UCUA Officer Help Center')
@section('page-subtitle', 'Complete guide for UCUA safety officers')
@section('dashboard-route', route('ucua.dashboard'))
@section('search-route', route('help.ucua.search'))

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
    <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg p-8 text-white mb-8">
        <div class="flex items-center">
            <div class="flex-1">
                <h2 class="text-3xl font-bold mb-2">UCUA Officer Control Center</h2>
                <p class="text-green-100 text-lg">
                    Your comprehensive guide for managing safety reports, investigations, and ensuring port safety compliance.
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-clipboard-check text-6xl text-green-200"></i>
            </div>
        </div>
    </div>

    <!-- UCUA Dashboard Overview -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-chart-line text-green-600 mr-3"></i>
            UCUA Officer Dashboard Overview
        </h3>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <i class="fas fa-file-alt text-3xl text-blue-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 mb-2">Report Overview</h4>
                <p class="text-sm text-gray-600">Monitor all safety reports</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <i class="fas fa-tasks text-3xl text-green-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 mb-2">Assign Departments</h4>
                <p class="text-sm text-gray-600">Assign reports to departments</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <i class="fas fa-exclamation-triangle text-3xl text-yellow-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 mb-2">Warning Letters</h4>
                <p class="text-sm text-gray-600">Suggest warning letters</p>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <i class="fas fa-bell text-3xl text-red-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 mb-2">Reminders</h4>
                <p class="text-sm text-gray-600">Track deadlines and alerts</p>
            </div>
        </div>
    </div>

    <!-- Help Sections -->
    @foreach($helpSections as $key => $section)
        <div id="{{ $key }}" class="help-section bg-white rounded-lg shadow-sm border p-6 mb-8">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="{{ $section['icon'] }} text-green-600 mr-3"></i>
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

    <!-- Investigation Workflow -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-search text-blue-600 mr-3"></i>
            Investigation Workflow
        </h3>
        
        <div class="space-y-6">
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">1</div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 mb-2">Initial Report Review</h4>
                    <p class="text-gray-700">Review submitted reports for completeness and severity assessment.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold text-sm">2</div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 mb-2">Department Assignment</h4>
                    <p class="text-gray-700">Assign reports to appropriate departments based on incident type and location.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-yellow-600 text-white rounded-full flex items-center justify-center font-bold text-sm">3</div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 mb-2">Investigation Monitoring</h4>
                    <p class="text-gray-700">Monitor department progress and provide guidance as needed.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm">4</div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 mb-2">Resolution Review</h4>
                    <p class="text-gray-700">Review department responses and determine if further action is needed.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center font-bold text-sm">5</div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 mb-2">Warning Letter Assessment</h4>
                    <p class="text-gray-700">Evaluate if violations warrant formal warning letters or escalation.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Guidelines -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-flag text-red-600 mr-3"></i>
            Priority Guidelines
        </h3>
        
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <div class="w-4 h-4 bg-red-600 rounded-full mr-2"></div>
                    <h4 class="font-semibold text-red-800">High Priority</h4>
                </div>
                <ul class="text-sm text-red-700 space-y-1">
                    <li>• Immediate safety hazards</li>
                    <li>• Potential for serious injury</li>
                    <li>• Equipment failures</li>
                    <li>• Environmental incidents</li>
                </ul>
                <p class="text-xs text-red-600 mt-2 font-medium">Response: Within 24 hours</p>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <div class="w-4 h-4 bg-yellow-600 rounded-full mr-2"></div>
                    <h4 class="font-semibold text-yellow-800">Medium Priority</h4>
                </div>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• Safety violations</li>
                    <li>• Procedural non-compliance</li>
                    <li>• Minor equipment issues</li>
                    <li>• Training deficiencies</li>
                </ul>
                <p class="text-xs text-yellow-600 mt-2 font-medium">Response: Within 3 days</p>
            </div>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <div class="w-4 h-4 bg-green-600 rounded-full mr-2"></div>
                    <h4 class="font-semibold text-green-800">Low Priority</h4>
                </div>
                <ul class="text-sm text-green-700 space-y-1">
                    <li>• Suggestions for improvement</li>
                    <li>• Minor housekeeping issues</li>
                    <li>• Documentation updates</li>
                    <li>• General observations</li>
                </ul>
                <p class="text-xs text-green-600 mt-2 font-medium">Response: Within 7 days</p>
            </div>
        </div>
    </div>

    <!-- Troubleshooting Section -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-tools text-orange-600 mr-3"></i>
            UCUA Officer Troubleshooting
        </h3>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50" 
                        onclick="toggleAccordion('ucua-faq1')">
                    <div class="flex justify-between items-center">
                        <span>Department not responding to assigned reports</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="ucua-faq1-icon"></i>
                    </div>
                </button>
                <div id="ucua-faq1" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Check if department received assignment notification</li>
                            <li>Verify department login credentials are working</li>
                            <li>Send reminder through the system</li>
                            <li>Contact department head directly</li>
                            <li>Escalate to admin if no response within deadline</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50" 
                        onclick="toggleAccordion('ucua-faq2')">
                    <div class="flex justify-between items-center">
                        <span>Cannot suggest warning letter for a report</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="ucua-faq2-icon"></i>
                    </div>
                </button>
                <div id="ucua-faq2" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Ensure report has been investigated by department</li>
                            <li>Check if violator information is available</li>
                            <li>Verify report status is appropriate for warning</li>
                            <li>Review escalation rules and previous warnings</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50" 
                        onclick="toggleAccordion('ucua-faq3')">
                    <div class="flex justify-between items-center">
                        <span>Report assignment to department failed</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="ucua-faq3-icon"></i>
                    </div>
                </button>
                <div id="ucua-faq3" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Check if department exists and is active</li>
                            <li>Verify your permissions for assignment</li>
                            <li>Ensure report is not already assigned</li>
                            <li>Try refreshing the page and attempting again</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Support -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-3 flex items-center">
            <i class="fas fa-headset text-green-600 mr-3"></i>
            UCUA Support
        </h3>
        <p class="text-green-800 mb-4">
            For assistance with investigations or system issues, contact the UCUA support team.
        </p>
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center text-green-700">
                <i class="fas fa-envelope mr-2"></i>
                <span>ucua@johorport.com</span>
            </div>
            <div class="flex items-center text-green-700">
                <i class="fas fa-phone mr-2"></i>
                <span>+60 7-123-4567 ext. 200</span>
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
