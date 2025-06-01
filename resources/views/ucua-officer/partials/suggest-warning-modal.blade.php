<!-- Suggest Warning Modal -->
<div class="modal fade" id="suggestWarningModal" tabindex="-1" role="dialog" aria-labelledby="suggestWarningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-yellow-600 text-white">
                <h5 class="modal-title" id="suggestWarningModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Suggest Warning Letter
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('ucua.suggest-warning') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="report_id" id="warningReportId">

                    <!-- Report Info Display -->
                    <div class="alert alert-warning mb-4">
                        <h6 class="mb-2"><i class="fas fa-info-circle mr-2"></i>Report Information</h6>
                        <p class="mb-1"><strong>Report ID:</strong> <span id="warningDisplayReportId"></span></p>
                        <p class="mb-0"><strong>Current Status:</strong> <span id="warningDisplayReportStatus"></span></p>
                    </div>

                    <div class="form-group">
                        <label for="warning_type" class="font-weight-bold">Warning Type <span class="text-danger">*</span></label>
                        <select name="warning_type" id="warning_type" class="form-control" required>
                            <option value="">Select warning severity...</option>
                            <option value="minor">Minor Warning - First-time or low-risk violation</option>
                            <option value="moderate">Moderate Warning - Repeated or medium-risk violation</option>
                            <option value="severe">Severe Warning - Serious or high-risk violation</option>
                        </select>
                        <small class="form-text text-muted">Choose the appropriate warning level based on the severity of the safety violation</small>
                    </div>

                    <div class="form-group mt-3">
                        <label for="warning_reason" class="font-weight-bold">Reason for Warning <span class="text-danger">*</span></label>
                        <textarea name="warning_reason" id="warning_reason" class="form-control" rows="3" required
                                  placeholder="Describe the specific safety violation or unsafe behavior that warrants this warning..."></textarea>
                        <small class="form-text text-muted">Provide clear details about what safety rule or procedure was violated</small>
                    </div>

                    <div class="form-group mt-3">
                        <label for="suggested_action" class="font-weight-bold">Suggested Corrective Action <span class="text-danger">*</span></label>
                        <textarea name="suggested_action" id="suggested_action" class="form-control" rows="3" required
                                  placeholder="Recommend specific actions to prevent future violations (e.g., safety training, equipment checks, policy review)..."></textarea>
                        <small class="form-text text-muted">Suggest concrete steps to address the safety issue and prevent recurrence</small>
                    </div>

                    <div class="alert alert-info mt-3">
                        <small><i class="fas fa-info-circle mr-2"></i><strong>Note:</strong> This warning suggestion will be sent to the admin for review and approval before being issued to the employee.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Warning Suggestion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>