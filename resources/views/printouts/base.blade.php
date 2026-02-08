<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name'))</title>
    <style>
        /* Page Setup */
        @page {
            margin-top: 3%;
            margin-bottom: 1%;
            margin-left: 4%;
            margin-right: 4%;
        }

        /* Base Styles */
        * {
            box-sizing: border-box;
        }

        body {
            font-size: 10.5pt;
            line-height: 14pt;
            margin: 0;
            padding: 0;
            font-family: 'Aptos', sans-serif;
        }

        /* Header Styles */
        header {
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            background: #fff;
            z-index: 1000;
        }

        header.header-default {
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            background: #fff;
            padding: 10px;
            text-align: right;
            height: 80px;
            z-index: 1000;
            font-size: 10.5pt;
            line-height: 14pt;
        }

        /* Footer Styles */
        footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            height: 60px;
            text-align: center;
            font-size: 7pt;
            font-weight: 500;
            font-style: italic;
            background: #fff;
        }

        .footer-content {
            border-top: 1px solid #000;
            padding-top: 0px;
            margin-top: 5px;
        }

        .page-number::after {
            content: counter(page);
        }

        /* Typography */
        p {
            margin: 0;
        }

        /* Table Styles */
        table {
            border-collapse: collapse;
        }

        th {
            background-color: #ECE9E9;
            border: 1px solid #181212;
            padding: 8px;
            text-align: left;
        }

        td {
            text-align: left;
        }

        .receipt-table {
            width: 100%;
            font-size: 10.0pt;
            font-family: 'Aptos', sans-serif;
        }

        .receipt-table td {
            margin-bottom: 1px;
            padding-bottom: 1px;
        }

        /* Specific Table Styles */
        #cover-s {
            padding: 0;
            font-size: 10.0pt;
            font-family: 'Aptos', sans-serif;
            width: 100%;
        }

        #cover-header,
        #slip-header {
            width: 100%;
        }

        #slip-details tr td.s-l {
            font-size: 10.0pt;
            width: 40%;
        }

        #slip-details tr td.s-r {
            font-size: 10.0pt;
            width: 60%;
        }

        #cover-details {
            padding-left: 5pt;
            width: 100%;
            margin-bottom: 5px;
        }

        #cover-details td {
            font-size: 8pt;
            padding: 4px;
            font-family: 'Aptos', sans-serif;
        }

        .reinsurer-details,
        #particular-details,
        #credit-details,
        #breakdown-details {
            width: 100%;
        }

        .reinsurer-details td,
        #particular-details td,
        #cover-details td,
        #credit-details td,
        #breakdown-details td {
            text-align: left;
            font-size: 8pt;
            font-family: 'Aptos', sans-serif;
        }

        #particular-details tr {
            border-bottom: 1px solid #000;
        }

        #particular-details td {
            border-bottom: 1px solid #000;
            padding: 3pt 9pt;
        }

        /* Utility Classes - Padding */
        .p-0 {
            padding: 0;
        }

        .p-3 {
            padding: 3pt;
        }

        .p-4 {
            padding: 4pt 9pt;
        }

        .p-6 {
            padding: 6pt;
        }

        .p-8 {
            padding: 8px;
        }

        .p-9-l {
            padding: 0 0 0 9pt;
        }

        .p-9-r {
            padding: 0 9pt 0 0;
        }

        .pt-4 {
            padding-top: 6px;
        }

        /* Utility Classes - Margin */
        .m-0 {
            margin: 0;
        }

        /* Utility Classes - Spacing */
        .spacing-top {
            padding-top: 10px;
        }

        .spacing-large-top {
            padding-top: 90px;
        }

        .spacing-large-bottom {
            padding-bottom: 50px;
        }

        .spacing-bottom {
            padding-bottom: 10px;
        }

        /* Utility Classes - Text */
        .bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        /* Utility Classes - Font Sizes */
        .courier-7,
        .fs-7 {
            font-size: 7pt;
            font-family: 'Aptos', sans-serif;
        }

        .courier-8,
        .fs-8 {
            font-size: 8pt;
            font-family: 'Aptos', sans-serif;
        }

        .courier-9,
        .fs-9 {
            font-size: 9pt;
            font-family: 'Aptos', sans-serif;
        }

        .courier-10,
        .calibri-10,
        .fs-10 {
            font-size: 10pt;
            font-family: 'Aptos', sans-serif;
        }

        /* Utility Classes - Borders */
        .border-bottom,
        .bottom-border {
            border-bottom: 1px solid #000;
        }

        .hr-line {
            border-top: 0.5pt solid #000;
            padding: 0;
            margin: 2px;
        }

        .hr-line-btm {
            border-bottom: 0.5pt solid #000;
            padding: 0;
            margin: 2px;
        }

        .no-border {
            border: none;
        }

        /* Utility Classes - Width */
        .w-100 {
            width: 100%;
        }

        /* Layout Components */
        .info-box {
            display: inline-block;
            padding: 2px;
            margin-left: 2px;
            min-width: 80px;
            font-size: 8.0pt;
        }

        .prepared-by {
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* Page Break Controls */
        .fac-page {
            page-break-after: always;
            page-break-inside: avoid;
            min-height: 0;
            position: relative;
        }

        .fac-page:last-child {
            page-break-after: auto;
        }

        .first-page {
            page-break-before: avoid;
        }

        @media print {
            .page-break {
                page-break-before: always;
            }
        }

        /* Header Layout */
        .row-table {
            width: 100%;
            display: table;
            table-layout: fixed;
            position: relative;
            top: 22px;
            height: 112px;
        }

        .logo {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: left;
        }

        .company-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .company-info p {
            font-size: 10.5pt;
            line-height: 15pt;
        }

        .brand-logo {
            width: 210px;
            height: auto;
        }

        /* Visibility Controls */
        .header {
            display: block;
        }

        .no-header .header {
            display: none;
        }
    </style>
</head>

<body class="pdf_wrapper">
    {{-- Auto Header --}}
    @if (!isset($disableAutoHeader) || !$disableAutoHeader)
        <header class="logo-header">
            <div class="row-table">
                <div class="logo">
                    @if (isset($company) && file_exists(base_path('public/assets/images/brand-logos/default-logo.png')))
                        <img class="brand-logo"
                            src="data:image/png;base64,{{ base64_encode(file_get_contents(base_path('public/assets/images/brand-logos/default-logo.png'))) }}"
                            alt="{{ $company->company_name ?? 'Company Logo' }}" />
                    @endif
                </div>
                <div class="company-info">
                    @if (isset($company))
                        <p>{{ $company->company_name ?? '' }}</p>
                        <p>{{ $company->postal_address ?? '' }}</p>
                        <p>Phone: {{ $company->mobilephone ?? '' }}</p>
                        <p>Email: {{ $company->email ?? '' }}</p>
                    @endif
                </div>
            </div>
            <hr style="padding: 0; border-top: 0.5pt solid #ddd;  margin: 0pt 1pt 10pt 5.5pt; clear: both;" />
        </header>
        <div style="height: 122px; clear: both; background: transparent; border: none; outline: none;"></div>
    @endif

    {{-- Auto Footer --}}
    @if (!isset($disableAutoFooter) || !$disableAutoFooter)
        <footer>
            <div class="footer-content">
                <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved. | Page No: <span
                        class="page-number"></span></span>
            </div>
        </footer>
    @endif

    {{-- Main Content --}}
    <div>
        @yield('content')
    </div>

    @stack('script')
</body>

</html>
