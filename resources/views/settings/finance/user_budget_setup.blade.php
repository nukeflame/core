@extends('layouts.app')

@push('styles')
    <style>
        /* ── Stats Cards ── */
        .ubs-stat-card {
            border: none;
            border-radius: 0.75rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .ubs-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .ubs-stat-card .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .ubs-stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .ubs-stat-card .stat-label {
            font-size: 0.78rem;
            color: #6c757d;
            margin-top: 2px;
        }

        /* ── DataTable ── */
        .ubs-table thead th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .ubs-table tbody td {
            vertical-align: middle;
            font-size: 0.88rem;
        }

        /* ── Modal Premium Look ── */
        .ubs-modal .modal-header {
            background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
            color: #fff;
            border-bottom: 0;
            padding: 14px 20px;
        }

        .ubs-modal .modal-header .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .ubs-modal .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        /* ── Info Alert ── */
        .ubs-modal .ubs-info-alert {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: none;
            border-radius: 0.5rem;
            padding: 10px 16px;
            font-size: 0.82rem;
            color: #1565c0;
        }

        /* ── Section Headers ── */
        .ubs-modal .ubs-section-header {
            font-weight: 700;
            font-size: 0.84rem;
            color: #37474f;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e0e0e0;
        }

        .ubs-modal .ubs-section-header i {
            margin-right: 6px;
            font-size: 1rem;
        }

        /* ── Checkbox Grid ── */
        .ubs-checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 8px;
        }

        .ubs-checkbox-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            cursor: pointer;
            background: #fafafa;
        }

        .ubs-checkbox-item:hover {
            background: #e3f2fd;
            border-color: #90caf9;
        }

        .ubs-checkbox-item input[type="checkbox"] {
            margin-right: 8px;
            width: 16px;
            height: 16px;
            accent-color: #1976d2;
        }

        .ubs-checkbox-item input[type="checkbox"]:checked+label {
            color: #1565c0;
            font-weight: 600;
        }

        .ubs-checkbox-item label {
            cursor: pointer;
            font-size: 0.84rem;
            margin-bottom: 0;
            user-select: none;
        }

        /* ── Input Fields ── */
        .ubs-modal .form-control,
        .ubs-modal .form-select {
            border-radius: 0.5rem;
            border-color: #d0d5dd;
            font-size: 0.88rem;
        }

        .ubs-modal .form-control:focus,
        .ubs-modal .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12);
        }

        .ubs-modal .input-group-text {
            background: linear-gradient(135deg, #f5f5f5, #eeeeee);
            border-color: #d0d5dd;
            font-weight: 600;
            font-size: 0.82rem;
            color: #616161;
        }

        /* ── Modal Footer ── */
        .ubs-modal .modal-footer {
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 12px 20px;
        }

        /* ── Nav Tabs ── */
        .ubs-nav-tabs .nav-link {
            font-weight: 600;
            font-size: 0.85rem;
            color: #6c757d;
            border: none;
            padding: 10px 20px;
            border-radius: 0.5rem 0.5rem 0 0;
            transition: all 0.2s ease;
        }

        .ubs-nav-tabs .nav-link.active {
            color: #1976d2;
            background: #fff;
            border-bottom: 3px solid #1976d2;
        }

        .ubs-nav-tabs .nav-link:hover:not(.active) {
            color: #495057;
            background: #f5f5f5;
        }
    </style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">User Budget Setup Management</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Assign budget parameters, sectors, policies, and categories to users.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Settings</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('settings.budgetSetup.index') }}">Budget Setup</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User Setup</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Navigation Tabs --}}
    <ul class="nav nav-tabs ubs-nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('settings.budgetSetup.index') }}">
                <i class="bx bx-buildings me-1"></i>Company Budgets
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('settings.budgetSetup.users') }}">
                <i class="bx bx-user-check me-1"></i>User Budget Setup
            </a>
        </li>
    </ul>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card ubs-stat-card overflow-hidden">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-primary-transparent text-primary me-3">
                        <i class="bx bx-group"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-total-users">0</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card ubs-stat-card overflow-hidden">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success-transparent text-success me-3">
                        <i class="bx bx-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-configured">0</div>
                        <div class="stat-label">Configured</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card ubs-stat-card overflow-hidden">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-warning-transparent text-warning me-3">
                        <i class="bx bx-time-five"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-pending">0</div>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card ubs-stat-card overflow-hidden">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-info-transparent text-info me-3">
                        <i class="bx bx-filter-alt"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-filtered">0</div>
                        <div class="stat-label">Filtered</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Users DataTable --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Active Users</h5>
                        <small class="text-muted">Click <strong>Details</strong> to configure budget parameters for each
                            user</small>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover ubs-table" id="user-budget-table"
                            aria-label="User budget setup table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Full Name</th>
                                    <th>Department</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th style="width: 15%">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- User Budget Setup Modal --}}
    <div class="modal effect-scale md-wrapper ubs-modal" id="user_budget_modal" tabindex="-1"
        aria-labelledby="userBudgetModalLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="userBudgetModalLabel">
                        <i class="bx bx-user-circle me-2"></i>User Budget Setup
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="user_budget_form" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="ubs_user_id" name="user_id">

                        {{-- Info Alert --}}
                        <div class="ubs-info-alert mb-3">
                            <i class="bx bx-info-circle me-1"></i>
                            Select the appropriate sectors, policies, and categories for this user
                        </div>

                        {{-- Production & ROI --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bx bx-wallet me-1 text-muted"></i>Estimated Production
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">KES</span>
                                    <input type="text" class="form-control" id="ubs_est_production"
                                        name="est_production" placeholder="Enter estimated Production">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bx bx-dollar-circle me-1 text-muted"></i>Return On Investment
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">KES</span>
                                    <input type="text" class="form-control" id="ubs_roi"
                                        name="return_on_investment" placeholder="Enter ROI">
                                </div>
                            </div>
                        </div>

                        {{-- Sector --}}
                        <div class="mb-4">
                            <div class="ubs-section-header">
                                <i class="bx bx-building-house"></i>Sector
                            </div>
                            <div class="ubs-checkbox-grid" id="ubs_sectors_grid">
                                @php
                                    $sectors = ['NGO', 'Corporate', 'Government', "SME's", 'Retail'];
                                @endphp
                                @foreach ($sectors as $sector)
                                    <div class="ubs-checkbox-item">
                                        <input type="checkbox" name="sectors[]" value="{{ $sector }}"
                                            id="sector_{{ Str::slug($sector) }}">
                                        <label for="sector_{{ Str::slug($sector) }}">{{ $sector }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Policy --}}
                        <div class="mb-4">
                            <div class="ubs-section-header">
                                <i class="bx bx-shield-quarter"></i>Policy
                            </div>
                            <div class="ubs-checkbox-grid" id="ubs_policies_grid">
                                @php
                                    $policies = ['Medical', 'Life', 'Retail', 'Aviation', 'General', 'Others'];
                                @endphp
                                @foreach ($policies as $policy)
                                    <div class="ubs-checkbox-item">
                                        <input type="checkbox" name="policies[]" value="{{ $policy }}"
                                            id="policy_{{ Str::slug($policy) }}">
                                        <label for="policy_{{ Str::slug($policy) }}">{{ $policy }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Category --}}
                        <div class="mb-2">
                            <div class="ubs-section-header">
                                <i class="bx bx-category-alt"></i>Category
                            </div>
                            <div class="ubs-checkbox-grid" id="ubs_categories_grid">
                                @if ($budgetCategories->count() > 0)
                                    @foreach ($budgetCategories->unique('budget_category') as $cat)
                                        <div class="ubs-checkbox-item">
                                            <input type="checkbox" name="categories[]"
                                                value="{{ $cat->budget_category }}"
                                                id="cat_{{ Str::slug($cat->budget_category) }}">
                                            <label for="cat_{{ Str::slug($cat->budget_category) }}">
                                                {{ ucwords(str_replace('_', ' ', $cat->budget_category)) }}
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted fs-13 mb-0">
                                        <i class="bx bx-info-circle me-1"></i>
                                        No active budget categories found. Please configure in
                                        <a href="{{ route('settings.budgetSetup.index') }}">Company Budgets</a> first.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="save_user_budget">
                            <i class="fas fa-save me-1"></i> Save Setup
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {

            // ── DataTable Init ──
            const table = $('#user-budget-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                ajax: "{{ route('settings.budgetSetup.users.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'name',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'department',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'user_title',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'setup_status',
                        defaultContent: "<b class='dashes'>_</b>",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        defaultContent: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                drawCallback: function(settings) {
                    const json = settings.json || {};
                    const rows = this.api().rows({
                        page: 'current'
                    }).data().toArray();
                    const configured = rows.filter(r => (r.setup_status || '').indexOf('Configured') > -
                        1).length;
                    const totalVisible = rows.length;

                    $('#stat-total-users').text(json.recordsTotal || 0);
                    $('#stat-filtered').text(json.recordsFiltered || 0);
                    $('#stat-configured').text(configured);
                    $('#stat-pending').text(Math.max(totalVisible - configured, 0));
                }
            });

            // ── Details Button Click → Open Modal ──
            $('#user-budget-table').on('click', '#user_budget_details', function() {
                const userId = $(this).data('user-id');
                if (!userId) return;

                // Reset form
                resetModal();

                // Fetch existing setup
                $.ajax({
                    url: "{{ url('settings/budget-setup/users') }}/" + userId + "/show",
                    method: 'GET',
                    success: function(resp) {
                        if (!resp.success) {
                            if (window.toastr) toastr.error(resp.message ||
                                'Failed to load user data');
                            return;
                        }

                        const user = resp.user || {};
                        const setup = resp.setup || null;

                        // Set user info
                        $('#ubs_user_id').val(user.id);
                        $('#userBudgetModalLabel').html(
                            '<i class="bx bx-user-circle me-2"></i>User Budget Setup — ' +
                            '<span class="fw-normal opacity-75">' + (user.name || '') +
                            '</span>'
                        );

                        if (setup) {
                            // Populate fields
                            $('#ubs_est_production').val(formatNumber(setup.est_production ||
                                0));
                            $('#ubs_roi').val(formatNumber(setup.return_on_investment || 0));

                            // Check sectors
                            if (setup.sectors && Array.isArray(setup.sectors)) {
                                setup.sectors.forEach(function(s) {
                                    $('input[name="sectors[]"][value="' + s + '"]')
                                        .prop('checked', true);
                                });
                            }

                            // Check policies
                            if (setup.policies && Array.isArray(setup.policies)) {
                                setup.policies.forEach(function(p) {
                                    $('input[name="policies[]"][value="' + p + '"]')
                                        .prop('checked', true);
                                });
                            }

                            // Check categories
                            if (setup.categories && Array.isArray(setup.categories)) {
                                setup.categories.forEach(function(c) {
                                    $('input[name="categories[]"][value="' + c + '"]')
                                        .prop('checked', true);
                                });
                            }
                        }

                        $('#user_budget_modal').modal('show');
                    },
                    error: function(xhr) {
                        if (window.toastr) toastr.error('Failed to load user budget setup');
                    }
                });
            });

            // ── Form Submit ──
            $('#user_budget_form').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                const userId = $('#ubs_user_id').val();

                if (!userId) {
                    if (window.toastr) toastr.warning('No user selected');
                    return;
                }

                // Clean numeric values before sending
                const estProd = cleanNumber($('#ubs_est_production').val());
                const roi = cleanNumber($('#ubs_roi').val());

                // Build form data
                const formData = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    user_id: userId,
                    est_production: estProd,
                    return_on_investment: roi,
                    sectors: [],
                    policies: [],
                    categories: []
                };

                $('input[name="sectors[]"]:checked').each(function() {
                    formData.sectors.push($(this).val());
                });
                $('input[name="policies[]"]:checked').each(function() {
                    formData.policies.push($(this).val());
                });
                $('input[name="categories[]"]:checked').each(function() {
                    formData.categories.push($(this).val());
                });

                Swal.fire({
                    title: 'Save user budget setup?',
                    text: 'Do you want to save these budget settings?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Save',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('settings.budgetSetup.users.update') }}",
                            method: 'POST',
                            data: formData,
                            success: function(resp) {
                                if (window.toastr) toastr.success(resp.message ||
                                    'User budget setup saved');
                                $('#user_budget_modal').modal('hide');
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                    let msgs = [];
                                    $.each(xhr.responseJSON.errors, function(key, val) {
                                        msgs.push(val.join(', '));
                                    });
                                    if (window.toastr) toastr.error(msgs.join('<br>'));
                                } else if (window.toastr) {
                                    toastr.error(xhr.responseJSON?.message ||
                                        'Failed to save');
                                }
                            }
                        });
                    }
                });
            });

            // ── Number formatting ──
            $('#ubs_est_production, #ubs_roi').on('keyup change', function() {
                this.value = formatNumber(cleanNumber(this.value));
            });

            function formatNumber(val) {
                const num = parseFloat(String(val).replace(/,/g, '')) || 0;
                return num.toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
            }

            function cleanNumber(val) {
                return parseFloat(String(val).replace(/,/g, '')) || 0;
            }

            function resetModal() {
                $('#ubs_user_id').val('');
                $('#ubs_est_production').val('');
                $('#ubs_roi').val('');
                $('input[name="sectors[]"]').prop('checked', false);
                $('input[name="policies[]"]').prop('checked', false);
                $('input[name="categories[]"]').prop('checked', false);
            }
        });
    </script>
@endpush
