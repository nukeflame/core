<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailData['subject'] }}</title>
    <style>
        body {
            font-family: 'Aptos', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .claim-details {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
        }

        .highlight {
            background-color: #fff3cd;
            padding: 10px;
            border-left: 4px solid #ffc107;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="content">
        {!! $messageContent !!}

        <p>If you have any questions regarding this claim, please contact us with the tracking ID:
            <strong>{{ $trackingId }}</strong>
        </p>
    </div>

    {{-- <div class="footer">
        <p>This is an automated notification. Please do not reply directly to this email.</p>
        <p>Tracking ID: {{ $trackingId }}</p>
        <p>Sent on: {{ $sentAt->format('Y-m-d H:i:s T') }}</p>
    </div> --}}
</body>

</html>
