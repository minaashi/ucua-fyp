<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTP Verification - UCUA Reporting System</title>
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
            text-align: center;
            margin-bottom: 30px;
        }
        .otp-container {
            background-color: rgba(246, 248, 246, 0.05);
            border: 2px solid rgba(246, 248, 246, 0.05);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color:rgb(40, 167, 135);
            letter-spacing: 3px;
            font-family: 'Courier New', monospace;
            background-color: rgba(255, 255, 255, 0.05);
            padding: 15px 25px;
            border-radius: 8px;
            border: 1px solidrgb(3, 8, 4);
            display: inline-block;
            margin: 10px 0;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin: 15px 0;
            color: #856404;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="color:rgb(3, 3, 3);">Dear {{ $userName }},</h1>
    </div>

    <p>Your One-Time Password (OTP) for account registration is:</p>

    <div class="otp-container">
        <p style="margin: 0; font-size: 18px; color:rgb(11, 17, 13); font-weight: bold;">Your OTP Code:</p>
        <div class="otp-code">{{ $otp }}</div>
    </div>

    <div class="warning">
        <strong>⚠️ Important:</strong> This code will expire in 5 minutes for security purposes.
    </div>

    <p>Please enter this OTP in the verification form to complete your registration process. Do not share this code with anyone.</p>

    <div class="footer">
        <p>Regards,<br>
        <strong>UCUA Reporting System</strong><br>
        <em>UCUA Department</em></p>
    </div>
</body>
</html>
