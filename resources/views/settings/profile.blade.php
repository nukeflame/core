@extends('layouts.app', [
    'pageTitle' => 'Profile - ' . $company->company_name,
])

@section('content')
    <style>
        .profile-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
            display: block;
        }

        .profile-value {
            font-size: 1rem;
            font-weight: 500;
            color: #212529;
        }

        .profile-value.empty {
            color: #adb5bd;
            font-style: italic;
        }

        .card-title-icon {
            margin-right: 0.75rem;
            color: #0d6efd;
        }

        .progress {
            height: 8px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #0d6efd;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }
    </style>


    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Profile</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-4 col-xl-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-sm-flex align-items-top p-4 border-bottom border-block-end-dashed main-profile-cover"
                        style="background-image: none; background-color: #131313;">
                        <div>
                            <span class="avatar avatar-xxl avatar-rounded online me-3 profile-avatar">
                                <img src="{{ asset('user-avator.png') }}" alt="img" width="5rem" height="5rem"
                                    class="rounded-circle">
                            </span>
                        </div>
                        <div class="flex-fill main-profile-info">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="fw-semibold mb-1 text-fixed-white"> {{ $fullname }}</h6>
                                <button type="button" id="editProfile" class="btn btn-light btn-wave fs-15"><i
                                        class="bx bx-pencil me-1 align-middle d-inline-block"></i>Edit</button>
                            </div>
                            <p class="mb-1 text-muted text-fixed-white op-7"> -- </p>
                            {{-- <p class="fs-12 text-fixed-white mb-4 op-5">
                                <span class="me-3"><i class="ri-building-line me-1 align-middle"></i>Georgia</span>
                                <span><i class="ri-map-pin-line me-1 align-middle"></i>Washington D.C</span>
                            </p> --}}
                        </div>
                    </div>
                    <div class="p-4 border-bottom border-block-end-dashed">
                        <div class="mb-4">
                            <p class="fs-15 mb-2 fw-semibold">Professional Bio :</p>
                            <p class="fs-12 text-muted op-7 mb-0">
                                --
                            </p>
                        </div>
                    </div>
                    <div class="p-4 border-bottom border-block-end-dashed">
                        <p class="fs-15 mb-2 me-4 fw-semibold">Contact Information :</p>
                        <div class="text-muted">
                            <p class="mb-2 d-flex">
                                <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                    <i class="ri-mail-line align-middle fs-14"></i>
                                </span>
                                {{ $user?->email ?? '--' }}
                            </p>
                            <p class="mb-2 d-flex">
                                <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                    <i class="ri-phone-line align-middle fs-14"></i>
                                </span>
                                {{ $user?->phone_number ?? '--' }}
                            </p>
                            <p class="mb-0 d-flex">
                                <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                    <i class="ri-map-pin-line align-middle fs-14"></i>
                                </span>
                                {{ $user?->location ?? '--' }}
                            </p>
                        </div>
                    </div>
                    <div class="p-4 border-bottom border-block-end-dashed d-flex align-items-center">
                        <p class="fs-15 mb-2 me-4 fw-semibold">Social Networks :</p>
                        <div class="btn-list mb-0">
                            <button class="btn btn-sm btn-icon btn-secondary-light btn-wave waves-effect waves-light">
                                <i class="bx bxl-linkedin fw-semibold"></i>
                            </button>
                            <button class="btn btn-sm btn-icon btn-secondary-light btn-wave waves-effect waves-light">
                                <i class="bx bxl-twitter fw-semibold"></i>
                            </button>
                            <button class="btn btn-sm btn-icon btn-warning-light btn-wave waves-effect waves-light">
                                <i class="bx bxl-instagram fw-semibold"></i>
                            </button>
                            <button class="btn btn-sm btn-icon btn-primary-light btn-wave waves-effect waves-light">
                                <i class="bx bxl-facebook fw-semibold"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 border-bottom border-block-end-dashed">
                        <p class="fs-15 mb-2 me-4 fw-semibold">Skills :</p>
                        <div>
                            <a href="javascript:void(0);">
                                <span class="badge bg-light text-muted m-1">Underwriting</span>
                            </a>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-8 col-xl-12">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body p-0">
                            <div
                                class="p-3 border-bottom border-block-end-dashed d-flex align-items-center justify-content-between">
                                <div>
                                    <ul class="nav nav-tabs mb-0 tab-style-6 justify-content-start" id="myTab"
                                        role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab"
                                                data-bs-target="#profile-tab-pane" type="button" role="tab"
                                                aria-controls="profile-tab-pane" aria-selected="true"><i
                                                    class="bx bx-user me-1 align-middle d-inline-block"></i>Professional
                                                Details</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="change-pwd-tab" data-bs-toggle="tab"
                                                data-bs-target="#change-pwd-tab-pane" type="button" role="tab"
                                                aria-controls="change-pwd-tab-pane" aria-selected="false" tabindex="-1"><i
                                                    class="bx bx-lock me-1 align-middle d-inline-block"></i>Change
                                                Password</button>
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    {{-- <p class="fw-semibold mb-2">Profile 60% completed - <a href="#"
                                            class="text-primary fs-12">Finish now</a></p>
                                    <div class="progress progress-xs progress-animate">
                                        <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="60"
                                            aria-valuemin="0" aria-valuemax="100" style="width: 60%"></div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="p-3">
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane show active fade p-0 border-0" id="profile-tab-pane" role="tabpanel"
                                    aria-labelledby="activity-tab" tabindex="0">
                                    <div class="card custom-card">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <i class="bx bx-user fs-15 me-2"></i>
                                                Personal Information
                                            </div>
                                        </div>
                                        <div class="card-body p-3 my-3">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Full Name</label>
                                                    <div class="profile-value">{{ $user->name ?? '--' }}</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Email Address</label>
                                                    <div class="profile-value">{{ $user->email ?? '--' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Phone Number</label>
                                                    <div class="profile-value">{{ $user->phone_number ?? '--' }}</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Designation</label>
                                                    <div class="profile-value">{{ $user->designation ?? '--' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Age</label>
                                                    <div class="profile-value {{ empty($user->age) ? 'empty' : '' }}">
                                                        {{ $user->age ?? 'Not provided' }}</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Experience</label>
                                                    <div
                                                        class="profile-value {{ empty($user->experience) ? 'empty' : '' }}">
                                                        {{ $user->experience ?? 'Not provided' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card custom-card">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <i class="bx bx-phone fs-15 me-2"></i>
                                                Contact Information
                                            </div>
                                        </div>
                                        <div class="card-body p-3 my-3">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Email</label>
                                                    <div class="profile-value">{{ $user->email ?? 'nyaate@gmail.com' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Phone</label>
                                                    <div class="profile-value">{{ $user->phone ?? '+254700000001' }}</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Address</label>
                                                    <div class="profile-value {{ empty($user->address) ? 'empty' : '' }}">
                                                        {{ $user->address ?? 'Not provided' }}</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">City</label>
                                                    <div class="profile-value {{ empty($user->city) ? 'empty' : '' }}">
                                                        {{ $user->city ?? 'Not provided' }}</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Country</label>
                                                    <div class="profile-value {{ empty($user->country) ? 'empty' : '' }}">
                                                        {{ $user->country ?? 'Not provided' }}</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Postal Code</label>
                                                    <div
                                                        class="profile-value {{ empty($user->postal_code) ? 'empty' : '' }}">
                                                        {{ $user->postal_code ?? 'Not provided' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card custom-card">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <i class="bx bx-folder fs-15 me-2"></i>
                                                Professional Information
                                            </div>
                                        </div>
                                        <div class="card-body  p-3 my-3">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Designation</label>
                                                    <div class="profile-value">{{ $user?->designation ?? '--' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Department</label>
                                                    <div class="profile-value empty">
                                                        Not provided
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Experience</label>

                                                    <div class="profile-value empty">
                                                        Not provided
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Skills</label>
                                                    <div class="profile-value empty">
                                                        Not provided
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Education</label>
                                                    <div class="profile-value empty">
                                                        Not provided
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="profile-label">Certifications</label>
                                                    <div class="profile-value empty">
                                                        Not provided
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade p-0 border-0" id="change-pwd-tab-pane" role="tabpanel"
                                    aria-labelledby="change-pwd-tab" tabindex="0">
                                    <div class="card custom-card">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <i class="bx bx-lock me-2 fs-15"></i> Change Password
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <form id="passwordForm" method="POST"
                                                action="{{ route('password.update') }}">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-8 mt-3">


                                                        <div class="mb-3">
                                                            <label for="current-password" class="form-label">Current
                                                                Password</label>
                                                            <div class="input-group">
                                                                <input type="password" id="current-password"
                                                                    name="current_password" class="form-control color-blk"
                                                                    placeholder="Enter your current password">
                                                                <button class="btn btn-outline-dark toggle-password"
                                                                    type="button" data-target="current-password">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="new-password" class="form-label">New
                                                                Password</label>
                                                            <div class="input-group">
                                                                <input type="password" id="new-password" name="password"
                                                                    class="form-control color-blk"
                                                                    placeholder="Enter new password">
                                                                <button class="btn btn-outline-dark toggle-password"
                                                                    type="button" data-target="new-password">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>
                                                            </div>
                                                            <div class="form-text">Password must be at least 8 characters
                                                                long with at least one uppercase letter, one lowercase
                                                                letter, one number, and one special character.</div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="confirm-password" class="form-label">Confirm New
                                                                Password</label>
                                                            <div class="input-group">
                                                                <input type="password" id="confirm-password"
                                                                    name="password_confirmation"
                                                                    class="form-control color-blk"
                                                                    placeholder="Confirm new password">
                                                                <button class="btn btn-outline-dark toggle-password"
                                                                    type="button" data-target="confirm-password">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="text-end mt-3">
                                                            <button type="submit" form="passwordForm"
                                                                class="btn btn-dark">Update Password</button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4"></div>
        </div>
    </div>
    </div>

    <div class="modal effect-scale" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title dc-modal-title" id="profileModalLabel"><i class="bx bx-pencil me-2"></i>Edit
                        Profile</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="profileForm" action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                {{-- <h5 class="text-primary mb-3">Personal Information</h5> --}}
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-inputs" name="name" value="{{ $user->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-inputs" name="email" value="{{ $user->email }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-inputs" name="phone_number" value="{{ $user->phone_number }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Designation</label>
                                    <input type="text" class="form-inputs" name="designation" value="{{ $user->designation }}">
                                </div>
                            </div>

                            <!-- Professional Details -->
                            <div class="col-md-6">
                                {{-- <h5 class="text-primary mb-3">Professional Details</h5> --}}
                                <div class="mb-3">
                                    <label class="form-label">Age</label>
                                    <input type="number" class="form-inputs" name="age" value="{{ $user->age }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Experience (Years)</label>
                                    <input type="number" class="form-inputs" name="experience" value="{{ $user->experience }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Skills</label>
                                    <div class="card-md">
                                        <select class="form-inputs select2" name="skills[]" multiple>
                                            <option value="underwriter" {{ in_array('underwriter', $user->skills ?? []) ? 'selected' : '' }}>Underwriter</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Bio -->
                        <div class="mb-3">
                            <label class="form-label">Professional Bio</label>
                            <textarea class="form-control color-blk" name="professional_bio" rows="4">{{ $user->professional_bio }}</textarea>
                        </div>

                        <!-- Social Networks -->
                        <div class="card mt-3 border-dark">
                            {{-- <div class="card-header bg-light">
                                <h5 class="text-primary mb-0">Social Networks</h5>
                            </div> --}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Facebook URL</label>
                                        <input type="url" class="form-inputs" name="social_networks[facebook]" value="{{ $user->social_networks['facebook'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Twitter URL</label>
                                        <input type="url" class="form-inputs" name="social_networks[twitter]" value="{{ $user->social_networks['twitter'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">LinkedIn URL</label>
                                        <input type="url" class="form-inputs" name="social_networks[linkedin]" value="{{ $user->social_networks['linkedin'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Instagram URL</label>
                                        <input type="url" class="form-inputs" name="social_networks[instagram]" value="{{ $user->social_networks['instagram'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#profileForm').on('submit', function(e) {
                var name = $('input[name="name"]').val();
                var email = $('input[name="email"]').val();

                if (!name || !email) {
                    e.preventDefault();
                    // alert('Please fill in required fields');
                }
            });

            $('#editProfile').on('click', function(e) {
                $('#profileModal').modal('show');
            });

            $('#passwordForm').on('submit', function(e) {
                // e.preventDefault()
            });
        });
    </script>
@endpush
