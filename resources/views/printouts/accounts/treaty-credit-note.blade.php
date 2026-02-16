@extends('printouts.base')

@section('content')
    <style>
        #particular-details td,
        #particular-details th {
            padding: 6px 8px;
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
            border-right: none !important;
        }

        .no-border {
            border: none !important;
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
            $sharePercent = (float) ($reinsurer->share ?? 0);
            $shareFactor = $sharePercent > 1 ? $sharePercent / 100 : $sharePercent;
            $premiumTaxRate = (float) ($cover->prem_tax_rate ?? 0);
            $applyNetTaxShare = !empty($reinsurer->net_of_tax) && $premiumTaxRate > 0;
            if ($applyNetTaxShare) {
                $shareFactor *= (100 - min(100, max(0, $premiumTaxRate))) / 100;
            }
            $shareFactor = max(0, $shareFactor);
            $reinsurerCreditItems = collect($credit_items)->map(function ($item) use ($shareFactor) {
                $itemClone = clone $item;
                $baseItemAmount = (float) ($item->item_amount ?? 0);
                $baseOriginalAmount = (float) ($item->original_amount ?? $baseItemAmount);
                $itemClone->item_amount = $baseItemAmount * $shareFactor;
                $itemClone->original_amount = $baseOriginalAmount * $shareFactor;

                return $itemClone;
            });
            $filteredCreditItems = $reinsurerCreditItems;
            if (!($with_brokerage ?? true)) {
                $filteredCreditItems = $filteredCreditItems->filter(function ($item) {
                    $itemCode = strtoupper((string) ($item->item_code ?? ''));
                    $itemName = strtolower((string) ($item->item_name ?? ''));
                    $description = strtolower((string) ($item->description ?? ''));

                    $isBrokerageCode = in_array($itemCode, ['IT06', 'BROK', 'BRC', 'BROKERAGE'], true);
                    $isBrokerageText = str_contains($itemName, 'brokerage') || str_contains($description, 'brokerage');

                    return !($isBrokerageCode || $isBrokerageText);
                });
            }

            $totalDebit = $filteredCreditItems->filter(fn($item) => $item->ledger === 'DR')->sum('item_amount');
            $totalCredit = $filteredCreditItems->filter(fn($item) => $item->ledger === 'CR')->sum('item_amount');
            $netAmount = $totalDebit - $totalCredit;
            $reinsurerTotals = (object) [
                'gross_premium' => $totals->gross_premium,
                'commission' => $totals->commission,
                'total_debits' => $totalDebit,
                'total_credits' => $totalCredit,
                'net_amount' => $netAmount,
            ];
            $displaySharePercent = $shareFactor * 100;
            $shareSumInsured = $reinsurer->sum_insured ?? ($cover->total_sum_insured ?? 0) * $shareFactor;
            $balanceDueLabel =
                ($document_type ?? 'Credit Note') === 'Cover Note' ? 'BALANCE DUE TO YOU' : 'BALANCE DUE FROM YOU';
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
                                            <strong>{{ $document_type ?? 'Credit Note' }}:</strong>
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
                                    <td class="pt-4 courier-9">
                                        {{ firstUpper($bus_class ?? ($cover->class_code ?? 'N/A')) }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-9"><strong>Treaty Type</strong></td>
                                    <td class="pt-4 courier-9">
                                        {{ firstUpper($treat_type ?? ($cover->treaty_type ?? 'N/A')) }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-9"><strong>Reinsured Name</strong></td>
                                    <td class="pt-4 courier-9">{{ firstUpper($customer->name) }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-9"><strong>Underwriting Quarter</strong></td>
                                    <td class="pt-4 courier-9">
                                        {{ $underwriting_quarter ?? ($credit->posting_quarter ?? '') . ' - ' . ($credit->posting_year ?? '') }}
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
                                        {{ firstUpper(optional($ppw ?? null)->pay_term_desc ?? 'Premium Due on the Posting Date') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-4 courier-9"><strong>Your Share</strong>
                                    </td>
                                    <td class="pt-4 courier-9">
                                        {{ number_format($displaySharePercent, 2) }}%
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td></td>
                    </tr>
                </table>

                <table id="particular-details"
                    style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 8pt;">
                    <thead style="border: 1px solid #181212; padding:0px; margin: 0px;">
                        <tr>
                            <th class="no-border align-left" style="width: 32.5%;">PARTICULARS</th>
                            <th class="no-border align-right" style="width: 32.5%;"></th>
                            <th class="no-border align-right" style="width: 17.5%;">DEBIT</th>
                            <th class="no-border align-right" style="width: 17.5%;">CREDIT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (collect($filteredCreditItems)->all() as $item)
                            <tr>
                                <td class="no-border align-left" style="width: 32.5%;">
                                    {{ ucwords(strtolower($item->item_name)) }} -
                                    {{ ucwords(strtolower($item->class_name ?? '')) }}
                                </td>
                                <td class="no-border align-right" style="width: 32.5%; text-align: right;">
                                    {{ number_format(abs($item->original_amount ?? $item->item_amount ?? 0), 2) }}
                                    {{ $item->line_rate > 0 ? '@' . number_format($item->line_rate, 2) . '%' : '' }}
                                </td>
                                <td class="no-border align-right" style="width: 17.5%; text-align: right;">
                                    @if (in_array($item->ledger, ['DR']))
                                        {{ number_format(abs($item->item_amount), 2) }}
                                    @else
                                        0.00
                                    @endif
                                </td>
                                <td class="no-border align-right" style="width: 17.5%; text-align: right;">
                                    @if (in_array($item->ledger, ['CR']))
                                        {{ number_format(abs($item->item_amount), 2) }}
                                    @else
                                        0.00
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        {{-- <tr style="border-top: 2px solid #181212;">
                            <td class="no-border align-left" style="font-weight: bold; width: 32.5%;">TOTAL</td>
                            <td class="no-border" style="width: 32.5%;">&nbsp;</td>
                            <td class="no-border align-right" style="font-weight: bold; width: 17.5%; text-align: right;">
                                {{ number_format(abs($reinsurerTotals->total_debits), 2) }}
                            </td>
                            <td class="no-border align-right" style="font-weight: bold; width: 17.5%; text-align: right;">
                                {{ number_format(abs($reinsurerTotals->total_credits), 2) }}
                            </td>
                        </tr> --}}
                    </tbody>
                </table>

                <table
                    style="width:100%; border: 1px solid #181212; border-collapse: collapse; margin-bottom: 10px; font-size:8pt;">
                    <thead>
                        <tr>
                            <th class="no-border align-left" style="padding: 6px 8px; width: 32.5%;">
                                <strong>{{ $balanceDueLabel }}</strong>
                            </th>
                            <th class="no-border" style="padding: 6px 8px; width: 32.5%;">&nbsp;</th>
                            <th class="no-border" style="padding: 6px 8px; width: 17.5%;">&nbsp;</th>
                            <th class="no-border align-right" style="padding: 6px 8px; width: 17.5%; text-align: right;">
                                <strong>{{ number_format(abs($reinsurerTotals->net_amount), 2) }}</strong>
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
