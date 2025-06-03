<div class="warning-details">
    <!-- Warning Information -->
    <div class="row mb-3">
        <div class="col-md-6">
            <h6 class="font-weight-bold text-primary">Warning ID</h6>
            <p class="mb-2">{{ $warning->formatted_id }}</p>
        </div>
        <div class="col-md-6">
            <h6 class="font-weight-bold text-primary">Status</h6>
            <span class="badge badge-{{ $warning->status === 'pending' ? 'warning' : ($warning->status === 'approved' ? 'success' : ($warning->status === 'rejected' ? 'danger' : 'primary')) }}">
                {{ ucfirst($warning->status) }}
            </span>
        </div>
    </div>

    <!-- Report Information -->
    <div class="card mb-3">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-file-alt mr-2"></i>Related Report</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Report ID:</strong> RPT-{{ str_pad($warning->report->id, 4, '0', STR_PAD_LEFT) }}<br>
                    <strong>Employee:</strong> {{ $warning->report->user->name ?? 'Unknown' }}<br>
                    <strong>Worker ID:</strong> {{ $warning->report->user->worker_id ?? 'N/A' }}
                </div>
                <div class="col-md-6">
                    <strong>Department:</strong> {{ $warning->report->user->department->name ?? 'N/A' }}<br>
                    <strong>Report Date:</strong> {{ $warning->report->created_at->format('M d, Y') }}<br>
                    <strong>Report Status:</strong> {{ ucfirst($warning->report->status) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Violator Information - CRITICAL FOR ADMIN REVIEW -->
    @if(isset($violator))
        <div class="card mb-3 border-{{ $violator->id ? 'success' : 'warning' }}">
            <div class="card-header bg-{{ $violator->id ? 'success' : 'warning' }} text-white">
                <h6 class="mb-0">
                    <i class="fas fa-user-times mr-2"></i>
                    Identified Violator - Warning Recipient
                    @if($violator->id)
                        <span class="badge badge-light text-success ml-2">
                            <i class="fas fa-check-circle mr-1"></i>System User
                        </span>
                    @else
                        <span class="badge badge-light text-warning ml-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>External Person
                        </span>
                    @endif
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Employee ID:</strong><br>
                        <span class="text-primary font-weight-bold">{{ $violator->worker_id ?? $warning->report->violator_employee_id ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Full Name:</strong><br>
                        <span class="text-primary font-weight-bold">{{ $violator->name ?? $warning->report->violator_name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Department:</strong><br>
                        <span class="text-primary font-weight-bold">{{ $warning->report->violator_department ?? 'N/A' }}</span>
                    </div>
                </div>

                @if($violator->id)
                    <div class="alert alert-success">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>System User:</strong> This person has an account in the system and will receive the warning letter via email automatically.
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>External Person:</strong> This person does not have a system account. Warning letter will need to be delivered manually or via alternative means.
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="card mb-3 border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Violator Not Identified - Cannot Approve Warning
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-danger mb-0">
                    <i class="fas fa-times-circle mr-2"></i>
                    <strong>Investigation Required:</strong> The violator has not been identified yet. The handling department must complete their investigation and identify the person responsible before this warning can be approved.
                </div>
            </div>
        </div>
    @endif

    <!-- Investigation Context -->
    @if(isset($investigationRemarks) && $investigationRemarks)
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-search mr-2"></i>
                    Investigation Context
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Investigated By:</strong> {{ $warning->report->handlingDepartment->name ?? 'Unknown Department' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Investigation Date:</strong> {{ $investigationRemarks->created_at->format('M d, Y H:i') }}
                    </div>
                </div>
                <div class="mb-2">
                    <strong>Investigation Findings:</strong>
                    <div class="mt-2 p-3 bg-light rounded">
                        {{ $investigationRemarks->content }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Warning Details -->
    <div class="card mb-3">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-exclamation-triangle mr-2"></i>Warning Details</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Warning Type:</strong>
                    <span class="badge badge-{{ $warning->type === 'minor' ? 'warning' : ($warning->type === 'moderate' ? 'orange' : 'danger') }} ml-2">
                        {{ ucfirst($warning->type) }}
                    </span>
                </div>
                <div class="col-md-6">
                    <strong>Suggested By:</strong> {{ $warning->suggestedBy->name ?? 'Unknown' }}
                </div>
            </div>

            <div class="mb-3">
                <strong>Reason for Warning:</strong>
                <div class="mt-2 p-3 bg-light rounded">
                    {{ $warning->reason }}
                </div>
            </div>

            <div class="mb-3">
                <strong>Suggested Corrective Action:</strong>
                <div class="mt-2 p-3 bg-light rounded">
                    {{ $warning->suggested_action }}
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Actions (if any) -->
    @if($warning->status !== 'pending')
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-user-shield mr-2"></i>Admin Actions</h6>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Reviewed By:</strong> {{ $warning->approvedBy->name ?? 'Unknown' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Review Date:</strong> {{ $warning->approved_at ? $warning->approved_at->format('M d, Y H:i') : 'N/A' }}
                    </div>
                </div>
                
                @if($warning->admin_notes)
                    <div class="mb-2">
                        <strong>Admin Notes:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            {{ $warning->admin_notes }}
                        </div>
                    </div>
                @endif
                
                @if($warning->warning_message && $warning->status === 'approved')
                    <div class="mb-2">
                        <strong>Final Warning Message:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            {{ $warning->warning_message }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Email Status (if sent) -->
    @if($warning->status === 'sent')
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-envelope mr-2"></i>Email Status</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Sent Date:</strong> {{ $warning->sent_at ? $warning->sent_at->format('M d, Y H:i') : 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Email Status:</strong> 
                        <span class="badge badge-{{ $warning->email_delivery_status === 'sent' ? 'success' : 'danger' }}">
                            {{ ucfirst($warning->email_delivery_status ?? 'Unknown') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
