@extends('layouts.app')

@section('content')
    <div>
        <nav class="breadcrumb">
            <a class="breadcrumb-item" href>Reinsurer </a><span> ➤ Details</span>
        </nav>
    </div>
    <div class="table-responsive">
        {{ html()->form('POST', '/customer/customer-dtl')->id('form_customer_datatable')->open() }}
        <input type="text" id="customer_id" name="customer_id" hidden />
        <table class="table text-nowrap table-striped table-hover" id="customer-table">
            <thead class="table-primary">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Tax No</th>
                    <th scope="col">Registration No</th>
                    <th scope="col">Email</th>
                    <th scope="col">Website</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>

        </table>
        {{ csrf_field() }}
        {{ html()->form()->close() }}
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {

            $('#customer-table').DataTable({ // New initialization
                processing: true,
                serverSide: true,
                order: [
                    [0, 'asc']
                ],
                ajax: '{{ route('reinsurer.data') }}',
                columns: [{
                        data: 'customer_id',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'name',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'customer_type_name',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'tax_no',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'registration_no',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'email',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'website',
                        defaultContent: "<b class='dashes' style=''>_</b>"
                    },
                    {
                        data: 'process',
                        searchable: false,
                        defaultContent: "<b style=''>_</b>"
                    },
                ]
            });

            $('.dataTable').on('click', 'tbody td .process_customer', function() {
                var rowIndex = $(this).parent().index('#customer-table tbody tr');
                var tdIndex = $(this).index('#customer-table tbody tr:eq(' + rowIndex + ') td');

                var customer_no = $(this).closest('tr').find('td:eq(0)').text();
                $("#customer_id").val(customer_no);

                if (tdIndex < 6) {
                    $("#form_customer_datatable").submit();
                }
            });

            $('.process_customer').on('click', 'tbody tr', function() {

            });

            function processCustomer(customer_id) {
                $("#customer_id").val(customer_id);
                $("#form_customer_datatable").submit();
            }

            $("#add_customer").click(function() {
                $("#form_add_customer").submit();
            });

            // <!-- END -->
        });
    </script>
@endpush
