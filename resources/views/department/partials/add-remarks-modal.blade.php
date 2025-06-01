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
function addRemarks(reportId) {
    $('#remarksReportId').val(reportId);
    $('#addRemarksModal').modal('show');
}
</script>
@endpush 