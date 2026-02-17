@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Treaty Operation Checklist</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Treaty Operation Checklist</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        @if (isset($StageDocuments))
                            Update
                        @else
                            Create
                        @endif
                    </li>

                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="row row-cols-12">
        <div class="col">
            <form id="store_schedule_header" enctype="multipart/form-data" action="{{ route('operationchecklist.store') }}"
                method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ isset($OperationChecklist) ? $OperationChecklist->id : '' }}">

                <div class="form-group">
                    <h4>Treaty Operation Checklist Details</h4>
                    <div class="row gy-4 partner_info">
                        <div class="col-xl-4">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-inputs" aria-label="stage" id="name" name="name"
                                required value="{{ isset($OperationChecklist) ? $OperationChecklist->name : '' }}">
                        </div>
                    </div>                  


                </div>



                <div class="row">
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary submit-btn" id="add_customer">Submit</button>
                    </div>
                </div>
        </div>
        {{ csrf_field() }}
        </form>
    </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            $('#normal_doc_container').hide();
            $('#checkbox_doc_container').hide();
            $('#cedant_file_container').hide();

            $('#business_type').on('change', function() {
                const selectedValue = $(this).val();

                // If "Treaty" is selected, show and open the checkbox_doc dropdown
                if (selectedValue === 'Treaty') {
                    $('#normal_doc_container').show();
                    // $('#checkbox_doc').select2('open');
                } else {
                    // Hide the checkbox_doc container for other selections
                    $('#normal_doc_container').hide();
                    $('#checkbox_doc_container').hide();
                    $('#cedant_file_container').hide();

                }
            });
            $('#normal_doc').on('change', function() {
                const selectedValue = $(this).val();

                // If "Treaty" is selected, show and open the checkbox_doc dropdown
                if (selectedValue === 'N') {
                    $('#checkbox_doc_container').show();
                } else {
                    $('#checkbox_doc_container').hide();
                    $('#cedant_file_container').hide();
                }
            });
            $('#checkbox_doc').on('change', function() {
                const selectedValue = $(this).val();

                // If "Treaty" is selected, show and open the checkbox_doc dropdown
                if (selectedValue === '1') {
                    $('#cedant_file_container').show();
                    // $('#checkbox_doc').select2('open');
                } else {
                    // Hide the checkbox_doc container for other selections
                    $('#cedant_file_container').hide();
                }
            });
            $(".typeof_select").select2({
                placeholder: "-- Select --"
            });

            $("#store_schedule_header").validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 100,
                    },
                   

                },
                messages: {
                    name: {
                        required: "name  is required",
                        maxlength: "max length is 100",
                        // pattern: "Customer name should contain letters only"
                    },
                },
                errorPlacement: function(error, element) {
                    error.addClass("text-danger"); // Add red color to the error message
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass('error').removeClass('valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('error').addClass('valid');
                },
                submitHandler: function(form, e) {
                    e.preventDefault();
                    var isConfirmed = confirm("Are you sure you want to submit the form?");
                    if (isConfirmed) {
                        form.submit();
                    } else {
                        return false;
                    }
                }
            });
        });
    </script>
@endpush
