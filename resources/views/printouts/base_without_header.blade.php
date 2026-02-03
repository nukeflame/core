<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME') }}</title>
    <style>
        header {
            /* background-color: #f0f0f0;  */
            padding: 0px 0px;
            text-align: right;
            margin: 0;
        }

        p {
            margin: 0;
        }

        table {
            /* border-collapse: collapse; */
        }

        th {
            background-color: #dad4d4;
            /* Set your desired background color here */
            border: 1px solid #181212;
            padding: 8px;
            text-align: left;
        }

        .receipt-table {
            width: 100%;
            font-size: 10.0pt;
            font-family: 'Calibri';
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
    </style>
</head>

<body>
    {{-- <header>
        <div class="row">
            <div class="logo">
                <img align="left" src="{{ asset('/assets/images/brand-logos/main-horizontal-logo.png')}}" alt="" style="width: 230px; height: auto;">
            </div>
            <div class="company-info">
                <p>{{ $company->company_name }}</p>
                <p>{{ $company->postal_address }}</p>
                <p>Phone: {{ $company->mobilephone }}</p>
                <p>Email: {{ $company->email }}</p>
            </div>
        </div>
    </header> --}}
    @yield('content')
</body>

</html>
