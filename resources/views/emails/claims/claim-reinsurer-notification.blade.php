<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $claim->claim_no }} - Claim Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .email-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }

        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .content {
            padding: 40px;
        }

        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .priority-high {
            background-color: #e74c3c;
            color: white;
        }

        .priority-normal {
            background-color: #f39c12;
            color: white;
        }

        .priority-low {
            background-color: #27ae60;
            color: white;
        }

        .claim-details {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }

        .claim-details h3 {
            margin: 0 0 15px 0;
            color: #2c3e50;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ecf0f1;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: #2c3e50;
            min-width: 150px;
        }

        .detail-value {
            color: #5a6c7d;
            flex: 1;
            text-align: right;
        }

        .message-content {
            background-color: #ffffff;
            border: 1px solid #e1e8ed;
            border-radius: 6px;
            padding: 25px;
            margin: 25px 0;
            line-height: 1.8;
        }

        .attachments-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e1e8ed;
        }

        .attachments-section h4 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 16px;
        }

        .attachment-item {
            padding: 10px;
            background-color: white;
            border-radius: 4px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            border: 1px solid #e1e8ed;
        }

        .attachment-item:last-child {
            margin-bottom: 0;
        }

        .attachment-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            opacity: 0.7;
        }

        .footer {
            background-color: #34495e;
            color: #ecf0f1;
            padding: 30px;
            text-align: center;
        }

        .footer h4 {
            margin: 0 0 10px 0;
            color: #3498db;
        }

        .footer p {
            margin: 5px 0;
            font-size: 14px;
            opacity: 0.8;
        }

        .footer a {
            color: #3498db;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #3498db, transparent);
            margin: 30px 0;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .content {
                padding: 20px;
            }

            .detail-row {
                flex-direction: column;
            }

            .detail-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="header">
            <h1>Claim Notification</h1>
            <p>{{ $companyName }}</p>
        </div>

        <!-- Content Section -->
        <div class="content">
            <!-- Priority Badge -->
            @if ($priority)
                <div class="priority-badge priority-{{ $priority }}">
                    {{ ucfirst($priority) }} Priority
                </div>
            @endif

            <!-- Claim Details -->
            <div class="claim-details">
                <h3>📋 Claim Information</h3>
                <div class="detail-row">
                    <span class="detail-label">Claim Number:</span>
                    <span class="detail-value"><strong>{{ $claim->claim_no }}</strong></span>
                </div>
                @if ($reference)
                    <div class="detail-row">
                        <span class="detail-label">Reference:</span>
                        <span class="detail-value">{{ $reference }}</span>
                    </div>
                @endif
                @if ($category)
                    <div class="detail-row">
                        <span class="detail-label">Category:</span>
                        <span class="detail-value">{{ ucfirst($category) }}</span>
                    </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ now()->format('F j, Y \a\t g:i A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Sent by:</span>
                    <span class="detail-value">{{ $senderName }} ({{ $senderEmail }})</span>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Message Content -->
            <div class="message-content">
                {!! $message !!}
            </div>

            <!-- Attachments Section -->
            @if (!empty($attachments))
                <div class="attachments-section">
                    <h4>📎 Attachments ({{ count($attachments) }})</h4>
                    @foreach ($attachments as $attachment)
                        <div class="attachment-item">
                            <svg class="attachment-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                            </svg>
                            <div>
                                <strong>{{ $attachment['name'] }}</strong>
                                <small style="color: #666; margin-left: 10px;">
                                    ({{ number_format($attachment['size'] / 1024, 1) }} KB)
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="divider"></div>

            <!-- Important Notice -->
            <div
                style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 15px; margin: 20px 0;">
                <p style="margin: 0; color: #856404;">
                    <strong>⚠️ Important:</strong> This is an automated notification regarding claim
                    {{ $claim->claim_no }}.
                    Please review all attached documents and respond accordingly. For any questions or clarifications,
                    please contact our claims department.
                </p>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <h4>{{ $companyName }}</h4>
            <p>Professional Reinsurance Services</p>
            <p>Email: <a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a></p>
            <p style="margin-top: 20px; font-size: 12px; opacity: 0.6;">
                © {{ $currentYear }} {{ $companyName }}. All rights reserved.<br>
                This email was sent regarding claim {{ $claim->claim_no }} at {{ now()->format('Y-m-d H:i:s') }}
            </p>
        </div>
    </div>
</body>

</html>
