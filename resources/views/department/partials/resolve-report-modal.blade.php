<!-- Resolve Report Modal -->
<div class="modal fade" id="resolveReportModal" tabindex="-1" role="dialog" aria-labelledby="resolveReportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resolveReportModalLabel">Resolve Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('department.resolve-report') }}" method="POST">
                @csrf
                <input type="hidden" name="report_id" id="resolveReportId">
                <div class="modal-body">
                    <div class="space-y-4">
                        <div>
                            <label for="resolution_notes" class="block text-sm font-medium text-gray-700">Resolution Notes</label>
                            <textarea name="resolution_notes" id="resolution_notes" rows="4" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      required></textarea>
                        </div>
                        <div>
                            <label for="resolution_date" class="block text-sm font-medium text-gray-700">Resolution Date</label>
                            <input type="date" name="resolution_date" id="resolution_date" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Mark as Resolved</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function resolveReport(reportId) {
    $('#resolveReportId').val(reportId);
    $('#resolveReportModal').modal('show');
}
</script>
@endpush 