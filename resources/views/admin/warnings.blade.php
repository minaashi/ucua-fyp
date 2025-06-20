@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <header class="bg-blue-800 text-white p-4 shadow-md rounded mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="flex items-center">
                <button id="sidebarToggle" class="lg:hidden mr-3 text-white hover:text-blue-200">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl sm:text-2xl font-bold">Warning Letters Management</h1>
            </div>
            <div class="text-xs sm:text-sm text-blue-100">
                <i class="fas fa-info-circle mr-1"></i>
                <span class="hidden sm:inline">All warnings must be suggested by UCUA Officers and approved by Admin</span>
                <span class="sm:hidden">UCUA → Admin Approval Required</span>
            </div>
        </div>
    </header>

    <!-- Alert for Approved Warnings Waiting to be Sent -->
    @php
        $approvedCount = $warnings->where('status', 'approved')->filter(function($warning) {
            return $warning->canBeSentViaEmail() || $warning->isExternalViolator();
        })->count();
    @endphp
    @if($approvedCount > 0)
        <div class="mb-6 p-4 bg-orange-50 border-l-4 border-orange-400 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-orange-400 text-xl animate-pulse"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-orange-700">
                        <span class="font-medium">{{ $approvedCount }} warning letter{{ $approvedCount > 1 ? 's' : '' }} approved but not sent yet!</span>
                        Look for purple "SEND EMAIL" buttons for internal employees, or blue "MANUAL DELIVERY" indicators for external violators.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('admin.warnings.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Search by report ID, officer name, reason..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
                <a href="{{ route('admin.warnings.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Reports with Multiple Warnings Alert -->
    @if(count($reportsWithMultipleWarnings) > 0)
        <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <span class="font-medium">{{ count($reportsWithMultipleWarnings) }} report{{ count($reportsWithMultipleWarnings) > 1 ? 's have' : ' has' }} multiple warning letters.</span>
                        This may indicate escalation scenarios or multiple violations in the same incident.
                    </p>
                </div>
            </div>
        </div>
    @endif
    <!-- Warning Letters Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-3 sm:p-4">
            <h3 class="text-xs sm:text-sm font-semibold text-gray-700">Total</h3>
            <p class="text-lg sm:text-2xl font-bold text-blue-600 mt-1">{{ $totalWarnings }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-3 sm:p-4">
            <h3 class="text-xs sm:text-sm font-semibold text-gray-700">Pending</h3>
            <p class="text-lg sm:text-2xl font-bold text-yellow-600 mt-1">{{ $pendingWarnings }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-3 sm:p-4">
            <h3 class="text-xs sm:text-sm font-semibold text-gray-700">Approved</h3>
            <p class="text-lg sm:text-2xl font-bold text-green-600 mt-1">{{ $approvedWarnings }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-3 sm:p-4">
            <h3 class="text-xs sm:text-sm font-semibold text-gray-700">Rejected</h3>
            <p class="text-lg sm:text-2xl font-bold text-red-600 mt-1">{{ $rejectedWarnings }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 col-span-2 sm:col-span-1">
            <h3 class="text-xs sm:text-sm font-semibold text-gray-700">Sent</h3>
            <p class="text-lg sm:text-2xl font-bold text-purple-600 mt-1">{{ $sentWarnings }}</p>
        </div>
    </div>
    <!-- Warning Letters Table -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-bold mb-4 text-gray-800">Warning Letter Suggestions</h2>

        <!-- Desktop Table View (hidden on mobile) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Officer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($warnings as $warning)
                        <tr class="{{ $warning->status === 'pending' ? 'bg-yellow-50' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $warning->formatted_id }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex flex-col">
                                    <a href="{{ route('admin.reports.show', $warning->report->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        RPT-{{ str_pad($warning->report->id, 4, '0', STR_PAD_LEFT) }}
                                    </a>
                                    @if(in_array($warning->report->id, $reportsWithMultipleWarnings))
                                        @php
                                            $warningCount = $warning->report->warnings()->count();
                                            $warningSequence = $warning->report->warnings()->where('created_at', '<=', $warning->created_at)->count();
                                        @endphp
                                        <span class="text-xs text-orange-600 font-medium">
                                            <i class="fas fa-layer-group mr-1"></i>Warning {{ $warningSequence }} of {{ $warningCount }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $warning->type === 'minor' ? 'bg-yellow-100 text-yellow-800' :
                                       ($warning->type === 'moderate' ? 'bg-orange-100 text-orange-800' :
                                        'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($warning->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <div class="truncate max-w-32" title="{{ $warning->suggestedBy ? $warning->suggestedBy->name : 'Unknown' }}">
                                    {{ $warning->suggestedBy ? $warning->suggestedBy->name : 'Unknown' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $warning->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                       ($warning->status == 'approved' && $warning->canBeSentViaEmail() ? 'bg-orange-100 text-orange-800 animate-pulse' :
                                        ($warning->status == 'approved' && $warning->isExternalViolator() ? 'bg-blue-100 text-blue-800' :
                                         ($warning->status == 'rejected' ? 'bg-red-100 text-red-800' :
                                          'bg-purple-100 text-purple-800'))) }}">
                                    {{ $warning->getDeliveryStatus() }}
                                </span>
                                @if($warning->status === 'approved')
                                    @if($warning->canBeSentViaEmail())
                                        <div class="text-xs text-orange-600 mt-1 font-medium">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Email not sent yet
                                        </div>
                                    @elseif($warning->isExternalViolator())
                                        <div class="text-xs text-blue-600 mt-1 font-medium">
                                            <i class="fas fa-hand-paper mr-1"></i>Manual delivery required
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-600 mt-1 font-medium">
                                            <i class="fas fa-search mr-1"></i>Awaiting violator identification
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $warning->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if($warning->status === 'approved')
                                        @if($warning->canBeSentViaEmail())
                                            <!-- Email Send Button for Internal Users -->
                                            <form action="{{ route('admin.warnings.send', $warning) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150 animate-pulse"
                                                        onclick="return confirm('Are you sure you want to send this warning letter to the violator via email?')">
                                                    <i class="fas fa-paper-plane mr-1"></i>SEND EMAIL
                                                </button>
                                            </form>
                                        @elseif($warning->isExternalViolator())
                                            <!-- External Violator - Manual Delivery Required -->
                                            <span class="inline-flex items-center px-3 py-1 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest cursor-default"
                                                  title="External violator - manual delivery will be handled separately">
                                                <i class="fas fa-hand-paper mr-1"></i>MANUAL DELIVERY
                                            </span>
                                        @else
                                            <!-- No Action Available - Violator Not Identified -->
                                            <span class="inline-flex items-center px-3 py-1 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest cursor-not-allowed">
                                                <i class="fas fa-search mr-1"></i>AWAITING ID
                                            </span>
                                        @endif
                                    @endif

                                    <div class="relative inline-block text-left">
                                        <button type="button"
                                                class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full hover:bg-gray-200 transition-colors duration-200"
                                                onclick="toggleDropdown('dropdown-{{ $warning->id }}')">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="dropdown-{{ $warning->id }}" class="hidden absolute right-0 z-10 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                            <div class="py-1">
                                                <button onclick="viewWarningDetails({{ $warning->id }})"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="fas fa-eye mr-2"></i>View Details
                                                </button>
                                                @if($warning->status === 'pending')
                                                    <button onclick="approveWarning({{ $warning->id }})"
                                                            class="w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50 flex items-center">
                                                        <i class="fas fa-check mr-2"></i>Approve
                                                    </button>
                                                    <button onclick="rejectWarning({{ $warning->id }})"
                                                            class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center">
                                                        <i class="fas fa-times mr-2"></i>Reject
                                                    </button>
                                                @elseif($warning->status === 'approved')
                                                    <div class="px-4 py-2 text-sm text-gray-500 italic">
                                                        @if($warning->canBeSentViaEmail())
                                                            <i class="fas fa-info-circle mr-2"></i>Use purple button to send email
                                                        @elseif($warning->isExternalViolator())
                                                            <i class="fas fa-hand-paper mr-2"></i>External violator - manual delivery required
                                                        @else
                                                            <i class="fas fa-exclamation-triangle mr-2"></i>Awaiting violator identification
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                <p>No warning letter suggestions found</p>
                                @if(request('search') || request('status'))
                                    <p class="text-sm mt-1">Try adjusting your search criteria</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (visible on mobile) -->
        <div class="lg:hidden space-y-4">
            @forelse($warnings as $warning)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm {{ $warning->status === 'pending' ? 'border-l-4 border-l-yellow-400' : ($warning->status === 'approved' ? 'border-l-4 border-l-orange-400' : '') }}">
                    @if($warning->status === 'approved')
                        @if($warning->canBeSentViaEmail())
                            <!-- Email Send Button for Mobile -->
                            <div class="mb-3 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                                        <span class="text-sm font-medium text-orange-800">Ready to Send Email</span>
                                    </div>
                                    <form action="{{ route('admin.warnings.send', $warning) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150 animate-pulse"
                                                onclick="return confirm('Are you sure you want to send this warning letter to the violator via email?')">
                                            <i class="fas fa-paper-plane mr-1"></i>SEND NOW
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @elseif($warning->isExternalViolator())
                            <!-- External Violator - Manual Delivery for Mobile -->
                            <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-hand-paper text-blue-500 mr-2"></i>
                                    <div>
                                        <span class="text-sm font-medium text-blue-800">External Violator</span>
                                        <p class="text-xs text-blue-600 mt-1">Manual delivery will be handled separately</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- No Action Available for Mobile -->
                            <div class="mb-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-search text-gray-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-800">Awaiting Violator Identification</span>
                                </div>
                            </div>
                        @endif
                    @endif

                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold text-gray-900">{{ $warning->formatted_id }}</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $warning->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                   ($warning->status == 'approved' && $warning->canBeSentViaEmail() ? 'bg-orange-100 text-orange-800 animate-pulse' :
                                    ($warning->status == 'approved' && $warning->isExternalViolator() ? 'bg-blue-100 text-blue-800' :
                                     ($warning->status == 'rejected' ? 'bg-red-100 text-red-800' :
                                      'bg-purple-100 text-purple-800'))) }}">
                                {{ $warning->getDeliveryStatus() }}
                            </span>
                        </div>
                        <div class="relative inline-block text-left">
                            <button type="button"
                                    class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full hover:bg-gray-200"
                                    onclick="toggleDropdown('mobile-dropdown-{{ $warning->id }}')">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="mobile-dropdown-{{ $warning->id }}" class="hidden absolute right-0 z-10 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <button onclick="viewWarningDetails({{ $warning->id }})"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <i class="fas fa-eye mr-2"></i>View Details
                                    </button>
                                    @if($warning->status === 'pending')
                                        <button onclick="approveWarning({{ $warning->id }})"
                                                class="w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50 flex items-center">
                                            <i class="fas fa-check mr-2"></i>Approve
                                        </button>
                                        <button onclick="rejectWarning({{ $warning->id }})"
                                                class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center">
                                            <i class="fas fa-times mr-2"></i>Reject
                                        </button>
                                    @elseif($warning->status === 'approved')
                                        <div class="px-4 py-2 text-sm text-gray-500 italic">
                                            @if($warning->canBeSentViaEmail())
                                                <i class="fas fa-info-circle mr-2"></i>Use orange button above to send email
                                            @elseif($warning->isExternalViolator())
                                                <i class="fas fa-hand-paper mr-2"></i>External violator - manual delivery required
                                            @else
                                                <i class="fas fa-exclamation-triangle mr-2"></i>Awaiting violator identification
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Report:</span>
                            <div class="text-right">
                                <a href="{{ route('admin.reports.show', $warning->report->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    RPT-{{ str_pad($warning->report->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                                @if(in_array($warning->report->id, $reportsWithMultipleWarnings))
                                    @php
                                        $warningCount = $warning->report->warnings()->count();
                                        $warningSequence = $warning->report->warnings()->where('created_at', '<=', $warning->created_at)->count();
                                    @endphp
                                    <div class="text-xs text-orange-600 font-medium mt-1">
                                        <i class="fas fa-layer-group mr-1"></i>Warning {{ $warningSequence }} of {{ $warningCount }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type:</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $warning->type === 'minor' ? 'bg-yellow-100 text-yellow-800' :
                                   ($warning->type === 'moderate' ? 'bg-orange-100 text-orange-800' :
                                    'bg-red-100 text-red-800') }}">
                                {{ ucfirst($warning->type) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Officer:</span>
                            <span class="text-gray-900 truncate max-w-32">{{ $warning->suggestedBy ? $warning->suggestedBy->name : 'Unknown' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span class="text-gray-900">{{ $warning->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="mt-3">
                            <span class="text-gray-600">Reason:</span>
                            <p class="text-gray-900 mt-1 text-sm">{{ Str::limit($warning->reason, 100) }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                    <p>No warning letter suggestions found</p>
                    @if(request('search') || request('status'))
                        <p class="text-sm mt-1">Try adjusting your search criteria</p>
                    @endif
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $warnings->links() }}
        </div>
    </div>

    <!-- View Warning Details Modal -->
    <div class="modal fade" id="viewWarningModal" tabindex="-1" role="dialog" aria-labelledby="viewWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-blue-600 text-white">
                    <h5 class="modal-title" id="viewWarningModalLabel">
                        <i class="fas fa-eye mr-2"></i>Warning Letter Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" onclick="closeModal('viewWarningModal')"></button>
                </div>
                <div class="modal-body" id="warningDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" onclick="closeModal('viewWarningModal')">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Warning Modal -->
    <div class="modal fade" id="approveWarningModal" tabindex="-1" role="dialog" aria-labelledby="approveWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white">
                    <h5 class="modal-title" id="approveWarningModalLabel">
                        <i class="fas fa-check mr-2"></i>Approve Warning Letter
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" onclick="closeModal('approveWarningModal')"></button>
                </div>
                <form id="approveWarningForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Review the warning suggestion and customize the message before approval.</strong>
                        </div>

                        <div id="approveWarningContent">
                            <!-- Content will be loaded dynamically -->
                        </div>

                        <div class="form-group mt-4">
                            <label for="warning_message" class="font-weight-bold">Final Warning Message <span class="text-danger">*</span></label>
                            <textarea name="warning_message" id="warning_message" class="form-control" rows="4" required
                                      placeholder="Enter the final warning message that will be sent to the employee..."></textarea>
                            <small class="form-text text-muted">This message will be included in the official warning letter</small>
                        </div>

                        <div class="form-group">
                            <label for="admin_notes" class="font-weight-bold">Admin Notes (Optional)</label>
                            <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3"
                                      placeholder="Add any internal notes about this approval..."></textarea>
                            <small class="form-text text-muted">These notes are for internal use only</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" onclick="closeModal('approveWarningModal')">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check mr-2"></i>Approve Warning
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Warning Modal -->
    <div class="modal fade" id="rejectWarningModal" tabindex="-1" role="dialog" aria-labelledby="rejectWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-red-600 text-white">
                    <h5 class="modal-title" id="rejectWarningModalLabel">
                        <i class="fas fa-times mr-2"></i>Reject Warning Letter
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" onclick="closeModal('rejectWarningModal')"></button>
                </div>
                <form id="rejectWarningForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Please provide a reason for rejecting this warning suggestion.</strong>
                        </div>

                        <div id="rejectWarningContent">
                            <!-- Content will be loaded dynamically -->
                        </div>

                        <div class="form-group mt-4">
                            <label for="reject_admin_notes" class="font-weight-bold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="admin_notes" id="reject_admin_notes" class="form-control" rows="4" required
                                      placeholder="Explain why this warning suggestion is being rejected..."></textarea>
                            <small class="form-text text-muted">This will be visible to the UCUA officer who made the suggestion</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" onclick="closeModal('rejectWarningModal')">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-ban mr-2"></i>Reject Warning
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
<script>
// Toggle dropdown menu
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const isHidden = dropdown.classList.contains('hidden');

    // Close all other dropdowns
    document.querySelectorAll('[id^="dropdown-"], [id^="mobile-dropdown-"]').forEach(el => {
        if (el.id !== dropdownId) {
            el.classList.add('hidden');
        }
    });

    // Toggle current dropdown
    if (isHidden) {
        dropdown.classList.remove('hidden');
    } else {
        dropdown.classList.add('hidden');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleDropdown"]') && !event.target.closest('[id^="dropdown-"], [id^="mobile-dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"], [id^="mobile-dropdown-"]').forEach(el => {
            el.classList.add('hidden');
        });
    }
});

// Sidebar toggle for mobile
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    const sidebar = document.querySelector('.sidebar, [class*="sidebar"]');
    if (sidebar) {
        sidebar.classList.toggle('hidden');
    }
});

// View warning details
function viewWarningDetails(warningId) {
    fetch(`/admin/warnings/${warningId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('warningDetailsContent').innerHTML = data.html;
                showModal('viewWarningModal');
            } else {
                alert('Error loading warning details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading warning details');
        });
}

// Approve warning
function approveWarning(warningId) {
    // Reset modal state first
    resetApprovalModal();

    fetch(`/admin/warnings/${warningId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate the approve modal with warning details
                document.getElementById('approveWarningContent').innerHTML = data.html;

                // Check if violator is identified
                if (data.violator) {
                    // Pre-fill the warning message with the suggested content and violator info
                    const violatorInfo = `Warning Letter for: ${data.violator.name} (${data.violator.employee_id})\nDepartment: ${data.violator.department || 'N/A'}\n\n`;
                    const suggestedMessage = violatorInfo + data.warning.reason + '\n\nSuggested Action: ' + data.warning.suggested_action;
                    document.getElementById('warning_message').value = suggestedMessage;

                    // Enable the approve button
                    const approveBtn = document.querySelector('#approveWarningModal .btn-success');
                    if (approveBtn) {
                        approveBtn.disabled = false;
                        approveBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Approve & Send Warning';
                    }
                } else {
                    // Violator not identified - disable approval
                    document.getElementById('warning_message').value = 'Cannot approve: Violator has not been identified yet. Investigation is required.';
                    document.getElementById('warning_message').disabled = true;

                    // Disable the approve button
                    const approveBtn = document.querySelector('#approveWarningModal .btn-success');
                    if (approveBtn) {
                        approveBtn.disabled = true;
                        approveBtn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Cannot Approve - Investigation Required';
                    }

                    // Show alert
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger mt-3';
                    alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i><strong>Cannot Approve:</strong> The violator must be identified before this warning can be approved. Please contact the handling department to complete their investigation.';
                    document.getElementById('approveWarningContent').appendChild(alertDiv);
                }

                // Set the form action
                document.getElementById('approveWarningForm').action = `/admin/warnings/${warningId}/approve`;

                showModal('approveWarningModal');
            } else {
                alert('Error loading warning details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading warning details');
        });
}

// Reject warning
function rejectWarning(warningId) {
    fetch(`/admin/warnings/${warningId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate the reject modal with warning details
                document.getElementById('rejectWarningContent').innerHTML = data.html;

                // Clear previous rejection reason
                document.getElementById('reject_admin_notes').value = '';

                // Set the form action
                document.getElementById('rejectWarningForm').action = `/admin/warnings/${warningId}/reject`;

                showModal('rejectWarningModal');
            } else {
                alert('Error loading warning details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading warning details');
        });
}

// Reset modal states when opening
function resetApprovalModal() {
    // Reset warning message field
    const warningMessageField = document.getElementById('warning_message');
    if (warningMessageField) {
        warningMessageField.disabled = false;
        warningMessageField.value = '';
    }

    // Reset approve button
    const approveBtn = document.querySelector('#approveWarningModal .btn-success');
    if (approveBtn) {
        approveBtn.disabled = false;
        approveBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Approve Warning';
    }

    // Clear admin notes
    const adminNotesField = document.getElementById('admin_notes');
    if (adminNotesField) {
        adminNotesField.value = '';
    }
}

// Auto-submit search form when status changes
document.getElementById('status').addEventListener('change', function() {
    this.form.submit();
});

// Universal modal functions - Bootstrap 4 compatible
function showModal(modalId) {
    $('#' + modalId).modal('show');
}

function closeModal(modalId) {
    $('#' + modalId).modal('hide');
}

// Handle clicking outside modals
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });
});
</script>
@endpush

@endsection