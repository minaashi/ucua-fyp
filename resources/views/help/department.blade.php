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
                                <li>• Speeding in workplace areas</li>
                                <li>• Smoking in prohibited areas</li>
                            </ul>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <h4 class="font-semibold text-blue-800 mb-2">⚠️ Unsafe Conditions (Fix the Issue)</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Slippery surfaces</li>
                                <li>• Exposed live wires</li>
                                <li>• Damaged equipment</li>
                                <li>• Gas Leaks</li>
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

    <!-- Dynamic Actions Guide -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-cogs text-purple-600 mr-3"></i>
            Dynamic Actions Guide
        </h3>

        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
            <h4 class="font-semibold text-purple-800 mb-3">📋 Smart Action System</h4>
            <p class="text-purple-700">
                The system automatically shows relevant actions based on each report's current status.
                This ensures you only see actions that are appropriate for the report's workflow stage.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Pending/In Progress Reports -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-semibold text-yellow-800 mb-3 flex items-center">
                    <i class="fas fa-clock text-yellow-600 mr-2"></i>
                    Pending & In Progress Reports
                </h4>
                <div class="space-y-2 text-yellow-700">
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full mr-2">
                            <i class="fas fa-eye mr-1"></i>Review
                        </span>
                        <span class="text-sm">View full report details</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full mr-2">
                            <i class="fas fa-comment mr-1"></i>Remark
                        </span>
                        <span class="text-sm">Add department comments</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full mr-2">
                            <i class="fas fa-check mr-1"></i>Resolve
                        </span>
                        <span class="text-sm">Mark as completed</span>
                    </div>
                </div>
            </div>

            <!-- Review Status Reports -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                    <i class="fas fa-search text-blue-600 mr-2"></i>
                    Review Status Reports
                </h4>
                <div class="space-y-2 text-blue-700">
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full mr-2">
                            <i class="fas fa-eye mr-1"></i>Review
                        </span>
                        <span class="text-sm">View report details</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full mr-2">
                            <i class="fas fa-comment mr-1"></i>Remark
                        </span>
                        <span class="text-sm">Add department feedback</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full mr-2">
                            <i class="fas fa-thumbs-up mr-1"></i>Accept
                        </span>
                        <span class="text-sm">Accept the report</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full mr-2">
                            <i class="fas fa-thumbs-down mr-1"></i>Reject
                        </span>
                        <span class="text-sm">Reject the report</span>
                    </div>
                </div>
            </div>

            <!-- Resolved Reports -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-semibold text-green-800 mb-3 flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    Resolved Reports
                </h4>
                <div class="space-y-2 text-green-700">
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full mr-2">
                            <i class="fas fa-eye mr-1"></i>Review
                        </span>
                        <span class="text-sm">View completed report</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full mr-2">
                            <i class="fas fa-download mr-1"></i>Export
                        </span>
                        <span class="text-sm">Download report documentation</span>
                    </div>
                    <p class="text-sm text-green-600 mt-2 italic">
                        ✓ No further actions needed - report is complete
                    </p>
                </div>
            </div>

            <!-- Rejected Reports -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="font-semibold text-red-800 mb-3 flex items-center">
                    <i class="fas fa-times-circle text-red-600 mr-2"></i>
                    Rejected Reports
                </h4>
                <div class="space-y-2 text-red-700">
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full mr-2">
                            <i class="fas fa-eye mr-1"></i>Review
                        </span>
                        <span class="text-sm">View rejection details</span>
                    </div>
                    <p class="text-sm text-red-600 mt-2 italic">
                        ⚠️ Report has been rejected - review for understanding
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-6">
            <h4 class="font-semibold text-gray-800 mb-3">💡 Pro Tips</h4>
            <ul class="space-y-2 text-gray-700 text-sm">
                <li class="flex items-start">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2 mt-1"></i>
                    <span>Actions automatically update when report status changes</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2 mt-1"></i>
                    <span>Review action is always available to view report details</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2 mt-1"></i>
                    <span>Use Accept/Reject actions carefully for review status reports</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2 mt-1"></i>
                    <span>Export function helps maintain documentation records</span>
                </li>
            </ul>
        </div>
    </div>

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

            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50"
                        onclick="toggleAccordion('dept-faq6')">
                    <div class="flex justify-between items-center">
                        <span>Why don't I see Accept/Reject buttons for some reports?</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="dept-faq6-icon"></i>
                    </div>
                </button>
                <div id="dept-faq6" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Explanation:</strong></p>
                        <p>Actions are dynamically displayed based on report status:</p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li><strong>Accept/Reject buttons:</strong> Only appear for reports with "Review" status</li>
                            <li><strong>Resolve button:</strong> Only appears for "Pending" and "In Progress" reports</li>
                            <li><strong>Remark button:</strong> Available for "Pending", "In Progress", and "Review" status</li>
                            <li><strong>Review button:</strong> Always available to view report details</li>
                            <li>This ensures you only see relevant actions for each report's workflow stage</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg">
                <button class="w-full px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-50"
                        onclick="toggleAccordion('dept-faq7')">
                    <div class="flex justify-between items-center">
                        <span>What happens when I accept or reject a report?</span>
                        <i class="fas fa-chevron-down transform transition-transform" id="dept-faq7-icon"></i>
                    </div>
                </button>
                <div id="dept-faq7" class="hidden px-4 pb-3">
                    <div class="text-gray-700 space-y-2">
                        <p><strong>Action Results:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li><strong>Accept:</strong> Changes report status to "In Progress" - you can then add remarks and eventually resolve it</li>
                            <li><strong>Reject:</strong> Changes report status to "Rejected" - only Review action will be available afterward</li>
                            <li><strong>Important:</strong> These actions are permanent and will notify relevant parties</li>
                            <li>Always review the report thoroughly before accepting or rejecting</li>
                            <li>Add appropriate remarks to explain your decision</li>
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
