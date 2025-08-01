<!DOCTYPE html>
<html>

<head>
    <title>Tender Approval Required</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
        }

        p {
            color: #000000;
        }



        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #cce0f6;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 style="color: #000;">Tender Approval Required</h1>
        <p style="color: #000;">Dear {{ $mainApprover->name }},</p>
        <p style="color: #000;">A new tender has been submitted for your approval. Please review the details below:</p>
        <p><strong>Tender ID:</strong> {{ $approval->tender_id }}</p>
        {{-- <p><strong>Details:</strong> {{ $approval->details ?? 'No additional details provided.' }}</p> --}}
        <p><strong>Submitted At:</strong> {{ $approval->created_at->format('d M Y, H:i') }}</p>
        {{-- <a href="{{ route('tender.tenderdetails', ['prospect_id' => $approval->prospect_id]) }}" class="button">Review
            Tender</a> --}}

        <p>Thank you,</p>

    </div>
</body>

</html>
