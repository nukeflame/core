@extends('printouts.base')

@section('content')
    <style>
        /* Base styles */
        #breakdown-details {
            width: 100%;
        }

        #breakdown-details tr,
        #breakdown-details td {
            border-bottom: 1px solid #000;
            padding: 3pt 9pt;
        }

        .date_generated {
            position: fixed;
            bottom: 18px;
            left: 0px;
            right: 0px;
            height: 14px;
            text-align: center;
            padding-top: 5px;
            font-size: 7pt;
            margin-top: 10px;
            font-weight: 500;
        }


        .page-section {
            position: relative;
            overflow: visible;
        }

        .reinsurer-page {
            page-break-before: always;
            font-family: inherit;
        }

        .first-page {
            page-break-before: auto;
        }

        .no-break {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /* Content layout */
        .content-row {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            page-break-inside: auto;
            /* Allow breaking between rows */
        }

        .content-cell {
            display: inline-block;
            vertical-align: top;
            padding: 5px;
            font-size: 10.0pt;
        }


        .wrap-text {
            word-wrap: break-word;
            white-space: normal;
            max-width: 290px;
        }

        .wrap-text-wordings {
            word-wrap: break-word;
            white-space: normal;
            max-width: 100%;
        }


        /* Header and content spacing */
        .logo-header {
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }

        .main-content {
            margin-top: 20px;
        }

        /* Force page breaks between major sections */
        .major-section {
            page-break-before: auto;
            page-break-after: auto;
        }


        .details-section {
            page-break-inside: auto;
        }


        .page-number::after {
            content: counter(page);
        }


        .reinsurer-page {
            min-height: 100vh;
            margin-bottom: 50px;
        }

        .main-content {
            margin-bottom: 60px;
            padding-bottom: 20px;
            font-family: inherit;
            font-size: 10.0pt;
        }

        @page {
            margin-bottom: 5mm;

            @bottom-right {
                content: counter(page);
            }
        }

        .content-cell1 {
            page-break-inside: avoid;
            break-inside: avoid;
            position: relative;
            font-size: 10.0pt;
        }
    </style>

    @php
        $disableAutoHeader = true;
        $disableAutoFooter = true;
        // dd($stage);
    @endphp
    @if ($stage == 4)
        <header class="logo-header">
            <div class="row">
                <div class="logo">
                    <?php
                    $logoPath = public_path('logo.png');
                    
                    $imgSrc = '';
                    
                    if (file_exists($logoPath) && is_readable($logoPath)) {
                        $imageData = file_get_contents($logoPath);
                        if ($imageData !== false) {
                            $base64 = base64_encode($imageData);
                            $imgSrc = 'data:image/png;base64,' . $base64;
                        }
                    }
                    ?>

                    <div class="logo">
                        <?php if (!empty($imgSrc)) : ?>
                        <img align="right" src="<?php echo $imgSrc; ?>" alt="" style="width: 230px; height: auto;">
                        <?php endif; ?>
                    </div>


                </div>
                <div class="company-info" align="left">
                    <p>{{ $company->company_name }}</p>
                    <p>{{ $company->postal_address }}</p>
                    <p>Phone: {{ $company->mobilephone }}</p>
                    <p>Email: {{ $company->email }}</p>
                </div>
            </div>
        </header>
        {{-- <!-- @foreach ($shares as $index => $sh) --> --}}
        {{-- <!-- <div class="reinsurer-page{{ $index === 0 ? ' first-page' : '' }}"> --> --}}
        <div class="main-content">
            <div class="content-row">
                <div class="content-cell" style="width: 100%;">
                    <b>{{ $tender->tender_no }}</b> <br><br>
                    <p>{{ now()->format('F j, Y') }}</p>
                    <br>
                    <p>
                        {{ firstUpper($customer->name) }}<br>
                        {{ firstUpper($customer->postal_address) }}<br>
                        {{ firstUpper($customer->street) }}.<br>
                        {{ $customer->city }},{{ $customer->country_iso }}
                    </p>
                    <br>
                    <br>
                    <p>Attention: {{ $contact_person }},</p>
                    <br>
                    <p><u><strong>RE:{{ $tender->commence_year }} REINSURANCE BROKER TENDER PROCESS</strong></u></p>
                    <br>
                    <p>The above refers;</p>
                    <br>
                    <p>We acknowledge receipt of your letter dated {{ $tender->email_dated }}, prequalifying Acentria
                        International Reinsurance brokers to participate in the {{ firstUpper($customer->name) }}
                        {{ $tender->commence_year }} Reinsurance Broker tender presentation.</p>
                    <br>

                    @if (!isset($our_checkbox))
                        <p>We thank you for prequalifying and giving us a chance to review the existing treaty programs and
                            receiving the documents to aid in our review analysis and formulate an optimum
                            {{ $tender->commence_year }} treaty programs.<br>
                    @endif

                    @if (isset($our_checkbox))
                        <p>We thank you for prequalifying and giving us a chance to review the existing treaty programs and
                            look forward to receiving the below additional documents to aid in our review analysis and
                            formulate an optimum 2024 treaty programs.<br>
                        <ol>
                            @foreach ($our_checkbox as $name)
                                <li>{{ $name }}</li>
                            @endforeach
                        </ol>
                        </p>
                    @endif
                    <br>
                    @if (isset($received_docs_checkboxes))
                        We have currently received the below documents:
                        <ol>
                            @foreach ($received_docs_checkboxes as $name)
                                <li>{{ $name }}</li>
                            @endforeach
                        </ol>
                    @endif
                    <br>
                    <p>We look forward to your feedback</p><br>
                    <p>Yours sincerely,<br>
                        FOR ACENTRIA INTERNATIONAL REINSURANCE BROKERS LIMITED
                    </p>
                    <br>
                    <div class="signature">
                        <p>Moses Mbwika Musau<br>
                            MANAGING DIRECTOR/CEO</p>
                    </div>
                </div>
            </div>
        </div>


        {{-- <!-- </div> --> --}}
        {{-- <!-- @endforeach --> --}}
        </div>
        </div>
        <div class="date_generated">
            <p style="font-size: 8.0pt; ">Generated on behalf of Acentria International on
                {{ now()->format('F j, Y') }}.</p>

        </div>
        {{-- <!-- @endforeach --> --}}

        <div class="footer">
            <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved. | Page No: <span
                    class="page-number"></span></span>
        </div>
    @else
        <header class="logo-header">
            <div class="row">
                <div class="logo">
                    <?php
                    $logoPath = public_path('logo.png');
                    
                    $imgSrc = '';
                    
                    if (file_exists($logoPath) && is_readable($logoPath)) {
                        $imageData = file_get_contents($logoPath);
                        if ($imageData !== false) {
                            $base64 = base64_encode($imageData);
                            $imgSrc = 'data:image/png;base64,' . $base64;
                        }
                    }
                    ?>

                    <div class="logo">
                        <?php if (!empty($imgSrc)) : ?>
                        <img align="right" src="<?php echo $imgSrc; ?>" alt="" style="width: 230px; height: auto;">
                        <?php endif; ?>
                    </div>


                </div>
                <div class="company-info" align="left">
                    <p>{{ $company->company_name }}</p>
                    <p>{{ $company->postal_address }}</p>
                    <p>Phone: {{ $company->mobilephone }}</p>
                    <p>Email: {{ $company->email }}</p>
                </div>
            </div>
        </header>
        {{-- <!-- @foreach ($shares as $index => $sh) --> --}}
        {{-- <!-- <div class="reinsurer-page{{ $index === 0 ? ' first-page' : '' }}"> --> --}}
        <div class="main-content">
            <div class="content-row">
                <div class="content-cell" style="width: 100%;">
                    <b>{{ $tender_no }}</b><br>
                    <br>
                    <p>{{ now()->format('F j, Y') }}</p>
                    <br>
                    <p>
                        {{ firstUpper($customer->name) }}<br>
                        {{ firstUpper($customer->postal_address) }}<br>
                        {{ firstUpper($customer->street) }}.<br>
                        {{ $customer->city }},{{ $customer->country_iso }}
                    </p>
                    <br>
                    <br>
                    <p>Attention: {{ $contact_person }},</p>
                    <br>
                    <p><u><strong>RE: CONFIRMATION OF INTEREST TO PARTICIPATE IN REINSURANCE BROKER TENDER
                                PROCESS</strong></u>
                    </p>
                    <br>
                    <p>We acknowledge with gratitude your letter dated {{ $email_dated }}, inviting Acentria International
                        Reinsurance
                        Brokers Ltd to participate in the reinsurance broker tender process for your treaty programs
                        commencing
                        in {{ $commence_year }}.</p>
                    <br>
                    <p>We hereby confirm our keen interest and commitment to participate in the tender process. As
                        requested,
                        please find attached the following documents:</p>
                    <ol>
                        @foreach ($doc_names as $name)
                            <li>{{ $name }}</li>
                        @endforeach
                    </ol>
                    <p>We confirm that all required documents are attached with this letter for your evaluation. We look
                        forward
                        to the next steps in the tender process and appreciate the opportunity to collaborate with
                        {{ firstUpper($customer->name) }}<br>.</p>
                    <br>
                    <p>Yours faithfully,</p>
                    <p>Moses Musau<br>
                        MANAGING DIRECTOR<br>
                        Acentria International Reinsurance Brokers Ltd</p>
                </div>
            </div>
        </div>


        {{-- <!-- </div> --> --}}
        {{-- <!-- @endforeach --> --}}
        </div>
        </div>
        <div class="date_generated">
            <p style="font-size: 8.0pt; ">Generated on behalf of Acentria International on
                {{ now()->format('F j, Y') }}.</p>

        </div>
        {{-- <!-- @endforeach --> --}}

        <div class="footer">
            <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved. | Page No: <span
                    class="page-number"></span></span>
        </div>
    @endif
@endsection
