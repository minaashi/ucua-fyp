@extends('help.layout')

@section('title', 'Department Help')
@section('page-title', 'Department Help Center')
@section('page-subtitle', 'Complete guide for department users')
@section('dashboard-route', route('department.dashboard'))
@section('search-route', route('help.department.search'))

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
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-lg p-8 text-white mb-8">
        <div class="flex items-center">
            <div class="flex-1">
                <h2 class="text-3xl font-bold mb-2">Department Control Center</h2>
                <p class="text-indigo-100 text-lg">
                    Your guide for managing assigned safety reports and maintaining department compliance.
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-building text-6xl text-indigo-200"></i>
            </div>
        </div>
    </div>

    <!-- Department Dashboard Overview -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-tachometer-alt text-indigo-600 mr-3"></i>
            Department Dashboard Overview
        </h3>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <i class="fas fa-file-alt text-3xl text-blue-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 mb-2">Total Reports</h4>
                <p class="text-sm text-gray-600">All reports assigned to your department</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <i class="fas fa-clock text-3xl text-yellow-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 mb-2">Pending Reports</h4>
                <p class="text-sm text-gray-600">Reports requiring your attention</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <i class="fas fa-check-circle text-3xl text-green-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 mb-2">Resolved Reports</h4>
                <p class="text-sm text-gray-600">Successfully completed reports</p>
            </div>
        </div>
    </div>

    <!-- Important Notice about Violator Identification -->
    <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mb-8">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-orange-600 text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-orange-900 mb-2">
                    Important: Violator Identification Guidelines
                </h3>
                <div class="text-orange-800 space-y-2">
                    <p class="font-medium">Violator identification is ONLY required for <strong>Unsafe Acts</strong>, not Unsafe Conditions.</p>
                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <h4 class="font-semibold text-red-800 mb-2">🚨 Unsafe Acts (Requires Violator ID)</h4>
                            <ul class="text-sm text-red-700 space-y-1">
                                <li>• Not wearing required PPE</li>
                                <li>• Improper lifting techniques</li>
                                <li>• Ignoring safety procedures</li>
                                <li>• Running in workplace areas</li>
                            </ul>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <h4 class="font-semibold text-blue-800 mb-2">⚠️ Unsafe Conditions (Fix the Issue)</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Slippery surfaces</li>
                                <li>• Poor lighting conditions</li>
                                <li>• Damaged equipment</li>
                                <li>• Blocked emergency exits</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Sections -->
    @foreach($helpSections as $key => $section)
        <div id="{{ $key }}" class="help-section bg-white rounded-lg shadow-sm border p-6 mb-8">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="{{ $section['icon'] }} text-indigo-600 mr-3"></i>
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

    <!-- Response Guidelines -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-clipboard-list text-blue-600 mr-3"></i>
            Response Guidelines
        </h3>
        
        <div class="space-y-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-800 mb-3">Effective Report Responses</h4>
                <ul class="space-y-2 text-blue-700">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Acknowledge receipt of the report promptly</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Investigate the incident thoroughly</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Document all actions taken</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Provide clear and detailed responses</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Include preventive measures implemented</span>
                    </li>
                </ul>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-semibold text-yellow-800 mb-3">Response Timeline</h4>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                        <span class="text-yellow-700"><strong>High Priority:</strong> Respond within 24 hours</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                        <span class="text-yellow-700"><strong>Medium Priority:</strong> Respond within 3 days</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-yellow-700"><strong>Low Priority:</strong> Respond within 7 days</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Communication Best Practices -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-comments text-green-600 mr-3"></i>
            Communication Best Practices
        </h3>
        
        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-gray-900">Do's</h4>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Be professional and courteous in all communications</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Provide specific details about actions taken</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Include timelines for corrective measures</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Acknowledge the reporter's contribution to safety</span>
                    </li>
                </ul>
            </div>
            
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-gray-900">Don'ts</h4>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-times text-red-500 mr-2 mt-1"></i>
                        <span>Don't dismiss reports without proper investigation</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-times text-red-500 mr-2 mt-1"></i>
                        <span>Don't provide vague or generic responses</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-times text-red-500 mr-2 mt-1"></i>
                        <span>Don't blame individuals in your responses</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-times text-red-500 mr-2 mt-1"></i>
                        <span>Don't ignore deadlines or response requirements</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Troubleshooting Section -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-tools text-orange-600 mr-3"></i>
            Department Troubleshooting
        </h3>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50" 
                        onclick="toggleAccordion('dept-faq1')">
                    <div class="flex justify-between items-center">
                        <span>Cannot access assigned reports</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="dept-faq1-icon"></i>
                    </div>
                </button>
                <div id="dept-faq1" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Verify your department login credentials</li>
                            <li>Check if reports have been assigned to your department</li>
                            <li>Ensure you're using the correct department login page</li>
                            <li>Contact UCUA officers if no reports are visible</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50" 
                        onclick="toggleAccordion('dept-faq2')">
                    <div class="flex justify-between items-center">
                        <span>Unable to submit department remarks</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="dept-faq2-icon"></i>
                    </div>
                </button>
                <div id="dept-faq2" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Ensure all required fields are completed</li>
                            <li>Check your internet connection</li>
                            <li>Try refreshing the page and submitting again</li>
                            <li>Save your remarks content before submitting</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50"
                        onclick="toggleAccordion('dept-faq3')">
                    <div class="flex justify-between items-center">
                        <span>Not receiving report assignment notifications</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="dept-faq3-icon"></i>
                    </div>
                </button>
                <div id="dept-faq3" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Check your department email settings</li>
                            <li>Verify email address is correct in system</li>
                            <li>Check spam/junk folders for notifications</li>
                            <li>Contact admin to update notification settings</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50"
                        onclick="toggleAccordion('dept-faq4')">
                    <div class="flex justify-between items-center">
                        <span>When should I identify violators?</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="dept-faq4-icon"></i>
                    </div>
                </button>
                <div id="dept-faq4" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Important:</strong> Violator identification is only required for <strong>Unsafe Acts</strong>, not Unsafe Conditions.</p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li><strong>Unsafe Acts:</strong> Human behaviors that violate safety procedures (requires violator identification)</li>
                            <li><strong>Unsafe Conditions:</strong> Environmental hazards or equipment issues (focus on fixing the issue, not identifying people)</li>
                            <li>Only use the violator identification fields when investigating unsafe acts</li>
                            <li>For unsafe conditions, concentrate on corrective actions and preventive measures</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50"
                        onclick="toggleAccordion('dept-faq5')">
                    <div class="flex justify-between items-center">
                        <span>Cannot find violator identification fields</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="dept-faq5-icon"></i>
                    </div>
                </button>
                <div id="dept-faq5" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Solutions:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Ensure the report is categorized as "Unsafe Act" (violator fields only appear for unsafe acts)</li>
                            <li>Toggle the "Investigation Update" checkbox in the remarks section</li>
                            <li>Violator identification fields will appear below the toggle</li>
                            <li>If still not visible, refresh the page and try again</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Support -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-indigo-900 mb-3 flex items-center">
            <i class="fas fa-headset text-indigo-600 mr-3"></i>
            Department Support
        </h3>
        <p class="text-indigo-800 mb-4">
            For assistance with report management or system access, contact the UCUA support team.
        </p>
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center text-indigo-700">
                <i class="fas fa-envelope mr-2"></i>
                <span>departments@ucua.com</span>
            </div>
            <div class="flex items-center text-indigo-700">
                <i class="fas fa-phone mr-2"></i>
                <span>+60 7-253 5888 ext. 300</span>
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
