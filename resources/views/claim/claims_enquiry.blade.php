@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Claims Enquiry</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href>Claims Enquiry</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Claims list
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Claims list</div>
                </div>
                <div class="card-body">
                    {!! html()->form('POST', route('claim_detail'))->id('form_claim_datatable')->open() !!}
                    <input type="hidden" name="claim_no" id="clm_claim_no">
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
                    {{ csrf_field() }}
                    {{ html()->form()->close() }}
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
                const claim_no = $(this).data('claim_no');
                if (claim_no != '') {
                    $("#clm_claim_no").val(claim_no);
                    $("#form_claim_datatable").submit();
                }
            });
        });
    </script>
@endpush
