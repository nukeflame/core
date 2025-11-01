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
        @php
            $filteredReinsurer = null;

            if (isset($reinsurerId) && !empty($reinsurerId)) {
                foreach ($reinsurers as $reinsurer) {
                    if (isset($reinsurer['id']) && $reinsurer['reinsurer_id'] == $reinsurerId) {
                        $filteredReinsurer = $reinsurer;
                        break;
                    }
                }
            }

            $reinsurersToDisplay = $filteredReinsurer ? [$filteredReinsurer] : $reinsurers;
        @endphp

        @foreach ($reinsurersToDisplay as $index => $reinsurer)
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
                    <div style="width:100%; margin-top: 0px; padding:0px; font-size: 9pt; font-family: 'Open Sans';">
                        <table id="fac-header">
                            <tr>
                                <td class="text-center">
                                    <div class="hr-line-btm uppercase"></div>
                                    <b> FACULTATIVE PLACEMENT - {{ strtoupper($opportunity['class_name']) }} -
                                        {{ strtoupper($opportunity['insured_name']) }}</b>
                                    </b>
                                    <div class="hr-line-btm"></div>
                                </td>
                            </tr>
                        </table>
                        <table id="slip-details" style="width: 100%; margin-top: 15px; padding:0px;">
                            <tr>
                                <td valign="top" style="width: 100%;">
                                    <table style="width:100%;">
                                        <tr>
                                            <td class="opensans-9 s-l bold" style="width: 30%;"><strong> Our
                                                    Reference:</strong></td>
                                            <td class="opensans-9 s-r" style="width: 70%;">{{ $reference_no }}</td>
                                        </tr>
                                        <tr>
                                            <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Cedant
                                                    Name:</strong></td>
                                            <td class="opensans-9 s-r" style="width: 70%;">
                                                {{ $opportunity['customer_name'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Insured
                                                    Name:</strong></td>
                                            <td class="opensans-9 s-r" style="width: 70%;">
                                                {{ ucwords(strtolower($opportunity['insured_name'])) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Insurance Group:
                                                </strong>
                                            </td>
                                            <td class="opensans-9 s-r" style="width: 70%;">
                                                {{ $opportunity['type_of_bus'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Class Of
                                                    Business:</strong>
                                            </td>
                                            <td class="opensans-9 s-r" style="width: 70%;">
                                                {{ ucwords(strtolower($opportunity['class_name'])) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Period Of
                                                    Cover:</strong></td>
                                            <td class="opensans-9 s-r" style="width: 70%;">
                                                {{ !empty($opportunity['effective_date']) && !empty($opportunity['closing_date']) ? date('d M, Y', strtotime($opportunity['effective_date'])) . ' To ' . date('d M, Y', strtotime($opportunity['closing_date'])) : 'TBA' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Total Sum
                                                    Insured
                                                    (100%)
                                                    :</strong></td>
                                            <td class="opensans-9 s-r" style="width: 70%;">
                                                {{ $opportunity['currency_code'] }}
                                                {{ number_format($opportunity['sum_insured']) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Premium (100%):
                                                </strong></td>
                                            <td class="opensans-9 s-r" style="width: 70%;">
                                                {{ $opportunity['currency_code'] }}
                                                {{ number_format($opportunity['cedant_premium']) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Cedant
                                                    Commission Rate ({{ number_format($opportunity['commission_rate']) }}%)
                                                    :</strong> </td>
                                            <td class="opensans-9 s-r" style="width: 70%;">
                                                {{ $opportunity['commission_rate'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="opensans-9 s-l bold" style="width: 30%;"><strong>Placed
                                                    With:</strong> </td>
                                            <td class="opensans-9 s-r" style="width: 70%;">{{ $reinsurer['name'] }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <div class="main-content">
                            <div class="content-row">
                                <div class="content-cell" style="width: 100%;">
                                    @php
                                        $writtenShareDecimal = $reinsurer['written_share'] / 100;
                                        $reinsurerSumInsured = $opportunity['sum_insured'] * $writtenShareDecimal;
                                        $reinsurerPremium = $opportunity['cedant_premium'] * $writtenShareDecimal;

                                        $reinsurerData = [
                                            [
                                                'name' => $reinsurer['name'],
                                                'written_share' => $reinsurer['written_share'],
                                                'sum_insured' => $reinsurerSumInsured,
                                                'premium' => $reinsurerPremium,
                                            ],
                                        ];

                                        $totalWrittenShare = $reinsurer['written_share'];
                                        $totalSumInsured = $reinsurerSumInsured;
                                        $totalPremium = $reinsurerPremium;
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
                                                    Total Sum Insured ({{ $currency ?? 'KES' }})
                                                </th>
                                                <th
                                                    style="text-align: right; font-weight: 600; border: none; background-color: transparent; padding: 5px;">
                                                    Premium ({{ $currency ?? 'KES' }})
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
                    </div>
                </div>
            </div>
        @endforeach
    @else
        {!! $currentStage !!}
    @endif
@endsection
