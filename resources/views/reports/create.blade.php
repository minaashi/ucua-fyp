@extends('layouts.app')

@section('content')
<div class="bg-gray-100 py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-800 px-6 py-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-white mr-3 text-xl"></i>
                    <h1 class="text-xl font-bold text-white">UCUA Incident Report</h1>
                </div>
                <p class="text-blue-100 text-sm mt-1">Report unsafe conditions and acts to maintain workplace safety</p>
            </div>

            {{-- Show all error messages --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>There were some problems with your input:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="p-6">
                @csrf
                
                <!-- Anonymous Reporting Option -->
                <div class="mb-8">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" id="is_anonymous" name="is_anonymous" value="1"
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                       onchange="toggleAnonymousMode()">
                            </div>
                            <div class="ml-3">
                                <label for="is_anonymous" class="text-sm font-medium text-blue-900">
                                    <i class="fas fa-user-secret mr-1"></i>
                                    Submit this report anonymously
                                </label>
                                <p class="text-xs text-blue-700 mt-1">
                                    When checked, your identity will be hidden from all users (admin, UCUA officers, departments) while maintaining the investigation process.
                                </p>
                            </div>
                        </div>

                        <!-- Anonymous Mode Explanation -->
                        <div id="anonymous-explanation" class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded hidden">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-2"></i>
                                <div class="text-xs text-yellow-800">
                                    <p class="font-medium mb-1">Anonymous Reporting Information:</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Your name and email will be hidden from all system users</li>
                                        <li>Employee ID, department, and phone number remain visible for investigation purposes</li>
                                        <li>The report will follow the same review and investigation process</li>
                                        <li>You can still track your report status through your dashboard</li>
                                        <li>Warning letters and resolution processes work normally</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information Section -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Personal Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="employee_id">
                                Employee ID*
                            </label>
                            <input type="text" id="employee_id" name="employee_id"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50"
                                   value="{{ Auth::user()->worker_id ?? '' }}" readonly required>
                            <p class="text-xs text-gray-500 mt-1">Auto-populated from your registration</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="department">
                                Department
                            </label>
                            <input type="text" id="department_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ Auth::user()->department->name ?? 'N/A' }}" readonly>
                            <input type="hidden" name="department_id" value="{{ Auth::user()->department_id }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="phone">
                                Phone Number*
                            </label>
                            <input type="tel" id="phone" name="phone" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                        </div>

                        
                    </div>
                </div>



                <!-- Incident Information Section -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Incident Information</h2>
                    <div class="space-y-4">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category*</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="category_type" value="unsafe_condition" onclick="toggleCategory()" required>
                                    <span class="ml-2">Unsafe Condition</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="category_type" value="unsafe_act" onclick="toggleCategory()" required>
                                    <span class="ml-2">Unsafe Act</span>
                                </label>
                            </div>
                        </div>

                        <!-- Unsafe Conditions Section -->
                        <div id="unsafeConditionSection" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="unsafe_condition">
                                Unsafe Conditions*
                            </label>
                            <select id="unsafe_condition" name="unsafe_condition" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" onchange="toggleOtherField()">
                                <option value="">Select Unsafe Condition...</option>
                                <option value="Slippery floor surface">Slippery floor surface - Permukaan lantai licin</option>
                                <option value="Exposed live wire (Electrical)">Exposed live wire (Electrical) - Penebat wayar elektrik terdedah</option>
                                <option value="Fire & explosion hazards">Gas leak - Kebocoran gas</option>
                                <option value="Other">Other</option>
                            </select>
                            <div id="otherUnsafeConditionDiv" style="display:none;">
                                <label class="block text-sm font-medium text-gray-700 mb-1" for="other_unsafe_condition">
                                    Please specify other unsafe condition
                                </label>
                                <textarea id="other_unsafe_condition" name="other_unsafe_condition" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Please state here"></textarea>
                            </div>
                        </div>

                        <!-- Unsafe Acts Section -->
                        <div id="unsafeActSection" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="unsafe_act">
                                Unsafe Acts*
                            </label>
                            <select id="unsafe_act" name="unsafe_act" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" onchange="toggleOtherField()">
                                <option value="">Select Unsafe Act...</option>
                                <option value="Not wearing proper Personal Protective Equipment (PPE)">Not wearing proper PPE - Tidak memakai PPE yang betul</option>
                                <option value="Speeding inside premise">Speeding inside premise - Memandu laju di dalam premis</option>
                                <option value="Smoking at prohibited area">Smoking at prohibited area - Merokok ditempat yang dilarang</option>
                                <option value="Other">Other</option>
                            </select>
                            <div id="otherUnsafeActDiv" style="display:none;">
                                <label class="block text-sm font-medium text-gray-700 mb-1" for="other_unsafe_act">
                                    Please specify other unsafe act
                                </label>
                                <textarea id="other_unsafe_act" name="other_unsafe_act" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Please state here"></textarea>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="location">
                                Location of event *
                            </label>
                            <select id="location" name="location" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required onchange="toggleOtherField()">
                                <option value="">Select the location of event...</option>
                                <option value="Building A">Building A - Bangunan A</option>
                                <option value="Building B">Building B - Bangunan B</option>
                                <option value="Building C">Building C - Bangunan C</option>
                                <option value="Loading dock">Loading dock - Kawasan Pemuat</option>
                                <option value="Cointaner Yard">Cointaner Yard - Kawasan Kontena</option>
                                <option value="Security Checkpoint">Security Checkpoint - Pusat Pemeriksaan Keselamatan</option>
                                <option value="Other">Other</option>
                            </select>
                            <div id="otherLocationDiv" style="display:none;">
                                <label class="block text-sm font-medium text-gray-700 mb-1" for="other_location">
                                    Please specify other location
                                </label>
                                <textarea id="other_location" name="other_location" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Please state here"></textarea>
                            </div>
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
                             
                                <label for="description" class="block text-sm font-medium text-gray-700">
                            Detailed Description*
                        </label>
                        <textarea id="description" name="description" rows="4"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Please provide specific details about the event..."
                                oninput="this.value = this.value.toUpperCase();" required></textarea>

                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="attachment">
                                Upload Attachment (Optional)
                            </label>
                            <input type="file" id="attachment" name="attachment"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <p class="text-xs text-gray-500 mt-1">Supported formats: JPG, PNG, PDF, DOC (Max: 5MB)</p>
                        </div>
                    </div>
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="mt-6 flex gap-4">
                    <button type="button" 
                            onclick="window.location.href='{{ route('dashboard') }}'"
                            class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleAnonymousMode() {
    const checkbox = document.getElementById('is_anonymous');
    const explanation = document.getElementById('anonymous-explanation');

    if (checkbox.checked) {
        explanation.classList.remove('hidden');
    } else {
        explanation.classList.add('hidden');
    }
}

function toggleCategory() {
    const selectedCategory = document.querySelector('input[name="category_type"]:checked').value;

    document.getElementById('unsafeConditionSection').style.display = (selectedCategory === 'unsafe_condition') ? 'block' : 'none';
    document.getElementById('unsafeActSection').style.display = (selectedCategory === 'unsafe_act') ? 'block' : 'none';

    // Reset values when hidden
    if (selectedCategory === 'unsafe_condition') {
        document.getElementById('unsafe_act').value = '';
        document.getElementById('otherUnsafeActDiv').style.display = 'none';
    } else {
        document.getElementById('unsafe_condition').value = '';
        document.getElementById('otherUnsafeConditionDiv').style.display = 'none';
    }
}

function toggleOtherField() {
    const unsafeCondition = document.getElementById('unsafe_condition');
    const unsafeAct = document.getElementById('unsafe_act');
    const location = document.getElementById('location').value;

    document.getElementById('otherUnsafeConditionDiv').style.display = (unsafeCondition && unsafeCondition.value === 'Other') ? '' : 'none';
    document.getElementById('otherUnsafeActDiv').style.display = (unsafeAct && unsafeAct.value === 'Other') ? '' : 'none';
    document.getElementById('otherLocationDiv').style.display = (location === 'Other') ? '' : 'none';
}

// Function to set maximum date/time with timezone buffer
function setMaxDateTime() {
    const now = new Date();
    // Add 5 minute buffer to handle processing delays
    const maxTime = new Date(now.getTime() + (5 * 60 * 1000));

    // Format to YYYY-MM-DDTHH:MM (required for datetime-local input)
    const maxDateTime = maxTime.getFullYear() + '-' +
                       String(maxTime.getMonth() + 1).padStart(2, '0') + '-' +
                       String(maxTime.getDate()).padStart(2, '0') + 'T' +
                       String(maxTime.getHours()).padStart(2, '0') + ':' +
                       String(maxTime.getMinutes()).padStart(2, '0');

    const incidentDateInput = document.getElementById('incident_date');
    if (incidentDateInput) {
        incidentDateInput.setAttribute('max', maxDateTime);
    }
}

// Set initial max date/time when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Set max datetime immediately
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

                // Add 5 minute buffer to handle processing delays
                const maxAllowedTime = new Date(now.getTime() + (5 * 60 * 1000)); // 5 minutes from now

                if (selectedDate > maxAllowedTime) {
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
