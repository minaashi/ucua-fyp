<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safety Warning Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
        }
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-level {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
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
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details-table th,
        .details-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .details-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
        }
        .action-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .urgent {
            background-color: #dc3545;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚨 SAFETY WARNING LETTER</h1>
        <p>{{ $companyName ?? 'UCUA' }} - UCUA Department</p>
    </div>

    <div class="content">
        <h2>Dear {{ $recipient->name }},</h2>

        @if($warning->type === 'severe')
        <div class="urgent">
            ⚠️ URGENT: This is a SEVERE safety warning requiring immediate attention
        </div>
        @endif

        <div class="warning-box">
            <p><strong>Warning Level:</strong> 
                <span class="warning-level {{ $warning->type }}">{{ ucfirst($warning->type) }} Warning</span>
            </p>
            <p>This official warning letter is issued due to a safety violation identified in your recent report.</p>
        </div>

        <table class="details-table">
            <tr>
                <th>Employee Name</th>
                <td>{{ $recipient->name }}</td>
            </tr>
            <tr>
                <th>Employee ID</th>
                <td>{{ $recipient->worker_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Department</th>
                <td>{{ $recipient->department->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Report ID</th>
                <td>#{{ $report->id }}</td>
            </tr>
            <tr>
                <th>Incident Date</th>
                <td>{{ $report->incident_date->format('F j, Y') }}</td>
            </tr>
            <tr>
                <th>Warning Date</th>
                <td>{{ $warning->created_at->format('F j, Y') }}</td>
            </tr>
        </table>

        <h3>Violation Details</h3>
        <div class="warning-box">
            <p><strong>Reason for Warning:</strong></p>
            <p>{{ $warning->reason }}</p>

            @if($report->unsafe_condition)
            <p><strong>Unsafe Condition Identified:</strong></p>
            <p>{{ $report->unsafe_condition }}</p>
            @endif

            @if($report->unsafe_act)
            <p><strong>Unsafe Act Identified:</strong></p>
            <p>{{ $report->unsafe_act }}</p>
            @endif

            <p><strong>Location:</strong> {{ $report->location }}</p>
            <p><strong>Report Description:</strong></p>
            <p>{{ $report->description }}</p>
        </div>

        <h3>Required Corrective Action</h3>
        <div class="warning-box">
            <p><strong>Immediate Actions Required:</strong></p>
            <p>{{ $warning->suggested_action }}</p>

            <p><strong>Compliance Deadline:</strong> Please acknowledge receipt and implement corrective actions within 7 days of receiving this warning.</p>
        </div>

        @if($template && $emailContent)
        <h3>Additional Information</h3>
        <div class="warning-box">
            {!! nl2br(e($emailContent)) !!}
        </div>
        @endif

        <h3>Important Notice & Next Steps</h3>
        <div class="warning-box">
            <p><strong>Record Keeping:</strong></p>
            <ul>
                <li>This warning will remain on your employment record as per company policy</li>
                <li>Warning ID: {{ $warning->formatted_id }} for future reference</li>
            </ul>

            <p><strong>Required Actions:</strong></p>
            <ul>
                <li>Acknowledge receipt of this warning by contacting your supervisor within 48 hours</li>
                <li>Implement all corrective actions within the specified timeframe</li>
                <li>Attend any required safety training or meetings as directed</li>
            </ul>

            <p><strong>Consequences of Non-Compliance:</strong></p>
            <ul>
                <li>Failure to comply with corrective actions may result in further disciplinary measures</li>
                <li>Repeated safety violations may lead to escalation, including possible suspension or termination</li>
                <li>Additional safety violations within 3 months may trigger automatic escalation procedures</li>
            </ul>

            <p><strong>Support & Questions:</strong></p>
            <ul>
                <li>Contact the UCUA Department immediately if you have questions about this warning</li>
                <li>Request additional safety training if needed to prevent future violations</li>
                <li>You have the right to respond to this warning within 7 days if you wish to provide additional context</li>
            </ul>
        </div>

        @if($warning->type === 'severe')
        <div class="urgent">
            Multiple safety violations may result in escalation to disciplinary action, including possible suspension or termination.
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ url('/dashboard') }}" class="action-button">View Your Safety Record</a>
        </div>

        <div class="footer">
            <p><strong>This is an automated message from the {{ $companyName ?? 'UCUA' }} Reporting System.</strong></p>
            <p>For questions or concerns, please contact your supervisor or the UCUA Department.</p>
            <p>Warning ID: {{ $warning->formatted_id }} | Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</p>
            
            @if($warning->approvedBy)
            <p>Approved by: {{ $warning->approvedBy->name }} on {{ $warning->approved_at->format('F j, Y') }}</p>
            @endif
        </div>
    </div>
</body>
</html>
