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
    @if (!(($stage == 3 && $customers[0]->stageType == 2) || ($stage == 3 && $customers[0]->stageType == 1)))
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
        @foreach ($shares as $index => $sh)
            <div class="reinsurer-page{{ $index === 0 ? ' first-page' : '' }}">

                <div class="main-content">
                    @foreach ($customers as $customer)
                        <div class="major-section">
                            <!-- Title section -->
                            <table style="width:100%; margin-top:0px;">
                                <tr>
                                    <td colspan="2" class="text-center">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <b style="font-size:14;">Title</b>
                                            </div>
                                        </div>

                                        <hr
                                            style="border: none; border-bottom: 2px solid #ddd; width: 105%; margin: 10px 0 10px 0; margin-left: -2.5%;">

                                    </td>
                                </tr>
                            </table>


                            <!-- Details section -->
                            <div class="details-section">
                                <!-- Basic details -->
                                @foreach (['Our Reference', 'Cedant Name', 'Insured Name', 'Insurance Group', 'Class Of Business', 'Period Of Cover'] as $field)
                                    <div class="content-row">
                                        <div class="content-cell" style="width: 27%;">
                                            <b>{{ $field }}:</b>
                                        </div>
                                        <div class="content-cell">
                                            @switch($field)
                                                @case('Cedant Name')
                                                    {{ firstUpper($customer->customer_name) }}
                                                @break

                                                @case('Insured Name')
                                                    {{-- {{ firstUpper($customer->insured_name) }} --}}
                                                @break

                                                @case('Insurance Group')
                                                    {{ firstUpper($customer->type_of_bus) }}
                                                @break

                                                @case('Class Of Business')
                                                    {{-- {{ firstUpper($customer->class_name) }} --}}
                                                @break

                                                @case('Period Of Cover')
                                                    {{ firstUpper($customer->effective_date . ' To ' . $customer->closing_date) }}
                                                @break
                                            @endswitch
                                        </div>
                                    </div>
                                @endforeach
                                <div class="footer">
                                    <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved. | Page
                                        No: <span class="page-number"></span></span>
                                </div>

                                <!-- Schedule details -->
                                @php
                                    $scheduleDetails = isset($customer->facschedule_details)
                                        ? $customer->facschedule_details
                                        : $customer->schedule_details;
                                @endphp



                                <div style="width: 100%; margin-top: 5px; padding: 0; overflow: visible; page-break-before: auto; margin-bottom: 15px;"
                                    id="schedule-wrapper">
                                    <!-- Table Header -->

                                    <table
                                        style="width: 100%; border-collapse: collapse; font-size: 10.0pt; margin-bottom: 7px;"
                                        class="schedule-table">
                                        <caption
                                            style="font-size: 11pt; font-weight: bold; background-color: #f2f2f2; padding: 8px; text-align: center; border: 1px solid #ddd; border-bottom: none;">
                                            {{ firstUpper($customer->customer_name) }}
                                        </caption>
                                        <thead>

                                            <tr style="background-color: #f2f2f2;">
                                                <th
                                                    style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 25%;">
                                                    <strong>Name</strong>
                                                </th>
                                                <th
                                                    style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 25%;">
                                                    <strong>Current Structure</strong>
                                                </th>
                                                <th
                                                    style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 25%;">
                                                    <strong>Proposed Terms</strong>
                                                </th>
                                                <th
                                                    style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 25%;">
                                                    <strong>Final Terms</strong>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($scheduleDetails as $detail)
                                                @if (isset($detail['id']) && isset($detail['current_amount']))
                                                    <tr>
                                                        <!-- Name Column -->
                                                        <td style="border: 1px solid #ddd; padding: 8px;">
                                                            <strong>{{ isset($detail['name']) ? ucfirst($detail['name']) : '' }}</strong>

                                                        </td>
                                                        <!-- Current Structure Column -->
                                                        <td style="border: 1px solid #ddd; padding: 8px;">
                                                            @if (isset($detail['current_amount']) && $detail['current_amount'] !== null)
                                                                {{ $detail['current_amount'] }}
                                                            @endif
                                                        </td>
                                                        <!-- Proposed Terms Column -->
                                                        <td style="border: 1px solid #ddd; padding: 8px;">
                                                            @if (isset($detail['proposed_amount']) && $detail['proposed_amount'] !== null)
                                                                {{ $detail['proposed_amount'] }}
                                                            @endif
                                                        </td>
                                                        <!-- Final Terms Column -->
                                                        <td style="border: 1px solid #ddd; padding: 8px;">
                                                            @if (isset($detail['final_amount']) && $detail['final_amount'] !== null)
                                                                {{ $detail['final_amount'] }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach

                                            

                                        </tbody>
                                    </table>
                                </div>




                                <div style="page-break-inside: avoid;">



                                    @if (isset($sh['written_share']) && $sh['written_share'] != null && !isset($sh['signed_share']))
                                        @php
                                            $validNames = [
                                                'premium',
                                                'total sum insured',
                                                'first loss',
                                                'top location',
                                                'limit of indemnity',
                                                'maximum loss limit',
                                                'limit of liability',
                                                'agreed value',
                                            ];

                                            $displayValues = [];
                                            foreach ($scheduleDetails as $detail) {
                                                if (!isset($detail['name']) || !isset($detail['amount'])) {
                                                    continue;
                                                }

                                                $detailName = strtolower(trim($detail['name']));

                                                // Skip 'reinsurer commission rate' entirely
                                                if ($detailName === 'reinsurer commission rate') {
                                                    continue;
                                                }

                                                if (!in_array($detailName, $validNames)) {
                                                    continue;
                                                }

                                                $amount = floatval(str_replace(',', '', $detail['amount']));
                                                $writtenShare = isset($sh['written_share'])
                                                    ? floatval($sh['written_share'])
                                                    : 0;
                                                $calculatedAmount = $amount * ($writtenShare / 100);
                                                // dd($calculatedAmount);

                                                $displayValues[$detailName] = number_format(
                                                    $calculatedAmount,
                                                    2,
                                                    '.',
                                                    '',
                                                );
                                            }

                                            // Get only the first two valid names
                                            $selectedNames = array_keys($displayValues);
                                        @endphp

                                        <table style="width: 80%; margin-left: 20px;">
                                            <thead style="background-color: transparent;">
                                                <tr>
                                                    <td colspan="{{ 2 + count($selectedNames) }}"
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
                                                        Written Share (%)
                                                    </th>
                                                    @foreach ($selectedNames as $name)
                                                        <th
                                                            style="text-align: center; font-weight: 600; border: none; background-color: transparent;">
                                                            {{ firstUpper($name) }}
                                                            <span
                                                                style="font-size: 0.85em;">({{ $customer->currency_code }})</span>
                                                        </th>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td colspan="{{ 2 + count($selectedNames) }}" style="padding: 0;">
                                                        <hr
                                                            style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="padding: 4px; text-align: left; white-space: nowrap;">
                                                        @if (isset($sh['customer_name']))
                                                            {{ firstUpper($sh['customer_name']) }}
                                                        @endif
                                                        @if (isset($sh['name']))
                                                            {{ firstUpper($sh['name']) }}
                                                        @endif
                                                    </td>
                                                    <td style="padding: 4px; text-align: center;">
                                                        {{ $sh['written_share'] }}%
                                                    </td>
                                                    @foreach ($selectedNames as $name)
                                                        <td style="padding: 4px; text-align: center;">
                                                            {{ number_format($displayValues[$name] ?? 0, 2) }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td colspan="{{ 2 + count($selectedNames) }}" style="padding: 0;">
                                                        <hr
                                                            style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                </div>
                                <br>
                            @elseif(isset($sh['signed_share']) && $sh['signed_share'] != null)
                                @php
                                    $validNames = [
                                        'premium',
                                        'total sum insured',
                                        'first loss',
                                        'top location',
                                        'limit of indemnity',
                                        'maximum loss limit',
                                        'limit of liability',
                                        'agreed value',
                                    ];

                                    $displayValues = [];
                                    foreach ($scheduleDetails as $detail) {
                                        if (!isset($detail['name']) || !isset($detail['amount'])) {
                                            continue;
                                        }

                                        $detailName = strtolower(trim($detail['name']));

                                        // Skip 'reinsurer commission rate' entirely
                                        if ($detailName === 'cedant commission rate') {
                                            continue;
                                        }

                                        if (!in_array($detailName, $validNames)) {
                                            continue;
                                        }

                                        $amount = floatval(str_replace(',', '', $detail['amount']));
                                        $signedShare = isset($sh['signed_share']) ? floatval($sh['signed_share']) : 0;
                                        $calculatedAmount = $amount * ($signedShare / 100);
                                        // dd($calculatedAmount);

                                        $displayValues[$detailName] = number_format($calculatedAmount, 2, '.', '');
                                    }

                                    // Get only the first two valid names
                                    $selectedNames = array_keys($displayValues);
                                @endphp
                                <div style="page-break-inside: avoid;">
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
                                                    Written Share (%)
                                                </th>
                                                <th
                                                    style="text-align: center; font-weight: 600; border: none; background-color: transparent;">
                                                    Signed Share (%)
                                                </th>
                                                @foreach ($selectedNames as $name)
                                                    <th
                                                        style="text-align: center; font-weight: 600; border: none; background-color: transparent;">
                                                        {{ firstUpper($name) }}
                                                        <span
                                                            style="font-size: 0.85em;">({{ $customer->currency_code }})</span>
                                                    </th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td colspan="{{ 3 + count($selectedNames) }}" style="padding: 0;">
                                                    <hr
                                                        style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="padding: 4px; text-align: left; white-space: nowrap;">
                                                    @if (isset($sh['customer_name']))
                                                        {{ firstUpper($sh['customer_name']) }}
                                                    @endif
                                                    @if (isset($sh['name']))
                                                        {{ firstUpper($sh['name']) }}
                                                    @endif
                                                </td>
                                                <td style="padding: 4px; text-align: center;">
                                                    {{ $sh['written_share'] }}%
                                                </td>
                                                <td style="padding: 4px; text-align: center;">
                                                    {{ $sh['signed_share'] }}%
                                                </td>
                                                @foreach ($selectedNames as $name)
                                                    <td style="padding: 4px; text-align: center;">
                                                        {{ number_format($displayValues[$name] ?? 0, 2) }}
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
                                </div>

                                <br>
                    @endif
                </div>
            </div>
        @endforeach
        </div>
        </div>
        <div class="date_generated">
            <p style="font-size: 8.0pt; ">Generated on behalf of Acentria International on
                {{ now()->format('F j, Y') }}.</p>

        </div>
    @endforeach

    <div class="footer">
        <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved. | Page No: <span
                class="page-number"></span></span>
    </div>
@elseif(($stage == 3 && $customers[0]->stageType == 2) || ($stage == 2 && $customers[0]->stageType == 1))
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

    <div class="main-content">
        @foreach ($customers as $customer)
            <div class="major-section">
                <!-- Title section -->
                <table style="width:100%; margin-top:0px;">
                    <tr>
                        <td colspan="2" class="text-center">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <b>{{ $customer->quote_title_intro }}</b>
                                </div>
                            </div>

                            <hr
                                style="border: none; border-bottom: 2px solid #ddd; width: 105%; margin: 10px 0 10px 0; margin-left: -2.5%;">

                        </td>
                    </tr>
                </table>

                <div class="details-section">
                    <!-- Basic details -->
                    @foreach (['Our Reference', 'Cedant Name', 'Insured Name', 'Insurance Group', 'Class Of Business', 'Period Of Cover'] as $field)
                        <div class="content-row">
                            <div class="content-cell" style="width: 27%;">
                                <b>{{ $field }}:</b>
                            </div>
                            <div class="content-cell">
                                @switch($field)
                                    @case('Cedant Name')
                                        {{ firstUpper($customer->customer_name) }}
                                    @break

                                    @case('Insurance Group')
                                        {{ firstUpper($customer->type_of_bus) }}
                                    @break

                                    @case('Period Of Cover')
                                        {{ firstUpper($customer->effective_date . ' To ' . $customer->closing_date) }}
                                    @break
                                @endswitch
                            </div>
                        </div>
                    @endforeach
                    <div class="footer">
                        <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved. | Page
                            No: <span class="page-number"></span></span>
                    </div>

                    <!-- Schedule details -->
                    @php
                        $scheduleDetails = isset($customer->facschedule_details)
                            ? $customer->facschedule_details
                            : $customer->schedule_details;
                    @endphp

                    <div style="width: 100%; margin-top: 5px; padding: 0; overflow: visible; page-break-before: auto; margin-bottom: 15px;"
                        id="schedule-wrapper">
                        <!-- Table Header -->

                        <table style="width: 100%; border-collapse: collapse; font-size: 10.0pt; margin-bottom: 7px;"
                            class="schedule-table">
                            <caption
                                style="font-size: 11pt; font-weight: bold; background-color: #f2f2f2; padding: 8px; text-align: center; border: 1px solid #ddd; border-bottom: none;">
                                {{ firstUpper($customer->customer_name) }}
                            </caption>
                            <thead>

                                <tr style="background-color: #f2f2f2;">
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 25%;">
                                        <strong>Name</strong>
                                    </th>
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 25%;">
                                        <strong>Current Structure</strong>
                                    </th>
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 25%;">
                                        <strong>Proposed Terms</strong>
                                    </th>
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 25%;">
                                        <strong>Final Terms</strong>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($scheduleDetails as $detail)
                                    @if (isset($detail['id']) && isset($detail['current_amount']))
                                        <tr>
                                            <!-- Name Column -->
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                                <strong>{{ isset($detail['name']) ? ucfirst($detail['name']) : '' }}</strong>

                                            </td>
                                            <!-- Current Structure Column -->
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                                @if (isset($detail['current_amount']) && $detail['current_amount'] !== null)
                                                    {{ $detail['current_amount'] }}
                                                @endif
                                            </td>
                                            <!-- Proposed Terms Column -->
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                                @if (isset($detail['proposed_amount']) && $detail['proposed_amount'] !== null)
                                                    {{ $detail['proposed_amount'] }}
                                                @endif
                                            </td>
                                            <!-- Final Terms Column -->
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                                @if (isset($detail['final_amount']) && $detail['final_amount'] !== null)
                                                    {{ $detail['final_amount'] }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                    @foreach ($shares as $index => $sh)
                        @if (isset($sh['written_share']) && $sh['written_share'] != null)
                            @php
                                $validNames = [
                                    'premium',
                                    'total sum insured',
                                    'first loss',
                                    'top location',
                                    'limit of indemnity',
                                    'maximum loss limit',
                                    'limit of liability',
                                    'agreed value',
                                ];

                                $displayValues = [];
                                foreach ($scheduleDetails as $detail) {
                                    if (!isset($detail['name']) || !isset($detail['amount'])) {
                                        continue;
                                    }

                                    $detailName = strtolower(trim($detail['name']));

                                    // Skip 'reinsurer commission rate' entirely
                                    if ($detailName === 'reinsurer commission rate') {
                                        continue;
                                    }

                                    if (!in_array($detailName, $validNames)) {
                                        continue;
                                    }

                                    $amount = floatval(str_replace(',', '', $detail['amount']));
                                    $writtenShare = isset($sh['written_share']) ? floatval($sh['written_share']) : 0;
                                    $calculatedAmount = $amount * ($writtenShare / 100);
                                    // dd($calculatedAmount);

                                    $displayValues[$detailName] = number_format($calculatedAmount, 2, '.', '');
                                }

                                // Get only the first two valid names
                                $selectedNames = array_keys($displayValues);
                            @endphp
                        @endif
                    @endforeach
                    <div style="page-break-inside: avoid;">

                        <div class="content-row">
                            <div class="content-cell" style="width: 27%;">
                                <strong>Offered Share(%):</strong>
                            </div>
                            <div class="content-cell">
                                <div class="wrap-text">
                                    {{ $updated_written_share_total }}%

                                </div>
                            </div>
                        </div>
                        <div class="content-row">
                            <div class="content-cell" style="width: 27%;">
                                <strong>Placed With:</strong>
                            </div>

                        </div>

                        <table style="width: 80%; margin-left: 20px;">
                            <thead style="background-color: transparent;">
                                <tr>
                                    <td colspan="{{ 2 + count($selectedNames) }}"
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
                                        Written Share (%)
                                    </th>
                                    @foreach ($selectedNames as $name)
                                        <th
                                            style="text-align: center; font-weight: 600; border: none; background-color: transparent;">
                                            {{ firstUpper($name) }}
                                            <span style="font-size: 0.85em;">({{ $customer->currency_code }})</span>
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td colspan="{{ 2 + count($selectedNames) }}" style="padding: 0;">
                                        <hr
                                            style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shares as $sh)
                                    <tr>
                                        <td style="padding: 4px; text-align: left; white-space: nowrap;">
                                            @if (isset($sh['customer_name']))
                                                {{ firstUpper($sh['customer_name']) }}
                                            @endif
                                            @if (isset($sh['name']))
                                                {{ firstUpper($sh['name']) }}
                                            @endif
                                        </td>
                                        <td style="padding: 4px; text-align: center;">
                                            {{ $sh['written_share'] }}%
                                        </td>

                                        @foreach ($selectedNames as $name)
                                            <td style="padding: 4px; text-align: center;">
                                                {{ number_format($displayValues[$name] ?? 0, 2) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="{{ 2 + count($displayValues) }}" style="padding: 0;">
                                        <hr
                                            style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px; text-align: left; font-weight: 600;">
                                        Total
                                    </td>
                                    <td style="padding: 4px; text-align: center; font-weight: 600;">
                                        @php
                                            $totalWrittenShare = array_sum(array_column($shares, 'written_share'));
                                        @endphp
                                        {{ number_format($totalWrittenShare, 2) . '%' }}
                                    </td>
                                    @foreach ($selectedNames as $name)
                                        @php
                                            $columnTotal = 0;
                                            foreach ($shares as $sh) {
                                                if (!isset($sh['written_share']) || $sh['written_share'] == null) {
                                                    continue;
                                                }
                                                $writtenShare = floatval($sh['written_share']);

                                                // Retrieve the original value from scheduleDetails
                                                $originalAmount = 0;
                                                foreach ($scheduleDetails as $detail) {
                                                    if (
                                                        isset($detail['name'], $detail['amount']) &&
                                                        strtolower(trim($detail['name'])) === $name
                                                    ) {
                                                        $originalAmount = floatval(
                                                            str_replace(',', '', $detail['amount']),
                                                        );
                                                        break;
                                                    }
                                                }

                                                // If no valid amount is found, ensure it does not cause an error
                                                if ($originalAmount > 0) {
                                                    $columnTotal += ($originalAmount * $writtenShare) / 100;
                                                }
                                            }
                                        @endphp
                                        <td style="padding: 4px; text-align: center; font-weight: 600;">
                                            {{ number_format($columnTotal, 2) }}
                                        </td>
                                    @endforeach
                                </tr>

                            </tbody>
                        </table>
                        <hr
                            style="width:573px; border: none; height: 0px; background-color: #000; opacity: 0.5; margin-left: 20px;">
                    </div>

                    <br>
        @endforeach
        @if ($unplaced > 0)
            <div class="content-row">
                <div class="content-cell" style="width: 27%;">
                    <strong>Unplaced Share(%)</strong>
                </div>
                <div class="content-cell">
                    <div class="wrap-text">
                        {{ $unplaced }} %


                    </div>
                </div>
            </div>
        @endif

        @if (isset($sh['signed_share']))
            <div class="content-row">
                <div class="content-cell" style="width: 27%;">
                    <strong>Signed Share</strong>
                </div>
                <div class="content-cell">
                    {{ $sh['signed_share'] . '%' }}
                </div>
            </div>
        @endif

    </div>
    </div>

    </div>




    <div class="date_generated">
        <p style="font-size: 8.0pt; ">Generated on behalf of Acentria International on
            {{ now()->format('F j, Y') }}.</p>

    </div>


    <div class="footer">
        <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved. | Page No: <span
                class="page-number"></span></span>
    </div>
@else
    {{-- <div class="reinsurer-page{{ $index === 0 ? ' first-page' : '' }}"> --}}

    <!-- Header section -->
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


    <div class="main-content">
        @foreach ($customers as $customer)
            <div class="major-section">
                <!-- Title section -->
                <table style="width:100%; margin-top: 0px;">
                    <tr>
                        <td colspan="2" class="text-center">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <b>{{ 'Title' }}</b>
                                </div>
                            </div>
                            <hr
                                style="border: none; border-bottom: 2px solid #ddd; width: 105%; margin: 10px 0 10px 0; margin-left: -2.5%;">
                        </td>
                    </tr>
                </table>

                <!-- Details section -->
                <div class="details-section">
                    <!-- Basic details -->
                    @foreach (['Our Reference', 'Cedant Name', 'Insured Name', 'Insurance Group', 'Class Of Business', 'Period Of Cover'] as $field)
                        <div class="content-row">
                            <div class="content-cell" style="width: 27%;">
                                <b>{{ $field }}:</b>
                            </div>
                            <div class="content-cell">
                                @switch($field)
                                    @case('Cedant Name')
                                        {{ firstUpper($customer->customer_name) }}
                                    @break

                                    @case('Insurance Group')
                                        {{ firstUpper($customer->type_of_bus) }}
                                    @break

                                    @case('Period Of Cover')
                                        {{ firstUpper($customer->effective_date . ' To ' . $customer->closing_date) }}
                                    @break
                                @endswitch
                            </div>
                        </div>
                    @endforeach
                    <div class="footer">
                        <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved. | Page
                            No: <span class="page-number"></span></span>
                    </div>

                    <!-- Schedule details -->
                    @php
                        $scheduleDetails = isset($customer->facschedule_details)
                            ? $customer->facschedule_details
                            : $customer->schedule_details;
                            $hasCurrentAmount = false;
                            $hasProposedAmount = false;
                        $hasFinalAmount = false;

                        if (!empty($scheduleDetails)) {
                            foreach ($scheduleDetails as $item) {
                                if (array_key_exists('current_amount', $item) && !empty($item['current_amount'])) {
                                    $hasCurrentAmount = true;
                                }
                                if (array_key_exists('proposed_amount', $item) && !empty($item['proposed_amount'])) {
                                    $hasProposedAmount = true;
                                }
                                if (array_key_exists('final_amount', $item) && !empty($item['final_amount'])) {
                                    $hasFinalAmount = true;
                                }
                            }
                        }

                        // dd($scheduleDetails);

                    @endphp

                    <div style="width: 100%; margin-top: 5px; padding: 0; overflow: visible; page-break-before: auto; margin-bottom: 15px;"
                        id="schedule-wrapper">
                        <!-- Table Header -->

                        <table style="width: 100%; border-collapse: collapse; font-size: 10.0pt; margin-bottom: 7px;"
                            class="schedule-table">
                            <caption
                                style="font-size: 11pt; font-weight: bold; background-color: #f2f2f2; padding: 8px; text-align: center; border: 1px solid #ddd; border-bottom: none;">
                                {{ firstUpper($customer->customer_name) }}
                            </caption>

                            <thead>

                                <tr style="background-color: #f2f2f2;">
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 25%;">
                                        <strong>Name</strong>
                                    </th>
                                    @if (!empty($scheduleDetails) && $hasCurrentAmount)
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 25%;">
                                            <strong>Current Structure</strong>
                                        </th>
                                    @endif
                                    @if (!empty($scheduleDetails) && $hasProposedAmount)
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 25%;">
                                            <strong>Proposed Terms</strong>
                                        </th>
                                    @endif
                                    @if (!empty($scheduleDetails) && $hasFinalAmount)
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 25%;">
                                            <strong>Final Terms</strong>
                                        </th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($scheduleDetails as $detail)
                                    @if (isset($detail['id']) && isset($detail['current_amount']))
                                        <tr>
                                            <!-- Name Column -->
                                            @if(isset($detail['name']) && ($detail['name'])!==null)
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                                <strong>{{ isset($detail['name']) ? ucfirst($detail['name']) : '' }}</strong>
                                            </td>
                                            @endif
                                            <!-- Current Structure Column -->
                                            @if (isset($detail['current_amount']) && $detail['current_amount'] !== null)
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                                    {{ $detail['current_amount'] }}
                                            </td>
                                            @endif
                                            <!-- Proposed Terms Column -->
                                            @if (isset($detail['proposed_amount']) && $detail['proposed_amount'] !== null)
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                                
                                                    {{ $detail['proposed_amount'] }}
                                            </td>
                                            @endif
                                            <!-- Final Terms Column -->
                                            @if (isset($detail['final_amount']) && $detail['final_amount'] !== null)
                                            <td style="border: 1px solid #ddd; padding: 8px;">
                                               
                                                    {{ $detail['final_amount'] }}
                                            </td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach



                            </tbody>
                        </table>
                    </div>

                    <br>



                </div>
            </div>
        @endforeach
    </div>
    <div class="date_generated">
        <p style="font-size: 8.0pt; ">Generated on behalf of Acentria International on {{ now()->format('F j, Y') }}.
        </p>

    </div>
    @endif

    <div class="footer">
        <span>&copy; {{ date('Y') }} Acentriagroup. All rights reserved. | Page No: <span
                class="page-number"></span></span>
    </div>
@endsection
