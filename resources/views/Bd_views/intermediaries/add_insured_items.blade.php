@extends('layouts.intermediaries.base')

@section('content')
    <nav class="page-title fw-semibold fs-18 mb-0 bg-white mt-2 mb-2 p-1"
        style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);"
        aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Client</li>
            <li class="breadcrumb-item"><a href="#"
                    id="to-customer">{{ Str::ucfirst(strtolower($customer->full_name)) }}</a></li>
            <li class="breadcrumb-item">Cover</li>
            <li class="breadcrumb-item"><a href="#" id="to-cover">{{ $policy_dtl->policy_no }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cover Details</li>
            <li class="breadcrumb-item"><a href="#" id="to-cover">{{ $policy_dtl->policy_no }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Insured Items</li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="" id="schedulesForm">
                @csrf
                <input type="hidden" name="policy_no" id="policy_no" value="{{ $policy_dtl->policy_no }}">
                @if ($riskdtls != '')
                    <input type="hidden" name="id" id="id" value="{{ $riskdtls->id }}">
                @endif
                <input type="hidden" name="endorsement_no" id="endorsement_no" value="{{ $policy_dtl->endorsement_no }}">
                <button class="btn btn-primary btn-sm add-item">Add Item</button>

                <div class="formdatas">
                    <div class="formdata" style="background-color: #f5f5f5;">
                        <h5>Section 1</h5>
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <label class="required-label" for="">Item Name/Title</label>
                                @if ($riskdtls != '')
                                    <input type="text" name="title[]" id="item_name0"
                                        class="form-control form-control-sm" value="{{ $riskdtls->name }}" required
                                        onkeyup="this.value=this.value.toUpperCase();">
                                @else
                                    <input type="text" name="title[]" id="item_name0"
                                        class="form-control form-control-sm" required
                                        onkeyup="this.value=this.value.toUpperCase();">
                                @endif
                            </div>
                            @if ($riskdtls != '')
                                <div class="col-md-3">
                                    <label class="required-label" for="">Basis</label>
                                    <select class="form-control form-control-sm" id="basis0" name="basis[]" required>
                                        <!-- <option  disabled value="">Select An Option...</option> -->
                                        <option value="1" selected>Sum Insured</option>
                                        <!-- <option value="2" @if ($riskdtls->basis == 2) selected @endif>Amount</option> -->

                                    </select>
                                </div>
                            @else
                                <div class="col-md-3">
                                    <label class="required-label" for="">Basis</label>
                                    <select class="form-control form-control-sm" id="basis0" name="basis[]" required>
                                        <!-- <option selected disabled value="">Select An Option...</option> -->
                                        <option value="1" selected>Sum Insured</option>

                                        <!-- <option value="2">Amount</option> -->

                                    </select>
                                </div>
                            @endif

                            @if ($riskdtls != '')
                                <div class="col-md-3 valuediv" style="display: none;" id="valuediv0">
                                    <label class="required-label" for="">Sum Insured</label>
                                    <input type="text" name="schedule_value[]" id="schedule_value0"
                                        value="{{ $riskdtls->sum_insured }}" class="form-control form-control-sm amount"
                                        required>
                                </div>
                            @else
                                <div class="col-md-3 valuediv" style="display: none;" id="valuediv0">
                                    <label class="required-label" for="">Sum Insured</label>
                                    <input type="text" name="schedule_value[]" id="schedule_value0"
                                        class="form-control form-control-sm amount" required>
                                </div>
                            @endif
                            @if ($riskdtls != '')
                                <div class="col-md-3" id="amtdiv0">
                                    <label class="required-label" for="">Amount</label>
                                    <input type="text" name="premium[]" id="premium0"
                                        class="form-control form-control-sm prem" value="{{ $riskdtls->sum_insured }}"
                                        required>
                                </div>
                            @else
                                <div class="col-md-3" id="amtdiv0">
                                    <label class="required-label" for="">Amount</label>
                                    <input type="text" name="premium[]" id="premium0"
                                        class="form-control form-control-sm prem" required>
                                </div>
                            @endif
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="">Details</label>
                                <div id="schedule-descr0" class="schedule-descr0"></div>
                                <input type="hidden" name="details[]" id="details0">
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary btn-sm" id="schedule-save-btn">Save</button>


            </form>

        </div>
    </div>
@endsection
@section('page_scripts')
    <script>
        var ckeditors = {};
        var counter = 0;
        $(document).ready(function() {


            initializeCKEditor(0);
            @if ($riskdtls != '')
                let res = "{!! $riskdtls->details !!}"
                setTimeout(function() {
                    setCKEditorValue(0, res);
                }, 1000);
            @endif

            $("#schedulesForm").validate({
                errorClass: "errorClass",
                rules: {
                    title: {
                        required: true
                    },
                    details: {
                        required: true
                    },
                },
                submitHandler: function(form) {

                    $('#schedule-save-btn').prop('disabled', true).text('Saving...')

                    // Get form data
                    var formData = new FormData(form);
                    // var formData = $(this).serialize();

                    // Make a fetch request
                    fetch("{!! route('cover.add_schedule') !!}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams(formData),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status == 200) {
                                toastr.success("Schedule Successfully saved")

                                form.reset()
                                var policy_no = $("#endorsement_no").val()
                                window.location = "{{ route('policy.addrisk', '') }}" + "/" +
                                    policy_no;

                            } else if (data.status == 422) {
                                toastr.error("Failed to save details")

                                $('#schedule-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to save details")
                            }
                            $('#schedule-save-btn').prop('disabled', false).text('Save')
                        })
                        .catch(error => {
                            $('#schedule-save-btn').prop('disabled', false).text('Save')
                        });
                }
            })

            $(function() {
                $('.add-item').click(function(e) {
                    e.preventDefault()


                    if (counter > 0) {
                        var item_name = $('#item_name' + counter).val()
                        var schedule_value = $('#premium' + counter).val()
                    } else if (counter == 0) {
                        var item_name = $('#item_name0').val()
                        var schedule_value = $('#premium0').val()
                    }
                    if (item_name == '' || schedule_value == '') {
                        Swal.fire({
                            icon: 'warning',
                            text: 'Please fill all details'
                        });
                    } else {
                        counter++;


                        $('.formdatas').append(`<div class="formdata mt-4" style="background-color: #f5f5f5;"> \
                                <h5>Section ${counter+1}</h5>\
                                <button class="btn btn-danger btn-sm remove-item mb-3" style="padding: 0.2rem 0.4rem; font-size: 0.75rem;">Remove Item</button>\

                                <div class="row mb-2"> \
                                    <div class="col-md-3"> \
                                        <label class="required-label" for="">Item Name/Title</label> \
                                        <input type="text" name="title[]" id="item_name${counter}" class="form-control form-control-sm" required onkeyup="this.value=this.value.toUpperCase();"> \
                                    </div> \
                                    <div class="col-md-3">\
                                        <label class="required-label">Basis</label>\
                                        <select class="form-control form-control-sm" id="basis${counter}" name="basis[]" required>\

                                            <option value="1" selected>Sum Insured</option>\

                                        </select>\
                                    </div>\

                                    <div class="col-md-3 valuediv" style="display: none;" id="valuediv${counter}"> \
                                        <label class="required-label">Item Value</label> \
                                        <input type="text" name="schedule_value[]"  id="schedule_value${counter}" class="form-control form-control-sm amount" required> \
                                    </div> \
                                    <div class="col-md-3">
                                        <label class="required-label">Amount</label>
                                        <input type="text" name="premium[]" id="premium${counter}" class="form-control form-control-sm prem" required>
                                    </div>\
                                </div> \
                                <div class="row"> \
                                    <div class="col-md-12"> \
                                        <label for="">Details</label> \
                                        <div class="schedule-descr${counter}"></div> \
                                        <input type="hidden" name="details[]" id="details${counter}" class="sched-details"> \
                                    </div> \
                                </div> \
                            </div>`);
                        initializeCKEditor(counter);

                    }
                    $('input[type=radio]').change(function() {
                        $('input[type=radio]:checked').not(this).prop('checked', false)
                    })
                });
                $('#schedule-save-btn').click(function() {
                    // const editorData = editor.getData()


                    for (var i = 0; i <= counter; i++) {
                        $(`#details${i}`).val(ckeditors[i].getData());


                    }
                    $('#schedulesForm').submit()
                });



                $(document).on('change', '[id^="basis"]', function() {
                    var selectedOption = $(this).val();

                    var counter = this.id.match(/\d+/)[0]; // Extract the counter from the ID
                    if (selectedOption == '1') {
                        $(`#valuediv${counter}`).show();
                        $(`#valuediv${counter}`).attr('required', 'required');
                        $(`#amtdiv${counter}`).hide();
                        $(`#amtdiv${counter}`).removeAttr('required');


                    } else {
                        $(`#valuediv${counter}`).hide();
                        $(`#valuediv${counter}`).removeAttr('required');
                        $(`#amtdiv${counter}`).show();
                        $(`#amtdiv${counter}`).attr('required', 'required');

                    }
                });
                $(document).on('keyup', 'input.amount', function() {
                    var inputVal = $(this).val();
                    var numericVal = inputVal.replace(/\D/g, '');
                    var formattedVal = numericVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    $(this).val(formattedVal);
                });
                $(document).on('keyup', 'input.prem', function() {
                    var inputVal = $(this).val();
                    var numericVal = inputVal.replace(/\D/g, '');
                    var formattedVal = numericVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    $(this).val(formattedVal);
                });
                $('.formdatas').on('keyup', 'input[name="schedule_value[]"]', function() {

                    // Get the index of the current input field
                    var index = $(this).closest('.formdata').index();


                    // Get the rate and item value
                    var rate = parseFloat($('#rate' + index).val());
                    var numericVal = $(this).val().replace(/,/g, '');
                    var itemValue = parseFloat(numericVal);

                    // Calculate premium
                    var premium = rate / 100 * itemValue;

                    // var numericVal = premium.toFixed(2).replace(/\D/g, '');
                    var formattedVal = premium.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    // Update the "Premium" input field with the calculated premium
                    $('#premium' + index).val(formattedVal); // Adjust decimal places as needed
                });

                $('.formdatas').delegate('.remove-item', 'click', function(e) {
                    e.preventDefault()

                    //$(this).parent().parent().parent().remove();
                    $(this).closest('.formdata').remove();
                });

            });
            $('[id^="basis"]').trigger('change');



        })

        function initializeCKEditor(counter) {
            ClassicEditor
                .create(document.querySelector(`.schedule-descr${counter}`), {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote',
                        'undo', 'redo'
                    ],
                    fontFamily: {
                        options: ['default', 'Tahoma']
                    },
                    fontSize: {
                        options: ['default', '12px', 'small', 'big'],
                        supportAllValues: true
                    },
                    fontSize_default: '12px',
                    fontFamily_default: 'Tahoma'
                })
                .then(editor => {
                    ckeditors[counter] = editor;

                    // Set default font family and size
                    editor.model.change(writer => {
                        writer.setSelectionAttribute('fontFamily', 'Tahoma');
                        writer.setSelectionAttribute('fontSize', '12px');
                    });
                })
                .catch(err => {
                    console.error(err.stack);
                });
        }

        function setCKEditorValue(counter, value) {
            if (ckeditors[counter]) {
                ckeditors[counter].setData(value);
            } else {
                console.error(`CKEditor instance for counter ${counter} is not initialized.`);
            }
        }
    </script>
@endsection
