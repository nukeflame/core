<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light"
    data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Acentria </title>
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard Template">
    <meta name="Author" content="Stephen Munyao">
    <meta name="keywords"
        content="blazor bootstrap, c# blazor, admin panel, blazor c#, template dashboard, admin, bootstrap admin template, blazor, blazorbootstrap, bootstrap 5 templates, dashboard, dashboard template bootstrap, admin dashboard bootstrap.">

    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <style>
        .mini-logo {
            background: #fff;
            border-radius: 0px;
        }

        .mini-logo>.mini-logo-img {
            object-fit: contain;
            width: 216px;
            /* width: 47px;
            height: 47px; */
        }

        .mini-logo::after {
            /* content: "|";
            margin: 0px .5rem;
            height: 22px; */
        }

        .login-header .title {
            font-family: "Lato", sans-serif;
            font-size: 28px;
            font-weight: 500;
        }

        .company-title {
            font-family: inherit;
            font-weight: 400;
            font-size: 1.8rem;
        }

        .bg-success {
            background: #22c55e !important;
        }

        .text-success {
            color: #166534 !important;
        }

        .bg-danger {
            background: #ef4444 !important;
        }

        .text-danger,
        .error-message {
            color: #ef4444 !important;
        }

        .bg-warning {
            background: #eab308 !important;
        }

        .text-warning {
            color: #ca8a04 !important;
        }

        .psw-requirements li {
            font-size: 12px !important;
        }

        .input-group>:not(:first-child):not(.dropdown-menu):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback) #pswd-icon {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
            <div class="col-xxl-5 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12">
                <div class="card shadow-sm mt-5">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="mini-logo p-2 me-1">
                                <img src="http://localhost:8000/logo.png" alt="logo" class="mini-logo-img">
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <h2 class="company-title mb-2">Welcome to Acentria</h2>
                            <div class="my-4">
                                <div class="d-flex">
                                    <div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm"
                                        role="alert">
                                        <div class="d-flex justify-content-between">
                                            <div style="margin-right: .5rem">
                                                <svg class="svg-success" xmlns="http://www.w3.org/2000/svg"
                                                    height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="#000000">
                                                    <path d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                                                </svg>
                                            </div>
                                            <div class="flex-column text-start">
                                                For security reasons, you need to set a new password on your first
                                                login.
                                                <br />
                                                Please create a strong password you'll remember.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form id="password-change-form" method="POST" action="{{ route('first-login.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}">

                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium">New Password</label>
                                <div class="input-group mb-1">
                                    <input type="password" class="form-control" placeholder="Enter new password"
                                        id="password" name="password" aria-label="Password"
                                        aria-describedby="pswd-icon">
                                    <span class="input-group-text" id="pswd-icon"><button
                                            class="btn btn-light m-0 p-0 toggle-password" type="button"
                                            data-target="password">
                                            <i class="bi bi-eye-slash"></i>
                                        </button></span>
                                </div>
                            </div>

                            <div class="mb-3" id="password-strength-container" style="display: none;">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small text-muted">Password strength:</span>
                                    <span class="small fw-medium" id="strength-text"></span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div id="password-strength-meter" class="progress-bar" role="progressbar"
                                        style="width: 0%"></div>
                                </div>
                            </div>

                            <div class="mb-4 p-3 bg-light border rounded">
                                <p class="small fw-medium text-dark mb-2 fs-13">Password requirements:</p>
                                <ul class="psw-requirements list-unstyled mb-0 small">
                                    <li class="mb-1 d-flex align-items-center" id="req-length">
                                        <i class="bx bx-circle me-2 text-muted small"></i>
                                        At least 8 characters
                                    </li>
                                    <li class="mb-1 d-flex align-items-center" id="req-uppercase">
                                        <i class="bx bx-circle me-2 text-muted small"></i>
                                        Contains uppercase letter
                                    </li>
                                    <li class="mb-1 d-flex align-items-center" id="req-lowercase">
                                        <i class="bx bx-circle me-2 text-muted small"></i>
                                        Contains lowercase letter
                                    </li>
                                    <li class="mb-1 d-flex align-items-center" id="req-number">
                                        <i class="bx bx-circle me-2 text-muted small"></i>
                                        Contains number
                                    </li>
                                    <li class="d-flex align-items-center" id="req-special">
                                        <i class="bx bx-circle me-2 text-muted small"></i>
                                        Contains special character
                                    </li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-medium">Confirm
                                    Password</label>
                                <div class="input-group mb-1">
                                    <input type="password" class="form-control" placeholder="Enter new password"
                                        id="password_confirmation" name="password_confirmation"
                                        aria-label="Password Confirmation" aria-describedby="pswd-confirm-icon">
                                    <span class="input-group-text" id="pswd-confirm-icon"><button
                                            class="btn btn-light m-0 p-0 toggle-password" type="button"
                                            data-target="password_confirmation">
                                            <i class="bi bi-eye-slash"></i>
                                        </button></span>
                                </div>
                            </div>

                            <button type="submit" id="submit-btn" class="btn btn-dark w-100 py-2 fw-medium">
                                SET NEW PASSWORD
                            </button>

                            <div class="text-center mt-3 fs-13">
                                <a href="{{ route('login') }}" class="text-decoration-none text-muted small">
                                    Back to login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery-3.6.1.min.js') }}"></script>
    <script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.toggle-password').click(function() {
                const targetId = $(this).data('target');
                const input = $('#' + targetId);
                const icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('bi bi-eye').addClass('bi-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            $('#password').on('input', function() {
                const password = $(this).val();

                if (password.length > 0) {
                    $('#password-strength-container').fadeIn();
                } else {
                    $('#password-strength-container').fadeOut();
                    return;
                }

                const reqLength = password.length >= 8;
                const reqUppercase = /[A-Z]/.test(password);
                const reqLowercase = /[a-z]/.test(password);
                const reqNumber = /[0-9]/.test(password);
                const reqSpecial = /[^A-Za-z0-9]/.test(password);

                updateRequirement('req-length', reqLength);
                updateRequirement('req-uppercase', reqUppercase);
                updateRequirement('req-lowercase', reqLowercase);
                updateRequirement('req-number', reqNumber);
                updateRequirement('req-special', reqSpecial);

                const metRequirements = [reqLength, reqUppercase, reqLowercase, reqNumber, reqSpecial]
                    .filter(Boolean).length;
                let strength = 'weak';
                let percentage = 20 * metRequirements; // 20% per requirement
                let colorClass = 'bg-danger';

                if (metRequirements <= 1) {
                    strength = 'Weak';
                    colorClass = 'bg-danger';
                } else if (metRequirements <= 3) {
                    strength = 'Medium';
                    colorClass = 'bg-warning';
                } else {
                    strength = 'Strong';
                    colorClass = 'bg-success';
                }

                $('#strength-text').text(strength);
                $('#strength-text').removeClass('text-danger text-warning text-success');
                $('#strength-text').addClass(colorClass.replace('bg-', 'text-'));
                $('#password-strength-meter').removeClass('bg-danger bg-warning bg-success');
                $('#password-strength-meter').addClass(colorClass);
                $('#password-strength-meter').css('width', percentage + '%');
            });

            $('#password_confirmation').on('input', function() {
                const password = $('#password').val();
                const confirmPassword = $(this).val();

                if (password && confirmPassword && password !== confirmPassword) {
                    return;
                }
            });

            $('#password-change-form').validate({
                errorClass: 'error-message',
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "password" || element.attr("name") ===
                        "password_confirmation") {
                        error.insertAfter(element.closest('.input-group'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                rules: {
                    password: {
                        required: true,
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    password: {
                        required: "Password is required",
                        pattern: "Password must meet the security requirements"
                    },
                    password_confirmation: {
                        required: "Please confirm your password",
                        equalTo: "Passwords do not match"
                    }
                },
                submitHandler: function(form, e) {
                    e.preventDefault();
                    const password = $('#password').val();

                    const reqLength = password.length >= 8;
                    const reqUppercase = /[A-Z]/.test(password);
                    const reqLowercase = /[a-z]/.test(password);
                    const reqNumber = /[0-9]/.test(password);
                    const reqSpecial = /[^A-Za-z0-9]/.test(password);

                    const metRequirements = [reqLength, reqUppercase, reqLowercase, reqNumber,
                            reqSpecial
                        ]
                        .filter(Boolean).length;

                    if (metRequirements < 3) {
                        $('.password-requirements-error').remove();

                        $('#password').closest('.input-group').after(
                            '<div class="error-message password-requirements-error">Password must include at least 3 of the following: 8+ characters, uppercase letter, lowercase letter, number, special character</div>'
                        );
                        return false;
                    }

                    $('.invalid-feedback').remove();
                    $('.is-invalid').removeClass('is-invalid');

                    $('#submit-btn').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
                    );

                    $.ajax({
                        url: $(form).attr('action'),
                        method: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.redirect) {
                                showSuccess(
                                    response.message ||
                                    'Password successfully changed! Redirecting to login...'
                                );
                                setTimeout(() => {
                                    window.location.href = response.redirect ||
                                        '/login';
                                }, 2000);
                            }

                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                Object.keys(errors).forEach(field => {
                                    $(`#${field}`).addClass('is-invalid');
                                    $(`#${field}`).after(
                                        `<div class="invalid-feedback">${errors[field][0]}</div>`
                                    );
                                });
                            } else {
                                console.log(xhr.responseJSON)
                                showError(xhr.responseJSON.message ||
                                    'An error occurred');
                            }
                        },
                        complete: function() {
                            $('#submit-btn').prop('disabled', false).html(
                                'SET NEW PASSWORD');
                        }
                    });

                    return false;
                }
            });

            function updateRequirement(id, met) {
                const element = $('#' + id);
                const icon = element.find('i');

                if (met) {
                    icon.removeClass('bx-circle text-muted').addClass('bx-plus-circle text-success');
                    element.addClass('text-success');
                } else {
                    icon.removeClass('bx-plus-circle text-success').addClass('bx-circle text-muted');
                    element.removeClass('text-success');
                }
            }

            function showError(message) {
                toastr.error(message)
            }

            function showSuccess(message) {
                toastr.success(message)
            }
        });
    </script>
</body>

</html>
