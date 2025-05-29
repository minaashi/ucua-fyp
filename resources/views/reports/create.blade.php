@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
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

            <!-- Form -->
            <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="p-6">
                @csrf
                
                <!-- Personal Information Section -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Personal Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="employee_id">
                                Employee ID*
                            </label>
                            <input type="text" id="employee_id" name="employee_id" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="department">
                                Department*
                            </label>
                            <select id="department" name="department" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->name }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="phone">
                                Phone Number*
                            </label>
                            <input type="tel" id="phone" name="phone" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                        </div>

                        <div class="flex items-center mt-4">
                            <input type="checkbox" id="is_anonymous" name="is_anonymous" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <label for="is_anonymous" class="ml-2 block text-sm text-gray-700">
                                I want my information to be anonymous
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Incident Information Section -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Incident Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="unsafe_condition">
                                Unsafe Conditions*
                            </label>
                            <select id="unsafe_condition" name="unsafe_condition" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required onchange="toggleOtherField()">
                                <option value="">Select Unsafe Condition...</option>
                                <option value="Slippery floor surface">Slippery floor surface - Permukaan lantai licin</option>
                                <option value="Exposed live wire (Electrical)">Exposed live wire (Electrical)- Penebat wayar elektrik terdedah</option>
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="unsafe_act">
                                Unsafe Acts*
                            </label>
                            <select id="unsafe_act" name="unsafe_act" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required onchange="toggleOtherField()">
                                <option value="">Select Unsafe Act...</option>
                                <option value="Not wearing proper Personal Protective Equipment (PPE)">Not wearing proper Personal Protective Equipment (PPE)- Tidak memakai Alat Pelindung Diri (PPE) yang betul</option>
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
                                   required>
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
                                Upload Attachment*
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
function toggleOtherField() {
    const unsafeCondition = document.getElementById('unsafe_condition');
    const unsafeAct = document.getElementById('unsafe_act');
    const location = document.getElementById('location').value;
    // Show/hide 'Other' textareas
    document.getElementById('otherUnsafeConditionDiv').style.display = (unsafeCondition.value === 'Other') ? '' : 'none';
    document.getElementById('otherUnsafeActDiv').style.display = (unsafeAct.value === 'Other') ? '' : 'none';
    document.getElementById('otherLocationDiv').style.display = (location === 'Other') ? '' : 'none';
    // Mutual exclusion logic
    if (unsafeCondition.value && unsafeCondition.value !== '') {
        unsafeAct.value = '';
        unsafeAct.disabled = true;
        document.getElementById('otherUnsafeActDiv').style.display = 'none';
    } else {
        unsafeAct.disabled = false;
    }
    if (unsafeAct.value && unsafeAct.value !== '') {
        unsafeCondition.value = '';
        unsafeCondition.disabled = true;
        document.getElementById('otherUnsafeConditionDiv').style.display = 'none';
    } else {
        unsafeCondition.disabled = false;
    }
}
</script>
@endsection
