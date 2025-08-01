@extends('layouts.app', [
    'pageTitle' => 'General Configuration - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">General configuration</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">General configuration</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <ul class="nav nav-pills justify-content-start nav-style-3 mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page"
                                href="#general-tab" aria-selected="true">General</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                href="#notification-tab" aria-selected="true">Notifications</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="general-tab" role="tabpanel">
                            <div class="row mt-4 mb-2 mx-1">
                                <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12">
                                    <div class="card custom-card border border-dark">
                                        <div class="card-header justify-content-between">
                                            <div class="card-title">
                                                <i class="bx bx-bell me-2"></i> Notifications
                                            </div>

                                        </div>
                                        <div class="card-body p-3">
                                            <div class="pt-3">
                                                <div>
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                                                        <div class="">
                                                            <p class="text-dark fs-14 m-0">Email Alerts</p>
                                                        </div>
                                                        <div class="custom-toggle-switch ms-2">
                                                            <input id="email-alerts" name="emailAlert" type="checkbox"
                                                                checked="">
                                                            <label for="email-alerts" class="label-success mb-1"></label>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                                                        <div class="">
                                                            <p class="text-dark fs-14 m-0">Browser Notifications</p>
                                                        </div>
                                                        <div class="custom-toggle-switch ms-2">
                                                            <input id="browser-notification" name="browserNotification"
                                                                type="checkbox" checked="false">
                                                            <label for="browser-notification"
                                                                class="label-success mb-1"></label>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                                                        <div class="">
                                                            <p class="text-dark fs-14 m-0">Error Notifications</p>
                                                        </div>
                                                        <div class="custom-toggle-switch ms-2">
                                                            <input id="error-notification" name="errorNotification"
                                                                type="checkbox" checked="">
                                                            <label for="error-notification"
                                                                class="label-success mb-1"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="notification-tab" role="tabpanel">
                            <div class="row mt-4 mb-2 mx-1">
                                <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12">
                                    <div class="card custom-card border border-dark">
                                        <div class="card-header justify-content-between">
                                            <div class="card-title">
                                                <i class="bx bx-bell me-2"></i> Notifications
                                            </div>

                                        </div>
                                        <div class="card-body p-3">
                                            <div class="pt-3">
                                                <div>
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                                                        <div class="">
                                                            <p class="text-dark fs-14 m-0">Email Alerts</p>
                                                        </div>
                                                        <div class="custom-toggle-switch ms-2">
                                                            <input id="email-alerts" name="emailAlert" type="checkbox"
                                                                checked="">
                                                            <label for="email-alerts" class="label-success mb-1"></label>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                                                        <div class="">
                                                            <p class="text-dark fs-14 m-0">Browser Notifications</p>
                                                        </div>
                                                        <div class="custom-toggle-switch ms-2">
                                                            <input id="browser-notification" name="browserNotification"
                                                                type="checkbox" checked="false">
                                                            <label for="browser-notification"
                                                                class="label-success mb-1"></label>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                                                        <div class="">
                                                            <p class="text-dark fs-14 m-0">Error Notifications</p>
                                                        </div>
                                                        <div class="custom-toggle-switch ms-2">
                                                            <input id="error-notification" name="errorNotification"
                                                                type="checkbox" checked="">
                                                            <label for="error-notification"
                                                                class="label-success mb-1"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <script>
            $(document).ready(function() {});
        </script>
    @endpush
