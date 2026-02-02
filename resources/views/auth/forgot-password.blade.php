<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Forgot Password - Acentria Group</title>
    <meta name="Description" content="Acentria Group - Reset your password">
    <meta name="Author" content="Stephen Munyao">

    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <!-- Left Side - Image -->
        <div class="image-side">
            <img src="/assets/images/1759911722603-2.jpg" alt="Team collaboration" class="hero-image">
            <div class="image-side-overlay">
                <img src="/assets/images/brand-logos/logo-graphic-transparent.png" alt="" class="">
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="form-side">
            <div class="form-content">
                <div class="logo-container">
                    <div class="logo-wrap">
                        <img src="/assets/images/brand-logos/horizontal-logo.png" alt="Logo" class="logo-icon">
                    </div>
                </div>
                <div class="form-header">
                    <h1>Forgot Password?</h1>
                    <p>No problem. Just enter your email address and we'll send you a password reset link.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success" style="padding: 10px; margin-bottom: 15px; background: #d1fae5; border: 1px solid #10b981; border-radius: 8px; color: #065f46; font-size: 14px;">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Your Email</label>
                        <input type="email" id="email" name="email" class="form-input"
                            placeholder="name@company.com" value="{{ old('email') }}" autocomplete="email" required
                            autofocus aria-describedby="email-error">
                        @error('email')
                            <span class="error-message" id="email-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login" id="submitBtn">
                        Email Password Reset Link
                    </button>
                </form>

                <div class="divider">
                    <span>Or</span>
                </div>

                <div class="register-link" style="text-align: center;">
                    <a href="{{ route('login') }}" style="color: var(--accent-color, #dc2626); text-decoration: none; font-weight: 500;">
                        ← Back to Login
                    </a>
                </div>
            </div>

            <!-- Fluid Dots Background at Bottom -->
            <div class="geometric-overlay">
                <img src="/assets/images/brand-logos/fluid-dots-red.svg" alt="Decorative background pattern"
                    class="hero-image dots-image">
            </div>
        </div>
    </div>

    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.textContent = 'Sending...';
        });

        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>

</html>
