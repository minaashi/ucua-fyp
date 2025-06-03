@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-white mr-3 text-xl"></i>
                        <h1 class="text-xl font-bold text-white">Admin - Create Safety Report</h1>
                    </div>
                    <a href="{{ route('admin.reports.index') }}" class="bg-white text-blue-800 px-4 py-2 rounded shadow hover:bg-gray-100 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Reports
                    </a>
                </div>
                <p class="text-blue-100 text-sm mt-1">Create safety incident reports on behalf of users</p>
            </div>

            {{-- Show all error messages --}}
            @if ($errors->any())
                <div class="alert alert-danger m-4">
                    <strong>There were some problems with your input:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('admin.reports.store') }}" enctype="multipart/form-data" class="p-6">
                @csrf
                
                <!-- Report Information Section -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Report Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="title">
                                Report Title*
                            </label>
                            <input type="text" id="title" name="title"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('title') }}" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="employee_id">
                                Employee ID*
                            </label>
                            <input type="text" id="employee_id" name="employee_id"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('employee_id') }}" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="department">
                                Department*
                            </label>
                            <input type="text" id="department" name="department"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('department') }}" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="phone">
                                Phone Number*
                            </label>
                            <input type="tel" id="phone" name="phone" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('phone') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Incident Information Section -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Incident Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="non_compliance_type">
                                Non-Compliance Type*
                            </label>
                            <select id="non_compliance_type" name="non_compliance_type" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select type...</option>
                                <option value="unsafe_condition" {{ old('non_compliance_type') == 'unsafe_condition' ? 'selected' : '' }}>Unsafe Condition</option>
                                <option value="unsafe_act" {{ old('non_compliance_type') == 'unsafe_act' ? 'selected' : '' }}>Unsafe Act</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="location">
                                Location*
                            </label>
                            <select id="location" name="location" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select location...</option>
                                <option value="Building A" {{ old('location') == 'Building A' ? 'selected' : '' }}>Building A - Bangunan A</option>
                                <option value="Building B" {{ old('location') == 'Building B' ? 'selected' : '' }}>Building B - Bangunan B</option>
                                <option value="Building C" {{ old('location') == 'Building C' ? 'selected' : '' }}>Building C - Bangunan C</option>
                                <option value="Loading dock" {{ old('location') == 'Loading dock' ? 'selected' : '' }}>Loading dock - Kawasan Pemuat</option>
                                <option value="Container Yard" {{ old('location') == 'Container Yard' ? 'selected' : '' }}>Container Yard - Kawasan Kontena</option>
                                <option value="Security Checkpoint" {{ old('location') == 'Security Checkpoint' ? 'selected' : '' }}>Security Checkpoint - Pusat Pemeriksaan Keselamatan</option>
                                <option value="Other" {{ old('location') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="incident_date">
                                Date and Time of Event*
                            </label>
                            <input type="datetime-local" id="incident_date" name="incident_date" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('incident_date') }}" required>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                You can only select dates and times up to the current moment
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="description">
                                Detailed Description*
                            </label>
                            <textarea id="description" name="description" rows="4"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Please provide specific details about the event..."
                                    required>{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="mt-6 flex gap-4">
                    <button type="button" 
                            onclick="window.location.href='{{ route('admin.reports.index') }}'"
                            class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Create Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Function to set maximum date/time to current moment
function setMaxDateTime() {
    const now = new Date();
    // Format to YYYY-MM-DDTHH:MM (required for datetime-local input)
    const maxDateTime = now.getFullYear() + '-' + 
                       String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(now.getDate()).padStart(2, '0') + 'T' + 
                       String(now.getHours()).padStart(2, '0') + ':' + 
                       String(now.getMinutes()).padStart(2, '0');
    
    const incidentDateInput = document.getElementById('incident_date');
    if (incidentDateInput) {
        incidentDateInput.setAttribute('max', maxDateTime);
    }
}

// Set initial max date/time when page loads
document.addEventListener('DOMContentLoaded', function() {
    setMaxDateTime();
    
    // Update max date/time every minute to handle edge cases
    setInterval(setMaxDateTime, 60000);
    
    // Add validation on form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const incidentDate = document.getElementById('incident_date').value;
            if (incidentDate) {
                const selectedDate = new Date(incidentDate);
                const now = new Date();
                
                if (selectedDate > now) {
                    e.preventDefault();
                    alert('The incident date and time cannot be in the future. Please select a date and time that has already occurred.');
                    return false;
                }
            }
        });
    }
});
</script>
@endsection
