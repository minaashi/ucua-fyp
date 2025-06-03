<!-- Add Remarks Modal -->
<div class="modal fade" id="addRemarksModal" tabindex="-1" role="dialog" aria-labelledby="addRemarksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-green-50 border-b border-green-200">
                <div>
                    <h5 class="modal-title text-lg font-semibold text-green-800" id="addRemarksModalLabel">Add Department Remark</h5>
                    <p class="text-sm text-green-600 mt-1">
                        Report: <span id="remarksDisplayReportId" class="font-medium"></span> |
                        Status: <span id="remarksDisplayReportStatus" class="font-medium"></span>
                    </p>
                </div>
                <button type="button" class="close text-green-600 hover:text-green-800" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('department.add-remarks') }}" method="POST">
                @csrf
                <input type="hidden" name="report_id" id="remarksReportId">
                <div class="modal-body p-6">
                    <!-- Investigation Update Toggle - ENHANCED -->
                    <div class="mb-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                        <h6 class="font-semibold text-blue-800 mb-3">
                            <i class="fas fa-search-plus mr-1"></i>
                            Investigation Status
                        </h6>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="investigation-toggle"
                                   class="w-5 h-5 rounded border-blue-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-3 text-base font-medium text-blue-800">
                                <i class="fas fa-user-check mr-2 text-blue-600"></i>
                                ✅ I have completed the investigation and identified the violator
                            </span>
                        </label>
                        <p class="text-sm text-blue-700 mt-2 ml-8">
                            <i class="fas fa-info-circle mr-1"></i>
                            Check this box if you know who was responsible for the safety violation. This will allow UCUA officers to issue warning letters to the correct person.
                        </p>
                    </div>

                    <!-- Violator Information Section (Hidden by default) -->
                    <div id="violator-section" class="hidden mb-6 p-5 bg-red-50 border-2 border-red-300 rounded-lg">
                        <h4 class="text-lg font-bold text-red-800 mb-4">
                            <i class="fas fa-user-times mr-2 text-red-600"></i>
                            🚨 VIOLATOR IDENTIFICATION (REQUIRED)
                        </h4>
                        <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded">
                            <p class="text-sm font-medium text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>IMPORTANT:</strong> The person you identify here will receive official warning letters and disciplinary actions. Please ensure accuracy.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="violator_employee_id" class="block text-sm font-bold text-red-700 mb-2">
                                    <i class="fas fa-id-badge mr-1"></i>
                                    Violator Employee ID*
                                </label>
                                <input type="text" name="violator_employee_id" id="violator_employee_id"
                                       class="w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-lg font-medium"
                                       placeholder="Enter violator's employee ID"
                                       style="background-color: #fef2f2;">
                                <p class="text-xs text-red-600 mt-1 font-medium">This person will receive warning letters</p>
                            </div>
                            <div>
                                <label for="violator_name" class="block text-sm font-bold text-red-700 mb-2">
                                    <i class="fas fa-user mr-1"></i>
                                    Violator Full Name*
                                </label>
                                <input type="text" name="violator_name" id="violator_name"
                                       class="w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-lg font-medium"
                                       placeholder="Enter violator's full name"
                                       style="background-color: #fef2f2;">
                            </div>
                            <div class="md:col-span-2">
                                <label for="violator_department" class="block text-sm font-bold text-red-700 mb-2">
                                    <i class="fas fa-building mr-1"></i>
                                    Violator Department*
                                </label>
                                <input type="text" name="violator_department" id="violator_department"
                                       class="w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-lg font-medium"
                                       placeholder="Enter violator's department"
                                       style="background-color: #fef2f2;">
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-red-200 border border-red-400 rounded">
                            <p class="text-sm font-bold text-red-900">
                                <i class="fas fa-shield-alt mr-1"></i>
                                <strong>SYSTEM UPDATE:</strong> Once you submit this form, the violator information will be officially recorded in the system. UCUA officers will be able to suggest warning letters for this person.
                            </p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-comment mr-1"></i>
                            Department Remark
                        </label>
                        <textarea name="remarks" id="remarks" rows="5"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 resize-none"
                                  placeholder="Add your department's remark regarding this report..."
                                  required></textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-shield-alt mr-1"></i>
                            This remark will be submitted confidentially without user identification.
                        </p>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 border-t border-gray-200">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#addRemarksModal').modal('hide')">
                        <i class="fas fa-times mr-1"></i>
                        Cancel
                    </button>
                    <button type="submit" id="submit-remark-btn" class="btn btn-success">
                        <i class="fas fa-comment mr-1"></i>
                        <span id="submit-btn-text">Add Remark</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function addRemarks(reportId, status, reportCode) {
    // Populate report information
    $('#remarksReportId').val(reportId);
    $('#remarksDisplayReportId').text(reportCode);
    $('#remarksDisplayReportStatus').text(status.charAt(0).toUpperCase() + status.slice(1));

    // Clear previous content
    $('#remarks').val('');
    $('#investigation-toggle').prop('checked', false);
    $('#violator-section').addClass('hidden');
    $('#violator_employee_id').val('');
    $('#violator_name').val('');
    $('#violator_department').val('');

    // Show add remarks modal
    $('#addRemarksModal').modal('show');
}

// Handle investigation toggle
$(document).ready(function() {
    $('#investigation-toggle').change(function() {
        if ($(this).is(':checked')) {
            $('#violator-section').removeClass('hidden');
            // Make violator fields required when visible
            $('#violator_employee_id, #violator_name, #violator_department').attr('required', true);
            // Update button text
            $('#submit-btn-text').text('Complete Investigation & Identify Violator');
            $('#submit-remark-btn').removeClass('btn-success').addClass('btn-danger');
            $('#submit-remark-btn i').removeClass('fa-comment').addClass('fa-user-check');
        } else {
            $('#violator-section').addClass('hidden');
            // Remove required attribute when hidden
            $('#violator_employee_id, #violator_name, #violator_department').removeAttr('required');
            // Clear values
            $('#violator_employee_id').val('');
            $('#violator_name').val('');
            $('#violator_department').val('');
            // Reset button text
            $('#submit-btn-text').text('Add Remark');
            $('#submit-remark-btn').removeClass('btn-danger').addClass('btn-success');
            $('#submit-remark-btn i').removeClass('fa-user-check').addClass('fa-comment');
        }
    });

    // Add validation for violator fields
    $('#violator_employee_id, #violator_name').on('input', function() {
        if ($('#investigation-toggle').is(':checked')) {
            var employeeId = $('#violator_employee_id').val().trim();
            var violatorName = $('#violator_name').val().trim();

            if (employeeId && violatorName) {
                $('#submit-btn-text').html('<strong>⚠️ IDENTIFY ' + violatorName.toUpperCase() + ' AS VIOLATOR</strong>');
            } else {
                $('#submit-btn-text').text('Complete Investigation & Identify Violator');
            }
        }
    });
});
</script>
@endpush