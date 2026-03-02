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
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin: 0 0 1.5rem 0;
            padding: 11px;
            border: 1px solid #e2e8f0;
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
            color: #fff;
        }

        .step.completed {
            background: #059669;
            color: #fff;
        }

        .step-arrow {
            color: #9ca3af;
            font-size: 12px;
        }
    </style>

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Claims Enquiry</h1>
            <p class="text-muted mb-0 pt-1">Review and manage claim debit records</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href>Claims Administration</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Claims Enquiry
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="page-title-section">
        <div class="workflow-steps">
            <div class="step completed">
                <i class="bx bx-bell"></i>
                <span>Notification</span>
            </div>
            <i class="bx bx-right-arrow step-arrow"></i>
            <div class="step completed">
                <i class="bx bx-file"></i>
                <span>Debit Creation</span>
            </div>
            <i class="bx bx-right-arrow step-arrow"></i>
            <div class="step active">
                <i class="bx bx-check-circle"></i>
                <span>Claims Enquiry</span>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Claims List</div>
                </div>
                <div class="card-body">
                    <table id="claimlist-table" class="table text-nowrap table-hover table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID No.</th>
                                <th>Claim Number </th>
                                <th>Cover No</th>
                                <th>Endorsement No </th>
                                <th>Business Type</th>
                                <th>Class</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#claimlist-table').DataTable({
                order: [
                    [1, 'desc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: true,
                pageLength: 15,
                lengthMenu: [15, 30, 50, 100],
                ajax: {
                    url: '{!! route('claims.enquiry.datatable') !!}',
                },
                columns: [{
                        data: 'id',
                        searchable: false,
                        class: "highlight-idx",
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    }, {
                        data: 'claim_no',
                        searchable: false,
                        class: "highlight-index",
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
                        data: 'status',
                        searchable: false
                    },
                    {
                        data: 'action',
                        sortable: false,
                        searchable: false,
                    },
                ]
            });

            $(document).on('click', '.view_claim', function(e) {
                e.preventDefault();
                var claim_no = $(this).data("claim_no");
                const detailUrl = $(this).data('detail-url');

                const claimDetailUrl = new URL(
                    detailUrl,
                    window.location.origin
                );

                if (claim_no !== "") {
                    try {
                        claimDetailUrl.searchParams.set("claim_no", claim_no);
                        window.location.href = claimDetailUrl.toString();
                    } catch (error) {
                        console.error("URL error:", error);
                        Swal.fire("Error", "Invalid URL parameters", "error");
                    }
                } else {
                    Swal.fire("Error", "No intimation number provided.", "error");
                }
            });
        });
    </script>
@endpush
