@extends('layouts.app')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Customers</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Customers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Customer list</div>
                </div>
                <div class="card-body">
                    {{ html()->form('get', '/customer/customer-new')->id('form_add_customer')->open() }}
                    <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_customer"><i
                            class='bx bx-plus'></i> Add
                        Customer</button>
                    {{ csrf_field() }}
                    {{ html()->form()->close() }}
                    {{-- {{ html()->form('post', '/customer/customer-dtl')->id('form_customer_datatable')->open() }} --}}
                    {{-- <input type="text" id="customer_id" name="customer_id" hidden /> --}}
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover" id="customer-table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Tax No</th>
                                    <th scope="col">Registration No</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Website</th>
                                    <th scope="col">Debited Covers</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    {{-- {{ csrf_field() }}
                    {{ html()->form()->close() }} --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $customerTbl = $('#customer-table').DataTable({
                pageLength: 50,
                lengthMenu: [50, 100, 200],
                processing: true,
                serverSide: true,
                order: [
                    [0, 'asc']
                ],
                ajax: {
                    url: '{{ route('customer.data') }}',
                    error: function(xhr, error, code) {
                        console.error('DataTables AJAX error:', error);
                    }
                },
                columns: [{
                        data: 'customer_id',
                        name: 'customer_id',
                        title: 'ID',
                        defaultContent: '<span class="no-data">—</span>',
                        className: 'highlight-idx'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        title: 'Name',
                        defaultContent: '<span class="no-data">—</span>',
                        className: 'highlight-2view-point'
                    },
                    {
                        data: 'customer_type_name',
                        name: 'customer_type_name',
                        title: 'Customer Type',
                        defaultContent: '<span class="no-data">—</span>',
                        className: 'highlight-action'
                    },
                    {
                        data: 'tax_no',
                        name: 'tax_no',
                        title: 'Tax Number',
                        defaultContent: '<span class="no-data">—</span>'
                    },
                    {
                        data: 'registration_no',
                        name: 'registration_no',
                        title: 'Registration Number',
                        defaultContent: '<span class="no-data">—</span>'
                    },
                    {
                        data: 'email',
                        name: 'email',
                        title: 'Email',
                        defaultContent: '<span class="no-data">—</span>',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                return `<a href="mailto:${data}">${data}</a>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'website',
                        name: 'website',
                        title: 'Website',
                        defaultContent: '<span class="no-data">—</span>',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                const url = data.startsWith('http') ? data : `https://${data}`;
                                return `<a href="${url}" target="_blank" rel="noopener noreferrer">${data}</a>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'debited_covers',
                        name: 'debited_covers',
                        title: 'Debited Covers',
                        className: 'highlight-index',
                        defaultContent: '<span class="no-data">—</span>'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        title: 'Actions',
                        searchable: false,
                        orderable: false,
                        defaultContent: '<span class="no-data">—</span>',
                        className: 'highlight-action'
                    }
                ],
                language: {
                    processing: "Processing...",
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                    emptyTable: "No customers available",
                    zeroRecords: "No matching customers found"
                }
            });

            // $customerTbl.on('click', 'tbody td .process_customer', function() {
            //     var rowIndex = $(this).parent().index('#customer-table tbody tr');
            //     var tdIndex = $(this).index('#customer-table tbody tr:eq(' + rowIndex + ') td');

            //     var customer_no = $(this).closest('tr').find('td:eq(0)').text();
            //     $("#customer_id").val(customer_no);

            //     if (tdIndex < 6) {
            //         $("#form_customer_datatable").submit();
            //     }
            // });

            $customerTbl.on('click', '.edit_customer', function(e) {
                e.preventDefault();
                const customerId = $(this).data('id');
                window.location.href = `/customer/${customerId}/edit`;
            });

            // function processCustomer(customer_id) {
            //     $("#customer_id").val(customer_id);
            //     $("#form_customer_datatable").submit();
            // }

            $("#add_customer").click(function() {
                $("#form_add_customer").submit();
            });
        });
    </script>
@endpush
