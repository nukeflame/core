<!DOCTYPE html>
<html>

<head>
    <title>Tender Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }


        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .status-Approved {
            color: #28a745;
        }

        .status-Rejected {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Tender Status Update</h1>
        <p>Dear {{ $submitter->name }},</p>
        <p>Your tender submission has been reviewed. The current status is:</p>
        <p><strong>Tender ID:</strong> {{ $approval->tender_id }}</p>
        <p><strong>Status:</strong> <span class="status-{{ $statusText }}">{{ $statusText }}</span></p>
        <p><strong>Remarks:</strong> {{ $approval->remarks ?? 'No additional details provided.' }}</p>
        <p><strong>Updated At:</strong> {{ $approval->updated_at->format('d M Y, H:i') }}</p>
        {{-- <a href="{{ url('/tenders/' . $approval->tender_id) }}" class="button">View Tender</a> --}}
        <p>Thank you,</p>
    </div>
</body>

</html>
