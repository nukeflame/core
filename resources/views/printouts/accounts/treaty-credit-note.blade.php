@extends('printouts.base')

@section('content')
    <style>
        #particular-details td,
        #particular-details th {
            padding: 6px 8px;
        }

        #particular-details thead th {
            border-bottom: 1px solid #181212;
        }

        #particular-details tbody tr td {
            border-bottom: 1px solid #ddd;
            border-right: 1px solid transparent;
            border-left: 1px solid transparent;
        }

        .align-right {
            text-align: right;
        }

        .align-left {
            text-align: left;
        }

        .no-border-left {
            border-left: none !important;
        }

        .no-border-right {
            border-left: none !important;
        }

        .reinsurer-page {
            min-height: 0;
            position: relative;
            font-family: 'Aptos', sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
            page-break-after: always;
        }

        .reinsurer-page:last-child {
            page-break-after: auto;
        }

        .reinsurer-page.first-page {
            page-break-before: avoid !important;
        }

        .reinsurer-page:not(.first-page) {
            page-break-before: always;
            page-break-inside: avoid;
        }
    </style>

    @foreach ($reinsurers as $index => $reinsurer)
        @php
            $reinsurerShare = $reinsurer->share / 100;

            // Filter out brokerage items if with_brokerage is false
            $filteredCreditItems = collect($credit_items);
            if (!($with_brokerage ?? true)) {
                $filteredCreditItems = $filteredCreditItems->filter(function ($item) {
                    // IT06 is Brokerage Fee item code
                    return !in_array($item->item_code, ['IT06', 'BROK']);
                });
            }

            $reinsurerCreditItems = $filteredCreditItems->map(function ($item) use ($reinsurerShare) {
                $clone = clone $item;
                $clone->item_amount = $item->item_amount * $reinsurerShare;
                return $clone;
            });

            // Calculate totals from filtered items
            $totalDebit = $reinsurerCreditItems->filter(fn($item) => $item->ledger === 'DR')->sum('item_amount');
            $totalCredit = $reinsurerCreditItems->filter(fn($item) => $item->ledger === 'CR')->sum('item_amount');
            $netAmount = $totalDebit - $totalCredit;

            $reinsurerTotals = (object) [
                'gross_premium' => $totals->gross_premium * $reinsurerShare,
                'commission' => $totals->commission * $reinsurerShare,
                'net_amount' => $netAmount,
            ];
        @endphp

        <div class="reinsurer-page {{ $index === 0 ? 'first-page' : '' }}">
            <div style="width:100%; margin-top: 0px; padding:0px; font-size: 9pt; font-family: 'Aptos';">
                <table id="cover-header">
                    <tr>
                        <td>
                            <table class="w-100 courier-10 p-0 m-0 reinsurer-details">
                                <tr>
                                    <td> <strong>TO </strong></td>
                                </tr>
                                <tr>
                                    <td> {{ $reinsurer->partner->name ?? 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <td> {{ $reinsurer->partner->postal_address ?? '' }} </td>
                                </tr>
                                <tr>
                                    <td> {{ $reinsurer->partner->street ?? '' }} </td>
                                </tr>
                                <tr>
                                    <td>{{ $reinsurer->partner->city ?? '' }},
                                        {{ \App\Models\Country::where('country_iso', $reinsurer->partner->country_iso ?? '')->value('country_name') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td> {{ $reinsurer->partner->telephone ?? '' }} </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="w-100 courier-10" style="margin-top: 0px;padding-left:300px;">
                                <tr>
                                    <td class="">
                                        <div class="info-box uppercase">
                                            <strong>Credit Note:</strong>
                                        </div>
                                        <div class="info-box text-left">
                                            {{ $credit->credit_note_no }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="info-box uppercase">
                                            <strong>Date:</strong>
                                        </div>
                                        <div class="info-box text-left">
                                            {!! formatDate($credit->posting_date) !!}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="info-box uppercase">
                                            <strong>Currency:</strong>
                                        </div>
                                        <div class="info-box text-left">
                                            {!! $cover->currency_code !!}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center">
                            <div class="hr-line-btm"></div>
                            <b> {{ $cover->cover_title }} </b>
                            <div class="hr-line-btm"></div>
                        </td>
                    </tr>
                </table>

                <table id="cover-details">
                    <tr>
                        <td valign="top">
                            <table class="w-100">
                                <tr>
                                    <td class="courier-10"><strong> Cover Number</strong></td>
                                    <td class="courier-10">{{ $cover->cover_no }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-9"><strong>Cover Reference</strong></td>
                                    <td class="pt-4 courier-9">{{ $cover->endorsement_no }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-9"><strong>Business Class</strong></td>
                                    <td class="pt-4 courier-9">{{ $cover->type_of_bus ?? 'Surplus Treaty' }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-9"><strong>Treaty Type</strong></td>
                                    <td class="pt-4 courier-9">{{ $reinsurer->treaty_code ?? 'Surplus Treaty' }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-9"><strong>Reinsured Name</strong></td>
                                    <td class="pt-4 courier-9">{{ firstUpper($customer->name) }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-9"><strong>Underwriting Quarter</strong></td>
                                    <td class="pt-4 courier-9">Q{{ $credit->posting_quarter ?? '1' }} -
                                        {{ $credit->posting_year ?? date('Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-9"><strong>Period of Cover</strong></td>
                                    <td class="pt-4 courier-9">From: {{ formatDate($cover->cover_from) }} To:
                                        {{ formatDate($cover->cover_to) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-9"><strong>Payment Terms</strong></td>
                                    <td class="pt-4 courier-9">
                                        Premium Due On The Posting Date
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-9"><strong> Your share S.I
                                            ({{ number_format($reinsurer->share, 2) }}%)
                                        </strong>
                                    </td>
                                    <td class="pt-4 courier-9">
                                        {{ number_format($reinsurer->sum_insured ?? 0, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td></td>
                    </tr>
                </table>

                <table id="particular-details"
                    style="width: 100%; border: 1px solid #181212; border-collapse: collapse; margin-bottom: 10px; font-size: 8pt;">
                    <thead style="border: 1px solid #181212; padding:0px; margin: 0px;">
                        <tr>
                            <th class="no-border align-left" style="width: 45%;">PARTICULARS</th>
                            <th class="no-border align-left" style="width: 20%;"></th>
                            <th class="no-border align-left" style="width: 17.5%;">DEBIT</th>
                            <th class="no-border align-left" style="width: 17.5%;">CREDIT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reinsurerCreditItems as $item)
                            <tr>
                                <td class="no-border align-left">
                                    {{ ucwords(strtolower($item->item_name)) }} -
                                    {{ ucwords(strtolower($item->class_name ?? '')) }}
                                </td>
                                <td class="align-left">
                                    {{ number_format($item->item_amount, 2) }}
                                    {{ $item->line_rate > 0 ? '@' . number_format($item->line_rate, 2) . '%' : '' }}
                                </td>
                                {{-- DEBIT column: For credit notes, CR items (claims/deductions) go here --}}
                                <td class="float-right">
                                    @if (in_array($item->ledger, ['CR']))
                                        {{ number_format($item->item_amount, 2) }}
                                    @else
                                        0.00
                                    @endif
                                </td>
                                {{-- CREDIT column: For credit notes, DR items (premium) go here --}}
                                <td class="float-right">
                                    @if (in_array($item->ledger, ['DR']))
                                        {{ number_format($item->item_amount, 2) }}
                                    @else
                                        0.00
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #181212;">
                            <td class="no-border align-left" style="font-weight: bold;">TOTAL</td>
                            <td class="no-border align-left">&nbsp;</td>
                            <td class="no-border align-right" style="font-weight: bold;">
                                {{ number_format($reinsurerTotals->net_amount, 2) }}
                            </td>
                            <td class="no-border align-right" style="font-weight: bold;">
                                {{ number_format($reinsurerTotals->net_amount, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table
                    style="width:100%; border: 1px solid #181212; border-collapse: collapse; margin-bottom: 10px; font-size:8pt;">
                    <thead>
                        <tr>
                            <th class="no-border align-left" style="padding: 6px 8px; width: 45%;"><strong>BALANCE DUE FROM
                                    YOU</strong></th>
                            <th class="no-border" style="padding: 6px 8px; width: 20%;">&nbsp;</th>
                            <th class="no-border" style="padding: 6px 8px; width: 17.5%;">&nbsp;</th>
                            <th class="no-border align-right" style="padding: 6px 8px; width: 17.5%;">
                                <strong>{{ number_format($reinsurerTotals->net_amount, 2) }}</strong>
                            </th>
                        </tr>
                    </thead>
                </table>

                <br />
                <table style="width: 100%;">
                    <tr>
                        <td align="left" style="font-size: 10.0pt; font-family: 'Aptos';">
                            {{ $company->company_name }}
                        </td>
                        <td align="left">&nbsp;</td>
                        <td align="left"></td>
                    </tr>
                    <tr>
                        <td align="left">
                            <br />
                            @stampImageOrEmpty('app/private/sample-sign.png')
                        </td>
                        <td align="left">&nbsp;</td>
                        <td align="left"></td>
                    </tr>
                </table>

                <table style="width: 100%;">
                    <tr>
                        <td align="left">________________________</td>
                        <td align="left">&nbsp;</td>
                        <td align="left"></td>
                    </tr>
                    <tr>
                        <td class="text-left courier-10">Signature</td>
                        <td class="text-left">&nbsp;</td>
                        <td class="text-right courier-10">Date: {!! formatDate($credit->created_at) !!} </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach
@endsection
