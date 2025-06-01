<!-- Suggest Warning Modal -->
<style>
.text-orange { color: #f97316 !important; }
.border-orange { border-color: #f97316 !important; }
.border-left { border-left: 3px solid; }
</style>
<div class="modal fade" id="suggestWarningModal" tabindex="-1" role="dialog" aria-labelledby="suggestWarningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-yellow-600 text-white">
                <h5 class="modal-title" id="suggestWarningModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Suggest Warning Letter
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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

                        <!-- Warning Type Guidelines -->
                        <div class="mt-3">
                            <div class="card">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 text-dark"><i class="fas fa-info-circle mr-2"></i>Warning Type Guidelines</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="border-left border-warning pl-3">
                                                <h6 class="text-warning mb-2"><i class="fas fa-exclamation-circle mr-1"></i>Minor Warning</h6>
                                                <ul class="small mb-0">
                                                    <li>First-time violations</li>
                                                    <li>Low safety risk</li>
                                                    <li>Minor PPE issues</li>
                                                    <li>Procedural oversights</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="border-left border-orange pl-3">
                                                <h6 class="text-orange mb-2"><i class="fas fa-exclamation-triangle mr-1"></i>Moderate Warning</h6>
                                                <ul class="small mb-0">
                                                    <li>Repeated violations</li>
                                                    <li>Medium safety risk</li>
                                                    <li>Ignoring safety protocols</li>
                                                    <li>Equipment misuse</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="border-left border-danger pl-3">
                                                <h6 class="text-danger mb-2"><i class="fas fa-ban mr-1"></i>Severe Warning</h6>
                                                <ul class="small mb-0">
                                                    <li>High safety risk</li>
                                                    <li>Willful negligence</li>
                                                    <li>Endangering others</li>
                                                    <li>Multiple violations</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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

                        <!-- Examples of Corrective Actions -->
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="toggleExamples()">
                                <i class="fas fa-lightbulb mr-1"></i><span id="examplesButtonText">View Examples</span>
                            </button>
                            <div class="mt-2" id="correctiveActionExamples" style="display: none;">
                                <div class="card card-body bg-light">
                                    <h6 class="mb-2">Example Corrective Actions:</h6>
                                    <ul class="small mb-0">
                                        <li><strong>Training:</strong> "Mandatory PPE training session within 7 days"</li>
                                        <li><strong>Equipment:</strong> "Daily equipment safety checks before use"</li>
                                        <li><strong>Supervision:</strong> "Increased supervision for 2 weeks"</li>
                                        <li><strong>Documentation:</strong> "Review and sign safety procedures"</li>
                                        <li><strong>Assessment:</strong> "Safety competency re-assessment required"</li>
                                        <li><strong>Mentoring:</strong> "Pair with experienced worker for guidance"</li>
                                        <li><strong>Review:</strong> "Weekly safety performance review meetings"</li>
                                        <li><strong>Certification:</strong> "Complete safety certification course"</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <small><i class="fas fa-info-circle mr-2"></i><strong>Note:</strong> This warning suggestion will be sent to the admin for review and approval before being issued to the employee.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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

<script>
function toggleExamples() {
    const examplesDiv = document.getElementById('correctiveActionExamples');
    const buttonText = document.getElementById('examplesButtonText');

    if (examplesDiv.style.display === 'none') {
        examplesDiv.style.display = 'block';
        buttonText.textContent = 'Hide Examples';
    } else {
        examplesDiv.style.display = 'none';
        buttonText.textContent = 'View Examples';
    }
}
</script>