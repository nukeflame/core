@extends('printouts.base')

@section('content')
    <style>
        .fac-page {
            page-break-after: always;
            page-break-inside: avoid;
            font-family: 'Aptos', sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
            width: 100%;
            position: relative;
        }

        .fac-page:last-of-type {
            page-break-after: auto;
        }

        .first-page {
            page-break-before: avoid;
        }

        #fac-header {
            width: 100%;
            text-align: center;
            margin: 15px 0;
        }

        .opensans-10,
        .opensans-9 {
            font-family: 'Aptos';
            vertical-align: top;
            padding: 5px;
            font-size: 9pt;
        }

        .opensans-10 {
            font-size: 10pt;
        }

        .content-row {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .content-cell {
            display: inline-block;
            vertical-align: top;
            padding: 5px;
            font-size: 10pt;
        }

        .main-content {
            font-family: inherit;
        }

        table {
            page-break-inside: avoid;
        }

        .bold {
            font-weight: 600;
        }

        .text-center {
            text-align: center;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .hr-line-btm {
            border-bottom: 1px solid #000;
            opacity: 0.5;
            margin: 2px 0;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 75pt;
            font-weight: 700;
            color: rgba(255, 0, 0, 0.15);
            z-index: 1000;
            pointer-events: none;
            white-space: nowrap;
            letter-spacing: 10px;
        }

        .date_generated {
            position: fixed;
            bottom: -8px;
            left: 0px;
            right: 0px;
            height: 14px;
            text-align: center;
            padding-top: 5px;
            font-size: 7pt;
            margin-top: 8px;
            font-weight: 500;
        }
    </style>

    @php
        $reinsurersToDisplay = !empty($reinsurers) ? $reinsurers : [];
        $currency = $currency ?? 'KES';
        $reference_no = $reference_no ?? 'N/A';
    @endphp

    @if ($stage !== 'negotiation')
        @foreach ($reinsurersToDisplay as $index => $reinsurer)
            {{-- <div class="watermark">PROVISIONAL SLIP</div> --}}
            <div class="fac-page{{ $index === 0 ? ' first-page' : '' }}">
                <div style="width:100%; margin-top: 0; padding:0; font-size: 10pt; font-family: 'Aptos';"
                    class="debit-reinsurer-page">

                    <table id="slip-header" style="width: 100%; margin-bottom: 20px;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding-right: 20px;">
                                <div style="font-size: 10pt;">
                                    <div style="font-weight: 600; margin-bottom: 5px;">TO</div>
                                    <div style="margin-bottom: 3px;">{{ $reinsurer->customer_name ?? 'N/A' }}</div>
                                    @if (!empty($reinsurer->address))
                                        <div style="margin-bottom: 3px;">{{ $reinsurer->address }}</div>
                                    @endif
                                    @if (!empty($reinsurer->city))
                                        <div style="margin-bottom: 3px;">{{ $reinsurer->city }}</div>
                                    @endif
                                    @if (!empty($reinsurer->location))
                                        <div style="margin-bottom: 3px;">{{ $reinsurer->location }}</div>
                                    @endif
                                    @if (!empty($reinsurer->country))
                                        <div style="margin-bottom: 3px;">{{ $reinsurer->country }}</div>
                                    @endif
                                    @if (!empty($reinsurer->phone))
                                        <div>{{ $reinsurer->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td style="width: 50%; vertical-align: top; text-align: right;">
                                <div style="font-size: 10pt;">
                                    <div style="margin-bottom: 3px;">
                                        <strong>Date:</strong> {{ now()->format('d M, Y') }}
                                    </div>
                                    <div>
                                        <strong>Currency:</strong> {{ $currency }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div style="width:100%; margin: 20px 0; text-align: center; font-size: 10pt; font-weight: 600;">
                        <div class="hr-line-btm"></div>
                        <div style="padding: 5px 0;">
                            {{ $title ?? 'FACULTATIVE SLIP' }}
                        </div>
                        <div class="hr-line-btm"></div>
                    </div>

                    <table id="slip-details" style="width: 100%; margin-top: 15px;">
                        <tr>
                            <td style="width: 35%; font-weight: 600; padding: 5px;">Our Reference:</td>
                            <td style="width: 65%; padding: 5px;">{{ $reference_no }}</td>
                        </tr>
                        <tr>
                            <td style="width: 35%; font-weight: 600; padding: 5px;">Cedant Name:</td>
                            <td style="width: 65%; padding: 5px;">{{ $opportunity['customer_name'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 35%; font-weight: 600; padding: 5px;">Insured Name:</td>
                            <td style="width: 65%; padding: 5px;">
                                {{ ucwords(strtolower($opportunity['insured_name'] ?? 'N/A')) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 35%; font-weight: 600; padding: 5px;">Insurance Group:</td>
                            <td style="width: 65%; padding: 5px;">{{ $opportunity['type_of_bus'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 35%; font-weight: 600; padding: 5px;">Class Of Business:</td>
                            <td style="width: 65%; padding: 5px;">
                                {{ ucwords(strtolower($opportunity['class_name'] ?? 'N/A')) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 35%; font-weight: 600; padding: 5px;">Period Of Cover:</td>
                            <td style="width: 65%; padding: 5px;">
                                @if (!empty($opportunity['effective_date']) && !empty($opportunity['closing_date']))
                                    {{ date('d M, Y', strtotime($opportunity['effective_date'])) }} To
                                    {{ date('d M, Y', strtotime($opportunity['closing_date'])) }}
                                @else
                                    TBA
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 35%; font-weight: 600; padding: 5px;">Offered Share (%):</td>
                            <td style="width: 65%; padding: 5px;">
                                {{ number_format($updated_written_share_total ?? 0, 2) }}%
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 35%; font-weight: 600; padding: 5px;">Total Sum Insured (100%):</td>
                            <td style="width: 65%; padding: 5px;">
                                {{ number_format($opportunity['sum_insured'] ?? 0, 2) }}
                            </td>
                        </tr>
                        @if ($stage !== 'lead')
                            <tr>
                                <td style="width: 35%; font-weight: 600; padding: 5px;">Premium (100%):</td>
                                <td style="width: 65%; padding: 5px;">
                                    {{ number_format($opportunity['premium'] ?? 0, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 35%; font-weight: 600; padding: 5px;">Cedant Commission Rate:</td>
                                <td style="width: 65%; padding: 5px;">
                                    {{ number_format($opportunity['commission_rate'] ?? 0, 2) }}%
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 35%; font-weight: 600; padding: 5px;">Placed With:</td>
                                <td style="width: 65%; padding: 5px;">{{ $reinsurer->customer_name ?? 'N/A' }}</td>
                            </tr>
                        @endif
                    </table>

                    @if ($stage !== 'lead')
                        <div class="main-content" style="margin-top: 30px;">
                            <div class="content-row">
                                <div class="content-cell" style="width: 100%;">
                                    @php
                                        $writtenShare = $reinsurer->written_share ?? 0;
                                        $writtenShareDecimal = $writtenShare / 100;
                                        $sumInsured = $opportunity['sum_insured'] ?? 0;
                                        $cedantPremium = $opportunity['premium'] ?? 0;

                                        $reinsurerSumInsured = $sumInsured * $writtenShareDecimal;
                                        $reinsurerPremium = $cedantPremium * $writtenShareDecimal;

                                        $reinsurerData = [
                                            [
                                                'name' => $reinsurer->customer_name ?? 'N/A',
                                                'written_share' => $writtenShare,
                                                'sum_insured' => $reinsurerSumInsured,
                                                'premium' => $reinsurerPremium,
                                            ],
                                        ];

                                        $totalWrittenShare = $writtenShare;
                                        $totalSumInsured = $reinsurerSumInsured;
                                        $totalPremium = $reinsurerPremium;
                                    @endphp

                                    <table style="width: 100%; border-collapse: collapse; font-size: 9pt;">
                                        <thead style="background-color: transparent;">
                                            <tr>
                                                <td colspan="4" style="padding: 0;">
                                                    <hr
                                                        style="border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="text-align: left; font-weight: 600; padding: 5px; border: none;">
                                                    Reinsurer
                                                </th>
                                                <th
                                                    style="text-align: center; font-weight: 600; padding: 5px; border: none;">
                                                    Written Share (%)
                                                </th>
                                                <th
                                                    style="text-align: right; font-weight: 600; padding: 5px; border: none;">
                                                    Total Sum Insured ({{ $currency }})
                                                </th>
                                                <th
                                                    style="text-align: right; font-weight: 600; padding: 5px; border: none;">
                                                    Premium ({{ $currency }})
                                                </th>
                                            </tr>
                                            <tr>
                                                <td colspan="4" style="padding: 0;">
                                                    <hr
                                                        style="border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reinsurerData as $data)
                                                <tr>
                                                    <td style="text-align: left; padding: 5px; border: none;">
                                                        {{ $data['name'] }}
                                                    </td>
                                                    <td style="text-align: center; padding: 5px; border: none;">
                                                        {{ number_format($data['written_share'], 2) }}%
                                                    </td>
                                                    <td style="text-align: right; padding: 5px; border: none;">
                                                        {{ number_format($data['sum_insured'], 2) }}
                                                    </td>
                                                    <td style="text-align: right; padding: 5px; border: none;">
                                                        {{ number_format($data['premium'], 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="4" style="padding: 0;">
                                                    <hr
                                                        style="border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 10px 0 5px 0;">
                                                </td>
                                            </tr>
                                            <tr style="font-weight: 600;">
                                                <td style="text-align: left; padding: 5px; border: none;">
                                                    TOTAL
                                                </td>
                                                <td style="text-align: center; padding: 5px; border: none;">
                                                    {{ number_format($totalWrittenShare, 2) }}%
                                                </td>
                                                <td style="text-align: right; padding: 5px; border: none;">
                                                    {{ number_format($totalSumInsured, 2) }}
                                                </td>
                                                <td style="text-align: right; padding: 5px; border: none;">
                                                    {{ number_format($totalPremium, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" style="padding: 0;">
                                                    <hr
                                                        style="border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="date_generated">
                    <p style="font-size: 8.0pt; margin: 0;">Generated on behalf of Acentria International Reinsurance
                        Brokers Limited
                        on {{ now()->format('F j, Y') }}.
                    </p>
                </div>
            </div>
        @endforeach
    @else
        <div class="fac-page">
            <div style="width:100%; margin-top: 0; padding:0; font-size: 10pt; font-family: 'Aptos';"
                class="debit-reinsurer-page">

                <table id="slip-header" style="width: 100%; margin-bottom: 20px;">
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-right: 20px;">
                            <div style="font-size: 10pt;">
                                <div style="font-weight: 600; margin-bottom: 5px;">TO</div>
                                <div style="margin-bottom: 3px;">{{ $cedant->customer_name ?? 'N/A' }}</div>
                                @if (!empty($cedant->address))
                                    <div style="margin-bottom: 3px;">{{ $cedant->address }}</div>
                                @endif
                                @if (!empty($cedant->city))
                                    <div style="margin-bottom: 3px;">{{ $cedant->city }}</div>
                                @endif
                                @if (!empty($cedant->location))
                                    <div style="margin-bottom: 3px;">{{ $cedant->location }}</div>
                                @endif
                                @if (!empty($cedant->country))
                                    <div style="margin-bottom: 3px;">{{ $cedant->country }}</div>
                                @endif
                                @if (!empty($cedant->phone))
                                    <div>{{ $cedant->phone }}</div>
                                @endif
                            </div>
                        </td>
                        <td style="width: 50%; vertical-align: top; text-align: right;">
                            <div style="font-size: 10pt;">
                                <div style="margin-bottom: 3px;">
                                    <strong>Date:</strong> {{ now()->format('d M, Y') }}
                                </div>
                                <div>
                                    <strong>Currency:</strong> {{ $currency }}
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                <div style="width:100%; margin: 20px 0; text-align: center; font-size: 10pt; font-weight: 600;">
                    <div class="hr-line-btm"></div>
                    <div style="padding: 5px 0;">
                        {{ $title ?? 'FACULTATIVE SLIP' }}
                    </div>
                    <div class="hr-line-btm"></div>
                </div>

                <table id="slip-details" style="width: 100%; margin-top: 15px;">
                    <tr>
                        <td style="width: 35%; font-weight: 600; padding: 5px;">Our Reference:</td>
                        <td style="width: 65%; padding: 5px;">{{ $reference_no }}</td>
                    </tr>
                    <tr>
                        <td style="width: 35%; font-weight: 600; padding: 5px;">Cedant Name:</td>
                        <td style="width: 65%; padding: 5px;">{{ $opportunity['customer_name'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="width: 35%; font-weight: 600; padding: 5px;">Insured Name:</td>
                        <td style="width: 65%; padding: 5px;">
                            {{ ucwords(strtolower($opportunity['insured_name'] ?? 'N/A')) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 35%; font-weight: 600; padding: 5px;">Insurance Group:</td>
                        <td style="width: 65%; padding: 5px;">{{ $opportunity['type_of_bus'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="width: 35%; font-weight: 600; padding: 5px;">Class Of Business:</td>
                        <td style="width: 65%; padding: 5px;">
                            {{ ucwords(strtolower($opportunity['class_name'] ?? 'N/A')) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 35%; font-weight: 600; padding: 5px;">Period Of Cover:</td>
                        <td style="width: 65%; padding: 5px;">
                            @if (!empty($opportunity['effective_date']) && !empty($opportunity['closing_date']))
                                {{ date('d M, Y', strtotime($opportunity['effective_date'])) }} To
                                {{ date('d M, Y', strtotime($opportunity['closing_date'])) }}
                            @else
                                TBA
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 35%; font-weight: 600; padding: 5px;">Offered Share (%):</td>
                        <td style="width: 65%; padding: 5px;">
                            {{ number_format($updated_written_share_total ?? 0, 2) }}%
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 35%; font-weight: 600; padding: 5px;">Total Sum Insured (100%):</td>
                        <td style="width: 65%; padding: 5px;">
                            {{ number_format($opportunity['sum_insured'] ?? 0, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 35%; font-weight: 600; padding: 5px;">Premium (100%):</td>
                        <td style="width: 65%; padding: 5px;">
                            {{ number_format($opportunity['premium'] ?? 0, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 35%; font-weight: 600; padding: 5px;">Placed With:</td>
                        <td style="width: 65%; padding: 5px;"></td>
                    </tr>
                </table>

                <div class="main-content" style="margin-top: 30px;">
                    <div class="content-row">
                        <div class="content-cell" style="width: 100%;">
                            @php
                                $reinsurerData = [];
                                $totalSignedShare = 0;
                                $totalSumInsured = 0;
                                $totalPremium = 0;

                                foreach ($reinsurers as $reinsurer) {
                                    $signedShare = $reinsurer->signed_share ?? 0;
                                    $signedShareDecimal = $signedShare / 100;
                                    $sumInsured = $opportunity['sum_insured'] ?? 0;
                                    $cedantPremium = $opportunity['premium'] ?? 0;

                                    $reinsurerSumInsured = $sumInsured * $signedShareDecimal;
                                    $reinsurerPremium = $cedantPremium * $signedShareDecimal;

                                    $reinsurerData[] = [
                                        'name' => $reinsurer->customer_name ?? 'N/A',
                                        'signed_share' => $signedShare,
                                        'sum_insured' => $reinsurerSumInsured,
                                        'premium' => $reinsurerPremium,
                                    ];

                                    $totalSignedShare += $signedShare;
                                    $totalSumInsured += $reinsurerSumInsured;
                                    $totalPremium += $reinsurerPremium;
                                }
                            @endphp

                            <table style="width: 100%; border-collapse: collapse; font-size: 9pt;">
                                <thead style="background-color: transparent;">
                                    <tr>
                                        <td colspan="4" style="padding: 0;">
                                            <hr
                                                style="border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left; font-weight: 600; padding: 5px; border: none;">
                                            Reinsurer
                                        </th>
                                        <th style="text-align: center; font-weight: 600; padding: 5px; border: none;">
                                            Signed Share (%)
                                        </th>
                                        <th style="text-align: right; font-weight: 600; padding: 5px; border: none;">
                                            Total Sum Insured ({{ $currency }})
                                        </th>
                                        <th style="text-align: right; font-weight: 600; padding: 5px; border: none;">
                                            Premium ({{ $currency }})
                                        </th>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="padding: 0;">
                                            <hr
                                                style="border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reinsurerData as $data)
                                        <tr>
                                            <td style="text-align: left; padding: 5px; border: none;">
                                                {{ $data['name'] }}
                                            </td>
                                            <td style="text-align: center; padding: 5px; border: none;">
                                                {{ number_format($data['signed_share'], 2) }}%
                                            </td>
                                            <td style="text-align: right; padding: 5px; border: none;">
                                                {{ number_format($data['sum_insured'], 2) }}
                                            </td>
                                            <td style="text-align: right; padding: 5px; border: none;">
                                                {{ number_format($data['premium'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4" style="padding: 0;">
                                            <hr
                                                style="border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 10px 0 5px 0;">
                                        </td>
                                    </tr>
                                    <tr style="font-weight: 600;">
                                        <td style="text-align: left; padding: 5px; border: none;">
                                            TOTAL
                                        </td>
                                        <td style="text-align: center; padding: 5px; border: none;">
                                            {{ number_format($totalSignedShare, 2) }}%
                                        </td>
                                        <td style="text-align: right; padding: 5px; border: none;">
                                            {{ number_format($totalSumInsured, 2) }}
                                        </td>
                                        <td style="text-align: right; padding: 5px; border: none;">
                                            {{ number_format($totalPremium, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="padding: 0;">
                                            <hr
                                                style="border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="date_generated">
                <p style="font-size: 8.0pt; margin: 0;">Generated on behalf of Acentria International Reinsurance Brokers
                    Limited
                    on {{ now()->format('F j, Y') }}.
                </p>
            </div>
        </div>
    @endif

@endsection
