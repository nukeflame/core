<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('Not Found'))</title>
    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #f8fafc;
            color: #636b6f;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            font-weight: 400;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .content {
            text-align: center;
            max-width: 700px;
            padding: 40px 20px;
        }

        .illustration {
            max-width: 300px;
            margin: 0 auto 30px;
        }

        .title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2d3748;
        }

        .subtitle {
            font-size: 18px;
            color: #4a5568;
            margin-bottom: 30px;
        }

        .code {
            display: inline-block;
            font-size: 100px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 20px;
            line-height: 1;
        }

        .message {
            margin-bottom: 30px;
            color: #718096;
        }

        .back-link {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        .back-link:hover {
            background-color: #4338ca;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="flex-center full-height">
        <div class="content">
            <div class="code" role="heading" aria-level="1" aria-label="Error code: @yield('code', '404')">
                @yield('code', '404')
            </div>

            <div class="illustration">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" fill="none">
                    <!-- Server/Computer -->
                    <rect x="150" y="200" width="200" height="150" rx="10" fill="#e2e8f0" stroke="#4a5568"
                        stroke-width="4" />

                    <!-- Server Lights/Buttons -->
                    <circle cx="180" cy="225" r="8" fill="#fc8181" />
                    <circle cx="180" cy="255" r="8" fill="#f6ad55" />
                    <circle cx="180" cy="285" r="8" fill="#68d391" />

                    <!-- Server Slots -->
                    <rect x="210" y="225" width="120" height="10" rx="2" fill="#a0aec0" />
                    <rect x="210" y="245" width="120" height="10" rx="2" fill="#a0aec0" />
                    <rect x="210" y="265" width="120" height="10" rx="2" fill="#a0aec0" />
                    <rect x="210" y="285" width="120" height="10" rx="2" fill="#a0aec0" />

                    <!-- Lightning Bolt -->
                    <path d="M320 120L250 200L280 220L200 320" stroke="#ed8936" stroke-width="8" stroke-linecap="round"
                        stroke-linejoin="round" />

                    <!-- Smoke Clouds -->
                    <ellipse cx="230" cy="180" rx="25" ry="15" fill="#cbd5e0" />
                    <ellipse cx="260" cy="160" rx="30" ry="20" fill="#cbd5e0" />
                    <ellipse cx="290" cy="170" rx="20" ry="15" fill="#cbd5e0" />

                    <!-- Error Symbol -->
                    <circle cx="350" cy="150" r="40" fill="#fc8181" opacity="0.8" />
                    <path d="M335 135L365 165M365 135L335 165" stroke="white" stroke-width="6" stroke-linecap="round" />
                </svg>
            </div>

            <div class="title">@yield('title', 'Not Found')</div>
            <div class="subtitle">@yield('message', 'Sorry, the page you are looking for could not be found.')</div>

            <div class="message">
                @yield('description', 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.')
            </div>

            <a href="{{ url('/') }}" class="back-link">Back to Home</a>
        </div>
    </div>
</body>

</html>
