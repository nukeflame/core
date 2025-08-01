<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Welcome to ReInsurance Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            box-sizing: border-box;
            background-color: #f8fafc;
            color: #3d4852;
            height: 100%;
            line-height: 1.4;
            margin: 0;
            width: 100% !important;
        }

        .container {
            max-width: 580px;
            margin: 0 auto;
            padding: 0;
            width: 580px;
        }

        .email-body {
            background-color: #ffffff;
            border: 1px solid #e8e5ef;
            border-radius: 2px;
            box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015);
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .email-content {
            padding: 25px;
        }

        h1 {
            color: #2d3748;
            font-size: 18px;
            font-weight: bold;
            margin-top: 0;
            text-align: left;
        }

        p {
            color: #3d4852;
            font-size: 16px;
            line-height: 1.5em;
            margin-top: 0;
            text-align: left;
        }

        .credentials-box {
            background-color: #f8f9ff;
            border: 1px solid #e3e8f4;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }

        .button {
            background-color: #1a56db;
            border-radius: 4px;
            color: #ffffff;
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            padding: 12px 24px;
            text-align: center;
            text-decoration: none;
            margin: 10px 0 20px 0;
        }

        .footer {
            font-size: 12px;
            padding: 25px;
            text-align: center;
            color: #718096;
        }

        .security-notice {
            background-color: #fff8f3;
            border-left: 4px solid #f97316;
            padding: 12px 15px;
            margin: 20px 0;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="container" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td align="center" class="email-body">
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="email-content">
                                        <h1>Welcome to {{ config('app.name') }}</h1>

                                        <p>Dear {{ $user->first_name }},</p>

                                        <p>Your account has been successfully created for the ReInsurance Portal. As a
                                            new user, you now have access to our comprehensive reinsurance management
                                            system.</p>

                                        <div class="credentials-box">
                                            <p><strong>Username:</strong> [Username]</p>
                                            <p><strong>Temporary Password:</strong> [TemporaryPassword]</p>
                                        </div>

                                        <div align="center">
                                            <a href="[LoginURL]" class="button">Access Portal Now</a>
                                        </div>

                                        <div class="security-notice">
                                            <strong>Important Security Information:</strong><br>
                                            You will be required to change your password when you first log in. Please
                                            create a strong password that includes at least one uppercase letter, one
                                            number, and one special character.
                                        </div>

                                        <p>Your account gives you access to:</p>
                                        <ul>
                                            <li>Policy management dashboard</li>
                                            <li>Claims processing tools</li>
                                            <li>Risk assessment analytics</li>
                                            <li>Reporting and documentation center</li>
                                        </ul>

                                        <p>If you have any questions or need assistance with your account, please
                                            contact our support team at [SupportEmail].</p>

                                        <p>Thank you for joining our reinsurance platform.</p>

                                        <p>Regards,<br>
                                            ReInsurance Portal Team</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table class="footer" align="center" width="100" cellpadding="0" cellspacing="0"
                                role="presentation">
                                <tr>
                                    <td align="center">
                                        <p>© 2025 ReInsurance Portal. All rights reserved.</p>
                                        <p>This email contains confidential information and is intended only for the
                                            named recipient.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>