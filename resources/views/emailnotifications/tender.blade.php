<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Tender Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
        }

        .header {
            background-color: #f8f8f8;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #000000;
        }

        .content {
            background: #fff;
            padding: 20px;
        }

        .content p {
            margin: 0 0 10px;
            color: #000000;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="content">
            <p>Dear {{ $recipientName }},</p>
            <p>We acknowledge receipt of your letter dated {{ $tender->email_dated }}.<br>
                We thank you for inviting us to participate in the tender and confirm our interest <br> in participating
                in
                the tender and attach herewith the requested information.<br>
                We look forward to your feedback.</p>
            <p>Best regards,<br>Acentria</p>
        </div>
    </div>
</body>

</html>
