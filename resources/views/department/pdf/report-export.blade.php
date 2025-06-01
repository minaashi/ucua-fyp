<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Export - RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 30%;
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-resolved {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .description-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 15px;
            margin: 10px 0;
        }
        .remarks-section {
            background-color: #f8fafc;
            border-left: 4px solid #1e40af;
            padding: 15px;
            margin: 10px 0;
        }
        .remark-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .remark-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .remark-content {
            margin-bottom: 5px;
        }
        .remark-meta {
            font-size: 10px;
            color: #666;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">UCUA Port Safety System</div>
        <div class="title">Safety Report Export</div>
        <div class="subtitle">Report ID: RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}</div>
    </div>

    <!-- Report Information -->
    <div class="section">
        <div class="section-title">Report Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Report ID:</div>
                <div class="info-value">RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Employee ID:</div>
                <div class="info-value">{{ $report->employee_id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Reporter:</div>
                <div class="info-value">{{ $report->user->name ?? 'Anonymous' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $report->phone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Department:</div>
                <div class="info-value">{{ $report->department }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Category:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $report->category)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Location:</div>
                <div class="info-value">{{ $report->location }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Incident Date:</div>
                <div class="info-value">{{ $report->incident_date ? $report->incident_date->format('M d, Y h:i A') : 'Not specified' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-badge {{ $report->status === 'resolved' ? 'status-resolved' : 'status-pending' }}">
                        {{ ucfirst($report->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Details -->
    <div class="section">
        <div class="section-title">Assignment Details</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Assigned to:</div>
                <div class="info-value">{{ $report->handlingDepartment->name ?? 'Not assigned' }}</div>
            </div>
            @if($report->deadline)
            <div class="info-row">
                <div class="info-label">Deadline:</div>
                <div class="info-value">{{ $report->deadline->format('M d, Y') }}</div>
            </div>
            @endif
            @if($report->resolved_at)
            <div class="info-row">
                <div class="info-label">Resolved Date:</div>
                <div class="info-value">{{ $report->resolved_at->format('M d, Y h:i A') }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Description -->
    <div class="section">
        <div class="section-title">Incident Description</div>
        <div class="description-box">
            {{ $report->description }}
        </div>
    </div>

    <!-- Safety Issue Details -->
    <div class="section">
        <div class="section-title">Safety Issue Details</div>
        @if($report->unsafe_condition)
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Unsafe Condition:</div>
                <div class="info-value">{{ $report->unsafe_condition }}</div>
            </div>
            @if($report->other_unsafe_condition)
            <div class="info-row">
                <div class="info-label">Other Condition:</div>
                <div class="info-value">{{ $report->other_unsafe_condition }}</div>
            </div>
            @endif
        </div>
        @endif

        @if($report->unsafe_act)
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Unsafe Act:</div>
                <div class="info-value">{{ $report->unsafe_act }}</div>
            </div>
            @if($report->other_unsafe_act)
            <div class="info-row">
                <div class="info-label">Other Act:</div>
                <div class="info-value">{{ $report->other_unsafe_act }}</div>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Resolution Details -->
    @if($report->status === 'resolved' && $report->resolution_notes)
    <div class="section">
        <div class="section-title">Resolution Details</div>
        <div class="description-box">
            {{ $report->resolution_notes }}
        </div>
    </div>
    @endif

    <!-- Remarks -->
    @if($report->remarks && $report->remarks->count() > 0)
    <div class="section">
        <div class="section-title">Department Remarks</div>
        <div class="remarks-section">
            @foreach($report->remarks as $remark)
            <div class="remark-item">
                <div class="remark-content">{{ $remark->content }}</div>
                <div class="remark-meta">
                    Added on {{ $remark->created_at->format('M d, Y h:i A') }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was exported by {{ $department->name }} on {{ now()->format('M d, Y h:i A') }}</p>
        <p>UCUA Port Safety & Security System - Confidential Document</p>
    </div>
</body>
</html>
