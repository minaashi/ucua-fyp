<!-- Send Reminder Modal -->
<div class="modal fade" id="sendReminderModal" tabindex="-1" role="dialog" aria-labelledby="sendReminderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-red-600 text-white">
                <h5 class="modal-title" id="sendReminderModalLabel">
                    <i class="fas fa-bell mr-2"></i>Send Follow-up Reminder
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('ucua.send-reminder') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="report_id" id="reminderReportId">

                    <!-- Report Info Display -->
                    <div class="alert alert-danger mb-4">
                        <h6 class="mb-2"><i class="fas fa-info-circle mr-2"></i>Report Information</h6>
                        <p class="mb-1"><strong>Report ID:</strong> <span id="reminderDisplayReportId"></span></p>
                        <p class="mb-0"><strong>Current Status:</strong> <span id="reminderDisplayReportStatus"></span></p>
                    </div>

                    <div class="form-group">
                        <label for="reminder_type" class="font-weight-bold">Reminder Type <span class="text-danger">*</span></label>
                        <select name="reminder_type" id="reminder_type" class="form-control" required>
                            <option value="">Select reminder urgency level...</option>
                            <option value="gentle">Gentle Reminder - Polite follow-up</option>
                            <option value="urgent">Urgent Reminder - Requires immediate attention</option>
                            <option value="final">Final Notice - Last warning before escalation</option>
                        </select>
                        <small class="form-text text-muted">Choose the appropriate reminder level based on how overdue the report is</small>
                    </div>

                    <div class="form-group mt-3">
                        <label for="reminder_message" class="font-weight-bold">Additional Message</label>
                        <textarea name="reminder_message" id="reminder_message" class="form-control" rows="4"
                                  placeholder="Add any specific instructions, concerns, or additional context for this reminder..."></textarea>
                        <small class="form-text text-muted">This message will be included with the reminder notification</small>
                    </div>

                    <div class="form-group mt-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="extend_deadline" name="extend_deadline">
                            <label class="custom-control-label font-weight-bold" for="extend_deadline">Extend Deadline</label>
                        </div>
                        <small class="form-text text-muted">Check this if you want to give the department more time to complete the task</small>
                    </div>

                    <div class="form-group mt-3" id="new_deadline_group" style="display: none;">
                        <label for="new_deadline" class="font-weight-bold">New Deadline <span class="text-danger">*</span></label>
                        <input type="date" name="new_deadline" id="new_deadline" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        <small class="form-text text-muted">New deadline must be at least 1 day from today</small>
                    </div>

                    <div class="alert alert-info mt-3">
                        <small><i class="fas fa-info-circle mr-2"></i><strong>Note:</strong> This reminder will be sent to the assigned department and logged in the report history.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-paper-plane mr-2"></i>Send Reminder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('extend_deadline').addEventListener('change', function() {
        const newDeadlineGroup = document.getElementById('new_deadline_group');
        newDeadlineGroup.style.display = this.checked ? 'block' : 'none';
        document.getElementById('new_deadline').required = this.checked;
    });
</script>
@endpush 