@extends('layouts.app')

@section('content')
    <style>
        @media (min-width: 992px) {
            .app-content {
                min-height: calc(100vh - 7.5rem);
            }
        }

        .page-title-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin: 0 0 1.5rem 0;
            padding: 11px;
            border: 1px solid #e2e8f0;
        }

        .stage-indicator {
            margin-bottom: 0;
        }

        .workflow-steps {
            display: flex;
            align-items: center;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .step.active {
            background: #dc2626;
            color: white;
        }

        .step.completed {
            background: #059669;
            color: white;
        }

        .step.pending {
            background: #f3f4f6;
            color: #6b7280;
        }

        .step-arrow {
            color: #9ca3af;
            font-size: 12px;
        }

        .bg-modern-primary {
            background: #3b82f6 !important;
        }

        .bg-modern-warning {
            background: #f59e0b !important;
        }

        .bg-modern-success {
            background: #10b981 !important;
        }

        .bg-modern-secondary {
            background: #8b5cf6 !important;
        }

        .bg-modern-dark {
            background: #000 !important;
        }
    </style>

    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Claims Enquiry</h1>
            <p class="text-muted mb-0 pt-1">Manage active claims after debit creation</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href>Claims Administration</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Add New Claim
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="page-title-section">
        <div class="stage-indicator">
            <div class="workflow-steps">
                <div class="step active">
                    <i class="bx bx-bell"></i>
                    <span>Notification</span>
                </div>
                <i class="bx bx-right-arrow step-arrow"></i>
                <div class="step">
                    <i class="bx bx-file"></i>
                    <span>Debit Creation</span>
                </div>
                <i class="bx bx-right-arrow step-arrow"></i>
                <div class="step">
                    <i class="bx bx-check-circle"></i>
                    <span>Claims Enquiry</span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            {{-- <button class="btn btn-sm btn-secondary">
                <i class="bx bx-download"></i>
                Export
            </button> --}}
            <button type="button" class="btn btn-md btn-dark mb-0" id="newClaimBtn">
                <i class='bx bx-plus'></i>
                Add New Claim
            </button>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-top justify-content-between">
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">Active Claims</p>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-semibold">2</span>
                                        <span class="fs-12 text-success ms-2"><i
                                                class="ti ti-trending-up me-1 d-inline-block"></i>+2 this week</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="avatar avatar-md avatar-rounded bg-modern-primary text-white fs-18">
                                        <i class="bi bi-bell fs-16"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-top justify-content-between">
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">Pending Settlement</p>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-semibold">2</span>
                                        <span class="fs-12 text-success ms-2"><i
                                                class="ti ti-alert-circle me-1 d-inline-block"></i>Requires attention</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="avatar avatar-md avatar-rounded bg-modern-warning text-white fs-18">
                                        <i class="bi bi-clock fs-16"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-top justify-content-between">
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">Reserved Claims</p>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-semibold">12</span>
                                        <span class="fs-12 text-success ms-2">Can proceed to next stage</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="avatar avatar-md avatar-rounded bg-modern-success text-white fs-18">
                                        <i class="bi bi-check fs-16"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-top justify-content-between">
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">Total Value</p>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-semibold">773</span>
                                        <span class="fs-12 text-success ms-2">Pending notifications</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="avatar avatar-md avatar-rounded bg-modern-secondary text-white fs-18">
                                        <i class="bi bi-wallet fs-16"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <ul class="nav nav-pills mb-3" role="tablist" id="approvalTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page" href="#claims-list"
                        data-type="claims-list" aria-selected="true">Claims</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#reserve-tab"
                        data-type="fac" aria-selected="false">Reserved</a>
                </li>
            </ul> --}}

            <div class="tab-content" id="approvalTabsContent">
                <div class="tab-pane show active" id="claims-list" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Claims Overview</div>
                        </div>
                        <div class="card-body py-3 px-2">
                            {!! html()->form('GET', route('claim.notification.claim_detail'))->id('form_claim_datatable')->open() !!}
                            <input type="text" name="intimation_no" id="clm_intimation_no" hidden>
                            <div class="table-responsive">
                                <table id="claimlist-table" class="table text-nowrap table-hover table-striped"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Notification No</th>
                                            <th>Cover No</th>
                                            <th>Endorsement No </th>
                                            <th>Business Type</th>
                                            <th>Line of Business</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            {{ csrf_field() }}
                            {{ html()->form()->close() }}
                        </div>
                    </div>
                </div>
                <div class="tab-pane text-muted" id="reserve-tab" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Reserved List</div>
                        </div>
                        <div class="card-body py-3 px-2">
                            {!! html()->form('GET', route('claim.notification.claim_detail'))->id('form_claim_datatable')->open() !!}
                            <input type="text" name="intimation_no" id="clm_intimation_no" hidden>
                            <div class="table-responsive">
                                <table id="reseverd-list-table" class="table text-nowrap table-hover table-striped"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Notification No</th>
                                            <th>Cover No</th>
                                            <th>Endorsement No </th>
                                            <th>Business Type</th>
                                            <th>Line of Business</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            {{ csrf_field() }}
                            {{ html()->form()->close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Choose Customer Modal -->
    <div class="modal customer-model-wrapper md-wrapper effect-scale" id="newClaimModal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="new_claim_form" action="{{ route('claim.notification.form') }}">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <div class="d-flex flex-column ced-body">
                                    <label for="title" class="form-label">Choose Customer</label>
                                    <select class="form-inputs select2" id="customer_id" name="customer_id"
                                        required></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="next-save-btn"
                            class="btn btn-dark btn-sm btn-wave waves-effect waves-light">Next</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#newClaimModal').on('shown.bs.modal', function() {
                $('.select2').select2({
                    dropdownParent: $('#newClaimModal')
                });
            });

            $('#claimlist-table').DataTable({
                order: [
                    [6, 'asc']
                ],
                pageLength: 15,
                lengthMenu: [15, 30, 50, 100],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: true,
                ajax: {
                    url: "{{ route('claim.notification.enquiry.datatable') }}",

                },
                columns: [{
                        data: 'intimation_no',
                        searchable: true
                    },
                    {
                        data: 'cover_no',
                        searchable: true
                    },
                    {
                        data: 'endorsement_no',
                        searchable: true
                    },
                    {
                        data: 'type_of_bus',
                        searchable: false
                    },
                    {
                        data: 'class_desc',
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        searchable: false
                    },
                    {
                        data: 'status',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'action',
                        sortable: false
                    },
                ]
            });

            $('#reseverd-list-table').DataTable({
                order: [
                    [6, 'asc']
                ],
                pageLength: 15,
                lengthMenu: [15, 30, 50, 100],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: true,
                ajax: {
                    url: "{{ route('claim.notification.enquiry.datatable') }}",

                },
                columns: [{
                        data: 'intimation_no',
                        searchable: true
                    },
                    {
                        data: 'cover_no',
                        searchable: true
                    },
                    {
                        data: 'endorsement_no',
                        searchable: true
                    },
                    {
                        data: 'type_of_bus',
                        searchable: false
                    },
                    {
                        data: 'class_desc',
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        searchable: false
                    },
                    {
                        data: 'status',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'action',
                        sortable: false
                    },
                ]
            });

            $(document).on('click', '#view-notf-claimstatus', function(e) {
                e.preventDefault()
                var intimation_no = $(this).data('intimation_no');
                var process_type = $(this).data('process_type');
                const claimDetailUrl = new URL("{{ route('claim.notification.claim_detail') }}", window
                    .location.origin);
                if (intimation_no !== '') {
                    try {
                        claimDetailUrl.searchParams.set('intimation_no', intimation_no);
                        if (process_type) {
                            claimDetailUrl.searchParams.set('process_type', process_type);
                        }
                        window.location.href = claimDetailUrl.toString();
                    } catch (error) {
                        console.error('URL error:', error);
                        Swal.fire('Error', 'Invalid URL parameters', 'error');
                    }
                } else {
                    Swal.fire('Error', 'No intimation number provided.', 'error');
                }
            })

            $('#newClaimBtn').click(function(e) {
                e.preventDefault();
                $('#customer_id').empty();
                $.ajax({
                    url: "{{ route('claim.notification.get-customers') }}",
                    method: 'GET',
                    success: function(data) {
                        $('#customer_id').append(
                            '<option value=""> -- Select Customer --</option>');
                        $.each(data, function(key, customer) {
                            $('#customer_id').append('<option value="' + customer
                                .customer_id + '">' + customer.name + '</option>');
                        });
                        $('#newClaimModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching customers:', error);
                    }
                });
            });

            $("#new_claim_form").validate({
                errorClass: "errorClass",
                rules: {
                    customer_id: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $('#next-save-btn').prop('disabled', true).text('Submitting...');
                    form.submit()
                    $('#next-save-btn').prop('disabled', false).text('Next');
                }
            });
        });
    </script>
@endpush
