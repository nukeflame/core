@extends('layouts.app')

@push('styles')
    <style>
        :root {
            --dark-color: #1f2937;
            --light-bg: #f9fafb;
            --border-color: #e5e7eb;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;

            --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);

            --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 300ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 500ms cubic-bezier(0.4, 0, 0.2, 1);

            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-full: 9999px;

            --success-color: #10b981;
            --success-light: #34d399;
            --warning-color: #f59e0b;
            --warning-light: #fbbf24;
            --danger-color: #ef4444;
            --danger-light: #f87171;
            --info-color: #3b82f6;
            --info-light: #60a5fa;

            --spacing-xs: 0.25rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-2xl: 3rem;
        }

        .page-header-modern {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            box-shadow: var(--shadow-md);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .page-header-modern::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .page-header-modern h1 {
            color: white;
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .page-header-modern .breadcrumb {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-sm);
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }

        .page-header-modern .breadcrumb-item,
        .page-header-modern .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: var(--transition-fast);
        }

        .page-header-modern .breadcrumb-item a:hover {
            color: white;
            text-decoration: underline;
        }

        .page-header-modern .breadcrumb-item.active {
            color: white;
            font-weight: 500;
        }

        .page-header-modern .breadcrumb-item+.breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.6);
        }

        .customer-info-card {
            background: white;
            overflow: hidden;
            transition: var(--transition-base);
            margin: 1rem 0px;
            box-shadow: var(--shadow-lg);
        }

        .customer-info-card:hover {
            box-shadow: var(--shadow-lg);
        }

        .customer-info-header {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            padding: 1.25rem var(--spacing-md);
            border-bottom: 2px solid var(--primary-color);
            border-radius: 50px;
        }

        .customer-info-header h5 {
            color: var(--text-primary);
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .customer-info-body {
            padding: var(--spacing-lg);
        }

        .info-row {
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-lg);
            padding: var(--spacing-md) 0;
            border-bottom: 1px solid var(--border-color);
        }

        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-row:first-child {
            padding-top: 0;
        }

        .info-group {
            flex: 1;
            min-width: 250px;
        }

        .info-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: var(--spacing-xs);
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
        }

        .info-label i {
            font-size: 1rem;
            color: var(--primary-color);
        }

        .info-value {
            font-size: 1rem;
            color: var(--text-primary);
            font-weight: 500;
            line-height: 1.5;
            word-break: break-word;
        }

        .action-buttons-container {
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-md);
            padding: var(--spacing-lg);
        }

        .action-btn {
            flex: 1;
            min-width: 200px;
            padding: 0.875rem var(--spacing-lg);
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 0.9375rem;
            border: none;
            cursor: pointer;
            transition: var(--transition-base);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.625rem;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .action-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .action-btn:active {
            transform: translateY(1px);
        }

        .action-btn:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        .action-btn i {
            font-size: 1.125rem;
            z-index: 1;
        }

        .action-btn span {
            z-index: 1;
        }

        .btn-new-cover {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            color: white;
        }

        .btn-new-cover:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            filter: brightness(1.05);
        }

        .btn-claim-notification {
            background: linear-gradient(135deg, var(--warning-color) 0%, #dc2626 100%);
            color: white;
        }

        .btn-claim-notification:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            filter: brightness(1.05);
        }

        .nav-tabs-modern {
            border-bottom: 2px solid var(--border-color);
            background: white;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            padding: var(--spacing-md) var(--spacing-md) 0;
            margin-bottom: 0;
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .nav-tabs-modern::-webkit-scrollbar {
            height: 4px;
        }

        .nav-tabs-modern::-webkit-scrollbar-track {
            background: var(--light-bg);
        }

        .nav-tabs-modern::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: var(--radius-full);
        }

        .nav-tabs-modern .nav-link {
            border: none;
            border-radius: var(--radius-md) var(--radius-md) 0 0;
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0.875rem var(--spacing-lg);
            margin-right: var(--spacing-sm);
            transition: var(--transition-base);
            position: relative;
            white-space: nowrap;
            min-width: fit-content;
        }

        .nav-tabs-modern .nav-link:hover {
            color: var(--primary-color);
            background: var(--light-bg);
        }

        .nav-tabs-modern .nav-link.active {
            color: var(--primary-color);
            background: white;
            font-weight: 600;
        }

        .nav-tabs-modern .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px 3px 0 0;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                width: 0;
                left: 50%;
                right: 50%;
            }

            to {
                width: 100%;
                left: 0;
                right: 0;
            }
        }

        .nav-tabs-modern .nav-link i {
            margin-right: var(--spacing-sm);
            font-size: 1.125rem;
            vertical-align: middle;
        }

        .tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: var(--radius-full);
            background: var(--border-color);
            color: var(--text-secondary);
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: var(--spacing-xs);
        }

        .nav-link.active .tab-badge {
            background: var(--primary-color);
            color: white;
        }

        .tab-content-modern {
            background: white;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            border-top: none;
        }

        .tab-pane-modern {
            padding: var(--spacing-lg);
        }

        .datatable-container {
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .table-modern {
            margin-bottom: 0;
            font-size: 0.9375rem;
            width: 100% !important;
        }

        .table-modern thead {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        }

        .table-modern thead th {
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8125rem;
            letter-spacing: 0.05em;
            padding: 1rem 0.75rem;
            border: none;
            white-space: nowrap;
            vertical-align: middle;
        }

        .table-modern thead th:first-child {
            padding-left: var(--spacing-md);
        }

        .table-modern thead th:last-child {
            padding-right: var(--spacing-md);
        }

        .table-modern tbody tr {
            cursor: pointer;
            transition: var(--transition-fast);
            border-bottom: 1px solid var(--border-color);
        }

        .table-modern tbody tr:last-child {
            border-bottom: none;
        }

        .table-modern tbody tr:hover {
            background: linear-gradient(90deg,
                    rgba(79, 70, 229, 0.06) 0%,
                    rgba(79, 70, 229, 0.02) 100%);
            transform: scale(1.002);
        }

        .table-modern tbody tr:hover td:last-child {
            transform: none;
        }

        .table-modern tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            color: var(--text-primary);
        }

        .table-modern tbody td:first-child {
            padding-left: var(--spacing-md);
            font-weight: 600;
        }

        .table-modern tbody td:last-child {
            padding-right: var(--spacing-md);
        }

        .table-modern .text-primary {
            color: var(--primary-color) !important;
            font-weight: 600;
        }

        .table-modern .text-danger {
            color: var(--danger-color) !important;
            font-weight: 600;
        }

        .table-action-btn {
            padding: 2px 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: var(--transition-fast);
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            white-space: nowrap;
        }

        .table-action-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .table-action-btn:active {
            transform: translateY(0);
        }

        .table-action-btn i {
            font-size: 1rem;
        }

        .btn-view {
            background: linear-gradient(135deg, var(--info-color) 0%, #2563eb 100%);
            color: white;
        }

        .btn-view:hover {
            filter: brightness(1.1);
        }

        .btn-edit {
            background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
            color: white;
        }

        .btn-edit:hover {
            filter: brightness(1.1);
        }

        .dataTables_processing {
            background: white;
            border-radius: var(--radius-md);
            padding: var(--spacing-lg);
            box-shadow: var(--shadow-lg);
        }

        .spinner-border {
            width: 2rem;
            height: 2rem;
            border-width: 0.2em;
        }

        @media (max-width: 1024px) {
            .page-header-modern h1 {
                font-size: 1.5rem;
            }

            .info-group {
                min-width: 200px;
            }
        }

        @media (max-width: 768px) {
            .page-header-modern {
                padding: var(--spacing-md);
            }

            .page-header-modern h1 {
                font-size: 1.25rem;
            }

            .action-buttons-container {
                padding: var(--spacing-md);
            }

            .action-btn {
                min-width: 100%;
                flex: 1 1 100%;
            }

            .info-group {
                min-width: 100%;
                flex: 1 1 100%;
            }

            .nav-tabs-modern {
                padding: var(--spacing-sm) var(--spacing-sm) 0;
            }

            .nav-tabs-modern .nav-link {
                padding: 0.75rem var(--spacing-md);
                font-size: 0.875rem;
            }

            .tab-pane-modern {
                padding: var(--spacing-md);
            }

            .table-modern {
                font-size: 0.875rem;
            }

            .table-modern thead th,
            .table-modern tbody td {
                padding: 0.75rem 0.5rem;
            }

            .table-modern thead th:first-child,
            .table-modern tbody td:first-child {
                padding-left: var(--spacing-sm);
            }

            .table-modern thead th:last-child,
            .table-modern tbody td:last-child {
                padding-right: var(--spacing-sm);
            }

            .table-action-btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.8125rem;
            }
        }

        @media (max-width: 480px) {
            .page-header-modern h1 {
                font-size: 1.125rem;
            }

            .customer-info-header h5 {
                font-size: 1rem;
            }

            .info-label {
                font-size: 0.75rem;
            }

            .info-value {
                font-size: 0.9375rem;
            }

            .action-btn {
                padding: 0.75rem var(--spacing-md);
                font-size: 0.875rem;
            }

            .empty-state {
                padding: 2rem var(--spacing-sm);
            }

            .empty-state i {
                font-size: 3rem;
            }
        }

        *:focus-visible {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            :root {
                --border-color: #000;
                --text-secondary: #000;
            }
        }

        @media print {

            .page-header-modern,
            .action-buttons-container,
            .nav-tabs-modern,
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_paginate,
            .table-action-btn {
                display: none !important;
            }

            .customer-info-card,
            .tab-content-modern {
                box-shadow: none;
                border: 1px solid #000;
            }

            .table-modern tbody tr {
                page-break-inside: avoid;
            }
        }

        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        @keyframes shimmer {
            0% {
                background-position: -468px 0;
            }

            100% {
                background-position: 468px 0;
            }
        }

        .loading-shimmer {
            animation: shimmer 1.2s ease-in-out infinite;
            background: linear-gradient(to right,
                    #f6f7f8 0%,
                    #edeef1 20%,
                    #f6f7f8 40%,
                    #f6f7f8 100%);
            background-size: 800px 104px;
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Customer Management</h1>
            <p class="text-muted mb-0 mt-1">Manage and view customer</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i
                                class="bx bx-home-alt me-1"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Customers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">List</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="p-2 card">
        <div class="row row-cols-12 p-2">
            <button class="process_cover btn btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"
                id="processCover">
                <span></span>New Cover</button>
            <button class="process_cover btn btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"
                id="processClaim">
                <span></span>Claim Intimation / Notification</button>
        </div>
        <div class="row row-cols-12 mx-0">
            <div class="ml-0 border col">
                <div class="customer-info-card">
                    <div class="customer-info-header">
                        <h5>
                            <i class="bx bx-info-circle"></i>
                            Customer Information
                        </h5>
                    </div>
                    <div class="customer-info-body">
                        <div class="info-row">
                            <div class="info-group">
                                <div class="info-label">
                                    <i class="bx bx-building"></i>
                                    Company Name
                                </div>
                                <div class="info-value">
                                    {{ Str::title(strtolower($customer->name ?? 'N/A')) }}
                                </div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">
                                    <i class="bx bx-envelope"></i>
                                    Email Address
                                </div>
                                <div class="info-value">
                                    {{ $customer->email ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-group">
                                <div class="info-label">
                                    <i class="bx bx-mail-send"></i>
                                    Postal Address
                                </div>
                                <div class="info-value">
                                    @php
                                        $postalParts = array_filter([
                                            $customer->postal_address ?? null,
                                            $customer->postal_town ?? null,
                                            $customer->city ?? null,
                                        ]);
                                    @endphp
                                    {{ !empty($postalParts) ? implode(', ', $postalParts) : 'N/A' }}
                                </div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">
                                    <i class="bx bx-map"></i>
                                    Physical Address
                                </div>
                                <div class="info-value">
                                    @php
                                        $physicalParts = array_filter([
                                            $customer->city ?? null,
                                            $country->country_name ?? null,
                                        ]);
                                    @endphp
                                    {{ !empty($physicalParts) ? implode(', ', $physicalParts) : 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('claim.notification.form') }}" method="POST" id="new_claim_form">
        @csrf
        <input type="hidden" name="customer_id" value="{{ $customer->customer_id ?? '' }}">
    </form>

    {{ html()->form('POST', '/cover/cover-form')->id('new_cover_form')->open() }}
    <input type="text" name="customer_id" id="customer_id" value="{{ $customer->customer_id }} " hidden>
    <input type="text" name="trans_type" id="trans_type" hidden>
    {{ csrf_field() }}
    {{ html()->form()->close() }}

    <div class="row-cols-12 mx-0">
        <div class="card mb-2 custom-card border col">
            <div class="card-body pt-0">
                <nav>
                    <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card"
                        id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-coverlist-tab" data-bs-toggle="tab"
                            data-bs-target="#coverlist-tab" type="button" role="tab" aria-selected="false"
                            tabindex="-1"><i class="bx bx-file me-1 align-middle"></i>Cover List</button>
                        <button class="nav-link" id="nav-claimlist-tab" data-bs-toggle="tab" data-bs-target="#claimlist-tab"
                            type="button" role="tab" aria-selected="false" tabindex="-1"><i
                                class="bx bx-medal me-1 align-middle"></i>Claim List</button>
                    </div>
                </nav>
                <div class="tab-content reinsurers-tabpane-card" id="tab-style-4">
                    <div class="tab-pane active show" id="coverlist-tab" role="tabpanel" aria-labelledby="nav-coverlist-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                {{ html()->form('POST', '/cover/endorsements_list')->id('form_cover_datatable')->open() }}
                                <input type="text" name="cover_no" id="cov_cover_no" hidden>
                                <input type="text" name="customer_id" id="customer_id"
                                    value="{{ $customer->customer_id }} " hidden>
                                <table id="coverlist-table"
                                    class="table table-striped text-nowrap table-hover table-responsive"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">Cover No.</th>
                                            <th scope="col">Cover Type</th>
                                            <th scope="col">Class Description</th>
                                            <th scope="col">Expiry</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                {{ csrf_field() }}
                                {{ html()->form()->close() }}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="claimlist-tab" role="tabpanel" aria-labelledby="nav-claimlist-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                {{ html()->form('POST', '/claim/claim.detail')->id('form_claim_datatable')->open() }}
                                <input type="text" name="claim_no" id="clm_claim_no" hidden>
                                <input type="text" name="customer_id" id="customer_id"
                                    value="{{ $customer->customer_id }} " hidden>
                                <table id="claimlist-table"
                                    class="table table-striped text-nowrap table-hover table-responsive"
                                    style="width: 100%!important;">
                                    <thead>
                                        <tr>
                                            <th scope="col">Claim No.</th>
                                            <th scope="col">Cover No.</th>
                                            <th scope="col">Endorsement No.</th>
                                            <th scope="col">Bus Type</th>
                                            <th scope="col">Class</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>

                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                {{ csrf_field() }}
                                {{ html()->form()->close() }}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="statement-tab" role="tabpanel" aria-labelledby="nav-statement-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                {{ html()->form('POST', '/cover/statement')->id('form_statement_datatable')->open() }}
                                <input type="text" name="cover_no" id="st_cover_no" hidden>
                                <input type="text" name="customer_id" id="customer_id"
                                    value="{{ $customer->customer_id }} " hidden>
                                <table id="statement-table"
                                    class="table table-striped text-nowrap table-hover table-responsive"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">Doc Type</th>
                                            <th scope="col">Cover No.</th>
                                            <th scope="col">Endorsement No.</th>
                                            <th scope="col">Reference</th>
                                            <th scope="col">Entry Type</th>
                                            <th scope="col">Net Amount</th>
                                            <th scope="col">Date Created</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                {{ csrf_field() }}
                                {{ html()->form()->close() }}
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
        $(document).ready(function() {
            const customerId = "{{ $customer->customer_id ?? '' }}";

            if (!customerId) {
                toastr.error('Customer ID is missing. Please refresh the page.');
                return;
            }

            const dataTableConfig = {
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                lengthChange: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    processing: '<div class="d-flex justify-content-center align-items-center p-3">' +
                        '<div class="spinner-border text-primary" role="status">' +
                        '<span class="visually-hidden">Loading...</span>' +
                        '</div></div>',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '(filtered from _MAX_ total entries)',
                    search: 'Search:',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                },
                drawCallback: function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            };

            const coverTable = $('#coverlist-table').DataTable({
                ...dataTableConfig,
                order: [
                    [4, 'desc']
                ],
                ajax: {
                    url: '{{ route('cover.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.customer_id = customerId;
                    },
                    error: function(xhr, error, code) {
                        toastr.error('Failed to load cover data. Please refresh the page.');
                    }
                },
                columns: [{
                        data: 'cover_no',
                        name: 'cover_no',
                        render: function(data, type, row) {
                            return data ?
                                `<strong class="text-primary">${escapeHtml(data)}</strong>` :
                                '<span class="text-muted">N/A</span>';
                        }
                    },
                    {
                        data: 'cover_type',
                        name: 'cover_type',
                        render: function(data, type, row) {
                            return data ? escapeHtml(data) :
                                '<span class="text-muted">N/A</span>';
                        }
                    },
                    {
                        data: 'class_desc',
                        name: 'class_desc',
                        render: function(data, type, row) {
                            return data ? escapeHtml(data) :
                                '<span class="text-muted">N/A</span>';
                        }
                    },
                    {
                        data: 'cover_to',
                        name: 'cover_to',
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return data ? formatDate(data) :
                                    '<span class="text-muted">N/A</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return data ? formatDate(data) :
                                    '<span class="text-muted">N/A</span>';
                            }
                            return data;
                        }
                    },

                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        render: function(data, type, row) {
                            if (!data) {
                                return type === 'display' ?
                                    '<span class="text-muted">N/A</span>' :
                                    data;
                            }

                            const statusInfo = getStatusInfo(data);
                            if (type === 'display') {
                                return `
                                    <span class="status-badge ${statusInfo.class}">
                                        ${data}
                                    </span>
                                `;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!row.cover_no) return '';

                            if (type === 'display') {
                                return `
                                    <button class="table-action-btn btn-view view-cover"
                                        data-cover="${row.cover_no}"
                                        data-bs-toggle="tooltip"
                                        title="View cover details"
                                        aria-label="View cover ${row.cover_no}">
                                        <i class="bx bx-show"></i> View
                                    </button>
                                `;
                            }

                            return data;
                        }
                    }

                ]
            });

            const claimTable = $('#claimlist-table').DataTable({
                ...dataTableConfig,
                order: [
                    [5, 'desc']
                ],
                ajax: {
                    url: '{{ route('claim.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.customer_id = customerId;
                    },
                    error: function(xhr, error, code) {
                        toastr.error('Failed to load claim data. Please refresh the page.');
                    }
                },
                columns: [{
                        data: 'claim_no',
                        name: 'claim_no',
                        render: function(data, type, row) {
                            return data ?
                                `<strong class="text-danger">${escapeHtml(data)}</strong>` :
                                '<span class="text-muted">N/A</span>';
                        }
                    },
                    {
                        data: 'cover_no',
                        name: 'cover_no',
                        render: function(data, type, row) {
                            return data ? escapeHtml(data) :
                                '<span class="text-muted">N/A</span>';
                        }
                    },
                    {
                        data: 'endorsement_no',
                        name: 'endorsement_no',
                        render: function(data, type, row) {
                            return data ? escapeHtml(data) :
                                '<span class="text-muted">N/A</span>';
                        }
                    },
                    {
                        data: 'type_of_bus',
                        name: 'type_of_bus',
                        render: function(data, type, row) {
                            return data ? escapeHtml(data) :
                                '<span class="text-muted">N/A</span>';
                        }
                    },
                    {
                        data: 'class_desc',
                        name: 'class_desc',
                        render: function(data, type, row) {
                            return data ? escapeHtml(data) :
                                '<span class="text-muted">N/A</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return data ? formatDate(data) :
                                    '<span class="text-muted">N/A</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        render: function(data, type, row) {
                            if (!data) {
                                return type === 'display' ?
                                    '<span class="text-muted">N/A</span>' :
                                    data;
                            }

                            const statusInfo = getStatusInfo(data);
                            if (type === 'display') {
                                return `
                                    <span class="status-badge ${statusInfo.class}">
                                        ${data}
                                    </span>
                                `;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!row.claim_no) return '';

                            return `<button class="table-action-btn btn-view view-claim"
                                        data-claim="${escapeHtml(row.claim_no)}"
                                        data-bs-toggle="tooltip"
                                        title="View claim details"
                                        aria-label="View claim ${escapeHtml(row.claim_no)}">
                                    <i class="bx bx-show"></i>View
                                </button>`;
                        }
                    }
                ]
            });

            $('#coverlist-table').on('click', 'tbody tr', function(e) {
                if ($(e.target).closest('.table-action-btn').length) {
                    return;
                }

                const rowData = coverTable.row(this).data();
                if (rowData && rowData.cover_no) {
                    navigateToCover(rowData.cover_no);
                }
            });

            $('#coverlist-table').on('click', '.view-cover', function(e) {
                e.stopPropagation();
                const coverNo = $(this).data('cover');
                if (coverNo) {
                    navigateToCover(coverNo);
                }
            });

            $('#claimlist-table').on('click', 'tbody tr', function(e) {
                if ($(e.target).closest('.table-action-btn').length) {
                    return;
                }

                const rowData = claimTable.row(this).data();
                if (rowData && rowData.claim_no) {
                    navigateToClaim(rowData.claim_no);
                }
            });

            $('#claimlist-table').on('click', '.view-claim', function(e) {
                e.stopPropagation();
                const claimNo = $(this).data('claim');
                if (claimNo) {
                    navigateToClaim(claimNo);
                }
            });

            $('#processCover').on('click', function(e) {
                e.preventDefault();
                $("#trans_type").val('NEW');
                $("#new_cover_form").submit();
            });


            $('#processClaim').on('click', function(e) {
                e.preventDefault();
                $("#new_claim_form").submit();
            });

            function navigateToCover(coverNo) {
                if (!coverNo) {
                    toastr.warning('Invalid cover number');
                    return;
                }

                try {
                    $("#cov_cover_no").val(coverNo);
                    $("#form_cover_datatable").submit();
                } catch (error) {
                    console.error('Error navigating to cover:', error);
                    toastr.error('Failed to navigate. Please try again.');
                }
            }

            function navigateToClaim(claimNo) {
                if (!claimNo) {
                    toastr.warning('Invalid claim number');
                    return;
                }

                try {
                    $("#clm_claim_no").val(claimNo);
                    $("#form_claim_datatable").submit();
                } catch (error) {
                    console.error('Error navigating to claim:', error);
                    toastr.error('Failed to navigate. Please try again.');
                }
            }

            window.formatDate = function(dateString) {
                if (!dateString) return 'N/A';

                try {
                    const date = new Date(dateString);
                    if (isNaN(date.getTime())) return 'Invalid Date';

                    const options = {
                        month: "short",
                        day: "2-digit",
                        year: "numeric"
                    };
                    return date.toLocaleDateString("en-US", options);
                } catch (error) {
                    console.error('Date formatting error:', error);
                    return dateString;
                }
            };

            function getStatusInfo(status) {
                if (!status) {
                    return {
                        class: 'status-pending',
                        icon: 'bx-time-five'
                    };
                }

                const statusLower = status.toLowerCase();

                if (statusLower.includes('active') || statusLower.includes('approved')) {
                    return {
                        class: 'status-active',
                        icon: 'bx-check-circle'
                    };
                } else if (statusLower.includes('pending')) {
                    return {
                        class: 'status-pending',
                        icon: 'bx-time-five'
                    };
                } else if (statusLower.includes('processing')) {
                    return {
                        class: 'status-processing',
                        icon: 'bx-loader-circle'
                    };
                } else if (statusLower.includes('expired') || statusLower.includes('rejected')) {
                    return {
                        class: 'status-expired',
                        icon: 'bx-x-circle'
                    };
                }

                return {
                    class: 'status-pending',
                    icon: 'bx-time-five'
                };
            }

            function escapeHtml(text) {
                if (text === null || text === undefined) return '';

                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };

                return String(text).replace(/[&<>"']/g, function(m) {
                    return map[m];
                });
            }




        });
    </script>
@endpush
