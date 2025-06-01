<!-- Resolve Report Modal -->
<div class="modal fade" id="resolveReportModal" tabindex="-1" role="dialog" aria-labelledby="resolveReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-purple-50 border-b border-purple-200">
                <div>
                    <h5 class="modal-title text-lg font-semibold text-purple-800" id="resolveReportModalLabel">Mark Report as Resolved</h5>
                    <p class="text-sm text-purple-600 mt-1">
                        Report: <span id="resolveDisplayReportId" class="font-medium"></span> |
                        Status: <span id="resolveDisplayReportStatus" class="font-medium"></span>
                    </p>
                </div>
                <button type="button" class="close text-purple-600 hover:text-purple-800" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('department.resolve-report') }}" method="POST">
                @csrf
                <input type="hidden" name="report_id" id="resolveReportId">
                <div class="modal-body p-6">
                    <div class="space-y-4">
                        <div>
                            <label for="resolution_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clipboard-check mr-1"></i>
                                Resolution Notes
                            </label>
                            <textarea name="resolution_notes" id="resolution_notes" rows="5"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 resize-none"
                                      placeholder="Describe how this report was resolved and any actions taken..."
                                      required></textarea>
                        </div>
                        <div>
                            <label for="resolution_date" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-check mr-1"></i>
                                Resolution Date
                            </label>
                            <input type="date" name="resolution_date" id="resolution_date"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 border-t border-gray-200">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i>
                        Mark as Resolved
                    </button>
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