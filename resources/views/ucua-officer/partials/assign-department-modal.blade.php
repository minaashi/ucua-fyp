<!-- Assign Department Modal -->
<div class="modal fade" id="assignDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="assignDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignDepartmentModalLabel">Assign Department</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('ucua.assign-department') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="report_id" id="reportId">
                    <div class="form-group">
                        <label for="department">Select Department</label>
                        <select name="department" id="department" class="form-control" required>
                            <option value="">Select a department...</option>
                            <option value="Port Security & Safety (PSS)">Security & Safety Department</option>
                            <option value="Maintenance & Repair (M&R)">Maintenance & Repair Department</option>
                            <option value="Electrical & Service)">Electrical & Service Department</option>
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label for="deadline">Set Deadline</label>
                        <input type="date" name="deadline" id="deadline" class="form-control" required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="initial_remarks">Initial Remarks (Optional)</label>
                        <textarea name="initial_remarks" id="initial_remarks" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Department</button>
                </div>
            </form>
        </div>
    </div>
</div> 