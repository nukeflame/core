<!DOCTYPE html>
<html>

<head>
    <title>Application Error Notification</title>
    <style>
        body {
            font-family: 'Aptos', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .error-box {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .details-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        pre {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            overflow: auto;
            font-size: 12px;
        }

        /*  */
        .error-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding-bottom: 10px;
        }

        .error-title {
            margin-top: 0;
            color: #e74c3c;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .error-message {
            background-color: #fff5f5;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin-bottom: 20px;
            font-family: monospace;
            white-space: pre-wrap;
            overflow-x: auto;
        }

        .sql-error {
            font-weight: bold;
        }

        .sql-query {
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #eee;
            border-radius: 4px;
            margin-top: 10px;
            font-family: monospace;
        }

        .sql-highlight {
            background-color: #ffecec;
            color: #e74c3c;
            padding: 0 3px;
        }

        .connection-info {
            margin-top: 20px;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Application Error Notification</h1>

        <div class="error-box">
            <h2>Error Details</h2>
            <p><strong>Environment:</strong> {{ $environment }}</p>
            <p><strong>URL:</strong> {{ $url }}</p>
            <p><strong>Exception:</strong> {{ $exceptionClass }}</p>
            <p><strong>File:</strong> {{ $file }}</p>
            <p><strong>Line:</strong> {{ $line }}</p>
        </div>

        <div class="error-container">
            <h1 class="error-title">Database Error</h1>

            <div class="error-message">
                @if (isset($exceptionMessage))
                    {!! nl2br(e($exceptionMessage)) !!}
                @else
                    No error message available.
                @endif
            </div>

            @if (isset($sqlQuery))
                <div>
                    <strong>SQL Query:</strong>
                    <div class="sql-query">
                        {!! nl2br(e($sqlQuery)) !!}
                    </div>
                </div>
            @endif

            @if (isset($connection))
                <div class="connection-info">
                    <strong>Connection:</strong> {{ $connection }}
                </div>
            @endif
        </div>

        @if (!empty($user))
            <div class="details-box">
                <h2>User Information</h2>
                <p><strong>User ID:</strong> {{ $user['id'] }}</p>
                <p><strong>Email:</strong> {{ $user['email'] }}</p>
            </div>
        @endif

        <div class="details-box">
            <h2>Request Data</h2>
            <pre>{{ json_encode($request, JSON_PRETTY_PRINT) }}</pre>
        </div>

        <div class="details-box">
            <h2>Stack Trace</h2>
            <pre>{{ $trace }}</pre>
        </div>
    </div>

</body>

</html>
