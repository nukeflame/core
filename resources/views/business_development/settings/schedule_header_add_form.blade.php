@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">schedule header</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">schedule header</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        @if (isset($schedule))
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
            <form id="store_schedule_header" action="{{ route('bd.schedule.header.store') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ isset($schedule) ? $schedule->id : '' }}">

                <div class="form-group">
                    <h4>Schedule Header Details</h4>
                    <div class="row gy-4 partner_info">
                        <div class="col-md-4 mb-3">
                            <div class="col-xl-11">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-inputs" aria-label="name" id="name" name="name"
                                    required value="{{ isset($schedule) ? $schedule->name : '' }}">
                            </div>
                            <div class="col-xl-11">
                                <label class="form-label">Business Type</label>
                                <select class="form-inputs select2" name="business_type" id="business_type" required>
                                    <option value="">Select</option>
                                    <option value="FAC"
                                        {{ isset($schedule) && $schedule->business_type == 'FAC' ? 'selected' : '' }}>Facultative
                                    </option>
                                    <option value="TRT"
                                        {{ isset($schedule) && $schedule->business_type == 'TRT' ? 'selected' : '' }}>Treaty
                                    </option>
                                </select>
                            </div>
                            <div class="col-xl-11">
                                <label class="form-label">Position</label>
                                <input type="number" class="form-inputs" placeholder="position" aria-label="position"
                                    id="position" name="position" required
                                    value="{{ isset($schedule) ? $schedule->position : '' }}">
                            </div>

                            <div class="col-xl-11">
                                <label class="form-label" for="amount_field">Amount Field</label>
                                <select class="form-inputs select2" name="amount_field" id="amount_field" required>
                                    <option value="">Select</option>
                                    <option value="Y"
                                        {{ isset($schedule) && $schedule->amount_field == 'Y' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="N"
                                        {{ isset($schedule) && $schedule->amount_field == 'N' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                            </div>

                            <div class="col-xl-11 amountField" style="display: none">
                                <label class="form-label">Type of sum insured</label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="sum_insured_type" name="sum_insured_type">
                                        <option value="">--select sum insured type--</option>
                                        @foreach ($type_of_sum_insured as $sum_typ)
                                            <option value="{{ $sum_typ->sum_insured_code }}"
                                                {{ isset($schedule) && $schedule->sum_insured_type == $sum_typ->sum_insured_code ? 'selected' : '' }}>
                                                {{ $sum_typ->sum_insured_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="col-xl-11  amountField" style="display: none">
                                <label class="form-label">Data Determinant</label>
                                <select name="data_determinant" id="data_determinant" class="form-inputs select2">
                                    <option value="">--Select data determinant--</option>
                                    <option value="COM"
                                        {{ isset($schedule) && $schedule->data_determinant == 'COM' ? 'selected' : '' }}>
                                        Commission</option>
                                    <option value="PREM"
                                        {{ isset($schedule) && $schedule->data_determinant == 'PREM' ? 'selected' : '' }}>
                                        Premium</option>
                                    <option value="SI"
                                        {{ isset($schedule) && $schedule->data_determinant == 'SI' ? 'selected' : '' }}>Sum
                                        Insured</option>
                                </select>
                            </div>

                            <div class="col-xl-11" style="display: none" id="class_group">
                                <label class="form-label">Class Group</label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="class_group" name="class_group">
                                        <option value="">-- Select Class Group --</option>
                                        @foreach ($classGroups as $classGroup)
                                            <option value="{{ $classGroup->group_code }}"
                                                {{ isset($schedule) && $schedule->class_group == $classGroup->group_code ? 'selected' : '' }}>
                                                {{ $classGroup->group_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-11"  id="class_div">
                                <label class="form-label">Class</label>
                                <select name="class" id="class" class="form-inputs select2">
                                    <option value="">----select---</option>


                                </select>
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
            $('.amountField').hide();
            $(".typeof_select").select2({
                placeholder: "-- Select --"
            });
            $('#business_type').trigger('change');
            let business_type ='' ;
             business_type = $('#business_type').val();
            let rules = {
                name: {
                    required: true,
                    maxlength: 100,
                },
                position: {
                    required: true,
                },
                amount_field: {
                    required: true
                }
            };

            let messages = {
                name: {
                    required: "Schedule header name is required",
                    maxlength: "Schedule header name must be at most 100 characters",
                },
                position: {
                    required: "Position is required",
                },
                amount_field: {
                    required: "Amount field is required",
                }
            };

            // Add extra fields only if business_type is FAC
            if (business_type === 'FAC') {
                rules.class = {
                    required: true
                };
                rules.class_group = {
                    required: true
                };

                messages.class = {
                    required: "Class is required"
                };
                messages.class_group = {
                    required: "Class group is required"
                };
            }

            $("#store_schedule_header").validate({
                rules: rules,
                messages: messages,
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


            // $("select#class_group").change(function() {
            //     var class_group = $("select#class_group option:selected").attr('value');
            //     $('#class').append($('<option>').text('-- Select --').attr('value', ''));
            //     if ($(this).val() != '') {
            //         $('#class').prop('disabled', false)
            //         $.ajax({
            //             url: "{{ route('get_class') }}",
            //             data: {
            //                 "class_group": class_group
            //             },
            //             dataType: "json",
            //             type: "get",
            //             success: function(resp) {
            //                 console.log(resp)
            //                 if (Array.isArray(resp) && resp.length > 0) {
            //                     $('#sel_classcode').empty();
            //                     $('#sel_classcode').append($('<option>').text('-- Select --')
            //                         .attr('value', ''));
            //                     $.each(resp, function(i, value) {
            //                         $('#sel_classcode').append(`
            //                         <option value="${value.class_code}">${value.class_code} - ${value.class_name}</option>
            //                     `);
            //                     });

            //                 } else {
            //                     $('#sel_classcode').append(
            //                         '<option value="">No classes found</option>');
            //                 }
            //             },
            //             error: function(resp) {
            //                 console.error(resp);
            //             }
            //         })
            //     }
            // });
            const selectedClassCode = "{{ $schedule->class ?? '' }}";         
            $("select#class_group").change(function() {
                var class_group = $("select#class_group option:selected").attr('value');
                $('#class').append($('<option>').text('-- Selectr --').attr('value', ''));
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
                            if (Array.isArray(resp) && resp.length > 0) {
                                $('#class').empty();
                                $('#class').append($('<option>').text('-- Select --')
                                    .attr('value', ''));
                                $.each(resp, function(i, value) {
                                    $('#class').append(`
                                    
                                    <option value="${value.class_code}">${value.class_code} - ${value.class_name}</option>
                                `);
                                });
                                if (selectedClassCode) {
                                    $('#class').val(selectedClassCode).trigger('change');
                                }

                            } else {
                                $('#class').append(
                                    '<option value="">No classes found</option>');
                            }
                        },
                        error: function(resp) {
                            console.error(resp);
                        }
                    })
                }
            });


            function toggleFields() {
                const businessType = $('#business_type').val();
                const amountField = $('#amount_field').val();

                if (businessType === 'FAC') {
                    $('#class_group, #class_div').show();
                    if (amountField === 'Y') {
                        $('.amountField').show();
                    } else {
                        $('.amountField').hide();
                    }
                } else {
                    $('#class_group, #class_div').hide();
                    $('.amountField').hide();
                }
            }

            // Initial toggle
            toggleFields();

            $('#business_type, #amount_field').on('change', toggleFields);
            if ($('#class_group').val()) {
                $('#class_group').trigger('change');
            }
           
           

        });
    </script>
@endpush
