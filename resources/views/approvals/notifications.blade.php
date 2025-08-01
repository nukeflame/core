@extends('layouts.app', [
    'pageTitle' => 'Approvals - ' . $company->company_name,
])

@section('styles')
    <style>
        .badge-purple {
            background-color: #6f42c1;
            color: white;
        }

        .badge-teal {
            background-color: #20c997;
            color: white;
        }

        .summary-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .summary-card .number {
            font-size: 28px;
            font-weight: bold;
        }

        .summary-card .label {
            font-size: 16px;
            color: #6c757d;
        }

        .pending-card {
            background-color: #fff8e1;
            border: 1px solid #ffecb3;
        }

        .pending-card .number {
            color: #ff9800;
        }

        .modal-lg {
            max-width: 900px;
        }
    </style>
@endsection

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Approvals</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Approvals</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md">
            <div class="summary-card">
                <div class="number">{{ $counts['all'] }}</div>
                <div class="label">Total</div>
            </div>
        </div>
        <div class="col-md">
            <div class="summary-card">
                <div class="number">{{ $counts['claim'] }}</div>
                <div class="label">Claims</div>
            </div>
        </div>
        <div class="col-md">
            <div class="summary-card">
                <div class="number">{{ $counts['fac'] }}</div>
                <div class="label">Facultative</div>
            </div>
        </div>
        <div class="col-md">
            <div class="summary-card">
                <div class="number">{{ $counts['treaty'] }}</div>
                <div class="label">Treaties</div>
            </div>
        </div>
        <div class="col-md">
            <div class="summary-card pending-card">
                <div class="number">{{ $counts['pending'] }}</div>
                <div class="label">Pending</div>
            </div>
        </div>
    </div>

    <form method="POST" id="form_view_review">
        <input type="hidden" name="cover_no" id="app_cover_no">
        {{ csrf_field() }}
        <input type="hidden" name="endorsement_no" id="app_endorse_no">
        <input type="hidden" name="customer_id" id="app_customer_id">
    </form>

    <div class="row">
        <div class="col-xl-12">
            <ul class="nav nav-pills mb-3" role="tablist" id="approvalTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page" href="#all-tabs"
                        data-type="all" aria-selected="true">Approvals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#fac-tab"
                        data-type="fac" aria-selected="false">Facultative</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#claims-tab"
                        data-type="claim" aria-selected="false">Claims</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#treaty-tab"
                        data-type="treaty" aria-selected="false">Treaties</a>
                </li>
            </ul>

            <div class="tab-content" id="approvalTabsContent">
                <div class="tab-pane show active" id="all-tabs" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Approval Queue</div>
                        </div>
                        <div class="card-body py-3 px-2">
                            {{ html()->form('POST', '/customer/customer-dtl')->id('form_customer_datatable')->open() }}
                            {{ csrf_field() }}
                            <input type="text" id="customer_id" name="customer_id" hidden />
                            <table class="table table-striped text-nowrap table-hover table-responsive approved-table"
                                style="width: 100%">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Client</th>
                                        <th scope="col">Comment</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Priority</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            {{ html()->form()->close() }}
                        </div>
                    </div>
                </div>
                <div class="tab-pane text-muted" id="claims-tab" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Approval Queue</div>
                        </div>
                        <div class="card-body py-3 px-2">
                            {{ html()->form('POST', '/customer/customer-dtl')->id('form_customer_datatable')->open() }}
                            {{ csrf_field() }}
                            <input type="text" id="customer_id" name="customer_id" hidden />
                            <table class="table table-striped text-nowrap table-hover table-responsive approved-table"
                                style="width: 100%">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Client</th>
                                        <th scope="col">Comment</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Priority</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            {{ html()->form()->close() }}
                        </div>
                    </div>
                </div>
                <div class="tab-pane text-muted" id="fac-tab" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Approval Queue</div>
                        </div>
                        <div class="card-body py-3 px-2">
                            {{ html()->form('POST', '/customer/customer-dtl')->id('form_customer_datatable')->open() }}
                            {{ csrf_field() }}
                            <input type="text" id="customer_id" name="customer_id" hidden />
                            <table class="table table-striped text-nowrap table-hover table-responsive approved-table"
                                style="width: 100%">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Client</th>
                                        <th scope="col">Comment</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Priority</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            {{ html()->form()->close() }}
                        </div>
                    </div>
                </div>
                <div class="tab-pane text-muted" id="treaty-tab" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Approval Queue</div>
                        </div>
                        <div class="card-body py-3 px-2">
                            {{ html()->form('POST', '/customer/customer-dtl')->id('form_customer_datatable')->open() }}
                            {{ csrf_field() }}
                            <input type="text" id="customer_id" name="customer_id" hidden />
                            <table class="table table-striped text-nowrap table-hover table-responsive approved-table"
                                style="width: 100%">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Client</th>
                                        <th scope="col">Comment</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Priority</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            {{ html()->form()->close() }}
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
            var approvalTable = $('.approved-table').DataTable({
                // pageLength: 20,
                lengthChange: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('approvals.approval-data') }}",
                    data: function(d) {
                        d.type = $('.nav-link.active').data('type');
                    }
                },
                columns: [{
                        data: 'id',
                        searchable: true,
                        className: 'highlight-idx',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'title',
                        name: 'title',
                        className: 'highlight-desc-3'
                    },
                    {
                        data: 'client',
                        name: 'client',
                        className: 'highlight-view-point'
                    },
                    {
                        data: 'comment',
                        name: 'comment',
                        orderable: false,
                        className: 'highlight-description'
                    },
                    {
                        data: 'type_badge',
                        name: 'type',
                        orderable: false,
                        className: 'highlight-min-view'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'highlight-min-view'
                    },
                    {
                        data: 'priority_badge',
                        name: 'priority',
                        className: 'highlight-min-view'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        sortable: false,
                        className: 'highlight-desc-3 highlight-overflow'
                    }
                ]
            });

            $('#approvalTabs a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
                approvalTable.ajax.reload();
            });

            $('.approved-table').on('click', '.view-details', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#loader').show();
                $('#content-container').hide();
                $.ajax({
                    url: `{{ route('notifications.show', ['id' => ':id']) }}`.replace(':id',
                        id),
                    method: 'GET',
                    success: function(response) {
                        $('#loader').hide();
                        $('#content-container').show();
                        if (response) {
                            const data = response.data
                            const cover = JSON.parse(data.data);
                            $('#content-container').html(`
                                <div class="p-2 card">
                                    <div class="row row-cols-12 mx-0">
                                        <div class="col-md-6 h-10d0">
                                            <div class="card-body p-3">
                                                <a href="javascript:void(0);">
                                                    <div class="d-flex align-items-top mt-0 flex-wrap">
                                                        <div class="lh-1">
                                                            <span class="avatar avatar-md online me-3 avatar-rounded">
                                                                <img alt="avatar" src="${data.avatar || '/user-avator.png'}">
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <div class="d-flex align-items-center">
                                                                <div class="mt-sm-0 mt-2">
                                                                    <p class="mb-0 fs-14 fw-semibold">${data.created_by}</p>
                                                                    <p class="mb-0 text-muted">${data.comment || ''}</p>
                                                                    <span class="mb-0 d-block text-muted fs-12">${formatDateTime(data.created_at)}</span>
                                                                </div>
                                                                <div class="ms-auto">
                                                                    <span class="float-end badge bg-light text-muted">
                                                                        ${formatDateTime(data.created_at) || ''}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="row row-cols-12 p-2">
                                                <button class="process_cover btn btn-sm btn-primary-gradient btn-wave waves-effect waves-light col-md-2 m-2" id="">
                                                    <span class="bx bx-check fs-15 pr-2"></span>Approve
                                                </button>
                                                <button class="process_cover btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2">
                                                    <span class="bx bx-x fs-15 pr-2"></span>Reject
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 border">
                                            <div class="card-body">
                                                <div class="row bg-light p-2">
                                                    <div class="col-md-3">
                                                        <strong class="color-blk">Cover No.</strong>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong class="text-secondary">${cover.cover_no || ''}</strong>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Endorsement No.</strong>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span class="text-secondary">${cover.endorsement_no || ''}</span>
                                                    </div>
                                                </div>
                                                <div class="row mb-1 p-2">
                                                    <div class="col-md-3">
                                                        <strong>Business Type</strong>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span class="text-secondary">${cover.business_type || ''}</span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>No. of Installments</strong>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span class="text-secondary">${cover.no_of_installments || ''}.</span>
                                                    </div>
                                                </div>
                                                <div class="row bg-light p-2">
                                                    <div class="col-md-3">
                                                        <strong class="color-blk">Customer</strong>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <strong class="text-secondary">${cover.customer || ''}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                        } else {
                            $('#content-container').html(
                                '<p class="text-danger">No data found for this notification.</p>'
                            );
                        }
                    },
                    error: function() {
                        $('#loader').hide();
                        $('#content-container').show();
                    }
                });
            });

            $(document).on('click', '.review-btn', function(e) {
                e.preventDefault();
                const endorsement_no = $(this).data('endorsement_no')
                const type = $(this).data('type')
                const cover_no = $(this).data("cover_no")
                const customer_id = $(this).data("customer_id")
                const intimation_no = $(this).data('intimation_no');

                let newUrl = '';
                switch (type) {
                    case 'facultative':
                        const facUrl = "{!! route('cover.CoverHome') !!}";
                        newUrl = `${facUrl}?endorsement_no=${encodeURIComponent(endorsement_no)}`;
                        break;

                    case 'claim':
                        const claimUrl = "{!! route('claim.notification.claim_detail') !!}";
                        newUrl =
                            `${claimUrl}?intimation_no=${encodeURIComponent(intimation_no)}&process_type=claim`;
                        break;
                }
                if (newUrl) {
                    $.ajax({
                        url: newUrl,
                        type: 'POST',
                        data: {
                            cover_no,
                            endorsement_no,
                            customer_id,
                            claim_no: intimation_no
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val()
                        },
                        success: function(response) {
                            if (response) {
                                window.open(newUrl, '_blank');
                            }
                        },
                        error: function(xhr, status, error) {}
                    });
                }

            });

            $(document).on('click', '.approve-btn', function(e) {
                e.preventDefault();
                const approval_id = $(this).data('id')
                swal.fire({
                    title: 'Approval Confirmation',
                    input: "textarea",
                    inputLabel: "Your Comment",
                    inputPlaceholder: "Enter your comment here",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const data = {
                            id: approval_id,
                            action: 'A',
                            comment: result.value
                        }
                        approveDeclineRequest(data)
                    }
                });
            });

            $(document).on('click', '.decline-btn', function(e) {
                e.preventDefault();
                const approval_id = $(this).data('id')
                swal.fire({
                    title: 'Decline Approval Confirmation',
                    input: "textarea",
                    inputLabel: "Your Comment",
                    inputPlaceholder: "Enter your comment here",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const data = {
                            id: approval_id,
                            action: 'R',
                            comment: result.value
                        }
                        approveDeclineRequest(data)
                    }
                });
            });

            function approveDeclineRequest(data) {
                fetchWithCsrf("{!! route('approvals.approval-action') !!}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data?.status == 201) {
                            toastr.success("Approval rejected successful", 'Successful')
                            approvalTable.ajax.reload();
                            $("#apprv-view").hide();
                        } else if (data?.status == 422) {
                            showServerSideValidationErrors(data.errors)
                        } else {
                            toastr.error("Failed to approve details")
                        }
                    })
                    .catch(error => {
                        toastr.error("An internal error occured")
                    });
            }
        });
    </script>
@endpush
