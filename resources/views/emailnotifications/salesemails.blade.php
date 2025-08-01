<!DOCTYPE html>
<html>

<head>
    {{-- <title style="font-size: 10px;">{{ $title }}</title> --}}
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>

    <div class="container">
        {{-- <h2>{{ $title }}</h2> --}}

        <p
            style="font-family: Arial, sans-serif; font-size: 14px; color: #000; margin: 0; padding: 0; line-height: 1.0;">
            Dear {{ $salutation }},</p>

        <p style="margin: 0; padding: 0; line-height: 2.0;">{!! html_entity_decode($body) !!}</p>
        {{-- {!! html_entity_decode($detail['details']) !!} --}}
        {{-- {!! html_entity_decode($detail['details']) !!} --}}

        <div class="footer">
            <p>Regards,<br>
                The Reinsurance Team<br>
                Acentria International Reinsurance Brokers Limited</p>

            <p>This email contains confidential information intended only for the named recipient. If you received this
                in
                error, please contact us immediately.</p>
        </div>
    </div>

</body>

</html>
