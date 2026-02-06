@extends('printouts.base')

@section('content')
    <style>
        .debit-reinsurer-page {
            font-family: 'Aptos', sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

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
    </style>
    <div style="width:100%; margin-top: 0px; padding:0px; font-size: 9pt; font-family: 'Aptos';" class="debit-reinsurer-page">
        <table id="cover-header">
            <tr>
                <td>
                    <table class="w-100 courier-10 p-0 m-0 reinsurer-details">
                        <tr>
                            <td> <strong>TO </strong></td>
                        </tr>
                        <tr>
                            <td> {{ $customer->name }} </td>
                        </tr>
                        <tr>
                            <td> {{ $customer->postal_address }} </td>
                        </tr>
                        <tr>
                            <td> {{ $customer->street }} </td>
                        </tr>
                        <tr>
                            <td>{{ $customer->city }},
                                {{ \App\Models\Country::where('country_iso', $customer->country_iso)->value('country_name') }}
                            </td>
                        </tr>
                        <tr>
                            <td> {{ $customer->telephone }} </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="w-100 courier-10" style="margin-top: 0px;padding-left:300px;">
                        <tr>
                            <td class="">
                                <div class="info-box uppercase">
                                    <strong>Debit Note:</strong>
                                </div>
                                <div class="info-box text-left">
                                    {{ $debit->debit_note_no }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-box uppercase">
                                    <strong>Date:</strong>
                                </div>
                                <div class="info-box text-left">
                                    {!! formatDate($debit->posting_date) !!}
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
                            <td class="pt-4 courier-9">Fire</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong>Treaty Type</strong></td>
                            <td class="pt-4 courier-9">Surplus Treaty</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong>Reinsured Name</strong></td>
                            <td class="pt-4 courier-9">{{ firstUpper($customer->name) }}</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong>Underwriting Quarter</strong></td>
                            <td class="pt-4 courier-9">First Quarter - 2024</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong>Period of Cover</strong></td>
                            <td class="pt-4 courier-9">From: {{ formatDate($cover->cover_from) }} To:
                                {{ formatDate($cover->cover_to) }}</td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong>Payment Terms</strong></td>
                            <td class="pt-4 courier-9">
                                Premium Due On The Posting Date
                            </td>
                        </tr>
                        <tr>
                            <td class="pt-4 courier-9"><strong> Our share</strong>
                            </td>
                            <td class="pt-4 courier-9">{{ number_format($cover->share_offered, 2) }}%</td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>
        </table>

        <table id="particular-details" style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 8pt;">
            <thead style="border: 1px solid #181212; padding:0px; margin: 0px;">
                <tr>
                    <th class="no-border align-left" style="width: 43%;">PARTICULARS</th>
                    <th class="no-border align-right" style="width: 20%;"></th>
                    <th class="no-border align-right" style="width: 17.5%;">DEBIT</th>
                    <th class="no-border align-right" style="width: 17.5%;">CREDIT</th>
                    <th class="no-border align-right" style="width: 2%;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach (collect($debit_items)->all() as $item)
                    <tr>
                        <td class="no-border align-left" style="width: 43%;">
                            {{ ucwords(strtolower($item->item_name)) }} - {{ ucwords(strtolower($item->class_name)) }}
                        </td>
                        <td class="no-border align-right" style="width: 20%; text-align: right;">
                            {{ number_format($item->original_amount, 2) }} @ {{ number_format($item->line_rate, 2) }}%
                        </td>
                        <td class="no-border align-right" style="width: 17.5%; text-align: right;">
                            @if (in_array($item->ledger, ['DR']))
                                {{ number_format($item->item_amount, 2) }}
                            @else
                                0.00
                            @endif
                        </td>
                        <td class="no-border align-right" style="width: 17.5%; text-align: right;">
                            @if (in_array($item->ledger, ['CR']))
                                {{ number_format($item->item_amount, 2) }}
                            @else
                                0.00
                            @endif
                        </td>
                        <td class="no-border" style="width: 2%;">&nbsp;</td>
                    </tr>
                @endforeach
                <tr style="border-top: 2px solid #181212;">
                    <td class="no-border align-left" style="font-weight: bold; width: 43%;">TOTAL</td>
                    <td class="no-border" style="width: 20%;">&nbsp;</td>
                    <td class="no-border align-right" style="font-weight: bold; width: 17.5%; text-align: right;">
                        {{ number_format($totals->total_debits, 2) }}
                    </td>
                    <td class="no-border align-right" style="font-weight: bold; width: 17.5%; text-align: right;">
                        {{ number_format($totals->total_credits, 2) }}
                    </td>
                    <td class="no-border">&nbsp;</td>
                </tr>
            </tbody>
        </table>

        <table
            style="width:100%; border: 1px solid #181212; border-collapse: collapse; margin-bottom: 10px; font-size:8pt;">
            <thead>
                <tr>
                    <th class="no-border align-left" style="padding: 6px 8px; width: 43%;"><strong>BALANCE DUE FROM
                            YOU</strong></th>
                    <th class="no-border" style="padding: 6px 8px; width: 20%;">&nbsp;</th>
                    <th class="no-border" style="padding: 6px 8px; width: 17.5%;">&nbsp;</th>
                    <th class="no-border align-right" style="padding: 6px 8px; width: 17.5%; text-align: right;">
                        <strong>{{ number_format($debit->net_amount, 2) }}</strong>
                    </th>
                    <th class="no-border" style="padding: 6px 8px; width: 2%;">&nbsp;</th>
                </tr>
            </thead>
        </table>

        <br />
        <table style="width: 100%;">
            <tr>
                <td align="left" style="font-size: 10.0pt; font-family: 'Aptos';">
                    {{ $company->company_name }}</td>
                <td align="left">&nbsp;</td>
                <td align="left"></td>
            </tr>
            <tr>
                <td align="left">
                    @stampImageOrEmpty('app/private/sample-sign.png')
                </td>
                <td align="left">&nbsp;</td>
                <td align="left"></td>
            </tr>
            <tr rowspan=5></tr>
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
                <td class="text-right courier-10">Date: {!! formatDate($debit->created_at) !!} </td>
            </tr>
        </table>
    </div>
@endsection
