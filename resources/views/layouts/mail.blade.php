<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Acentria Group</title>
    <style>
        /* Reset styles */
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f5f5f5;
            -webkit-text-size-adjust: none;
        }

        * {
            box-sizing: border-box;
        }

        /* Main container */
        .email-wrapper {
            width: 100%;
            margin: 0;
            background-color: #ffffff;
        }

        /* Content area */
        .email-container {
            padding: 30px;
        }

        /* Header section */
        .email-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .email-header h1 {
            color: #444444;
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 5px 0;
        }

        /* Body content */
        .email-body {
            color: #333333;
            font-size: 15px;
        }

        .email-body p {
            margin: 0 0 15px 0;
        }

        /* Leave details section */
        .leave-details {
            background-color: #f9f9f9;
            border-left: 4px solid #FF0000;
            padding: 20px;
            margin: 20px 0;
        }

        .leave-details h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #444444;
            font-size: 18px;
            font-weight: 600;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 8px 5px 8px 0;
            vertical-align: top;
        }

        .details-table td:first-child {
            color: #666666;
            width: 120px;
        }

        /* Footer section with company info and logo */
        .company-footer {
            margin-top: 40px;
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
            display: table;
            width: 100%;
        }

        .company-info {
            display: table-cell;
            vertical-align: top;
            width: 60%;
        }

        .company-logo {
            display: table-cell;
            vertical-align: top;
            width: 40%;
            text-align: right;
        }

        .company-name {
            color: #FF0000;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .contact-info {
            margin-bottom: 5px;
            color: #555555;
            font-size: 14px;
        }

        .logo {
            max-width: 200px;
            height: auto;
        }

        /* Banner image */
        .banner-image {
            width: 100%;
            height: auto;
            display: block;
            margin-top: 20px;
        }

        /* Social icons */
        .social-icons {
            margin-top: 10px;
            text-align: right;
        }

        .social-icon {
            display: inline-block;
            width: 24px;
            height: 24px;
            margin-left: 5px;
        }

        /* Footer & disclaimer */
        .disclaimer {
            font-size: 10px;
            color: #777777;
            line-height: 1.3;
            margin-top: 15px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="email-wrapper" style="width: 100%; margin: 0; background-color: #ffffff;">
        <div class="email-container" style="padding: 30px;">
            @yield('content')

            <!-- Company Info and Logo Side by Side -->
            <div class="company-footer">
                <div class="company-info">
                    <div class="company-name">Acentria Group</div>
                    <div class="contact-info">📍 West Park Towers, 9th floor, Mpesi Lane, Muthithi Road</div>
                    <div class="contact-info">📮 P.O Box 5864-00100 Nairobi, Kenya | 📞 +254 705 200 222</div>
                    <div class="contact-info">📧 info@acentriagroup.com | 🌐 www.acentriagroup.com</div>
                </div>
                <div class="company-logo">
                    <img src="{{ config('app.url') . '/assets/images/brand-logos/main-horizontal-logo.png' }}"
                        alt="Acentria Logo" class="logo">
                    {{-- <div class="social-icons">
                        <a href="https://facebook.com/acentriagroup"><img
                                src="https://hrms.acentriagroup.com/images/icons/facebook.png" alt="Facebook"
                                class="social-icon"></a>
                        <a href="https://instagram.com/acentriagroup"><img
                                src="https://hrms.acentriagroup.com/images/icons/instagram.png" alt="Instagram"
                                class="social-icon"></a>
                        <a href="https://twitter.com/acentriagroup"><img
                                src="https://hrms.acentriagroup.com/images/icons/twitter.png" alt="Twitter"
                                class="social-icon"></a>
                        <a href="https://linkedin.com/company/acentriagroup"><img
                                src="https://hrms.acentriagroup.com/images/icons/linkedin.png" alt="LinkedIn"
                                class="social-icon"></a>
                        <a href="https://blog.acentriagroup.com"><img
                                src="https://hrms.acentriagroup.com/images/icons/blog.png" alt="Blog"
                                class="social-icon"></a>
                    </div> --}}
                </div>
            </div>

            <!-- Banner Image Below -->
            <img src="{{ config('app.url') . '/emailbanner.jpg' }}" alt="Acentria Banner" class="banner-image">

            <!-- Disclaimer -->
            <div class="disclaimer">
                DISCLAIMER: This email message and any file(s) transmitted with it is intended solely for the individual
                or entity to whom it is addressed and may contain confidential and/or legally privileged information
                which confidentiality and/or privilege is not lost or waived by reason of mistaken transmission. If you
                have received this message by error, you are not authorized to view disseminate distribute or copy the
                message without the written consent of Acentria Group and are you requested to contact the sender by
                telephone or e-mail and destroy the original. Although Acentria Group takes all reasonable precautions
                to ensure that this message and any file transmitted with it is virus free, Acentria Group accepts no
                liability for any damage that may be caused by any virus transmitted by this email.
            </div>
        </div>
    </div>
</body>

</html>
