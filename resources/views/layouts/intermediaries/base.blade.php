<!DOCTYPE html>
<html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{{ $cmp_prfiles1[0]->company_name }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">

        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />

        <!-- Sweet Alert -->
        <link type="text/css" href="{{ asset('intermediary/vendor/sweetalert2/dist/sweetalert2.min.css') }}"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.1/build/css/intlTelInput.css">
        <!-- Notyf -->
        <link type="text/css" href="{{ asset('intermediary/vendor/notyf/notyf.min.css') }}" rel="stylesheet">

        <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        <script type="text/javascript" src="{{ asset('client/js/bootstrap.bundle.min.js') }}"></script>

        <link type="text/css" href="{{ asset('intermediary/css/volt.css') }}" rel="stylesheet">
        <link href="{{ asset('css/index.css') }}" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://printjs-4de6.kxcdn.com/print.min.css">

        <!-- Include jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>




        <!-- Include CKEditor script -->
        <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- Include CKEditor script -->


        <style>
            #main-content {
                display: none
            }

            #selectedStatusReasons {
                display: none
            }

            .documentAknowledgementTable {
                display: none
            }

            .claimJourney {
                display: none
            }

            .spinnerButton {
                display: none
            }

            #claimCategoryTable {
                display: none;
            }

            #claimTypeTable {
                display: none;
            }

            #claimTableDiv {
                display: none
            }


            .AcknowledgementLetter {
                display: none
            }

            .claimForm {
                display: none
            }

            .select2-container {
                box-sizing: border-box;
                display: inline-block;
                margin: 0;
                position: relative;
                vertical-align: middle;
                width: 100% !important;
            }

            .select2-selection__rendered {
                line-height: 31px !important;
                font-size: calc(1.15em * var(--font-scale-factor)) !important;
            }

            .select2-container .select2-selection--single {
                height: 35px !important;
                /* font-size: calc(1em * var(--font-scale-factor)) !important; */
            }

            .select2-container .select2-results__option {
                font-size: calc(1em * var(--font-scale-factor)) !important;
            }

            .select2-selection__arrow {
                height: 34px !important;
            }

            .modal-header {
                background-color: #E1251B !important;
            }

            ul {
                list-style-type: none;
            }

            .navbar-fixed-top {
                background-color: #E1251B !important;
            }

            #sidebarMenu {
                background-color: #1C325B !important;

            }

            .dt-buttons {
                margin-bottom: 20px;
            }

            .errorClass {
                color: red;
            }

            .required-label::after {
                content: '*';
                color: red;
            }

            :root {
                --font-scale-factor: 0.75;
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6,
            a,
            input::placeholder,
            {
            font-size: calc(1.25em * var(--font-scale-factor));
            }

            .small,
            small {
                font-size: calc(1em * var(--font-scale-factor));
            }

            p,
            li {
                font-size: calc(1em * var(--font-scale-factor));
            }

            label {
                font-size: calc(1em * var(--font-scale-factor));
                font-weight: bold;
            }

            table {
                font-size: calc(1em * var(--font-scale-factor))
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                padding: 5px;
                line-height: 1.2;
            }

            th {
                background-color: #ffd6cc
            }

            input {
                padding: 2px 10px;
                font-size: calc(1em * var(--font-scale-factor));
                height: 35px;
            }

            textarea {
                padding: 2px 10px;
                font-size: 12px;
                height: 80px;
            }

            li span {
                font-size: calc(1em * var(--font-scale-factor));
            }

            .btn {
                font-size: 12px;
                padding: 6px 8px;
                height: auto;
            }

            td .btn {
                font-size: 10px;
                padding: 4px 8px;
                height: auto;
            }

            h2#swal2-title {
                font-size: 1em;
            }

            .navbar-custom {
                background-color: #f8f9fa;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                padding: 0.75rem 1rem;
            }

            .navbar-custom .navbar-brand {
                font-weight: bold;
                color: #333;
            }

            .navbar-custom .nav-icon {
                color: #6c757d;
                margin-right: 1rem;
                position: relative;
            }

            .navbar-custom .nav-icon:hover {
                color: #007bff;
            }

            .badge-custom {
                position: absolute;
                top: -5px;
                right: -39px;
                padding: 0.2em 0.4em;
                font-size: 0.7rem;
                background-color: #dc3545;
                color: white;
                border-radius: 50%;
            }

            .user-avatar {
                width: 35px;
                height: 35px;
                border-radius: 50%;
                object-fit: cover;
            }

            .dropdown-menu-custom {
                min-width: 250px;
            }

            html {
                position: relative;
                min-height: 100%;
            }

            body {
                margin: 0 0 100px;
            }

            .footer {
                position: absolute;
                bottom: 0;
                height: 100px;
                width: 100%;

            }
        </style>


    </head>

    <body>
        <nav id="sidebarMenu" class="sidebar d-lg-block bg-gray-800 text-white collapse" data-simplebar>
            <div class="sidebar-inner px-4">
                <div
                    class="user-card d-flex d-md-none align-items-center justify-content-between justify-content-md-center pb-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg me-4">
                            <img src="/img/avatar/{{ Auth::user()->avatar }}"
                                class="card-img-top rounded-circle border-white" alt="Bonnie Green">
                        </div>
                        <div class="">
                            <h2 class="h5 mb-3">Hi, {{ Auth::user()->username }}</h2>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="btn btn-secondary btn-sm d-inline-flex align-items-center">
                                <svg class="icon icon-xxs me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                Sign Out
                            </a>
                        </div>
                    </div>
                    <div class="collapse-close d-md-none">
                        <a href="#sidebarMenu" onclick="document.getElementById('sidebarMenu').style.display = 'none'">
                            <span class="fa fa-times"></span>
                        </a>
                    </div>
                </div>
                <div>
                    @foreach ($cmp_prfiles1 as $company_profile)
                        @if ($company_profile->logo)
                            <img src="https://acentriagroup.com/wp-content/uploads/2023/08/acentriagrouplogo.png"
                                alt="main_logo">
                        @endif
                    @endforeach
                </div>
                <ul class="nav flex-column pt-3 pt-md-0">
                    <li class="nav-item">
                        <a href="../../index.html" class="nav-link d-flex align-items-center">
                        </a>
                    </li>

                    {{-- @if (auth()->user()->hasRole('admin')) --}}

                    @foreach ($menus as $menu)
                        @checkurl($menu->id)
                            @if ($menu->id == 5)
                                @continue
                            @endif
                            @if ($menu->parent == 0)
                                <li class="nav-item">
                                    <a href="{{ url($menu->url) }}" class="nav-link">
                                        <span class="sidebar-icon">
                                            <i class="fa fa-fa fa-{{ $menu->icon }}"></i>
                                        </span>
                                        <span class="sidebar-text">{{ $menu->name }}</span>
                                    </a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" role="button" data-toggle="collapse"
                                        data-target="#menu{{ $menu->id }}" aria-expanded="false"
                                        aria-controls="menu{{ $menu->id }}" href="#menu{{ $menu->id }}">

                                        <span class="sidebar-icon">
                                            <i class="fa fa-{{ $menu->icon }}" fill="currentColor"></i>
                                        </span>
                                        <span class="nav-link-text ms-1">{{ $menu->name }}</span>
                                        <span class="link-arrow">
                                            <i class="fa fa-sort-down"></i>
                                        </span>
                                    </a>

                                    <ul class="collapse" id="menu{{ $menu->id }}">
                                        @foreach ($menu->submenus as $submenu)
                                            @check_smenu($submenu, $menu)
                                                @if ($submenu->parent == 0)
                                                    <li class="">
                                                        <a style="padding:0.3rem 0 0.3rem 0.8rem"
                                                            href="{{ url($submenu->url) }}" class="nav-link">
                                                            <span class="sidebar-icon">
                                                                <i class="fa fa-{{ $submenu->icon }}" fill="currentColor"></i>
                                                            </span>
                                                            <span class="nav-link-text ms-1">{{ $submenu->name }}</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>

                                                        <a style="padding:0.3rem 0 0.3rem 1.5rem"
                                                            href="#submenu{{ $submenu->id }}" data-bs-toggle="collapse"
                                                            aria-expanded="false" class="dropdown-toggle nav-link">
                                                            <span class="sidebar-icon">
                                                                <i class="fa fa-{{ $submenu->icon }}"></i>
                                                            </span>
                                                            <span class="nav-link-text ms-1">{{ $submenu->name }}</span>

                                                            <span class="link-arrow">
                                                                <i class="fa fa-sort-down"></i>
                                                            </span>
                                                        </a>
                                                        <ul class="collapse list-unstyled" id="submenu{{ $submenu->id }}">
                                                            @foreach ($submenu->children as $child)
                                                                <li>
                                                                    <a style="padding:0.3rem 0 0.3rem 2.5rem"
                                                                        href="{{ url($child->url) }}" class="nav-link">
                                                                        <span class="sidebar-icon">
                                                                            <i class="fa fa-{{ $submenu->icon }}"></i>
                                                                        </span>
                                                                        <span
                                                                            class="nav-link-text ms-1">{{ $child->name }}</span>
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endif
                                            @endcheck_smenu
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endcheckurl
                    @endforeach
                    {{-- @endif --}}
                    <!-- <li role="separator" class="dropdown-divider mt-4 mb-3 border-gray-700"></li>  -->
                </ul>
            </div>
        </nav>

        <main class="content">

            <nav class="navbar navbar-expand-lg navbar-custom navbar-light">
                <a class="navbar-brand" href="#">Dashboard</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav mr-auto">
                        <!-- Optional: Left-side navigation items -->
                        <!-- <li class="nav-item active">
                            <a class="nav-link" href="#">Home</a>
                        </li> -->
                    </ul>

                    <ul class="navbar-nav align-items-center">

                        <!-- Notifications -->
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link nav-icon" data-toggle="dropdown">
                                <i class="far fa-bell"></i>
                                <span class="badge badge-custom">3</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-custom">
                                <div class="dropdown-header">Notifications</div>
                                <a class="dropdown-item" href="#">
                                    <div class="d-flex justify-content-between">
                                        <span>New project assigned</span>
                                        <small class="text-muted">2 hrs ago</small>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="#">
                                    <div class="d-flex justify-content-between">
                                        <span>Meeting scheduled</span>
                                        <small class="text-muted">1 day ago</small>
                                    </div>
                                </a>
                            </div>
                        </li>

                        <!-- Messages -->
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link nav-icon" data-toggle="dropdown">
                                <i class="far fa-envelope"></i>
                                <span class="badge badge-custom">2</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-custom">
                                <div class="dropdown-header">Messages</div>
                                <a class="dropdown-item" href="#">
                                    <div class="d-flex justify-content-between">
                                        <span>John Doe</span>
                                        <small class="text-muted">Just now</small>
                                    </div>
                                    <small class="text-muted">Project update</small>
                                </a>
                                <a class="dropdown-item" href="#">
                                    <div class="d-flex justify-content-between">
                                        <span>Jane Smith</span>
                                        <small class="text-muted">2 hrs ago</small>
                                    </div>
                                    <small class="text-muted">Team meeting</small>
                                </a>
                            </div>
                        </li>

                        <!-- User Profile -->
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                                <img src="https://www.pngarts.com/files/11/Avatar-Transparent-Images.png"
                                    alt="User Avatar" class="user-avatar mr-2">
                                <span>{{ Auth::user()->username }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#"><i class="fas fa-user mr-2"></i>Profile</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-cog mr-2"></i>Settings</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <svg class="dropdown-icon text-danger me-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="m-3">
                @yield('content')
            </div>

            <div class="row footers">
                <div class="col-12">
                    <footer class="bg-white rounded shadow p-5 mb-4 mt-4 ">
                        <p class="mb-0 text-center text-lg-start">Copyright © 2024</span> <a
                                class="text-primary fw-bold">Intramake</a> All rights reserved</p>
                    </footer>
                </div>
            </div>

        </main>

        <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.1/build/js/intlTelInput.min.js"></script>
        <script>
            let input = document.querySelector(".phone");
            window.intlTelInput(input, {
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.1/build/js/utils.js",
            });

            let input2 = document.querySelector(".telephone");
            window.intlTelInput(input2, {
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.1/build/js/utils.js",
            });
        </script>
        <!-- Core -->
        <script src="{{ asset('intermediary/vendor/@popperjs/core/dist/umd/popper.min.js') }}"></script>
        <!-- <script src="{{ asset('intermediary/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script> -->

        <!-- Vendor JS -->
        <script src="{{ asset('intermediary/vendor/onscreen/dist/on-screen.umd.min.js') }}"></script>

        <!-- Smooth scroll -->
        <script src="{{ asset('intermediary/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js') }}"></script>


        <script src="{{ asset('admincast/vendors/chart.js/dist/Chart.min.js') }}" type="text/javascript"></script>


        <!-- Charts -->
        <!-- <script src="{{ asset('admincast/vendors/chart.js/dist/Chart.min.js') }}" type="text/javascript"></script> -->
        <script src="{{ asset('intermediary/vendor/chartist/dist/chartist.min.js') }}"></script>
        <script src="{{ asset('intermediary/vendor/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js') }}"></script>

        <!-- Datepicker -->
        <script src="{{ asset('intermediary/vendor/vanillajs-datepicker/dist/js/datepicker.min.js') }}"></script>

        <!-- Sweet Alerts 2 -->
        <script src="{{ asset('intermediary/vendor/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>


        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <!-- Moment JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>

        <!-- Vanilla JS Datepicker -->
        <script src="{{ asset('intermediary/vendor/vanillajs-datepicker/dist/js/datepicker.min.js') }}"></script>

        <!-- Notyf -->
        <script src="{{ asset('intermediary/vendor/notyf/notyf.min.js') }}"></script>

        <!-- Simplebar -->
        <script src="{{ asset('intermediary/vendor/simplebar/dist/simplebar.min.js') }}"></script>

        <!-- Github buttons -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
        <script src="https://cdn.socket.io/4.3.2/socket.io.min.js"></script>

        <script>
            $(document).ready(function() {
                $('form').attr('autocomplete', 'on');

                $('.select2').select2({
                    closeOnSelect: true,
                });

                $('.select2Multi').select2({
                    closeOnSelect: false,
                });
            });

            // Connect to the WebSocket server (Make sure this URL is correct)
            const socket = io('http://localhost:6001', {
                transports: ['websocket'], // Force WebSocket transport (you can also remove this)
                withCredentials: true, // Allow credentials (cookies, etc.)
            });

            socket.on('BD_Handover_notifications', function(message) {

                // const notificationElement = document.createElement('div');
                // notificationElement.textContent = message;
                // document.getElementById('notifications').appendChild(notificationElement);
            });
        </script>
        {{-- Datatable --}}
        <script src="{{ asset('admincast/vendors/DataTables/datatables.min.js') }}" type="text/javascript"></script>
        {{-- <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script> --}}
        <!-- <script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script> -->


        <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

        <!-- Volt JS -->
        <script src="{{ asset('intermediary/assets/js/volt.js') }}"></script>
        <script src=https://printjs-4de6.kxcdn.com/print.min.js></script>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <!-- Include Bootstrap Select JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>


        @yield('page_scripts')
    </body>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            @if (Session::has('success'))
                toastr.success("{{ Session::get('success') }}");
            @endif
            @if (Session::has('error'))
                toastr.error("{{ Session::get('error') }}");
            @endif
            @if (Session::has('info'))
                toastr.info("{{ Session::get('info') }}");
            @endif
            @if (Session::has('warning'))
                toastr.warning("{{ Session::get('warning') }}");
            @endif

        });
    </script>

</html>
