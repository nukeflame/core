@extends('layouts.app')

@section('content')

<div class="container">
    <nav class="breadcrumb pt-3">
        <a class="breadcrumb-item" href>Settings </a><span> ➤ Customer</span>
    </nav>

    <a class="btn btn-primary btn-sm custom-btn" href=""><i class='bx bx-plus'></i> Add Customer</a>

    <table class="table" id="arCustomer-table">
        <thead class="text-uppercase text-nowrap">
            <tr>
                <th>CUSTOMER GROUP</th>
                <th>NAME</th>
                <th>TELEPHONE</th>
                <th>EMAIL</th>
                <th>COUNTRY</th>
                <th style="width: 20%">Action</th>
            </tr>
        </thead>
    </table>
</div>

@endsection
@push('script')
<script>
    $(document).ready(function() {

        $('#arCustomer-table').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, 'asc']
            ],
            ajax: "{{ route('arCustomer.data') }}",
            columns: [{
                    data: 'group_id'
                },
                {
                    data: 'group_title'
                },
                {
                    data: 'group_description'
                },
                {
                    data: 'default_currency'
                },
                {
                    data: 'control_account'
                },
                {
                    data: 'tax_category'
                },
                {
                    data: 'action'
                },
            ]
        });

        $("#store_arCustomer").validate({
            rules: {
                group_id: {
                    required: true
                },
                group_title: {
                    required: true
                },
                group_description: {
                    required: true
                },
                default_currency: {
                    required: true
                },
                control_account: {
                    required: true
                },
                tax_category: {
                    required: true
                },
            },
            messages: {
                group_id: {
                    required: "group id is required"
                },
                group_title: {
                    required: "group title is required"
                },
                group_description: {
                    required: "group description is required"
                },
                default_currency: {
                    required: "default currency is required"
                },
                control_account: {
                    required: "control account is required"
                },
                tax_category: {
                    required: "tax category is required"
                },
            },
            errorPlacement: function(error, element) {
                // Customize the placement of error messages
                error.addClass("text-danger"); // Add red color to the error message
                error.insertAfter(element);
            },
            highlight: function(element) {
                // Highlight the input field with an error
                $(element).addClass('error').removeClass('valid');
            },
            unhighlight: function(element) {
                // Remove the highlight from the input field on valid input
                $(element).removeClass('error').addClass('valid');
            },
            submitHandler: function(form, event) {
                // Custom logic before form submission
                event.preventDefault();
                // For example, you might want to show a confirmation dialog
                var isConfirmed = confirm("Are you sure you want to submit the form?");

                if (isConfirmed) {
                    // If confirmed, you can proceed with the form submission
                    form.submit();
                } else {
                    // If not confirmed, prevent the form submission
                    return false;
                }
            }
        });
        // <!-- END -->
    });
</script>
@endpush