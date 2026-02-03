<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{ $pageTitle ?? ($company->company_name ?? 'Acentriagroup.com') }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #ed1c24;
        }

        .logo {
            max-width: 180px;
            margin-bottom: 15px;
        }

        h1 {
            color: #000;
            font-size: 24px;
            margin-bottom: 25px;
        }

        .greeting {
            font-weight: 600;
            margin-bottom: 20px;
        }

        .content {
            margin: 25px 0;
        }

        .credentials {
            background-color: #f7f9fc;
            border-left: 4px solid #ed1c24;
            padding: 15px;
            margin: 20px 0;
        }

        .credentials p {
            margin: 8px 0;
        }

        .credentials strong {
            color: #ed1c24;
            display: inline-block;
            width: 160px;
        }

        .security-note {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }

        .button {
            display: inline-block;
            background-color: #ed1c24;
            color: #fff !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="header">
        @if (isset($companyLogo))
            <img src="/assets/images/brand-logos/main-horizontal-logo.png" height="83" width="auto"
                alt="Acentria International Logo" class="logo">
        @endif
        <h1>Welcome to {{ config('app.name') }}</h1>
    </div>

    <div class="content">
        <p class="greeting">Dear {{ $userFirstname }},</p>

        <p>Your account has been successfully created for our Reinsurance Broking System. As a valued new user, you now
            have full access to our comprehensive reinsurance management platform.</p>

        <div class="credentials">
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Temporary Password:</strong> {{ $temporaryPassword }}</p>
        </div>

        @if ($requiredPasswordReset)
            <div class="security-note">
                <p><strong>Important Security Information:</strong></p>
                <p>You will be required to change your password when you first log in. Please create a strong password
                    that
                    includes at least one uppercase letter, one number, and one special character.</p>
            </div>
        @endif

        <center>
            <a href="{{ $loginUrl }}" class="button">Access Your Account</a>
        </center>

        <p>Our platform provides you with powerful tools to manage your reinsurance operations efficiently:</p>
        <ul>
            <li>Comprehensive portfolio management</li>
            <li>Real-time risk assessment</li>
            <li>Streamlined claims processing</li>
            <li>Custom reporting capabilities</li>
        </ul>

        <p>If you have any questions or need assistance with your account, please don't hesitate to contact our
            dedicated support team at <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>.</p>
    </div>

    <div class="footer">
        <p>Regards,<br>
            The Reinsurance Team<br>
            Acentria International Reinsurance Brokers Limited</p>

        <p>This email contains confidential information intended only for the named recipient. If you received this in
            error, please contact us immediately.</p>
    </div>
</body>

</html>
