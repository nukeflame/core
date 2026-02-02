<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Reset Password - Acentria Group</title>
    <meta name="Description" content="Acentria Group - Reset your password">
    <meta name="Author" content="Stephen Munyao">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">

    <style>
        .password-requirements {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .password-requirements p {
            font-size: 13px;
            font-weight: 500;
            color: #495057;
            margin-bottom: 10px;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .password-requirements li {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .password-requirements li i {
            margin-right: 8px;
            font-size: 10px;
        }

        .password-requirements li.met {
            color: #059669;
        }

        .password-requirements li.met i {
            color: #059669;
        }

        .password-strength-bar {
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-bar .strength-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s, background-color 0.3s;
        }

        .strength-text {
            font-size: 12px;
            margin-top: 5px;
            font-weight: 500;
        }

        .input-group-custom {
            position: relative;
        }

        .input-group-custom .form-input {
            padding-right: 45px;
        }

        .toggle-password-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            z-index: 5;
        }

        .toggle-password-btn:hover {
            color: #495057;
        }

        .error-message {
            color: #dc2626;
            font-size: 12px;
            margin-top: 5px;
        }

        .alert-error {
            padding: 10px;
            margin-bottom: 15px;
            background: #fee2e2;
            border: 1px solid #f87171;
            border-radius: 8px;
            color: #991b1b;
            font-size: 14px;
        }
    </style>
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
                    <h1>Reset Your Password</h1>
                    <p>Please enter your new password below.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success"
                        style="padding: 10px; margin-bottom: 15px; background: #d1fae5; border: 1px solid #10b981; border-radius: 8px; color: #065f46; font-size: 14px;">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-input"
                            placeholder="name@company.com" value="{{ old('email', $request->email) }}"
                            autocomplete="email" required readonly>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group-custom">
                            <input type="password" id="password" name="password" class="form-input"
                                placeholder="Enter your new password" autocomplete="new-password" required>
                            <button type="button" class="toggle-password-btn" data-target="password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <div class="password-strength-bar" id="strength-bar" style="display: none;">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                        <div class="strength-text" id="strength-text" style="display: none;"></div>
                    </div>

                    <!-- Password Requirements -->
                    <div class="password-requirements" id="password-requirements">
                        <p>Password requirements:</p>
                        <ul>
                            <li id="req-length"><i class="bi bi-circle"></i> At least 8 characters</li>
                            <li id="req-uppercase"><i class="bi bi-circle"></i> Contains uppercase letter</li>
                            <li id="req-lowercase"><i class="bi bi-circle"></i> Contains lowercase letter</li>
                            <li id="req-number"><i class="bi bi-circle"></i> Contains number</li>
                            <li id="req-special"><i class="bi bi-circle"></i> Contains special character</li>
                        </ul>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="input-group-custom">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-input" placeholder="Confirm your new password" autocomplete="new-password"
                                required>
                            <button type="button" class="toggle-password-btn" data-target="password_confirmation">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <div class="error-message" id="confirm-error" style="display: none;">Passwords do not match
                        </div>
                    </div>

                    <button type="submit" class="btn-login" id="submitBtn">
                        Reset Password
                    </button>
                </form>

                <div class="divider">
                    <span>Or</span>
                </div>

                <div class="register-link" style="text-align: center;">
                    <a href="{{ route('login') }}"
                        style="color: var(--accent-color, #dc2626); text-decoration: none; font-weight: 500;">
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
        // Toggle password visibility
        document.querySelectorAll('.toggle-password-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            });
        });

        // Password strength and requirements validation
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const strengthBar = document.getElementById('strength-bar');
        const strengthFill = document.getElementById('strength-fill');
        const strengthText = document.getElementById('strength-text');
        const confirmError = document.getElementById('confirm-error');

        passwordInput.addEventListener('input', function() {
            const password = this.value;

            if (password.length > 0) {
                strengthBar.style.display = 'block';
                strengthText.style.display = 'block';
            } else {
                strengthBar.style.display = 'none';
                strengthText.style.display = 'none';
                return;
            }

            const checks = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };

            // Update requirement indicators
            updateRequirement('req-length', checks.length);
            updateRequirement('req-uppercase', checks.uppercase);
            updateRequirement('req-lowercase', checks.lowercase);
            updateRequirement('req-number', checks.number);
            updateRequirement('req-special', checks.special);

            // Calculate strength
            const metCount = Object.values(checks).filter(Boolean).length;
            const percentage = metCount * 20;

            let strength, color;
            if (metCount <= 1) {
                strength = 'Weak';
                color = '#ef4444';
            } else if (metCount <= 3) {
                strength = 'Medium';
                color = '#eab308';
            } else {
                strength = 'Strong';
                color = '#22c55e';
            }

            strengthFill.style.width = percentage + '%';
            strengthFill.style.backgroundColor = color;
            strengthText.textContent = strength;
            strengthText.style.color = color;

            // Check password match
            if (confirmInput.value) {
                checkPasswordMatch();
            }
        });

        confirmInput.addEventListener('input', checkPasswordMatch);

        function checkPasswordMatch() {
            if (confirmInput.value && passwordInput.value !== confirmInput.value) {
                confirmError.style.display = 'block';
            } else {
                confirmError.style.display = 'none';
            }
        }

        function updateRequirement(id, met) {
            const element = document.getElementById(id);
            const icon = element.querySelector('i');

            if (met) {
                element.classList.add('met');
                icon.classList.remove('bi-circle');
                icon.classList.add('bi-check-circle-fill');
            } else {
                element.classList.remove('met');
                icon.classList.remove('bi-check-circle-fill');
                icon.classList.add('bi-circle');
            }
        }

        // Form submission handling
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmInput.value;

            // Check password match
            if (password !== confirmPassword) {
                e.preventDefault();
                confirmError.style.display = 'block';
                return;
            }

            // Check minimum requirements (at least 3)
            const checks = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };

            const metCount = Object.values(checks).filter(Boolean).length;
            if (metCount < 3) {
                e.preventDefault();
                alert(
                    'Password must include at least 3 of the following: 8+ characters, uppercase letter, lowercase letter, number, special character');
                return;
            }

            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.textContent = 'Resetting...';
        });

        // Focus effects
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
