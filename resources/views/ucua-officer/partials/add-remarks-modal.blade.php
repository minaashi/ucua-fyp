<!-- Add Remarks Modal -->
<div class="modal fade" id="addRemarksModal" tabindex="-1" role="dialog" aria-labelledby="addRemarksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-green-600 text-white">
                <h5 class="modal-title" id="addRemarksModalLabel">
                    <i class="fas fa-comment mr-2"></i>Add Discussion Comment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" onclick="closeModal('addRemarksModal')"></button>
            </div>
            <form action="{{ route('ucua.add-remarks') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="report_id" id="remarksReportId">

                    <!-- Report Info Display -->
                    <div class="alert alert-success mb-4">
                        <h6 class="mb-2"><i class="fas fa-info-circle mr-2"></i>Report Information</h6>
                        <p class="mb-1"><strong>Report ID:</strong> <span id="remarksDisplayReportId"></span></p>
                        <p class="mb-0"><strong>Current Status:</strong> <span id="remarksDisplayReportStatus"></span></p>
                    </div>

                    <div class="form-group">
                        <label for="content" class="font-weight-bold">Discussion Comment <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" class="form-control" rows="5" required
                                  placeholder="Add your investigation notes, observations, or discussion points about this report..."></textarea>
                        <small class="form-text text-muted">This comment will be visible to other officers and administrators reviewing this report</small>
                    </div>

                    <div class="alert alert-info mt-3">
                        <small><i class="fas fa-info-circle mr-2"></i><strong>Note:</strong> Discussion comments help track the investigation progress and facilitate communication between team members.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" onclick="closeModal('addRemarksModal')">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus mr-2"></i>Add Comment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>