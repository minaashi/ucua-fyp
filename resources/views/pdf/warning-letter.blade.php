<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Official Safety Warning Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .letterhead {
            text-align: center;
            border-bottom: 3px solid #dc3545;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .letterhead h1 {
            color: #dc3545;
            font-size: 24px;
            margin: 0;
        }
        .letterhead h2 {
            color: #666;
            font-size: 16px;
            margin: 5px 0;
        }
        .warning-header {
            background-color: #dc3545;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .employee-details {
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        .employee-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .employee-details td {
            padding: 5px 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .employee-details td:first-child {
            font-weight: bold;
            width: 30%;
        }
        .violation-section {
            margin: 20px 0;
            padding: 15px;
            border-left: 4px solid #dc3545;
            background-color: #fff5f5;
        }
        .violation-section h3 {
            color: #dc3545;
            margin-top: 0;
        }
        .warning-level {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .warning-level.minor {
            background-color: #d4edda;
            color: #155724;
        }
        .warning-level.moderate {
            background-color: #fff3cd;
            color: #856404;
        }
        .warning-level.severe {
            background-color: #f8d7da;
            color: #721c24;
        }
        .signature-section {
            margin-top: 40px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        .signature-box {
            width: 45%;
            display: inline-block;
            vertical-align: top;
            margin-right: 10%;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 40px;
            margin-bottom: 5px;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
        .urgent-notice {
            background-color: #dc3545;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="letterhead">
        <h1>{{ config('app.name', 'UCUA') }}</h1>
        <h2>Safety Department</h2>
        <p>Official Safety Warning Letter</p>
    </div>

    <div class="warning-header">
        🚨 SAFETY VIOLATION WARNING LETTER
    </div>

    <div style="text-align: right; margin-bottom: 20px;">
        <strong>Date:</strong> {{ now()->format('F j, Y') }}<br>
        <strong>Warning ID:</strong> {{ $warning->formatted_id }}<br>
        <strong>Report Reference:</strong> #{{ $report->id }}
    </div>

    <div class="employee-details">
        <h3 style="margin-top: 0; color: #dc3545;">Employee Information</h3>
        <table>
            <tr>
                <td>Full Name:</td>
                <td>{{ $recipient->name }}</td>
            </tr>
            <tr>
                <td>Employee ID:</td>
                <td>{{ $recipient->worker_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Department:</td>
                <td>{{ $recipient->department->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{ $recipient->email }}</td>
            </tr>
        </table>
    </div>

    <div style="margin: 20px 0;">
        <p><strong>Warning Level:</strong> 
            <span class="warning-level {{ $warning->type }}">{{ ucfirst($warning->type) }} Warning</span>
        </p>
    </div>

    @if($warning->type === 'severe')
    <div class="urgent-notice">
        ⚠️ URGENT: This is a SEVERE safety warning requiring immediate attention and corrective action
    </div>
    @endif

    <div class="violation-section">
        <h3>Incident Details</h3>
        <p><strong>Incident Date:</strong> {{ $report->incident_date->format('F j, Y') }}</p>
        <p><strong>Location:</strong> {{ $report->location }}</p>
        <p><strong>Category:</strong> {{ ucfirst(str_replace('_', ' ', $report->category)) }}</p>
        
        @if($report->unsafe_condition)
        <p><strong>Unsafe Condition:</strong> {{ $report->unsafe_condition }}</p>
        @endif
        
        @if($report->unsafe_act)
        <p><strong>Unsafe Act:</strong> {{ $report->unsafe_act }}</p>
        @endif
        
        <p><strong>Description:</strong></p>
        <p>{{ $report->description }}</p>
    </div>

    <div class="violation-section">
        <h3>Reason for Warning</h3>
        <p>{{ $warning->reason }}</p>
    </div>

    <div class="violation-section">
        <h3>Required Corrective Action</h3>
        <p>{{ $warning->suggested_action }}</p>
    </div>

    <div style="margin: 30px 0; padding: 15px; border: 2px solid #dc3545; background-color: #fff5f5;">
        <h3 style="color: #dc3545; margin-top: 0;">Important Notice</h3>
        <ul style="margin: 0; padding-left: 20px;">
            <li>This warning is issued in accordance with company safety policies</li>
            <li>This warning will remain on your employment record</li>
            <li>Failure to comply with corrective actions may result in further disciplinary measures</li>
            <li>Repeated safety violations may lead to escalation, including possible suspension or termination</li>
            <li>You have the right to respond to this warning within 7 days</li>
        </ul>
    </div>

    @if($warning->admin_notes)
    <div class="violation-section">
        <h3>Additional Notes</h3>
        <p>{{ $warning->admin_notes }}</p>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>Employee Acknowledgment:</strong></p>
            <p>I acknowledge receipt of this warning letter and understand the corrective actions required.</p>
            <div class="signature-line"></div>
            <p>Employee Signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date</p>
        </div>

        <div class="signature-box">
            <p><strong>Supervisor/Manager:</strong></p>
            <p>{{ $warning->approvedBy->name ?? 'Safety Officer' }}</p>
            <div class="signature-line"></div>
            <p>Supervisor Signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date</p>
        </div>
    </div>

    <div style="margin-top: 40px; font-size: 11px; color: #666;">
        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Employee must acknowledge receipt by signing above</li>
            <li>Implement all corrective actions within the specified timeframe</li>
            <li>Supervisor to monitor compliance and follow up as necessary</li>
            <li>Copy of this warning to be filed in employee's personnel record</li>
        </ol>
    </div>

    <div class="footer">
        <p>This document was generated electronically by the {{ config('app.name', 'UCUA') }} Safety Management System</p>
        <p>Warning ID: {{ $warning->formatted_id }} | Generated: {{ now()->format('F j, Y \a\t g:i A') }}</p>
        @if($warning->approvedBy)
        <p>Approved by: {{ $warning->approvedBy->name }} on {{ $warning->approved_at->format('F j, Y') }}</p>
        @endif
    </div>
</body>
</html>
