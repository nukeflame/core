<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            background: #f5f5f5;
            padding: 20px;
        }

        .content {
            padding: 20px;
        }

        .content p,
        .content h1,
        .content h2,
        .content h3,
        .content h4,
        .content h5,
        .content h6 {
            padding: 0px;
            margin: 0px;
        }
    </style>
</head>

<body>
    {{-- <div class="header">
        <h2>Policy Renewal Notice</h2>
    </div> --}}
    <div class="content">
        @if ($emailContent)
            {!! $emailContent !!}
        @else
            <p>Dear {{ $policy->client_name }},</p>

            <p>This is a reminder that your policy <strong>{{ $policy->policy_number }}</strong>
                is due for renewal in {{ $days_until_renewal }} days.</p>

            <p>Policy Details:</p>
            <ul>
                <li>Policy Number: {{ $policy->policy_number }}</li>
                <li>Renewal Date: {{ $policy->renewal_date->format('F j, Y') }}</li>
            </ul>

            <p>Please contact your broker to discuss renewal terms.</p>

            <p>Best regards,</p>
        @endif
    </div>
</body>

</html>
