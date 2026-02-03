@extends('layouts.app', [
    'pageTitle' => 'Company Profile - ' . $company->company_name,
])

@section('styles')
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #2980b9;
            --light-gray: #f5f7fa;
            --border-color: #e0e6ed;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
            --success: #27ae60;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f9fafb;
            color: var(--text-dark);
        }

        .container {
            /* max-width: 1200px; */
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
        }

        header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb {
            background-color: white;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .breadcrumb-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .breadcrumb-links a {
            color: var(--text-light);
            text-decoration: none;
        }

        .breadcrumb-links span {
            color: var(--text-light);
        }

        .breadcrumb-links .current {
            color: var(--text-dark);
            font-weight: 500;
        }

        main {
            padding: 2rem 0;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .card-content {
            padding: 1.5rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .info-group {
            margin-bottom: 1.5rem;
        }

        .info-label {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .info-value {
            font-size: 1rem;
            color: var(--text-dark);
            padding: 0.75rem;
            background-color: var(--light-gray);
            border-radius: 6px;
            border: 1px solid var(--border-color);
        }

        .company-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            height: 100%;
            min-height: 200px;
        }

        .logo-placeholder {
            width: 120px;
            height: 120px;
            background-color: var(--light-gray);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: var(--text-light);
            font-size: 2rem;
        }

        .upload-btn {
            background-color: transparent;
            color: var(--secondary-color);
            border: 1px solid var(--secondary-color);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .upload-btn:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .status-badge {
            background-color: var(--success);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        footer {
            background-color: white;
            padding: 1.5rem 0;
            border-top: 1px solid var(--border-color);
            color: var(--text-light);
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <section class="breadcrumb">
        <div class="container breadcrumb-links">
            <a href="{{ route('dashboard.index') }}">Dashboard</a>
            <span>›</span>
            <span class="current">Company Details</span>
        </div>
    </section>

    <main class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Company Information</h2>
                <div class="status-badge">Active</div>
            </div>
            <div class="card-content">
                <div class="info-grid">
                    <div>
                        <div class="info-group">
                            <div class="info-label">Company Name</div>
                            <div class="info-value">
                                {{ $company->company_name }}
                            </div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Company Code</div>
                            <div class="info-value">
                                {{ $company->company_id }}
                            </div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Address</div>
                            <div class="info-value">
                                {{ $company->postal_address }}, {{ $company->postal_code }}, {{ $company->postal_city }},
                                {{ $country->country_name }}

                            </div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Telephone No</div>
                            <div class="info-value">{{ $company->mobilephone }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="info-group">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $company->email }}</div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Fax No</div>
                            <div class="info-value">{{ $company->mobilephone }}</div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Country Code</div>
                            <div class="info-value">{{ $company->country_code }}</div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Country</div>
                            <div class="info-value">{{ $country->country_name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Company Logo</h2>
                {{-- <button class="btn btn-primary">Edit Details</button> --}}
            </div>
            <div class="card-content">
                <div class="company-logo">
                    <img src="{{ asset('/assets/images/brand-logos/main-horizontal-logo.png') }}" alt=""
                        class="desktop-logo" />
                    {{-- <div class="logo-placeholder">A</div> --}}
                    {{-- <button class="upload-btn">Upload Logo</button> --}}
                </div>
            </div>
        </div>
    </main>
    <!-- Logo Upload Modal -->
    <div class="modal fade" id="uploadLogoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Company Logo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="logoUploadForm">
                        @csrf
                        <input type="file" class="form-control" id="logoInput" accept="image/*">
                        <div class="mt-3 text-muted">
                            Recommended: Square image, max 2MB
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmUploadBtn">Upload</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script></script>
@endpush
