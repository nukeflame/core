<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login - Acentria Group</title>
    <meta name="Description" content="Acentria Group - Login to your account">
    <meta name="Author" content="Stephen Munyao">

    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <!-- Left Side - Form -->
        <div class="image-side">
            <img src="/assets/images/1759911722603-2.jpg" alt="Team collaboration" class="hero-image">
            <div class="image-side-overlay">
                <img src="/assets/images/brand-logos/logo-graphic-transparent.png" alt="" class="">
            </div>
            <!-- <div class="halftone-pattern"></div> -->
        </div>

        <!-- Right Side - Image with Overlays -->
        <div class="form-side">
            <div class="form-content">
                <div class="logo-container">
                    <div class="logo-wrap">
                        <img src="/assets/images/brand-logos/horizontal-logo.png" alt="Logo" class="logo-icon">
                    </div>
                </div>
                <div class="form-header">
                    <h1>Welcome Back</h1>
                    <p>Please sign in with your account credentials to continue</p>
                </div>

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Your Email</label>
                        <input type="email" id="email" name="email" class="form-input"
                            placeholder="name@company.com" value="{{ old('email') }}" autocomplete="email" required
                            aria-describedby="email-error">
                        @error('email')
                            <span class="error-message" id="email-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-input"
                            placeholder="Enter your password" autocomplete="current-password" required
                            aria-describedby="password-error">
                        @error('password')
                            <span class="error-message" id="password-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember Me</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        Login
                    </button>
                </form>

                <div class="divider">
                    <span>Or continue with</span>
                </div>

                @error('outlook')
                    <div class="alert alert-danger"
                        style="padding: 10px; margin-bottom: 15px; background: #fee2e2; border: 1px solid #ef4444; border-radius: 8px; color: #991b1b; font-size: 14px;">
                        {{ $message }}
                    </div>
                @enderror

                <div class="social-buttons">
                    <button type="button" class="btn-social" onclick="handleOutlookLogin()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill="#0078D4"
                                d="M24 7.875v8.25A3.375 3.375 0 0 1 20.625 19.5h-9.75L7.5 16.125V7.875L10.875 4.5h9.75A3.375 3.375 0 0 1 24 7.875z" />
                            <path fill="#0364B8" d="M10.875 4.5H7.5v11.625l3.375 3.375V4.5z" />
                            <path fill="#28A8EA"
                                d="M7.5 7.875V16.125L4.125 19.5H3.375A3.375 3.375 0 0 1 0 16.125v-8.25A3.375 3.375 0 0 1 3.375 4.5h.75L7.5 7.875z" />
                            <path fill="#0078D4" d="M7.5 7.875h10.875v8.25H7.5z" />
                            <path fill="#0A2767" d="M18.375 7.875h2.25v8.25h-2.25z" />
                            <ellipse cx="6" cy="12" rx="3.75" ry="4.5" fill="#FFF" />
                            <path fill="#0078D4"
                                d="M6 8.625c-1.243 0-2.25 1.507-2.25 3.375S4.757 15.375 6 15.375 8.25 13.868 8.25 12 7.243 8.625 6 8.625zm0 5.625c-.621 0-1.125-.84-1.125-1.875S5.379 10.5 6 10.5s1.125.84 1.125 1.875S6.621 14.25 6 14.25z" />
                        </svg>
                        Outlook
                    </button>
                    <!--
                    <button type="button" class="btn-social" onclick="handleSSOLogin()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v6l4 2" />
                        </svg>
                        SSO
                    </button> -->
                </div>

                {{-- <div class="register-link">
                    Don't have an account? <a href="{{ route('register') }}">Register</a>
                </div> --}}
            </div>

            <!-- Fluid Dots Background at Bottom -->
            <div class="geometric-overlay">
                <img src="/assets/images/brand-logos/fluid-dots-red.svg" alt="Decorative background pattern"
                    class="hero-image dots-image">
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.textContent = 'Logging in...';
        });

        function handleOutlookLogin() {
            window.location.href = '{{ route('auth.outlook') }}';
        }

        function handleSSOLogin() {
            console.log('SSO login initiated');
            // window.location.href = '/auth/sso';
        }

        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        }
    </script>
</body>

</html>
