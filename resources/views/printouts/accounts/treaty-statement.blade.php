<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Statement of Account - {{ $document->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #059669;
            padding-bottom: 15px;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 9px;
            color: #666;
        }

        .document-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .document-info {
            font-size: 9px;
            color: #666;
        }

        .document-info strong {
            color: #333;
        }

        /* Account Info */
        .account-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 15px;
        }

        .account-info-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .account-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .account-title {
            font-size: 12px;
            font-weight: bold;
            color: #166534;
            margin-bottom: 10px;
        }

        .info-row {
            margin-bottom: 4px;
        }

        .info-label {
            color: #666;
            font-size: 9px;
            display: inline-block;
            width: 100px;
        }

        .info-value {
            color: #333;
            font-weight: 500;
            font-size: 9px;
        }

        /* Balance Boxes */
        .balance-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .balance-box {
            display: table-cell;
            width: 32%;
            vertical-align: top;
            text-align: center;
            padding: 15px;
            border-radius: 6px;
        }

        .balance-box.opening {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
        }

        .balance-box.transactions {
            background: #dbeafe;
            border: 1px solid #93c5fd;
        }

        .balance-box.closing {
            background: #059669;
            color: white;
        }

        .balance-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .balance-amount {
            font-size: 16px;
            font-weight: bold;
            font-family: 'DejaVu Sans Mono', monospace;
        }

        /* Table */
        .table-section {
            margin-bottom: 20px;
        }

        .table-title {
            font-size: 11px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        table thead th {
            background: #059669;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        table thead th.text-right {
            text-align: right;
        }

        table tbody td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .amount {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
        }

        .amount-positive {
            color: #059669;
        }

        .amount-negative {
            color: #dc2626;
        }

        .text-center {
            text-align: center;
        }

        /* Transaction Type Icons */
        .type-icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            text-align: center;
            line-height: 16px;
            font-size: 8px;
            font-weight: bold;
            margin-right: 5px;
        }

        .type-icon.debit {
            background: #fee2e2;
            color: #dc2626;
        }

        .type-icon.credit {
            background: #dcfce7;
            color: #166534;
        }

        .type-icon.payment {
            background: #dbeafe;
            color: #1d4ed8;
        }

        /* Aging Table */
        .aging-section {
            margin-bottom: 20px;
        }

        .aging-table {
            width: 100%;
            border-collapse: collapse;
        }

        .aging-table th,
        .aging-table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .aging-table th {
            background: #f9fafb;
            font-size: 8px;
            text-transform: uppercase;
        }

        .aging-table td {
            font-family: 'DejaVu Sans Mono', monospace;
            font-weight: 600;
        }

        .aging-current {
            background: #dcfce7;
            color: #166534;
        }

        .aging-30 {
            background: #fef3c7;
            color: #92400e;
        }

        .aging-60 {
            background: #fed7aa;
            color: #c2410c;
        }

        .aging-90 {
            background: #fee2e2;
            color: #dc2626;
        }

        /* Summary */
        .summary-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .summary-left {
            display: table-cell;
            width: 55%;
            vertical-align: top;
            padding-right: 20px;
        }

        .summary-right {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }

        .bank-details {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 12px;
        }

        .bank-title {
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
            font-size: 10px;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #666;
        }

        .page-number {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">{{ $company['name'] }}</div>
                <div class="company-details">
                    {{ $company['address'] }}<br>
                    Tel: {{ $company['phone'] }} | Email: {{ $company['email'] }}
                </div>
            </div>
            <div class="header-right">
                <div class="document-title">STATEMENT OF ACCOUNT</div>
                <div class="document-info">
                    <strong>Reference:</strong> {{ $document->reference }}<br>
                    <strong>Statement Date:</strong> {{ $statement_date->format('d M Y') }}<br>
                    <strong>Period:</strong> {{ $period_from->format('d M Y') }} - {{ $period_to->format('d M Y') }}<br>
                    <strong>Currency:</strong> {{ $cover->currency ?? 'KES' }}
                </div>
            </div>
        </div>

        <!-- Account Info -->
        <div class="account-info">
            <div class="account-info-left">
                <div class="account-title">Account Holder</div>
                <div class="info-row">
                    <span class="info-label">Company:</span>
                    <span class="info-value">{{ $customer->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $customer->address ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Contact:</span>
                    <span class="info-value">{{ $customer->contact_person ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="account-info-right">
                <div class="account-title">Treaty Information</div>
                <div class="info-row">
                    <span class="info-label">Treaty No:</span>
                    <span class="info-value">{{ $cover->cover_no }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Treaty Type:</span>
                    <span class="info-value">{{ ucfirst($cover->treaty_type ?? 'N/A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">UW Year:</span>
                    <span class="info-value">{{ $year }}</span>
                </div>
            </div>
        </div>

        <!-- Balance Summary -->
        <div class="balance-section">
            <div class="balance-box opening" style="margin-right: 2%;">
                <div class="balance-label">Opening Balance</div>
                <div class="balance-amount">{{ $cover->currency ?? 'KES' }} {{ number_format($opening_balance, 2) }}
                </div>
            </div>
            <div class="balance-box transactions" style="margin-right: 2%;">
                <div class="balance-label">Net Movement</div>
                <div class="balance-amount">
                    {{ $cover->currency ?? 'KES' }}
                    {{ number_format($total_debits - $total_credits - $total_payments, 2) }}
                </div>
            </div>
            <div class="balance-box closing">
                <div class="balance-label">Closing Balance</div>
                <div class="balance-amount">{{ $cover->currency ?? 'KES' }} {{ number_format($closing_balance, 2) }}
                </div>
            </div>
        </div>

        <!-- Aging Summary -->
        <div class="aging-section">
            <div class="table-title">Aging Summary</div>
            <table class="aging-table">
                <thead>
                    <tr>
                        <th>Current (0-30 days)</th>
                        <th>31-60 Days</th>
                        <th>61-90 Days</th>
                        <th>Over 90 Days</th>
                        <th>Total Outstanding</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="aging-current">{{ number_format($aging['current'], 2) }}</td>
                        <td class="aging-30">{{ number_format($aging['days_31_60'], 2) }}</td>
                        <td class="aging-60">{{ number_format($aging['days_61_90'], 2) }}</td>
                        <td class="aging-90">{{ number_format($aging['over_90'], 2) }}</td>
                        <td><strong>{{ number_format($aging['total'], 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Transaction Details -->
        <div class="table-section">
            <div class="table-title">Transaction Details - {{ $quarter }} {{ $year }}</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">Date</th>
                        <th style="width: 8%;">Type</th>
                        <th style="width: 12%;">Reference</th>
                        <th style="width: 30%;">Description</th>
                        <th style="width: 13%;" class="text-right">Debit</th>
                        <th style="width: 13%;" class="text-right">Credit</th>
                        <th style="width: 14%;" class="text-right">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Opening Balance Row -->
                    <tr style="background: #f3f4f6;">
                        <td>{{ $period_from->format('d/m/Y') }}</td>
                        <td></td>
                        <td></td>
                        <td><strong>Opening Balance</strong></td>
                        <td class="amount"></td>
                        <td class="amount"></td>
                        <td class="amount"><strong>{{ number_format($opening_balance, 2) }}</strong></td>
                    </tr>

                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction['date']->format('d/m/Y') }}</td>
                            <td>
                                <span class="type-icon {{ $transaction['type'] }}">
                                    @if ($transaction['type'] === 'debit')
                                        D
                                    @elseif($transaction['type'] === 'credit')
                                        C
                                    @else
                                        P
                                    @endif
                                </span>
                            </td>
                            <td>{{ $transaction['reference'] }}</td>
                            <td>{{ $transaction['description'] }}</td>
                            <td class="amount {{ $transaction['debit'] > 0 ? 'amount-negative' : '' }}">
                                {{ $transaction['debit'] > 0 ? number_format($transaction['debit'], 2) : '' }}
                            </td>
                            <td class="amount {{ $transaction['credit'] > 0 ? 'amount-positive' : '' }}">
                                {{ $transaction['credit'] > 0 ? number_format($transaction['credit'], 2) : '' }}
                            </td>
                            <td class="amount"><strong>{{ number_format($transaction['balance'], 2) }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 20px; color: #666;">
                                No transactions found for this period
                            </td>
                        </tr>
                    @endforelse

                    <!-- Closing Balance Row -->
                    <tr style="background: #059669; color: white;">
                        <td colspan="4"><strong>Closing Balance</strong></td>
                        <td class="amount"><strong>{{ number_format($total_debits, 2) }}</strong></td>
                        <td class="amount"><strong>{{ number_format($total_credits + $total_payments, 2) }}</strong>
                        </td>
                        <td class="amount"><strong>{{ number_format($closing_balance, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-left">
                <div class="bank-details">
                    <div class="bank-title">Payment Instructions</div>
                    <p style="margin-bottom: 8px;">Please remit payment to:</p>
                    <div class="info-row">
                        <span class="info-label">Bank:</span>
                        <span
                            class="info-value">{{ $company['bank_details']['bank_name'] ?? 'Kenya Commercial Bank' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Branch:</span>
                        <span class="info-value">{{ $company['bank_details']['branch'] ?? 'Nairobi' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Account Name:</span>
                        <span
                            class="info-value">{{ $company['bank_details']['account_name'] ?? $company['name'] }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Account No:</span>
                        <span
                            class="info-value">{{ $company['bank_details']['account_number'] ?? 'XXXXXXXXXX' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Swift Code:</span>
                        <span class="info-value">{{ $company['bank_details']['swift_code'] ?? 'KCBLKENX' }}</span>
                    </div>
                </div>
            </div>
            <div class="summary-right">
                <table style="width: 100%; border: 1px solid #e5e7eb;">
                    <tr style="background: #f9fafb;">
                        <td style="padding: 8px;">Total Debits</td>
                        <td class="amount" style="padding: 8px;">{{ number_format($total_debits, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px;">Total Credits</td>
                        <td class="amount" style="padding: 8px;">({{ number_format($total_credits, 2) }})</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px;">Total Payments</td>
                        <td class="amount" style="padding: 8px;">({{ number_format($total_payments, 2) }})</td>
                    </tr>
                    <tr style="background: #059669; color: white;">
                        <td style="padding: 10px;"><strong>Amount Due</strong></td>
                        <td class="amount" style="padding: 10px;">
                            <strong>{{ $cover->currency ?? 'KES' }} {{ number_format($closing_balance, 2) }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This statement is computer-generated and does not require a signature.</p>
            <p>For any queries, please contact: {{ $company['email'] }} | {{ $company['phone'] }}</p>
            <p style="margin-top: 5px;">
                Generated on: {{ now()->format('d M Y H:i') }} | Document ID: {{ $document->uuid }}
            </p>
        </div>
    </div>
</body>

</html>
