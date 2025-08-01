@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Lead Status</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Lead Status</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        @if (isset($LeadStatus))
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
            <form id="store_lead_status" action="{{ route('lead.status.store') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ isset($LeadStatus) ? $LeadStatus->lead_id : '' }}">

                <div class="form-group">
                    <h4>Lead status Details</h4>
                    <div class="row gy-4 partner_info">
                        <div class="col-md-4 mb-3">
                            <div class="col-xl-11">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-inputs" aria-label="name" id="name" name="name"
                                    required value="{{ isset($LeadStatus) ? $LeadStatus->status_name : '' }}">
                            </div>
                            <div class="col-xl-11">
                                <label class="form-label">Stage</label>
                                <input type="number" class="form-inputs" placeholder="Stage" aria-label="Stage"
                                    id="stage" name="stage" required
                                    value="{{ isset($LeadStatus) ? $LeadStatus->id : '' }}">
                            </div>

                            <div class="col-xl-11">
                                <label class="form-label" for="category_type">Category Type</label>
                                <select class="form-inputs select2" name="category_type" id="category_type" required>
                                    <option value="">Select</option>
                                    <option value="1"
                                        {{ isset($LeadStatus) && $LeadStatus->category_type == '1' ? 'selected' : '' }}>Quotation
                                    </option>
                                    <option value="2"
                                        {{ isset($LeadStatus) && $LeadStatus->category_type == '2' ? 'selected' : '' }}>Facultative Offer
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <button type="submit" class="btn btn-primary submit-btn" id="add_lead_status">Submit</button>
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
            $(".typeof_select").select2({
                placeholder: "-- Select --"
            });

            $("#store_lead_status").validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 100,
                    },
                    stage: {
                        required: true,
                    },
                    category_type: {
                        required: true
                    },
                    // sum_insured_type: {
                    //     required: true
                    // },
                    class: {
                        required: true
                    },
                    class_group: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: "name is required",
                        maxlength: "name must be at most 100 characters",
                    },
                    stage: {
                        required: "stage is required",
                    },
                    category_type: {
                        required: "category type field is required",
                    },
                },
                errorPlacement: function(error, element) {
                    error.addClass("text-danger"); 
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

            $("select#class_group").change(function() {
                var class_group = $("select#class_group option:selected").attr('value');
                $('#sel_classcode').append($('<option>').text('-- Select --').attr('value', ''));
                if ($(this).val() != '') {
                    $('#class').prop('disabled', false)
                    $.ajax({
                        url: "{{ route('get_class') }}",
                        data: {
                            "class_group": class_group
                        },
                        dataType: "json",
                        type: "get",
                        success: function(resp) {
                            console.log(resp)
                            if (Array.isArray(resp) && resp.length > 0) {
                                $('#sel_classcode').empty();
                                $('#sel_classcode').append($('<option>').text('-- Select --')
                                    .attr('value', ''));
                                $.each(resp, function(i, value) {
                                    $('#sel_classcode').append(`
                                    <option value="${value.class_code}">${value.class_code} - ${value.class_name}</option>
                                `);
                                });

                            } else {
                                $('#sel_classcode').append(
                                    '<option value="">No classes found</option>');
                            }
                        },
                        error: function(resp) {
                            console.error(resp);
                        }
                    })
                }
            });
        });
    </script>
@endpush
