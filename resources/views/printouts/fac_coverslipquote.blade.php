@extends('printouts.base')

@section('content')
    <style>
        .fac-page {
            page-break-after: always;
            page-break-inside: avoid;
            page-break-before: auto;
            font-family: "Open Sans", sans-serif;
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
            margin: 15px 0px;
        }

        .opensans-10,
        .opensans-9 {
            font-family: 'Open Sans';
            vertical-align: top;
            padding: 5px;
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
            font-size: 10.0pt;
        }

        .main-content {
            font-family: inherit;
        }

        table {
            page-break-inside: avoid;
        }
    </style>

    @if ($currentStage === 'lead')
        @foreach ($reinsurers as $index => $reinsurer)
            @php
                logger()->debug(json_encode($reinsurer, JSON_PRETTY_PRINT));
            @endphp
            <div class="fac-page{{ $index === 0 ? ' first-page' : '' }}">
                <div style="width:100%; margin-top: 0px; padding:0px; font-size: 10pt; font-family: 'Open Sans';"
                    class="debit-reinsurer-page">
                    <table id="slip-header" style="width: 100%;">
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                <table class="w-100 courier-10 p-0 m-0 reinsurer-details">
                                    <tr>
                                        <td class="courier-10 bold"> TO </td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="font-size: 10.0pt; word-wrap: break-word; overflow-wrap: break-word; max-width: 200px;">
                                            {{ $reinsurer['name'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 10.0pt;">
                                            {{ $reinsurer['address'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 10.0pt;"> {{ $reinsurer['city'] }} </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 10.0pt;"> {{ $reinsurer['location'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 10.0pt;">
                                            {{ $reinsurer['country'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 10.0pt;"> {{ $reinsurer['phone'] }} </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <table style="width:100%; margin-top: 0px; padding-left:300px; font-size: 10.0pt;">
                                    <tr>
                                        <td style="font-size: 10.0pt;"> Date: {{ now()->format('d M, Y') }} </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 10.0pt;"> Currency: {{ $currency ?? 'KES' }} </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    {{--
                    <div class="main-content" style="margin-top: 20px;">
                        <table style="width: 100%;">
                            <tr>
                                <td>Reinsurer: <strong>{{ $reinsurer->name }}</strong></td>
                            </tr>
                            <tr>
                                <td>Share: {{ $reinsurer->share ?? 'N/A' }}%</td>
                            </tr>
                        </table>
                    </div> --}}
                </div>
            </div>
        @endforeach
    @endif



    {{-- <div class="fac-page">
        <div style="width:100%; margin-top: 0px; padding:0px; font-size: 9pt; font-family: 'Open Sans';">
            <table id="fac-header">
                <tr>
                    <td class="text-center">
                        <div class="hr-line-btm uppercase"></div>
                        <b> FACULTATIVE PLACEMENT - FIRE INDUSTRIAL - KENYA TEA DEVELOPMENT AUTHORITY (KTDA) </b>
                        <div class="hr-line-btm"></div>
                    </td>
                </tr>
            </table>
            <table id="slip-details" style="width: 100%; margin-top: 15px; padding:0px;">
                <tr>
                    <td valign="top" style="width: 100%;">
                        <table style="width:100%;">
                            <tr>
                                <td class="opensans-9 s-l bold" style="width: 30%;"><strong> Our Reference:</strong></td>
                                <td class="opensans-9 s-r" style="width: 70%;">233</td>
                            </tr>
                            <tr>
                                <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Cedant Name:</strong></td>
                                <td class="opensans-9 s-r" style="width: 70%;">APA INSURANCE</td>
                            </tr>
                            <tr>
                                <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Insured Name:</strong></td>
                                <td class="opensans-9 s-r" style="width: 70%;">KENYA TEA DEVELOPMENT AUTHORITY (KTDA)</td>
                            </tr>
                            <tr>
                                <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Insurance Group: </strong>
                                </td>
                                <td class="opensans-9 s-r" style="width: 70%;">Facultative Proportional</td>
                            </tr>
                            <tr>
                                <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Class Of Business:</strong>
                                </td>
                                <td class="opensans-9 s-r" style="width: 70%;">Fire Industrial</td>
                            </tr>
                            <tr>
                                <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Period Of Cover:</strong></td>
                                <td class="opensans-9 s-r" style="width: 70%;">12 Sep, 2025 To 12 Sep, 2026</td>
                            </tr>
                            <tr>
                                <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Total Sum Insured
                                        (100%):</strong></td>
                                <td class="opensans-9 s-r" style="width: 70%;">KES 3,000,000,000.00</td>
                            </tr>
                            <tr>
                                <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Premium (100%): </strong></td>
                                <td class="opensans-9 s-r" style="width: 70%;">KES 9,000,000.00</td>
                            </tr>
                            <tr>
                                <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Cedant Commission Rate
                                        (%):</strong> </td>
                                <td class="opensans-9 s-r" style="width: 70%;">30.00%</td>
                            </tr>
                            <tr>
                                <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Placed With:</strong> </td>
                                <td class="opensans-9 s-r" style="width: 70%;">Munich Re, Swiss Re, Hannover Re, Africa
                                    Re,
                                    Continental Re</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div class="main-content">
                <div class="content-row">
                    <div class="content-cell" style="width: 100%;">
                        @php
                            $reinsurerData = [
                                [
                                    'name' => 'Munich Re',
                                    'written_share' => 25.0,
                                    'signed_share' => 25.0,
                                    'sum_insured' => 750000000.0,
                                    'premium' => 2250000.0,
                                ],
                                [
                                    'name' => 'Swiss Re',
                                    'written_share' => 20.0,
                                    'signed_share' => 20.0,
                                    'sum_insured' => 600000000.0,
                                    'premium' => 1800000.0,
                                ],
                                [
                                    'name' => 'Hannover Re',
                                    'written_share' => 15.0,
                                    'signed_share' => 15.0,
                                    'sum_insured' => 450000000.0,
                                    'premium' => 1350000.0,
                                ],
                                [
                                    'name' => 'Africa Re',
                                    'written_share' => 20.0,
                                    'signed_share' => 20.0,
                                    'sum_insured' => 600000000.0,
                                    'premium' => 1800000.0,
                                ],
                                [
                                    'name' => 'Continental Reinsurance',
                                    'written_share' => 10.0,
                                    'signed_share' => 10.0,
                                    'sum_insured' => 300000000.0,
                                    'premium' => 900000.0,
                                ],
                                [
                                    'name' => 'Kenya Re',
                                    'written_share' => 10.0,
                                    'signed_share' => 10.0,
                                    'sum_insured' => 300000000.0,
                                    'premium' => 900000.0,
                                ],
                            ];

                            $totalWrittenShare = 0;
                            $totalSignedShare = 0;
                            $totalSumInsured = 0;
                            $totalPremium = 0;

                            foreach ($reinsurerData as $reinsurer) {
                                $totalWrittenShare += $reinsurer['written_share'];
                                $totalSignedShare += $reinsurer['signed_share'];
                                $totalSumInsured += $reinsurer['sum_insured'];
                                $totalPremium += $reinsurer['premium'];
                            }
                        @endphp

                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background-color: transparent;">
                                <tr>
                                    <td colspan="4" style="padding: 0; background-color: transparent;">
                                        <hr
                                            style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                    </td>
                                </tr>
                                <tr>
                                    <th
                                        style="text-align: left; font-weight: 600; white-space: nowrap; border: none; background-color: transparent; padding: 5px;">
                                        Reinsurer
                                    </th>
                                    <th
                                        style="text-align: center; font-weight: 600; border: none; background-color: transparent; padding: 5px;">
                                        Written Share (%)
                                    </th>
                                    <th
                                        style="text-align: right; font-weight: 600; border: none; background-color: transparent; padding: 5px;">
                                        Total Sum Insured (KES)
                                    </th>
                                    <th
                                        style="text-align: right; font-weight: 600; border: none; background-color: transparent; padding: 5px;">
                                        Premium (KES)
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="4" style="padding: 0; background-color: transparent;">
                                        <hr
                                            style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reinsurerData as $reinsurer)
                                    <tr>
                                        <td style="text-align: left; padding: 5px; border: none;">
                                            {{ $reinsurer['name'] }}
                                        </td>
                                        <td style="text-align: center; padding: 5px; border: none;">
                                            {{ number_format($reinsurer['written_share'], 2) }}%
                                        </td>
                                        <td style="text-align: right; padding: 5px; border: none;">
                                            {{ number_format($reinsurer['sum_insured'], 2) }}
                                        </td>
                                        <td style="text-align: right; padding: 5px; border: none;">
                                            {{ number_format($reinsurer['premium'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" style="padding: 0; background-color: transparent;">
                                        <hr
                                            style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 10px 0 5px 0;">
                                    </td>
                                </tr>
                                <tr style="font-weight: 600;">
                                    <td style="text-align: left; padding: 5px; border: none;">
                                        <strong>TOTAL</strong>
                                    </td>
                                    <td style="text-align: center; padding: 5px; border: none;">
                                        <strong>{{ number_format($totalWrittenShare, 2) }}%</strong>
                                    </td>
                                    <td style="text-align: right; padding: 5px; border: none;">
                                        <strong>{{ number_format($totalSumInsured, 2) }}</strong>
                                    </td>
                                    <td style="text-align: right; padding: 5px; border: none;">
                                        <strong>{{ number_format($totalPremium, 2) }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="padding: 0; background-color: transparent;">
                                        <hr
                                            style="width: 100%; border: none; height: 1px; background-color: #000; opacity: 0.5; margin: 1px 0;">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <table id="slip-details" style="width: 100%; margin-top: 15px; padding:0px;">
                <tr>
                    <td valign="top" style="width: 100%;">
                        <table style="width:100%;">
                            <tr>
                                <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Unplaced Share:</strong> </td>
                                <td class="opensans-9 s-r" style="width: 70%;">30%</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div> --}}
@endsection
