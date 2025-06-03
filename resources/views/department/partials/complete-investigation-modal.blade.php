<!-- Complete Investigation Modal -->
<div class="modal fade" id="completeInvestigationModal" tabindex="-1" role="dialog" aria-labelledby="completeInvestigationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('department.complete-investigation') }}" method="POST">
                @csrf
                <input type="hidden" name="report_id" id="investigationReportId">
                
                <div class="modal-header bg-blue-600 text-white">
                    <h5 class="modal-title" id="completeInvestigationModalLabel">
                        <i class="fas fa-search-plus mr-2"></i>
                        Complete Investigation
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-6">
                    <!-- Report Information Display -->
                    <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <h6 class="font-semibold text-gray-700 mb-2">
                            <i class="fas fa-file-alt mr-1"></i>
                            Report Information
                        </h6>
                        <p class="text-sm text-gray-600">
                            <strong>Report ID:</strong> <span id="investigationDisplayReportId"></span><br>
                            <strong>Status:</strong> <span id="investigationDisplayReportStatus"></span>
                        </p>
                    </div>

                    <!-- Investigation Results -->
                    <div class="mb-6">
                        <h6 class="font-semibold text-gray-700 mb-3">
                            <i class="fas fa-clipboard-check mr-1"></i>
                            Investigation Results
                        </h6>
                        
                        <div class="mb-4">
                            <label for="investigation_findings" class="block text-sm font-medium text-gray-700 mb-2">
                                Investigation Findings*
                            </label>
                            <textarea name="investigation_findings" id="investigation_findings" rows="4"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Describe your investigation findings (CCTV review, witness interviews, etc.)"
                                      required></textarea>
                        </div>
                    </div>

                    <!-- VIOLATOR IDENTIFICATION - PROMINENT SECTION -->
                    <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-lg">
                        <h6 class="font-semibold text-red-800 mb-3">
                            <i class="fas fa-user-times mr-1"></i>
                            VIOLATOR IDENTIFICATION (REQUIRED)
                        </h6>
                        <p class="text-sm text-red-700 mb-4">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            You must identify who was responsible for this safety violation. This information will be used for warning letters and disciplinary actions.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="final_violator_employee_id" class="block text-sm font-medium text-red-700 mb-1">
                                    <i class="fas fa-id-badge mr-1"></i>
                                    Violator Employee ID*
                                </label>
                                <input type="text" name="violator_employee_id" id="final_violator_employee_id"
                                       class="w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                       placeholder="Enter violator's employee ID"
                                       required>
                                <p class="text-xs text-red-600 mt-1">This person will receive warning letters</p>
                            </div>

                            <div>
                                <label for="final_violator_name" class="block text-sm font-medium text-red-700 mb-1">
                                    <i class="fas fa-user mr-1"></i>
                                    Violator Full Name*
                                </label>
                                <input type="text" name="violator_name" id="final_violator_name"
                                       class="w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                       placeholder="Enter violator's full name"
                                       required>
                            </div>

                            <div>
                                <label for="final_violator_department" class="block text-sm font-medium text-red-700 mb-1">
                                    <i class="fas fa-building mr-1"></i>
                                    Violator Department*
                                </label>
                                <input type="text" name="violator_department" id="final_violator_department"
                                       class="w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                       placeholder="Enter violator's department"
                                       required>
                            </div>

                            <div>
                                <label for="violator_position" class="block text-sm font-medium text-red-700 mb-1">
                                    <i class="fas fa-briefcase mr-1"></i>
                                    Position/Role
                                </label>
                                <input type="text" name="violator_position" id="violator_position"
                                       class="w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                       placeholder="Enter violator's position">
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-red-100 border border-red-300 rounded">
                            <p class="text-sm text-red-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Important:</strong> Once you submit this form, the violator will be officially identified in the system. UCUA officers will be able to suggest warning letters for this person.
                            </p>
                        </div>
                    </div>

                    <!-- Recommended Actions -->
                    <div class="mb-4">
                        <label for="recommended_actions" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lightbulb mr-1"></i>
                            Recommended Actions
                        </label>
                        <textarea name="recommended_actions" id="recommended_actions" rows="3"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Suggest corrective actions, training, or preventive measures"></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-gray-50 border-t border-gray-200">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle mr-1"></i>
                        Complete Investigation & Identify Violator
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function completeInvestigation(reportId, reportCode, status) {
    // Populate report information
    $('#investigationReportId').val(reportId);
    $('#investigationDisplayReportId').text(reportCode);
    $('#investigationDisplayReportStatus').text(status.charAt(0).toUpperCase() + status.slice(1));

    // Clear previous content
    $('#investigation_findings').val('');
    $('#final_violator_employee_id').val('');
    $('#final_violator_name').val('');
    $('#final_violator_department').val('');
    $('#violator_position').val('');
    $('#recommended_actions').val('');

    // Show modal
    $('#completeInvestigationModal').modal('show');
}
</script>
@endpush
