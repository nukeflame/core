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
    <script src="{{ asset('assets/js/authentication-main.js') }}"></script>
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">

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

        .login-header .title {
            font-family: "Lato", sans-serif;
            font-size: 28px;
            font-weight: 500;
        }

        .company-title {
            font-family: inherit;
            font-weight: 400;
            font-size: 21px;
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

        .text-danger {
            color: #ef4444 !important;
        }

        .bg-warning {
            background: #eab308 !important;
        }

        .text-warning {
            color: #ca8a04 !important;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-8 col-12">
                <div class="card shadow-sm mt-5">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="mini-logo p-2 me-1 mb-3">
                                <img src="/logo.png" alt="logo" class="mini-logo-img">
                            </div>
                        </div>
                        <div class="text-center mb-4">
                            <h2 class="company-title mb-3">Login</h2>
                            <p class="text-muted">Welcome back! Please login to your account.</p>
                        </div>
                        <div class="my-5"></div>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="row gy-3">
                                <div class="col-xl-12">
                                    <label for="signin-username" class="form-label text-default">Email</label>
                                    <input type="email" name="email" class="form-control form-control-lg"
                                        id="signin-username" placeholder="Enter Email">
                                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger"
                                        style="list-style: none;margin: 0px;padding: 0px;" />
                                </div>
                                <div class="col-xl-12 mb-2">
                                    <label for="signin-password" class="form-label text-default d-block">Password<a
                                            href="{{ route('password.request') }}" class="float-end text-danger">Forget
                                            password ?</a></label>
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control form-control-lg"
                                            id="signin-password" placeholder="password">
                                    </div>
                                    <div class="mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                value="" id="defaultCheck1">
                                            <label class="form-check-label text-muted fw-normal" for="defaultCheck1">
                                                Remember password ?
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 d-grid mt-2">
                                    <button type="submit"
                                        class="btn btn-dark btn-raised-shadow btn-lg btn-wave waves-effect waves-light">LOGIN</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="">
                    <footer class="text-center">
                        <p class="text-muted">&copy; {{ date('Y') }} Acentria Group. All rights reserved.</p>
                    </footer>
                </div>
            </div>
        </div>
        {{-- <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
            <div class="col-xxl-5 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12">
                <div class="my-5 d-flex justify-content-center">
                    {{-- <a href="#"></a> -
                </div>
                <div class="card custom-card">
                    <p class="h5 fw-semibold mb-2 text-center">
                        <img src="{{ asset('logo.png') }}" alt="" style="width: 300px; height: auto;">
                    </p>
                    <div class="card-body p-5">
                        <p class="h5 fw-semibold mb-2 text-center">Login</p>
                        {{-- <p class="mb-4 text-muted op-7 fw-normal text-center">Welcome back !</p> --

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="row gy-3">
                                <div class="col-xl-12">
                                    <label for="signin-username" class="form-label text-default">Email</label>
                                    <input type="email" name="email" class="form-control form-control-lg"
                                        id="signin-username" placeholder="Enter Email">
                                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger"
                                        style="list-style: none;margin: 0px;padding: 0px;" />
                                </div>
                                <div class="col-xl-12 mb-2">
                                    <label for="signin-password" class="form-label text-default d-block">Password<a
                                            href="{{ route('password.request') }}" class="float-end text-danger">Forget
                                            password ?</a></label>
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control form-control-lg"
                                            id="signin-password" placeholder="password">
                                    </div>
                                    <div class="mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                value="" id="defaultCheck1">
                                            <label class="form-check-label text-muted fw-normal" for="defaultCheck1">
                                                Remember password ?
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 d-grid mt-2">
                                    <button type="submit"
                                        class="btn btn-dark btn-raised-shadow btn-lg btn-wave waves-effect waves-light">LOGIN</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/show-password.js') }}"></script>
</body>

</html>
