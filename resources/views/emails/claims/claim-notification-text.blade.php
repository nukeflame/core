CLAIM NOTIFICATION
==================

From: {{ $sender->name ?? 'System' }}
Date: {{ $sentAt->format('F j, Y \a\t g:i A') }}

Dear Recipient,

{{ $messageContent }}

@if ($includeClaimDetails && isset($claim))
    CLAIM DETAILS
    -------------
    Claim Reference: {{ $claim->reference_number ?? 'N/A' }}
    Claim ID: {{ $claim->id }}
    @if (isset($claim->status))
        Status: {{ ucfirst($claim->status) }}
    @endif
    @if (isset($claim->amount))
        Amount: ${{ number_format($claim->amount, 2) }}
    @endif
    @if (isset($claim->incident_date))
        Incident Date: {{ $claim->incident_date->format('F j, Y') }}
    @endif
@endif
@if (isset($cedant))
    CEDANT INFORMATION
    ------------------
    {{ $cedant->name ?? 'N/A' }}
    @if (isset($cedant->contact_email))
        Contact: {{ $cedant->contact_email }}
    @endif
@endif
@if ($additionalNotes)
    ADDITIONAL NOTES
    ----------------
    {{ $additionalNotes }}
@endif

If you have any questions regarding this claim, please contact us with the tracking ID: {{ $trackingId }}

---
This is an automated notification. Please do not reply directly to this email.
Tracking ID: {{ $trackingId }}
Sent on: {{ $sentAt->format('Y-m-d H:i:s T') }}
