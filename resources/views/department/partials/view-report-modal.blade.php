<!-- View Report Modal -->
<div class="modal fade" id="viewReportModal" tabindex="-1" role="dialog" aria-labelledby="viewReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewReportModalLabel">Report Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div>
                        <h6 class="text-sm font-medium text-gray-500">Report ID</h6>
                        <p class="mt-1 text-sm text-gray-900" id="viewReportId"></p>
                    </div>
                    <div>
                        <h6 class="text-sm font-medium text-gray-500">Title</h6>
                        <p class="mt-1 text-sm text-gray-900" id="viewReportTitle"></p>
                    </div>
                    <div>
                        <h6 class="text-sm font-medium text-gray-500">Description</h6>
                        <p class="mt-1 text-sm text-gray-900" id="viewReportDescription"></p>
                    </div>
                    <div>
                        <h6 class="text-sm font-medium text-gray-500">Status</h6>
                        <p class="mt-1 text-sm text-gray-900" id="viewReportStatus"></p>
                    </div>
                    <div>
                        <h6 class="text-sm font-medium text-gray-500">Deadline</h6>
                        <p class="mt-1 text-sm text-gray-900" id="viewReportDeadline"></p>
                    </div>
                    <div>
                        <h6 class="text-sm font-medium text-gray-500">Remarks</h6>
                        <div class="mt-1 space-y-2" id="viewReportRemarks"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadReportDetails(reportId) {
    // Fetch report details via AJAX
    $.get(`/department/reports/${reportId}`, function(data) {
        $('#viewReportId').text('RPT-' + String(data.id).padStart(3, '0'));
        $('#viewReportTitle').text(data.title);
        $('#viewReportDescription').text(data.description);
        $('#viewReportStatus').text(data.status);
        $('#viewReportDeadline').text(data.deadline ? new Date(data.deadline).toLocaleDateString() : 'No Deadline');
        
        // Load remarks
        let remarksHtml = '';
        data.remarks.forEach(remark => {
            remarksHtml += `
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-sm text-gray-900">${remark.content}</p>
                    <p class="text-xs text-gray-500 mt-1">By ${remark.user_name} on ${new Date(remark.created_at).toLocaleString()}</p>
                </div>
            `;
        });
        $('#viewReportRemarks').html(remarksHtml || '<p class="text-sm text-gray-500">No remarks yet</p>');
    });
}

// Update the viewReport function
function viewReport(reportId) {
    loadReportDetails(reportId);
    $('#viewReportModal').modal('show');
}
</script>
@endpush 