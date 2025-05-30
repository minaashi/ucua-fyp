<!-- Add Remarks Modal -->
<div class="modal fade" id="addRemarksModal" tabindex="-1" role="dialog" aria-labelledby="addRemarksModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRemarksModalLabel">Add Remarks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('department.add-remarks') }}" method="POST">
                @csrf
                <input type="hidden" name="report_id" id="remarksReportId">
                <div class="modal-body">
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="4" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Remarks</button>
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