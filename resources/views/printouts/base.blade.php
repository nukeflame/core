<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name'))</title>
    <style>
        @page {
            margin-top: 140px;
            margin-bottom: 100px;
            margin-left: 5%;
            margin-right: 5%;
        }

        body {
            font-size: 10.5pt;
            line-height: 14pt;
            margin: 0;
            padding: 0;
        }

        header {
            position: fixed;
            left: 0px;
            right: 0px;
            top: 0px;
            height: 130px;
            margin-top: -140px;
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

        footer {
            position: fixed;
            left: 0px;
            right: 0px;
            bottom: 0px;
            height: 80px;
            margin-bottom: -100px;
            text-align: center;
            font-size: 7pt;
            font-weight: 500;
            font-style: italic;
            background: #fff;
        }

        .footer-content {
            border-top: 1px solid #000;
            padding-top: 8px;
        }

        .page-number::after {
            content: counter(page);
        }

        p {
            margin: 0;
        }

        th {
            background-color: #ECE9E9;
            border: 1px solid #181212;
            padding: 8px;
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

        .bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }

        .border-bottom {
            border-bottom: 1px solid black;
        }

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

        .prepared-by {
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .uppercase {
            text-transform: uppercase;
        }

        @media print {
            .page-break {
                page-break-before: always;
            }
        }

        .header {
            display: block;
        }

        .no-header .header {
            display: none;
        }

        .company-info p {
            font-size: 10.5pt;
            line-height: 14pt;
        }

        #cover-s {
            padding: 0px;
            font-size: 10.0pt;
            font-family: 'Aptos', sans-serif;
            width: 100%;
        }

        .reinsurer-details {
            width: 100%;
        }

        #cover-header {
            width: 100%;
        }

        #slip-header {
            width: 100%;
        }

        #slip-details tr td.s-l,
        #slip-details tr td.s-r {
            font-size: 10.0pt;
        }

        #slip-details tr td.s-l {
            width: 40% !important;
        }

        #slip-details tr td.s-r {
            width: 60% !important;
        }

        #cover-details,
        .reinsurer-details,
        #credit-details,
        table {
            border-collapse: collapse;
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

        .reinsurer-details td,
        #particular-details td,
        #cover-details td,
        #credit-details td,
        #breakdown-details td {
            text-align: left;
            font-size: 8pt;
            font-family: 'Aptos', sans-serif;
        }

        #particular-details {
            width: 100%;
        }

        #particular-details tr {
            border-bottom: 1px solid #000;
        }

        #particular-details td {
            border-bottom: 1px solid #000;
            padding: 3pt;
            padding-left: 9pt;
            padding-right: 9pt;
        }

        .p-4 {
            padding: 4pt;
            padding-left: 9pt;
            padding-right: 9pt;
        }

        .p-3 {
            padding: 3pt;
        }

        .p-9-l {
            padding: 0pt;
            padding-left: 9pt;
        }

        .p-9-r {
            padding: 0pt;
            padding-right: 9pt;
        }

        .m-0 {
            margin: 0;
        }

        .p-0 {
            padding: 0;
        }

        .p-6 {
            padding: 6pt;
        }

        .pt-4 {
            padding-top: 6px;
        }

        .text-center {
            text-align: center;
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

        .calibri-10 {
            font-size: 10.0pt;
            font-family: 'Aptos', sans-serif;
        }

        .courier-9 {
            font-size: 9.0pt;
            font-family: 'Aptos', sans-serif;
        }

        .courier-10 {
            font-size: 10.0pt;
            font-family: 'Aptos', sans-serif;
        }

        .courier-7 {
            font-size: 7pt;
            font-family: 'Aptos', sans-serif;
        }

        .courier-8 {
            font-size: 8pt;
            font-family: 'Aptos', sans-serif;
        }

        .w-100 {
            width: 100%;
        }

        .no-border {
            border: none;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .p-8 {
            padding: 8px;
        }

        .bottom-border {
            border-bottom: 0.5pt solid #181212;
        }

        .info-box {
            display: inline-block;
            padding: 2px;
            margin-left: 2px;
            min-width: 80px;
            font-size: 8.0pt;
        }

        .fs-9 {
            font-size: 9pt !important;
        }

        .fs-10 {
            font-size: 10pt !important;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

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

        .row-table {
            width: 100%;
            display: table;
            table-layout: fixed;
            position: relative;
            top: 39px;
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

        .brand-logo {}
    </style>
</head>

<body class="pdf_wrapper">
    @if (!isset($disableAutoHeader))
        <header class="logo-header">
            <div class="row-table">
                <div class="logo">
                    <img class="brand-logo" src="data:image/png;base64,<?php echo base64_encode(file_get_contents(base_path('public/assets/images/brand-logos/default-logo.png'))); ?>" alt=""
                        style="width: 210px; height: auto;" />
                </div>
                <div class="company-info">
                    <p>{{ $company->company_name }}</p>
                    <p>{{ $company->postal_address }}</p>
                    <p>Phone: {{ $company->mobilephone }}</p>
                    <p>Email: {{ $company->email }}</p>
                </div>
            </div>
            <hr
                style="padding:0px; border-top: .5pt solid #ddd; margin: 10pt 1pt; margin-left: 5.5pt; position: relative;" />
        </header>
    @endif

    @if (!isset($disableAutoFooter))
        <footer>
            <div class="footer-content">
                <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved. | Page No: <span
                        class="page-number"></span></span>
            </div>
        </footer>
    @endif

    <main>
        @yield('content')
    </main>

    @stack('script')
</body>

</html>
