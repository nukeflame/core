@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">{{ $treaty_name }} </h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href>Facultative Proportional Enquiry</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Add new
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="row">
        <div class="col-xl-6">
            <button type="button" class="btn btn-sm btn-dark btn-wave" id="newCoverBtn">Add new Cover</button>
        </div>
    </div>

    <!-- Start:: row-2 -->
    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Cedant list</div>
                </div>
                <div class="card-body">
                    {{ html()->form('POST', '/cover/endorsements_list')->id('form_cover_datatable')->open() }}
                    <input type="text" id="customer_id" name="customer_id" hidden />
                    <input type="text" name="cover_no" id="cov_cover_no" hidden>
                    <table id="coverlist" class="table text-nowrap table-hover table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Cover Number </th>
                                <th>Cover Type</th>
                                <th>Cedant</th>
                                <th>Expiry Date </th>
                            </tr>
                        </thead>
                    </table>
                    {{ csrf_field() }}
                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    </div>

    <!--Choose Customer Modal -->
    <div class="modal customer-model-wrapper effect-scale" id="newCoverModal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="new_cover_form" action="{{ route('cover.form') }}">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <div class="d-flex flex-column ced-body">
                                    <input type="hidden" id="trans_type" name="trans_type" value="NEW">
                                    <input type="hidden" id="type_of_bus" name="type_of_bus">
                                    <label for="title" class="form-label md-title mb-2">Choose Cedant</label>
                                    <select class="form-inputs select2" id="ccustomer_id" name="customer_id"
                                        required></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"
                            id="dismiss-cover-modal">Close</button>
                        <button type="button" id="next-save-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Next</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            var type_of_bus = {!! json_encode($type_of_bus) !!};
            $('#newCoverModal').on('shown.bs.modal', function() {
                $('.form-inputs').select2({
                    dropdownParent: $('#newCoverModal')
                });
            });
            $('#coverlist').DataTable({ // New initialization
                processing: true,
                serverSide: true,
                order: [
                    [0, 'asc']
                ],
                ajax: {
                    url: '{{ route('treatyenquiry.data') }}',
                    data: function(d) {
                        d.type_of_bus = type_of_bus;
                    }
                },
                columns: [{
                        data: 'cover_no',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'cover_type',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'name',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'cover_to',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    // {
                    //     data: 'process',
                    //     searchable: false,
                    //     defaultContent: "<b style=''>_</b>"
                    // },
                ]
            });
            $('#coverlist').on('click', 'tbody tr', function() {
                var cover_no = $(this).closest('tr').find('td:eq(0)').text();
                if (cover_no != '') {
                    $("#cov_cover_no").val(cover_no);
                    $("#form_cover_datatable").submit();
                }

            });
            $('#newCoverBtn').click(function(e) {
                e.preventDefault();

                $('#type_of_bus').val(type_of_bus);
                $('#ccustomer_id').empty();

                $.ajax({
                    url: "{{ route('cover.get-customers') }}",
                    method: 'GET',
                    success: function(data) {
                        $('#ccustomer_id').append(
                            '<option value=""> -- Select Cedant --</option>');
                        $.each(data, function(key, customer) {
                            $('#ccustomer_id').append('<option value="' + customer
                                .customer_id + '">' + customer.name + '</option>');
                        });
                        $('#newCoverModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching customers:', error);
                    }
                });
            });
            $('#next-save-btn').click(function(e) {
                $("#new_cover_form").submit();
            });
            $("#new_cover_form").validate({
                errorClass: "errorClass",
                rules: {
                    customer_id: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $('#next-save-btn').prop('disabled', true).text('Validating...');
                    form.submit();
                    $('#next-save-btn').prop('disabled', false).text('Next');
                }
            });

            $('#dismiss-cover-modal').on('click', function() {
                $("#new_cover_form")[0].reset();
                $("#ccustomer_id-error").css({
                    display: "none"
                });
            });
        });
    </script>
@endpush
