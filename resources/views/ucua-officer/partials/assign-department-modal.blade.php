<!-- Assign Department Modal -->
<div class="modal fade" id="assignDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="assignDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-blue-600 text-white">
                <h5 class="modal-title" id="assignDepartmentModalLabel">
                    <i class="fas fa-building mr-2"></i>Assign Department to Report
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('ucua.assign-department') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="report_id" id="assignReportId">

                    <!-- Report Info Display -->
                    <div class="alert alert-info mb-4">
                        <h6 class="mb-2"><i class="fas fa-info-circle mr-2"></i>Report Information</h6>
                        <p class="mb-1"><strong>Report ID:</strong> <span id="displayReportId"></span></p>
                        <p class="mb-0"><strong>Current Status:</strong> <span id="displayReportStatus"></span></p>
                    </div>

                    <div class="form-group">
                        <label for="department_id" class="font-weight-bold">Select Department <span class="text-danger">*</span></label>
                        <select name="department_id" id="department_id" class="form-control" required>
                            <option value="">Choose the appropriate department...</option>
                            @if(isset($departments))
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <small class="form-text text-muted">Select the department best suited to handle this report</small>
                    </div>

                    <div class="form-group mt-3">
                        <label for="deadline" class="font-weight-bold">Set Deadline <span class="text-danger">*</span></label>
                        <input type="date" name="deadline" id="deadline" class="form-control" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        <small class="form-text text-muted">Deadline must be at least 1 day from today</small>
                    </div>

                    <div class="form-group mt-3">
                        <label for="assignment_remark" class="font-weight-bold">Assignment Notes</label>
                        <textarea name="assignment_remark" id="assignment_remark" class="form-control" rows="4"
                                  placeholder="Provide context for why this department was chosen, specific instructions, or priority level..."></textarea>
                        <small class="form-text text-muted">These notes will help the department understand the assignment context</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check mr-2"></i>Assign Department
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>