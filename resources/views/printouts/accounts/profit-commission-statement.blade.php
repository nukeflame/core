@extends('printouts.base')

@section('content')
    <style>
        .pc-wrap {
            font-size: 9pt;
        }

        .pc-header,
        .pc-meta,
        .pc-summary {
            width: 100%;
            border-collapse: collapse;
        }

        .pc-header td {
            vertical-align: top;
            padding: 2px 4px;
        }

        .pc-meta td {
            padding: 4px 6px;
        }

        .pc-box {
            border: 1px solid #111;
        }

        .pc-box td {
            border: 1px solid #111;
            padding: 2px 6px;
            font-size: 8.5pt;
        }

        .pc-title {
            text-align: center;
            font-weight: 700;
            border-top: 1px solid #111;
            border-bottom: 1px solid #111;
            padding: 4px 0;
            margin: 10px 0 6px 0;
            letter-spacing: 0.3px;
        }

        .pc-lines {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .pc-lines th,
        .pc-lines td {
            border-bottom: 1px solid #222;
            padding: 5px 8px;
            font-size: 8.5pt;
        }

        .pc-lines th {
            border: 1px solid #111;
            background: #f0f0f0;
            font-weight: 700;
        }

        .pc-lines .right {
            text-align: right;
        }

        .pc-double {
            border-top: 2px double #111 !important;
            border-bottom: 2px double #111 !important;
            font-weight: 700;
        }

        .pc-summary {
            margin-top: 10px;
            border: 1px solid #111;
        }

        .pc-summary td {
            padding: 4px 8px;
            font-size: 9pt;
        }

        .pc-summary .label {
            font-weight: 700;
        }

        .pc-summary .value {
            text-align: right;
        }
    </style>

    <div class="pc-wrap">
        @php
            $recipient = $recipient ?? $customer ?? null;
            $noteHeaderLabel = strtoupper((string) ($noteHeaderLabel ?? 'DEBIT NOTE'));
            $noteNumber = $noteNumber ?? ($debit->debit_note_no ?? 'N/A');
            $dueLabel = strtoupper((string) ($dueLabel ?? 'DUE FROM YOU'));
        @endphp
        <table class="pc-header">
            <tr>
                <td style="width: 60%;">
                    <div><strong>TO</strong></div>
                    <div>{{ strtoupper($recipient->name ?? 'N/A') }}</div>
                    <div>{{ $recipient->postal_address ?? ($recipient->address ?? '') }}</div>
                    <div>{{ $recipient->city ?? '' }}</div>
                    <div>{{ $recipient->telephone ?? ($recipient->phone ?? '') }}</div>
                </td>
                <td style="width: 40%;">
                    <table class="pc-box" style="width: 100%;">
                        <tr>
                            <td style="width: 55%; text-align: right;"><strong>{{ $noteHeaderLabel }} :</strong></td>
                            <td style="width: 45%; text-align: right;">{{ $noteNumber }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><strong>DATE :</strong></td>
                            <td style="text-align: right;">{{ formatDate($debit->posting_date ?? $generated_date) }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><strong>CURRENCY :</strong></td>
                            <td style="text-align: right;">{{ $cover->currency_code ?? $cover->currency ?? 'KES' }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><strong>POSTING NO :</strong></td>
                            <td style="text-align: right;">{{ $noteNumber }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="pc-title">PROFIT COMMISSION STATEMENT</div>

        <table class="pc-meta">
            <tr>
                <td style="width: 22%;"><strong>Cover Reference</strong></td>
                <td style="width: 78%;">{{ $cover->cover_no ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Business Class</strong></td>
                <td>{{ $bus_class ?? ($cover->class_code ?? '-') }}</td>
            </tr>
            <tr>
                <td><strong>Period of Cover</strong></td>
                <td>{{ formatDate($cover->cover_from) }} to {{ formatDate($cover->cover_to) }}</td>
            </tr>
            <tr>
                <td><strong>Treaty Type</strong></td>
                <td>{{ $treat_type ?? ($cover->treaty_type ?? '-') }}</td>
            </tr>
            <tr>
                <td><strong>Cedant Name</strong></td>
                <td>{{ $customer->name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Payment Terms</strong></td>
                <td>{{ firstUpper(optional($ppw ?? null)->pay_term_desc ?? 'Premium Due on Posting Date') }}</td>
            </tr>
            <tr>
                <td><strong>Your Share</strong></td>
                <td>{{ number_format((float) ($yourShare ?? 0), 2) }}%</td>
            </tr>
        </table>

        <table class="pc-lines">
            <thead>
                <tr>
                    <th style="width: 50%; text-align:left;">PARTICULARS</th>
                    <th style="width: 25%;" class="right">TO CEDANT</th>
                    <th style="width: 25%;" class="right">TO REINSURERS</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td class="right">{{ number_format((float) ($item->to_cedant ?? 0), 2) }}</td>
                        <td class="right">{{ number_format((float) ($item->to_reinsurers ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td>No line items available</td>
                        <td class="right">0.00</td>
                        <td class="right">0.00</td>
                    </tr>
                @endforelse
                <tr>
                    <td></td>
                    <td class="right pc-double">{{ number_format((float) ($basicTotalCR ?? 0), 2) }}</td>
                    <td class="right pc-double">{{ number_format((float) ($basicTotalDR ?? 0), 2) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="pc-summary">
            <tr>
                <td style="width: 65%;" class="label">PROFIT REALIZED</td>
                <td style="width: 35%;" class="value">{{ number_format((float) ($profitRealized ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td class="label">PROFIT COMMISSION RATE</td>
                <td class="value">{{ number_format((float) ($profitCommissionRate ?? 0), 2) }} %</td>
            </tr>
            <tr>
                <td class="label">PROFIT COMMISSION AMT</td>
                <td class="value">{{ number_format((float) ($profitCommissionAmount ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td class="label">YOUR SHARE</td>
                <td class="value">{{ number_format((float) ($yourShare ?? 0), 2) }} %</td>
            </tr>
            <tr>
                <td class="label">{{ $dueLabel }}</td>
                <td class="value"><span class="pc-double">{{ number_format((float) ($dueFromYou ?? 0), 2) }}</span></td>
            </tr>
        </table>
    </div>
@endsection
