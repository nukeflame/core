@extends('printouts.base')

@section('content')
    <style>
        #breakdown-details {
            width: 100%;
        }

        #breakdown-details tr,
        #breakdown-details td {
            border-bottom: 1px solid #000;
            padding: 3pt 9pt;
        }

        /* Page break controls */
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
        }

        .content-cell {
            display: inline-block;
            vertical-align: top;
            padding: 5px;
            font-size: 10.0pt;
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

        /* Ensure details flow properly */
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
    </style>
    @php
        $disableAutoHeader = true;
        $disableAutoFooter = true;

        // Group quotes by a unique identifier (e.g., quote_number or insured_name + class_name)
        $groupedQuotes = [];
        foreach ($allQuotesData as $data) {
            $quoteKey =
                $data['quote']->quote_number ?? $data['opportunity']->insured_name . '_' . $data['class']->class_name;
            if (!isset($groupedQuotes[$quoteKey])) {
                $groupedQuotes[$quoteKey] = [
                    'data' => $data,
                    'reinsurers' => [],
                ];
            }
            $groupedQuotes[$quoteKey]['reinsurers'][] = $data['quote'];
        }
    @endphp
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
    <!-- Render each unique quote -->
    @foreach ($groupedQuotes as $quoteKey => $quoteGroup)
        @if ($loop->index > 0)
            <div class="page-break"></div>
        @endif
        @php
            $data = $quoteGroup['data'];
            $reinsurers = $quoteGroup['reinsurers'];
        @endphp
        <div class="main-content">
            <table style="width:100%; margin-top: 0px;">
                <tr>
                    <td colspan="2" class="text-center">
                        <div class="d-flex justify-content-between">
                            <div>
                                <b>{{ $stcomentData->quote_title_intro . ' - ' . $data['class']->class_name . ' (' . now()->format('Y') . ') - ' . strtoupper($data['opportunity']->insured_name ?? 'N/A') }}</b>
                            </div>
                        </div>
                        <hr
                            style="border: none; border-bottom: 2px solid #ddd; width: 105%; margin: 10px 0 10px 0; margin-left: -2.5%;">
                    </td>
                </tr>
            </table>

            <div class="content-row">
                <div class="content-cell" style="width: 27%;">
                    <b>Our Reference:</b>
                </div>
                <div class="content-cell">
                    {{ $data['quote']['quote']['quote_number'] }}
                </div>
            </div>

            <div class="content-row">
                <div class="content-cell" style="width: 27%;">
                    <b>Cedant Name:</b>
                </div>
                <div class="content-cell">
                    {{ firstUpper($data['opportunity']->customer_name) }}
                </div>
            </div>

            <div class="content-row">
                <div class="content-cell" style="width: 27%;">
                    <b>Insured Name:</b>
                </div>
                <div class="content-cell">
                    {{ firstUpper($data['opportunity']->insured_name) }}
                </div>
            </div>

            <div class="content-row">
                <div class="content-cell" style="width: 27%;">
                    <b>Insurance Group:</b>
                </div>
                <div class="content-cell">
                    {{ firstUpper($data['opportunity']->bus_type_name) }}
                </div>
            </div>

            <div class="content-row">
                <div class="content-cell" style="width: 27%;">
                    <b>Class Of Business:</b>
                </div>
                <div class="content-cell">
                    {{ firstUpper($data['class']->class_name) }}
                </div>
            </div>

            <div class="content-row">
                <div class="content-cell" style="width: 27%;">
                    <b>Period Of Cover:</b>
                </div>
                <div class="content-cell">
                    @if ($data['opportunity']->effective_date !== 'TBA')
                        {{ firstUpper($data['opportunity']->effective_date . ' To ' . $data['opportunity']->closing_date) }}
                    @else
                        TBA
                    @endif
                </div>
            </div>

            @php
                $orderedQuoteSchedules = [];
                $policyWording = null;
                foreach ($data['quoteSchedules'] as $detail) {
                    if (trim(strtolower($detail->name)) == 'policy wording') {
                        $policyWording = $detail;
                    } else {
                        $orderedQuoteSchedules[] = $detail;
                    }
                }
                if ($policyWording) {
                    $orderedQuoteSchedules[] = $policyWording;
                }
            @endphp

            @foreach ($orderedQuoteSchedules as $detail)
                @if (trim(strtolower($detail->name)) != 'policy wording')
                    <div style="width: 100%; margin-top: 5px; padding: 0; overflow: visible; page-break-before: auto; margin-bottom: 15px;"
                        id="schedule-wrapper">
                        <div style="width: 100%; margin-bottom: 7px; font-size: 10pt;" class="clearfix">
                            <div
                                style="width: 27%; padding-right: 10px; float: left; display: flex; align-items: center; margin-left:4px">
                                <strong>
                                    @if (isset($detail->name))
                                        {{ firstUpper($detail->name) }}
                                        @php
                                            $lowerName = trim(strtolower($detail->name));
                                        @endphp
                                        {{ in_array($lowerName, ['cedant commission rate', 'reinsurer commission rate']) ? ' (%):' : '' }}
                                        {{ in_array($lowerName, [
                                            'premium',
                                            'total sum insured',
                                            'first loss',
                                            'top location',
                                            'limit of indemnity',
                                            'maximum loss limit',
                                            'limit of liability',
                                            'agreed value',
                                        ])
                                            ? '(100%):'
                                            : '' }}
                                        {{ !in_array($lowerName, [
                                            'cedant commission rate',
                                            'reinsurer commission rate',
                                            'premium',
                                            'total sum insured',
                                            'first loss',
                                            'top location',
                                            'limit of indemnity',
                                            'maximum loss limit',
                                            'limit of liability',
                                            'agreed value',
                                        ])
                                            ? ':'
                                            : '' }}
                                    @endif
                                </strong>
                            </div>
                            @php
                                $hasTable =
                                    str_contains($detail->details ?? '', '<table') ||
                                    str_contains($detail->details ?? '', '<tr') ||
                                    str_contains($detail->details ?? '', '<td');
                                $content = strip_tags($detail->details ?? '');
                                $approxCharsPerLine = 100;
                                $wrappedContent = wordwrap($content, 100, "\n", false);
                                $lines = explode("\n", $wrappedContent);
                                $longestLineLength = 0;
                                foreach ($lines as $line) {
                                    $lineLength = strlen(trim($line));
                                    if ($longestLineLength < $lineLength) {
                                        $longestLineLength = $lineLength;
                                    }
                                }
                                $isLongContent = $hasTable || $longestLineLength > $approxCharsPerLine;
                                $tableWidth = $isLongContent ? '100%' : '70%';
                            @endphp
                            @if (!$isLongContent)
                                <div style="width: 70%; float: right; font-size: 10pt;" class="schedule-details">
                                    @if (in_array(trim(strtolower($detail->name)), [
                                            'premium',
                                            'first loss',
                                            'top location',
                                            'limit of indemnity',
                                            'maximum loss limit',
                                            'limit of liability',
                                            'agreed value',
                                            'total sum insured',
                                        ]))
                                        {{ $data['opportunity']->currency_code }}
                                    @endif
                                    {!! html_entity_decode($detail->details ?? '') !!}
                                    @php
                                        $lowerName = trim(strtolower($detail->name));
                                    @endphp
                                    {{ in_array($lowerName, ['cedant commission rate', 'reinsurer commission rate']) ? '%' : '' }}
                                </div>
                            @endif
                        </div>
                        @if ($isLongContent)
                            <div style="width: {{ $tableWidth }}; margin-top: 10px;">
                                @if ($hasTable)
                                    {!! preg_replace(
                                        '/<table[^>]*>/',
                                        '<table class="word-table" style="width:100%;">',
                                        html_entity_decode($detail->details),
                                    ) !!}
                                @else
                                    <div class="wrap-text-wordings" style="margin-left:4px">
                                        {!! html_entity_decode($detail->details) !!}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div style="page-break-inside: avoid;">
                        <div class="content-row" style="margin-left:4px;">
                            <div role="heading" aria-level="1" style="margin-left:29%; font-size: 15pt; font-weight: bold;">
                                <u>{{ firstUpper($detail->name) }}</u>
                            </div>
                            <br>
                            <div style="margin-left:4px;" class="wrap-text-wordings">
                                @if (isset($detail->details))
                                    {!! html_entity_decode($detail->details) !!}
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            <div style="page-break-inside: avoid;">
                @if ($data['stage'] == 2 && $data['stageType'] == 1)
                    <div class="content-row">
                        <div class="content-cell" style="width: 27%;">
                            <b>Reinsurers:</b>
                        </div>
                    </div>
                @else
                    <div class="content-row">
                        <div class="content-cell" style="width: 27%;">
                            <b>Placed With:</b>
                        </div>
                    </div>
                @endif

                @php
                    // Define valid names as in the first code block
                    $validNames = [
                        'premium',
                        'first loss',
                        'top location',
                        'limit of indemnity',
                        'maximum loss limit',
                        'limit of liability',
                        'agreed value',
                        'total sum insured',
                    ];

                    // Initialize arrays and totals
                    $displayValues = [];
                    $reinsurerData = [];
                    $totalWrittenShare = 0;
                    $totalSignedShare = 0;
                    $totalValues = []; // To store totals for each selected name

                    // Process quoteSchedules to extract values
                    foreach ($data['quoteSchedules'] as $detail) {
                        $detailName = trim(strtolower($detail->name));
                        if (!in_array($detailName, $validNames)) {
                            continue;
                        }
                        $amount = floatval(str_replace(',', '', $detail->details));
                        $displayValues[$detailName] = $amount; // Store original amount
                    }

                    // Dynamically select up to two valid names, as in the first code block
                    $selectedNames = array_slice(array_keys($displayValues), 0, 2);

                    // Process reinsurers
                    foreach ($reinsurers as $reinsurer) {
                        $writtenShare = isset($reinsurer->written_share) ? floatval($reinsurer->written_share) : 0;
                        $signedShare = isset($reinsurer->signed_share) ? floatval($reinsurer->signed_share) : 0;

                        // Calculate values for each selected name
                        $reinsurerValues = [];
                        foreach ($selectedNames as $name) {
                            if (isset($displayValues[$name])) {
                                $calculatedAmount = $displayValues[$name] * ($signedShare / 100);
                                $reinsurerValues[$name] = $calculatedAmount;
                                $totalValues[$name] = ($totalValues[$name] ?? 0) + $calculatedAmount;
                            } else {
                                $reinsurerValues[$name] = 0;
                            }
                        }

                        // Store reinsurer data
                        $reinsurerData[] = [
                            'name' => firstUpper($reinsurer->reinsurer_name ?? 'Unknown'),
                            'written_share' => $writtenShare,
                            'signed_share' => $signedShare,
                            'values' => $reinsurerValues,
                        ];

                        $totalWrittenShare += $writtenShare;
                        $totalSignedShare += $signedShare;
                    }
                @endphp

                @if (!empty($reinsurerData))
                    <div class="content-row">
                        <div class="content-cell" style="width: 100%;">
                            <table style="width: 80%; margin-left: 20px;">
                                <thead style="background-color: transparent;">
                                    <tr>
                                        <td colspan="{{ 3 + count($selectedNames) }}"
                                            style="padding: 0; background-color: transparent;">
                                            <hr
                                                style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th
                                            style="text-align: left; font-weight: 600; white-space: nowrap; border: none; background-color: transparent;">
                                            Reinsurer
                                        </th>
                                        <th
                                            style="text-align: center; font-weight: 600; border: none; background-color: transparent;">
                                            Written Share
                                        </th>
                                        <th
                                            style="text-align: center; font-weight: 600; border: none; background-color: transparent;">
                                            Signed Share
                                        </th>
                                        @foreach ($selectedNames as $name)
                                            <th
                                                style="text-align: center; font-weight: 600; border: none; background-color: transparent;">
                                                {{ firstUpper($name) }}
                                                <span
                                                    style="font-size: 0.85em;">({{ $data['opportunity']->currency_code }})</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td colspan="{{ 3 + count($selectedNames) }}"
                                            style="padding: 0; background-color: transparent;">
                                            <hr
                                                style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reinsurerData as $reinsurer)
                                        <tr>
                                            <td style="padding: 4px; text-align: left; white-space: nowrap;">
                                                {{ $reinsurer['name'] }}
                                            </td>
                                            <td style="padding: 4px; text-align: center;">
                                                {{ number_format($reinsurer['written_share'], 2) }}%
                                            </td>
                                            <td style="padding: 4px; text-align: center;">
                                                {{ number_format($reinsurer['signed_share'], 2) }}%
                                            </td>
                                            @foreach ($selectedNames as $name)
                                                <td style="padding: 4px; text-align: center;">
                                                    {{ number_format($reinsurer['values'][$name] ?? 0, 2, '.', ',') }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="{{ 3 + count($selectedNames) }}" style="padding: 0;">
                                            <hr
                                                style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 4px; text-align: left; font-weight: 600;">
                                            Total
                                        </td>
                                        <td style="padding: 4px; text-align: center; font-weight: 600;">
                                            {{ number_format($totalWrittenShare, 2) }}%
                                        </td>
                                        <td style="padding: 4px; text-align: center; font-weight: 600;">
                                            {{ number_format($totalSignedShare, 2) }}%
                                        </td>
                                        @foreach ($selectedNames as $name)
                                            <td style="padding: 4px; text-align: center; font-weight: 600;">
                                                {{ number_format($totalValues[$name] ?? 0, 2, '.', ',') }}
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td colspan="{{ 3 + count($selectedNames) }}" style="padding: 0;">
                                            <hr
                                                style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                        </div>
                    </div>
                @endif
            </div>

            <div class="date_generated">
                <p style="font-size: 10pt;">Generated on behalf of Acentria International on {{ now()->format('F j, Y') }}.
                </p>
            </div>
            <div class="footer">
                <span>© {{ date('Y') }} Acentriagroup. All rights reserved. | Page No: <span
                        class="page-number"></span></span>
            </div>
        </div>
    @endforeach
@endsection
