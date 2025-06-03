<!-- Add Comment Modal -->
<div class="modal fade" id="addCommentModal" tabindex="-1" role="dialog" aria-labelledby="addCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-blue-50 border-b border-blue-200">
                <div>
                    <h5 class="modal-title text-lg font-semibold text-blue-800" id="addCommentModalLabel">Add UCUA Comment</h5>
                    <p class="text-sm text-blue-600 mt-1">
                        Report: <span id="commentDisplayReportId" class="font-medium"></span> |
                        Status: <span id="commentDisplayReportStatus" class="font-medium"></span>
                    </p>
                </div>
                <button type="button" class="close text-blue-600 hover:text-blue-800" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="POST" action="{{ route('ucua.add-remarks') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="report_id" id="commentReportId">
                
                <div class="modal-body p-6">
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-comment mr-1"></i>
                            Comment
                        </label>
                        <textarea name="content" id="content" rows="5"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none"
                                  placeholder="Add your comment regarding this report..."
                                  required></textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            This comment will be visible to all authorized users.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-paperclip mr-1"></i>
                            Attachment (Optional)
                        </label>
                        <input type="file" name="attachment" id="attachment"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                        <p class="text-xs text-gray-500 mt-1">
                            Supported formats: JPG, PNG, GIF, PDF, DOC, DOCX, XLS, XLSX, TXT (Max: 10MB)
                        </p>
                    </div>
                </div>

                <div class="modal-footer bg-gray-50 border-t border-gray-200">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-comment mr-1"></i>
                        Add Comment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>