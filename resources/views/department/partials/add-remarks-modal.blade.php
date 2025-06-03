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
                    <!-- Investigation Update Toggle -->
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="investigation-toggle" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-search mr-1"></i>
                                This is an investigation update with violator identification
                            </span>
                        </label>
                    </div>

                    <!-- Violator Information Section (Hidden by default) -->
                    <div id="violator-section" class="hidden mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="text-sm font-semibold text-yellow-800 mb-3">
                            <i class="fas fa-user-check mr-1"></i>
                            Violator Identification
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="violator_employee_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Employee ID*
                                </label>
                                <input type="text" name="violator_employee_id" id="violator_employee_id"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                       placeholder="Enter violator's employee ID">
                            </div>
                            <div>
                                <label for="violator_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Full Name*
                                </label>
                                <input type="text" name="violator_name" id="violator_name"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                       placeholder="Enter violator's full name">
                            </div>
                            <div class="md:col-span-2">
                                <label for="violator_department" class="block text-sm font-medium text-gray-700 mb-1">
                                    Department
                                </label>
                                <input type="text" name="violator_department" id="violator_department"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                       placeholder="Enter violator's department">
                            </div>
                        </div>
                        <p class="text-xs text-yellow-700 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            This information will update the report and enable UCUA officers to issue targeted warning letters.
                        </p>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-comment mr-1"></i>
                        Add Remark
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
            $('#violator_employee_id, #violator_name').attr('required', true);
        } else {
            $('#violator-section').addClass('hidden');
            // Remove required attribute when hidden
            $('#violator_employee_id, #violator_name').removeAttr('required');
            // Clear values
            $('#violator_employee_id').val('');
            $('#violator_name').val('');
            $('#violator_department').val('');
        }
    });
});
</script>
@endpush