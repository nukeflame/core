<!DOCTYPE html>
<html>

<head>
    <title>Application Error - Fallback Notification</title>
    <style>
        body {
            font-family: 'Aptos', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .error-container {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .secondary-error {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 5px;
            padding: 15px;
        }

        h1 {
            color: #721c24;
        }
    </style>
</head>

<body>
    <h1>Application Error - Fallback Notification</h1>

    <p>An error occurred in your application, but there was a problem generating the detailed notification.</p>

    <div class="error-container">
        <h2>Original Error</h2>
        <p><strong>{{ $originalErrorClass }}</strong></p>
        <p>{{ $originalErrorMessage }}</p>
        <p>Location: {{ $originalErrorFile }}:{{ $originalErrorLine }}</p>
    </div>

    <div class="secondary-error">
        <h2>Error Notification Error</h2>
        <p>{{ $notificationErrorMessage }}</p>
    </div>

    <p>This is a simplified fallback notification. Check your logs for more details.</p>
</body>

</html>
