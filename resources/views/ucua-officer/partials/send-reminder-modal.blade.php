<!-- Send Reminder Modal -->
<div class="modal fade" id="sendReminderModal" tabindex="-1" role="dialog" aria-labelledby="sendReminderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendReminderModalLabel">Send Reminder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('ucua.send-reminder') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="report_id" id="reportId">
                    <div class="form-group">
                        <label for="reminder_type">Reminder Type</label>
                        <select name="reminder_type" id="reminder_type" class="form-control" required>
                            <option value="">Select reminder type...</option>
                            <option value="gentle">Gentle Reminder</option>
                            <option value="urgent">Urgent Reminder</option>
                            <option value="final">Final Warning</option>
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label for="reminder_message">Additional Message</label>
                        <textarea name="reminder_message" id="reminder_message" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group mt-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="extend_deadline" name="extend_deadline">
                            <label class="custom-control-label" for="extend_deadline">Extend Deadline</label>
                        </div>
                    </div>
                    <div class="form-group mt-3" id="new_deadline_group" style="display: none;">
                        <label for="new_deadline">New Deadline</label>
                        <input type="date" name="new_deadline" id="new_deadline" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Reminder</button>
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