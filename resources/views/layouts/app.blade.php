<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="dark" data-toggled="close">

<head>
    <!-- Meta Data -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title> {{ $pageTitle ?? ($company->company_name ?? 'Acentriagroup.com') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="Description" content="Reinsurance Broking System" />
    <meta name="Author" content="@pk305" />
    <meta name="keywords"
        content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit." />

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- Choices JS -->
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">

    <!-- Main Theme Js -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Style Css -->
    <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">

    <!-- Toast Css -->
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet">

    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">

    <!-- Node Waves Css -->
    <link href="{{ asset('assets/libs/node-waves/waves.min.css') }}" rel="stylesheet">

    <!-- Simplebar Css -->
    <link href="{{ asset('assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/@simonwep/pickr/themes/nano.min.css') }}">

    <!-- Choices Css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">

    <!-- dataTables -->
    <link href="{{ asset('assets/css/jquery.dataTables.css') }}" rel="stylesheet">

    <!-- Select2  -->
    <link href="{{ asset('assets/libs/select2/select2-4-1-0.min.css') }}" rel="stylesheet" />

    <!-- Sweetalert Css  -->
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert/sweetalert.min.js') }}">

    <!-- Fonts - Aptos  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    {{-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-1CFC6BYDBW"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-1CFC6BYDBW');
    </script> --}}

    <!-- Custom css  -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />

    <!-- Chartist CSS -->
    <link href="{{ asset('assets/css/chartist.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/chartist-plugin-tooltip.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/buttons.dataTables.min.css') }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill-better-table@1.2.10/dist/quill-better-table.css" rel="stylesheet" />
    {{-- <link href="https://unpkg.com/quill-better-table@1.2.10/dist/quill-better-table.css" rel="stylesheet"> --}}


    {{-- <link href="{{ asset('assets/libs/quill/quill.min.css') }}" rel="stylesheet" /> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/default.min.css" /> --}}

    @yield('styles')

    @stack('styles')
</head>

<body>
    <div class="page">
        <header class="app-header">
            <div class="main-header-container container-fluid">
                <div class="header-content-left">
                    <div class="header-element">
                        <div class="horizontal-logo">
                            <a href="javascript:void(0);" class="header-logo">
                                <img src="{{ asset('assets/images/brand-logos/main-horizontal-logo.png') }}"
                                    alt="" class="desktop-logo" />
                                <img src="{{ asset('assets/images/brand-logos/main-horizontal-black.png') }}"
                                    alt="" class="desktop-dark" {{-- style="filter: grayscale(1);" --}} />
                                <img src="{{ asset('assets/images/brand-logos/main-horizontal-logo.png') }}"
                                    alt="" class="toggle-logo" />
                                <img src="{{ asset('assets/images/brand-logos/main-horizontal-black.png') }}"
                                    alt="" class="toggle-dark" {{-- style="filter: grayscale(1);"  --}} />
                            </a>
                        </div>
                    </div>

                    <div class="header-element">
                        <a aria-label="Hide Sidebar"
                            class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle"
                            data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a>
                    </div>

                    <div class="header-element">
                        <div class="header-center d-flex align-items-center">
                            <h6 class="header-title mb-0 me-2">Reinsurance Brokerage System</h6>
                        </div>
                    </div>
                </div>

                <div class="header-content-right">
                    <div class="header-element pr-3">
                        <h6 class="header-title mb-0">Account Period:
                            {{ $current_account_year }}/{{ $current_account_month }}</h6>
                    </div>


                    <div class="header-element header-search">
                        <a href="javascript:void(0);" class="header-link" data-bs-toggle="modal"
                            data-bs-target="#searchModal">
                            <span class="rel-icon">🔍</span>
                            {{-- <i class="bx bx-search-alt-2 header-link-icon"></i> --}}
                        </a>
                    </div>

                    <div class="header-element header-theme-mode">
                        <a href="javascript:void(0);" onclick="toggleTheme()" class="header-link layout-setting"
                            id="toggle-layout-setting">
                            <span class="light-layout">
                                {{-- <i class="bx bx-moon header-link-icon"></i> --}}
                                <span class="rel-icon">🌙</span>
                            </span>
                            <span class="dark-layout">
                                <span class="rel-icon">☀️</span>
                                {{-- <i class="bx bx-sun header-link-icon"></i> --}}
                            </span>
                        </a>
                    </div>

                    <div class="header-element notifications-dropdown">
                        <a href="javascript:void(0);" id="notificationCounter" class="header-link dropdown-toggle"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside" id="messageDropdown"
                            aria-expanded="false">
                            <span class="rel-icon">🔔</span>
                            {{-- <i class="bx bx-bell header-link-icon"></i> --}}
                            <span id="notification-icon-badge" data-counter="0"
                                class="notification-badge  translate-middle rounded-pill d-none">
                                <span class="visually-hidden">unread messages</span>
                            </span>
                            {{-- <span
                                class="notification-badge badge bg-white rounded-pill header-icon-badge pulse pulse-danger"
                                id="notification-icon-badge" data-counter="0"></span> --}}
                        </a>
                        <div class="main-header-dropdown dropdown-menu dropdown-menu-end"
                            data-popper-placement="none">
                            <div class="p-2 px-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0 fs-17 fw-semibold">Notifications</p>
                                    <span class="badge bg-secondary-transparent d-none" id="notification-data"></span>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <ul class="list-unstyled mb-0" id="header-notification-scroll"></ul>
                        </div>
                    </div>

                    {{-- <div class="header-element header-fullscreen">
                        <a onclick="openFullscreen();" href="#" class="header-link">
                            <i class="bx bx-fullscreen full-screen-open header-link-icon"></i>
                            <i class="bx bx-exit-fullscreen full-screen-close header-link-icon d-none"></i>
                        </a>
                    </div> --}}

                    <div class="header-element px-3">
                        <a href="#" class="header-link dropdown-toggle" id="mainHeaderProfile"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="me-sm-2 me-0">
                                    {{-- <img src="{{ asset('user-avator.png') }}" alt="img" width="32"
                                        height="32" class="rounded-circle"> --}}
                                    <img src="{{ auth()->user()->avatar ?? asset('user-avator.png') }}"
                                        alt="User Avatar" class="rounded-circle" width="32" height="32">
                                    {{-- <span style="font-size: 30px;" class="rounded-circle">👨</span> --}}
                                </div>
                                <div class="d-sm-block d-none">
                                    <p class="fw-semibold mb-0 lh-1 fs-15">{{ $username }} <span
                                            class="fs-14"><i class="bx bx-chevron-down"></i>
                                        </span></p>
                                    {{-- <span class="op-7 fw-normal d-block fs-11 text-center">{{ $roleName }}</span> --}}
                                </div>
                            </div>
                        </a>
                        <ul class="main-header-dropdown dropdown-menu shadow-lg border-0 rounded-3 py-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
                            aria-labelledby="mainHeaderProfile">
                            <li
                                class="px-4 py-3 bg-gradient-to-r from-indigo-500 to-blue-600 text-white border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-white p-1">
                                        <img src="{{ auth()->user()->avatar ?? asset('user-avator.png') }}"
                                            alt="User Avatar" class="rounded-circle" width="48" height="48">
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold text-dark">{{ auth()->user()->name }}</h6>
                                        <small class="opacity-80 text-dark">{{ auth()->user()->email }}</small>
                                    </div>
                                </div>
                            </li>

                            <li class="px-4 py-2 border-bottom">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted fs-14">Status</span>
                                    <div class="dropdown">
                                        <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2 py-1"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="badge bg-success rounded-circle p-1 me-1"></span>
                                            <small>Online</small>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-sm py-1">
                                            <li><a class="dropdown-item py-1" href="#"><span
                                                        class="badge bg-success rounded-circle p-1 me-2"></span>Online</a>
                                            </li>
                                            <li><a class="dropdown-item py-1" href="#"><span
                                                        class="badge bg-warning rounded-circle p-1 me-2"></span>Away</a>
                                            </li>
                                            <li><a class="dropdown-item py-1" href="#"><span
                                                        class="badge bg-danger rounded-circle p-1 me-2"></span>Do Not
                                                    Disturb</a></li>
                                            <li><a class="dropdown-item py-1" href="#"><span
                                                        class="badge bg-secondary rounded-circle p-1 me-2"></span>Invisible</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>

                            <li class="dropdown-section">
                                <div class="d-flex flex-column">
                                    <a class="dropdown-item d-flex align-items-center py-2 hover-bg-light-primary"
                                        href="{{ route('settings.profile') }}">
                                        <i class="ti ti-user-circle fs-18 me-3 text-primary"></i>
                                        <div>
                                            <span class="fw-medium">My Profile</span>
                                            <small class="d-block text-muted">View and edit your information</small>
                                        </div>
                                    </a>

                                    <a class="dropdown-item d-flex align-items-center py-2 hover-bg-light-primary"
                                        href="/">
                                        <i class="ti ti-layout-dashboard fs-18 me-3 text-primary"></i>
                                        <div>
                                            <span class="fw-medium">Dashboard</span>
                                            <small class="d-block text-muted">Return to your workspace</small>
                                        </div>
                                    </a>

                                    <a class="dropdown-item d-flex align-items-center py-2 hover-bg-light-primary"
                                        href="{{ route('settings.departments') }}">
                                        <i class="ti ti-adjustments-horizontal fs-18 me-3 text-primary"></i>
                                        <div>
                                            <span class="fw-medium">Settings</span>
                                            <small class="d-block text-muted">Manage your preferences</small>
                                        </div>
                                    </a>
                                </div>
                            </li>

                            <li class="mt-2 bg-light">
                                <div class="d-flex border-top">
                                    <a href="#"
                                        class="flex-grow-1 text-center py-2 border-end text-decoration-none text-body">
                                        <i class="ti ti-help fs-16 d-block mb-1"></i>
                                        <small>Help</small>
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="post"
                                        class="flex-grow-1">
                                        @csrf
                                        <button type="submit" id="logout-btn"
                                            class="btn btn-link w-100 text-danger p-2 text-decoration-none">
                                            <i class="ti ti-logout fs-16 d-block mb-1"></i>
                                            <small>Log Out</small>
                                        </button>
                                    </form>
                                </div>
                            </li>
                            {{-- <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
                            aria-labelledby="mainHeaderProfile">
                            <li>
                                <a class="dropdown-item d-flex" href="{{ route('settings.profile') }}"><i
                                        class="ti ti-user-circle fs-18 me-2 op-7"></i>Profile</a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex" href="{{ route('settings.departments') }}"><i
                                        class="ti ti-adjustments-horizontal fs-18 me-2 op-7"></i>Settings</a>
                            </li>
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <a role="button" id="logout-btn" class="dropdown-item d-flex" href="#"><i
                                            class="ti ti-logout fs-18 me-2 op-7"></i>Log
                                        Out</a>
                                </form>
                            </li>
                        </ul> --}}
                    </div>
                </div>
            </div>
        </header>

        <aside class="app-sidebar sticky" id="sidebar">
            <div class="main-sidebar-header header-logo">
                <a href="javascript:void(0);" class="header-logo logo-link">
                    <img src="{{ asset('assets/images/brand-logos/main-horizontal-logo.png') }}" alt=""
                        class="desktop-logo" />
                    <img src="{{ asset('assets/images/brand-logos/logo-small.png') }}" alt=""
                        class="toggle-logo" />
                    <img src="{{ asset('assets/images/brand-logos/main-horizontal-logo.png') }}" alt=""
                        class="desktop-dark" {{-- style="filter: grayscale(1);" --}} />
                    <img src="{{ asset('assets/images/brand-logos/logo-small.png') }}" alt=""
                        class="toggle-dark" {{-- style="filter: grayscale(1);"  --}} />
                </a>
            </div>

            <x-sidebar></x-sidebar>
        </aside>

        <div class="main-content app-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

        <div class="modal effect-scale md-wrapper" id="searchModal" tabindex="-1" aria-labelledby="searchModal"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="input-group">
                            <a href="javascript:void(0);" class="input-group-text" id="Search-Grid"><i
                                    class="fe fe-search header-link-icon fs-18"></i></a>
                            <input type="search" class="form-control border-0 px-2" placeholder="Search"
                                aria-label="search" id="search-input" />
                        </div>
                        <div class="my-4">
                            <p class="font-weight-semibold text-muted mb-2">
                                Results :-
                            </p>
                            <div class="">
                                <ul id="search-results"></ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="btn-group ms-auto">
                            <button class="btn btn-sm btn-primary-light" id="search-btn">Search</button>
                            {{-- <button class="btn btn-sm btn-primary">Clear Recents</button> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Authentication Modal -->
        <div class="modal effect-super-scaled md-wrapper" id="authenticationModal" tabindex="-1" role="dialog"
            aria-labelledby="authenticationModalLabel" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="authenticationModalLabel"
                            style="font-size: 19px; vertical-align: -3px;"><i class="bx bx-lock"></i> Authentication
                            Required
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form method="post" id="authenticationForm">
                                    @csrf
                                    <div class="form-group">
                                        <label for="accessCode" class="form-label">
                                            <span>
                                                Enter Your Password
                                            </span>
                                        </label>
                                        <input class="form-inputs" autocomplete="off" id="accessCode"
                                            name="accessCode" type="password" required>
                                    </div>
                                    <div class="alert alert-danger" id="error-message" style="display: none;"></div>
                                    <button id="submitAuthentication" type="submit"
                                        class="btn btn-success btn-wave text-white waves-effect waves-light pull-right btn-sm"
                                        data-style="expand-right" data-size="s">
                                        Authenticate
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Start -->
        <footer class="footer mt-auto py-3 bg-white text-center">
            <div class="container">
                <span class="text-muted">
                    Copyright &copy; <span id="year"></span>
                    <a href="javascript:void(0);" class="text-dark fw-semibold">Acentria Technologies</a>. All rights
                    reserved
                </span>
            </div>
        </footer>
    </div>

    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>

    <div id="responsive-overlay"></div>

    {{-- Jquery --}}
    <script src="{{ asset('js/jquery-3.6.1.min.js') }}"></script>

    <!-- Popper JS -->
    <script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    {{-- Jquery Validate --}}
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>

    {{-- Jquery Datables --}}
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.print.min.js') }}"></script>

    <!-- Defaultmenu JS -->
    <script src="{{ asset('assets/js/defaultmenu.min.js') }}"></script>

    <!-- Node Waves JS-->
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

    <!-- Sticky JS -->
    <script src="{{ asset('assets/js/sticky.js') }}"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/simplebar.js') }}"></script>

    <!-- Color Picker JS -->
    <script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>

    {{-- Sweetalert Js --}}
    <script src="{{ asset('assets/libs/sweetalert/sweetalert.min.js') }}"></script>

    <!-- Apex Charts JS -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <!-- Chart JS -->
    <script src="{{ asset('assets/libs/chart.js/chart.min.js') }}"></script>

    <!-- Moment Js -->
    <script src="{{ asset('assets/js/moment.js') }}"></script>

    <!-- select2 JS -->
    <script src="{{ asset('assets/libs/select2/select2-4-1-0.min.js') }}"></script>

    <!--  Toast Js -->
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>

    <!-- Ckeditor Js  -->
    <script src="{{ asset('assets/ckeditor5/ckeditor.js') }}"></script>

    <!-- Custom JS-->
    <script src="{{ asset('js/custom.js') }}"></script>

    {{-- TinyMCE Editor --}}
    <script src="{{ asset('assets/libs/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

    <!-- Chartist JS -->
    <script src="{{ asset('assets/js/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/js/chartist-plugin-tooltip.min.js') }}"></script>

    <!-- Pusher Js  -->
    <script src="{{ asset('js/pusher.min.js') }}"></script>

    <!-- Laravel Echo Js  -->
    <script src="{{ asset('js/echo.iife.min.js') }}"></script>

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script>
        hljs.highlightAll();
    </script> --}}
    {{-- <script src="{{ asset('assets/libs/quill/quill.min.js') }}"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-better-table@1.2.10/dist/quill-better-table.min.js"></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script src="https://unpkg.com/quill-better-table@1.2.10/dist/quill-better-table.js"></script>

    <script>
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: "reverb",
            key: "{{ env('VITE_REVERB_APP_KEY') }}",
            // cluster: "mt1",
            wsHost: "{{ env('VITE_REVERB_HOST') }}",
            wsPort: "{{ env('VITE_REVERB_PORT') }}",
            // wssPort: 443,
            forceTLS: false,
            disableStats: true,
            auth: {
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
            },
            // enabledTransports: ["ws", "wss"],
            // disableStats: true,
        });

        function setActiveSidebar() {
            // Get the current URL path
            var currentUrl = window.location.pathname;

            const slideHasSub = document.querySelectorAll(".nav > ul > .slide.has-sub");
            const firstLevelItems = document.querySelectorAll(".nav > ul > .slide.has-sub > a");
            const innerLevelItems = document.querySelectorAll(".nav > ul > .slide.has-sub .slide.has-sub > a");

            $('#sidebar-scroll .main-menu a.side-menu__item').each(function() {
                var link = $(this).attr('href');
                const urlObj = new URL(link, window.location.origin);
                const path = urlObj.pathname;

                if (path === currentUrl) {
                    var $this = $(this);
                    $($this).addClass('active');

                    var parentUl = $($this).closest('.has-sub').find('.slide-menu')
                    var firstParentUl = $($this).closest('.has-sub').find('.slide-menu.child1')
                    var secondParentChildUl = $($this).closest('.has-sub').find('.slide-menu.child2')

                    if (firstParentUl.length) {
                        $(this).closest('.has-sub').addClass('open active');
                        $(this).closest('.slide-menu').addClass('active');

                        var firstLevelItemsActive = $(firstParentUl).siblings();
                        if (firstLevelItemsActive.length) {
                            $(firstLevelItemsActive).addClass('active');
                        }
                        $(firstParentUl).addClass('align-menu-list')

                    } else if (secondParentChildUl.length) {
                        $(this).closest('.has-sub').addClass('open active');
                        $(this).closest('.slide-menu').addClass('active');

                        var secondLevelItemsActive = $(secondParentChildUl).siblings();
                        if (secondLevelItemsActive.length) {
                            $(secondLevelItemsActive).addClass('active');
                        }
                        $(secondParentChildUl).addClass('align-menu-list')

                        var parentActiveLi = secondParentChildUl.parent().parent().parent();
                        parentActiveLi.addClass('open active')
                        var firstA = parentActiveLi.find('.side-menu__item')[0];
                        $(firstA).addClass('active')
                        firstChild = parentActiveLi.find('.slide-menu.child1')
                        $(firstChild).addClass('active align-menu-list')
                    }
                }
            });
        }

        function formatTimeAgo(createdAt) {
            const now = new Date();
            const createdTime = new Date(createdAt);
            const diffMs = now - createdTime;
            const diffSecs = Math.floor(diffMs / 1000);
            const diffMins = Math.floor(diffMs / (1000 * 60));
            if (diffSecs < 60) {
                return `${diffSecs} secs ago`;
            }
            if (diffMins < 60) {
                return `${diffMins} mins ago`;
            }
            const diffHrs = Math.floor(diffMins / 60);
            if (diffHrs < 24) {
                return `${diffHrs} hrs ago`;
            }
            return createdTime.toLocaleDateString();
        }

        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(";").shift();
            return null;
        }

        function hasSimplifiedSidebar() {
            return getCookie("show_report_sidebar") === "true";
        }

        function updateSidebarToggleUI() {
            const toggleBtn = document.getElementById("sidebar-toggle-btn");

            if (toggleBtn) {
                if (hasSimplifiedSidebar()) {
                    toggleBtn.classList.add("active");
                    toggleBtn.setAttribute(
                        "title",
                        "Currently using simplified sidebar"
                    );
                } else {
                    toggleBtn.classList.remove("active");
                    toggleBtn.setAttribute("title", "Switch to simplified sidebar");
                }
            }
        }

        $(document).ready(function() {
            // Realtime event listener
            const user = @json(auth()->user());
            const channelName = "{{ env('APPROVAL_NOTIFICATION_CREATED_CHANNEL_PRIVATE') }}" + '.' + user
                ?.id;
            const approvalEvent = '.' + "{{ env('APPROVAL_NOTIFICATION_CREATED_EVENT') }}";
            const localhostIcon = `${window.location.origin}/logo-small.png`;
            let unreadCount = 0;
            const notificationBadge = $("#notification-data");
            const notificationList = $("#header-notification-scroll");
            const notificationIconBadge = $("#notification-icon-badge");

            updateSidebarToggleUI();

            function showNotification(data) {
                if (Notification.permission !== "granted") {
                    Notification.requestPermission().then(permission => {
                        if (permission === "granted") {
                            createNotification(data);
                        } else {
                            alert("Please allow notifications to see this feature in action.");
                        }
                    });
                } else {
                    createNotification(data);
                }
            }

            function createNotification(data) {
                const notification = new Notification("You have a new notification.", {
                    body: data.title,
                    icon: localhostIcon,
                });

                notification.onclick = function() {
                    window.focus();
                };
            }

            function addNotificationToDropdown(data) {
                const notificationItem = $(`
                  <li class="dropdown-item">
                    <div class="d-flex align-items-start">
                        <div class="pe-2">
                            <span class="avatar avatar-md bg-primary-transparent avatar-rounded">
                                <i class="${data.icon} fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 d-flex align-items-center justify-content-between">
                            <div>
                                <p class="mb-0 fw-semibold">
                                    <a href="${data.link}">${data.title}</a>
                                </p>
                                <span class="text-muted fw-normal fs-12 header-notification-text">
                                    ${data.message}
                                </span>
                            </div>
                            <div>
                                <a href="javascript:void(0);" class="min-w-fit-content text-muted me-1 dropdown-item-close1" data-id="${data.id}">
                                    <i class="ti ti-x fs-16"></i>
                                </a>
                            </div>
                        </div>
                     </div>
                  </li>
                `);
                unreadCount++;
                $('#header-notification-scroll').append(notificationItem);
                notificationIconBadge.text('1').removeClass('d-none');
                notificationList.removeClass("d-none");
                notificationList.append(notificationItem)
                notificationBadge.text(`${unreadCount} unread`);
                $(".empty-item1").addClass('d-none');
            }

            window.Echo.private(channelName).listen(approvalEvent, (data) => {
                showNotification(data);
                addNotificationToDropdown(data);
            });

            setActiveSidebar();

            let debounceTimer;

            $('#search-input').on('input', function() {
                const query = $(this).val().trim();
                clearTimeout(debounceTimer);
                if (query) {
                    debounceTimer = setTimeout(function() {
                        fetchSearchResults(query);
                    }, 300);
                } else {
                    $('#search-results').empty();
                    $('#search-modal').hide();
                }
            });

            $('#notificationCounter').on('click', function() {
                const url = `{{ route('notifications.markAllAsRead') }}`;
                $.ajax({
                    url: url,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                            .attr('content'),
                    },
                    success: function(data) {
                        fetchNotifications()
                    },
                    error: function() {}
                });
            });


            function fetchSearchResults(query) {
                fetch('{{ route('search.results') }}?query=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            let resultsHtml = '';
                            data.forEach(item => {
                                resultsHtml +=
                                    `<li><a class="text-dark" href="javascript:void(0);">Invoice No: ${item.gl_updated_invoice_reference} Cover No: ${item.cover_no}, Endorsement No: ${item.endorsement_no}</a>
                                    </li>`
                            });
                            $('#search-results').html(resultsHtml);
                            $('#search-modal').show();
                        } else {
                            $('#search-results').html(
                                '<li>No results found.</li>');
                            $('#search-modal').show();
                        }
                    })
                    .catch(error => console.error('Error fetching results:', error));
            }

            $('#search-btn').click(function() {
                const query = $('#search-input').val();
                if (query) {
                    fetchSearchResults(query);
                }
            });

            $('#close-modal').click(function() {
                $('#search-modal').hide();
            });

            // Notifications
            function fetchNotifications() {
                $.ajax({
                    url: "{{ route('notifications.index') }}",
                    method: 'GET',
                    success: function(response) {
                        const {
                            count,
                            notifications
                        } = response;

                        if (count > 0) {
                            $('#notification-icon-badge')
                                .text(count > 10 ? '9+' : count)
                                .removeClass('d-none');
                            $(".empty-item1").addClass('d-none');
                        } else {
                            $('#notification-icon-badge').addClass('d-none');
                        }

                        const notificationList = $('#header-notification-scroll');
                        notificationList.empty();

                        if (notifications?.length > 0) {
                            notifications.forEach(notification => {
                                notificationList.append(`
                                  <li class="dropdown-item notif-item-${notification.id}" data-notification-id="${notification.id}">
                                        <div class="d-flex align-items-start">
                                            <div class="pe-2">
                                                <span class="avatar avatar-md bg-primary-transparent avatar-rounded">
                                                    <i class="${notification.icon} fs-18"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1 d-flex align-items-center justify-content-between">
                                                <div>
                                                    <p class="mb-0 fw-semibold">
                                                        <a href="${notification.link}">${notification.title}</a>
                                                    </p>
                                                    <span class="text-muted fw-normal fs-12 header-notification-text">
                                                        ${notification.message}
                                                    </span>
                                                    <div class="text-muted fw-normal fs-12 notification-time" data-created-at="${notification.created_at}">
                                                        ${notification.created_at}
                                                    </div>
                                                </div>
                                                <div>
                                                    <a href="javascript:void(0);"
                                                    class="min-w-fit-content text-muted me-1 dropdown-item-close1"
                                                    data-id="${notification.id}">
                                                        <i class="ti ti-x fs-16"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                `);
                            });

                            if ($('.empty-header-item1').length === 0) {
                                $('#header-notification-scroll').parent().append(`
                                   <div class="p-3 empty-header-item1 border-top">
                                        <div class="d-grid">
                                            <a href="{{ route('admin.approvals.index') }}" class="btn btn-primary">View All</a>
                                        </div>
                                    </div>
                                `);
                            }

                            var notfUl = $('#header-notification-scroll');
                            if (notfUl.children().length > 0) {
                                $('#header-notification-scroll .notification-time').each(function(e) {
                                    const createdAt = $(this).data('created-at');
                                    if (createdAt) {
                                        $(this).text(formatTimeAgo(createdAt));
                                    }
                                })

                                $('.dropdown-item-close1').on('click', function() {
                                    const notificationId = $(this).data('id');
                                    const url =
                                        `{{ route('notifications.markAsRead', ['id' => ':id']) }}`
                                        .replace(':id',
                                            notificationId);
                                    $.ajax({
                                        url: url,
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                                .attr('content'),
                                        },
                                        success: function(data) {
                                            $(`#header-notification-scroll .notif-item-${data.notificationId}`)
                                                .remove();
                                        },
                                        error: function() {}
                                    });
                                });
                            }
                        } else {
                            notificationList.append(`
                                <div class="p-5 empty-item1">
                                    <div class="text-center">
                                        <span class="avatar avatar-xl avatar-rounded bg-secondary-transparent">
                                            <i class="ri-notification-off-line fs-2"></i>
                                        </span>
                                        <h6 class="fw-semibold mt-3 fs-13">No New Notifications</h6>
                                    </div>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        console.error('Failed to fetch notifications.');
                    }
                });
            }

            // Fetch notifications on page load
            fetchNotifications();

            // Init select2
            $('.form-select, .select2').each(function() {
                $(this).select2({
                    placeholder: $(this).data('placeholder') || $(this).attr('placeholder') ||
                        'Select an option',
                    allowClear: false,
                    width: '100%'
                });

                $(this).on('change', function() {
                    let selectedValue = $(this).val();

                    if (selectedValue) {
                        $(this).removeClass('is-invalid');
                        $(this).next('span.error-message').hide();
                    }
                });
            });
            $('body').css('visibility', 'visible');

            tinymce.init({
                selector: 'textarea.tiny-editor',
                plugins: 'code table lists',
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table'
            });

            $('#logout-btn').click(function() {
                $('#logout-form').submit()
            });

            $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
                if (jqXHR.status === 419) {
                    toastr.error('Session expired. Please refresh the page.', 'Authentication Error');
                }
            });

            $(".reporting-dashboard").on("click", (e) => {
                e.preventDefault();
                $("#authenticationForm")[0].reset();
                $("#authenticationModal").modal("toggle"),
                    setTimeout(() => {
                        document.getElementById("accessCode").focus();
                    }, 500);
            });

            function showAuthModal() {
                $('#authenticationModal').modal('show');
            }

            $("#authenticationForm").validate({
                errorClass: "errorClass",
                rules: {
                    "accessCode": {
                        required: true,
                        // minlength: 12
                    }
                },
                messages: {
                    "accessCode": {
                        required: "Please enter your password",
                        // minlength: "Your password must be at least 6 characters long"
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form, e) {
                    e.preventDefault();
                    const submitBtn = $("#submitAuthentication");
                    const originalText = submitBtn.html();
                    submitBtn.html(
                        '<span class="me-2">Authenticating   ...</span><div class="loading"></div>'
                    );
                    submitBtn.prop('disabled', true);
                    const accessCode = $('#accessCode').val();
                    $.ajax({
                        url: "{!! route('security.authenticate_access_code') !!}",
                        type: 'POST',
                        data: {
                            accessCode: accessCode,
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $('#authenticationModal').modal('hide');
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                Swal.fire({
                                    title: 'Authentication Failed',
                                    text: response.message ||
                                        "Access Denied. Provided access code/password is incorrect.",
                                    icon: 'error',
                                    confirmButtonText: 'Try Again'
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred during authentication';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: 'Error',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        },
                        complete: function() {
                            submitBtn.html(originalText);
                            submitBtn.prop('disabled', false);
                            $("#authenticationForm")[0].reset();
                        }
                    });
                }
            });

            $('.show-auth-modal').on('click', function(e) {
                e.preventDefault();
                showAuthModal();
            });

            // Auto-show the modal when needed
            if (getUrlParameter('requireAuth') === 'true') {
                showAuthModal();
            }

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            $(".reset-sidebar").on("click", function(e) {
                e.preventDefault();

                fetch('{{ route('reset.sidebar') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error occurred:', error);
                    })
                // .finally(() => {
                //     window.location.reload();
                // })
            });
        });

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error(@json($error), '', {
                    progressBar: false,
                    timeOut: false,
                    closeButton: true,
                    newestOnTop: true,
                    // positionClass: 'toast-top-center'
                });
            @endforeach
        @endif
    </script>

    @stack('script')
</body>

</html>
