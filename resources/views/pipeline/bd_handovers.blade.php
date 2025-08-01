@extends('layouts.app')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">BD Handovers</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href>BD Handovers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Details
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">BD Handovers list</div>
                </div>
                <div class="card-body">
                    {{ html()->form('POST', '/cover/cover-form')->id('newCoverForm')->open() }}
                    {{ csrf_field() }}
                    <input type="hidden" name="customer_id" id="customerId">
                    <input type="hidden" name="trans_type" id="transType">
                    <input type="hidden" name="prospect_id" id="prospectId">
                    {{ html()->form()->close() }}

                    <table class="table text-nowrap table-striped table-hover" id="bd_handovers-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cedant</th>
                                <th>Insured name</th>
                                <th>Division</th>
                                <th>Business class</th>
                                <th>Currency</th>
                                <th>Sum Insured</th>
                                <th>Premium</th>
                                <th>Effective date</th>
                                <th>Closing date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal effect-fall md-wrapper" id="rejectedCommentModal" tabindex="-1"
        aria-labelledby="rejectedCommentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectedCommentModalLabel">Rejection Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="rejection_message"
                        style="min-height: 340px; border: 1px solid #eeeeeef2;padding: 10px;border-radius: 4px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            const $bdTable = $('#bd_handovers-table').DataTable({
                pageLength: 15,
                lengthMenu: [15, 30, 50, 100],
                processing: true,
                serverSide: true,
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: "{!! route('pipeline.bd_handovers_datatable') !!}",
                },
                columns: [{
                        data: 'opportunity_id',
                        searchable: false,
                        className: 'highlight-index',
                    },
                    {
                        data: 'cedant',
                        searchable: true,
                        className: 'highlight-view-point',
                    },
                    {
                        data: 'insured_name',
                        searchable: true,
                        className: 'highlight-action',
                    },
                    {
                        data: 'division_name',
                        class: 'highlight-action'
                    },
                    {
                        data: 'business_class',
                        searchable: true,
                    },
                    {
                        data: 'currency_code',
                        className: 'highlight-index',
                        sortable: false,
                    },
                    {
                        data: 'effective_sum_insured',
                        searchable: true,
                    },
                    {
                        data: 'cedant_premium',
                        searchable: true,
                    },
                    {
                        data: 'effective_date',
                        searchable: true,
                    },
                    {
                        data: 'closing_date',
                        searchable: true,
                    },
                    {
                        data: 'bd_status',
                        searchable: true,
                        sortable: false,

                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        sortable: false,
                        className: 'highlight-desc-3 highlight-overflow '
                    },
                ]
            });

            $(document).on('click', '.remove_process_customer', function(e) {
                e.preventDefault();
                const cedantId = $(this).data('cedant_id');
                const cedantName = $(this).data('name');
                Swal.fire({
                    title: 'WARNING: Clear All Covers',
                    text: `You are about to permanently delete all insurance covers and related data for ${cedantName}.\n\nThis action cannot be undone.\n\nPlease confirm to proceed.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete everything',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    const data = {
                        id: cedantId
                    }
                    if (result.isDismissed) {
                        return false;
                    }
                    fetchWithCsrf("{!! route('customer.clear_cedant_data') !!}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(data),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status == 202) {
                                toastr.success(data.message)
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 201) {
                                toastr.success("Action was successful", 'Successful')
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                showServerSideValidationErrors(data.errors)
                            } else {
                                toastr.error("Failed to save details")
                            }
                        })
                        .catch(error => {
                            toastr.error("An internal error occurred")
                        });
                });
            });

            $(document).on('click', '.integrate-btn', function(e) {
                e.preventDefault();
                const propsId = $(this).data('id');

                Swal.fire({
                    title: 'Create Cover Document',
                    text: 'Are you sure you want to generate a cover document for this handover?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Generate Cover',
                    cancelButtonText: 'Not Now',
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#d33',
                    backdrop: true,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing',
                            text: 'Creating your cover document...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{!! route('pipeline.create_cover') !!}",
                            type: 'POST',
                            data: {
                                id: propsId,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Cover document has been generated successfully.',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        $bdTable.ajax.reload(null, false);
                                        processCover(response)
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message ||
                                            'Failed to generate cover document. Please try again.',
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr) {
                                const errorMessage = xhr.responseJSON?.message ||
                                    'An unexpected error occurred while generating the cover document';

                                Swal.fire({
                                    title: 'Error',
                                    text: errorMessage,
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            $bdTable.on('click', '.approve-btn', function(e) {
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
                            action: '1',
                            type: 'approve',
                            comment: result.value
                        }
                        approveDeclineRequest(data)
                    }
                });
            });

            $bdTable.on('click', '.reject-bd-btn', function(e) {
                e.preventDefault();
                const approval_id = $(this).data('id')
                swal.fire({
                    title: 'Reject Confirmation',
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
                            action: '0',
                            type: 'decline',
                            comment: result.value
                        }
                        approveDeclineRequest(data)
                    }
                });
            });


            $bdTable.on('click', '.review-btn', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                window.open(url, '_blank');
            });

            $bdTable.on('click', '.rejected-bd-comment', function(e) {
                e.preventDefault();
                const reason = $(this).data('reason');
                $('#rejection_message').html(reason);
                $('#rejectedCommentModal').modal('show');
            });

            function approveDeclineRequest(data) {
                $.ajax({
                    url: "{!! route('approvals.bd-approval-action') !!}",
                    type: 'POST',
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 201) {
                            toastr.success(response.message, 'Successful');
                            $bdTable.ajax.reload();
                        } else if (response.status === 422) {
                            showServerSideValidationErrors(response.errors);
                        } else {
                            toastr.error("Failed to approve details");
                        }
                    },
                    error: function(xhr) {
                        toastr.error("An internal error occurred");
                    }
                });
            }

            function processCover(response) {
                if (response.status) {
                    $("#newCoverForm #customerId").val(response.customerId);
                    $("#newCoverForm #prospectId").val(response.prospectId);
                    $("#newCoverForm #transType").val('NEW');
                    $("#newCoverForm").submit();
                }
            }
        });
    </script>
@endpush
