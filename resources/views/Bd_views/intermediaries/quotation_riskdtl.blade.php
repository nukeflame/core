@extends('layouts.intermediaries.base')
@section('content')

    <div class="card mt-2 mx-2">
        <div class="card-header">
            <h4>MOTOR POLICY</h4>
        </div>

        <div class="card-body p-2 step mb-3" id="vehinfo">
            @if ($source == 'client')
                <h5 class="text-start">Client details</h5>
            @else
                <h5 class="text-start">Lead details</h5>
            @endif
            <hr>
            <div class="row">
                @if ($source == 'client')
                    <x-QuotationInputDiv>
                        <x-Input name="fname" id="" inputLabel="Full Name" req=""
                            value="{{ $clientdtls->full_name }}" readonly />
                    </x-QuotationInputDiv>

                    <x-QuotationInputDiv>
                        <x-Input name="lobno" id="" inputLabel="ID Number" req=""
                            value="{{ $clientdtls->id_value }}" readonly />
                    </x-QuotationInputDiv>

                    <x-QuotationInputDiv>
                        <x-Input name="email" id="" inputLabel="Email" req=""
                            value="{{ $clientdtls->email }}" readonly />
                    </x-QuotationInputDiv>

                    <x-QuotationInputDiv>
                        <x-Input name="phone" id="" inputLabel="Phone Number" req=""
                            value="{{ $clientdtls->phone_1 }}" readonly />
                    </x-QuotationInputDiv>
                @else
                    <x-QuotationInputDiv>
                        <x-Input name="fname" id="" inputLabel="Full Name" req=""
                            value="{{ $lead->full_name }}" readonly />
                    </x-QuotationInputDiv>
                    <x-QuotationInputDiv>
                        <x-Input name="email" id="" inputLabel="Email" req="" value="{{ $lead->email }}"
                            readonly />
                    </x-QuotationInputDiv>

                    <x-QuotationInputDiv>
                        <x-Input name="phone" id="" inputLabel="Phone Number" req=""
                            value="{{ $lead->phone_number }}" readonly />
                    </x-QuotationInputDiv>
                @endif

            </div>
            <h5 class="text-start mt-3">Cover details</h5>
            <hr>

            <form id="vehicle_details" enctype="multipart/form-data">

                @csrf
                <div class="row">
                    <x-QuotationInputDiv>
                        <x-SelectInput name="brk_company" id="brk_company" req="required" inputLabel="Company">
                            <option selected disabled>Select a Company</option>
                            @foreach ($compdata as $compdat)
                                @if ($policydtl != '')
                                    @if ($policydtl->company_code == $compdat->company_id)
                                        <option value="{{ $compdat->company_id }}" selected>{{ $compdat->company_name }}
                                        </option>
                                    @endif
                                @else
                                    <option value="{{ $compdat->company_id }}">{{ $compdat->company_name }}</option>
                                @endif
                            @endforeach

                        </x-SelectInput>
                    </x-QuotationInputDiv>
                    @if (auth()->user()->hasRole('admin'))
                        <x-QuotationInputDiv>
                            <x-SearchableSelect name="branch" id="branch" req="required" inputLabel="Branch">
                                <option value="">Select branch</option>
                                @foreach ($branches as $branch)
                                    @if ($policydtl != '')
                                        @if ($policydtl->branch == $branch->branch_code)
                                            <option value="{{ $branch->branch_code }}" selected>{{ $branch->description }}
                                            </option>
                                        @endif
                                    @else
                                        <option value="{{ $branch->branch_code }}">{{ $branch->description }}</option>
                                    @endif
                                @endforeach
                            </x-SearchableSelect>
                        </x-QuotationInputDiv>
                    @endif

                    <x-QuotationInputDiv>
                        <x-SelectInput class="select2" name="classbs" id="classbs" req="required" inputLabel="Class">
                            <option value="">Select Class...</option>
                            @foreach ($classes as $class)
                                @if ($policydtl != '')
                                    @if ($policydtl->class == $class->class_code)
                                        <option value="{{ $class->class_code }}" selected>
                                            {{ $class->class_description }}</option>
                                    @endif
                                @else
                                    <option value="{{ $class->class_code }}">
                                        {{ $class->class_description }}</option>
                                @endif
                            @endforeach
                        </x-SelectInput>
                    </x-QuotationInputDiv>


                    <input type="text" name="source" id="source" value="{{ $source }}" hidden>
                    <input type="text" name="type" id="type" value="{{ $type }}" hidden>
                    <input type="text" name="prev_pol" id="prev_pol"
                        @if ($policydtl != '') value="{{ $policydtl->endorsement_no }}" @endif hidden>
                    <input type="text" name="agent" id="agent" " value="1" hidden>
                        <input type="text" name="client_no" id="client_no" value="{{ $clientdtls->global_customer_id }}" hidden>



                        <x-QuotationInputDiv>
                            <x-SelectInput name="ast_marker" id="plan" req="required" inputLabel="Plan">
                                     @if ($policydtl != '')
                    @if (trim($policydtl->ast_marker) == 'A')
                        <option value="A" selected>Annual</option>
                        <option value="S">Short term</option>
                    @endif
                    @if (trim($policydtl->ast_marker) == 'S')
                        <option value="A">Annual</option>
                        <option value="S" selected>Short term</option>
                    @endif
                @else
                    <option value="">Select plan</option>
                    <option value="A">Annual</option>
                    <option value="S">Short term</option>
                    @endif
                    </x-SelectInput>
                    </x-QuotationInputDiv>


                    <x-QuotationInputDiv>
                        @if ($policydtl != '')
                            <x-DateInput name="eff_date" id="eff_date" placeholder="Enter Transaction Date"
                                inputLabel="Transaction Date" req="required" value="{{ $policydtl->effective_date }}" />
                        @else
                            <x-DateInput name="eff_date" id="eff_date" placeholder="Enter Transaction Date"
                                inputLabel="Transaction Date" req="required" />
                        @endif
                    </x-QuotationInputDiv>
                    <x-QuotationInputDiv>
                        @if ($policydtl != '')
                            <x-DateInput name="period_from" id="period_from" placeholder="Enter Period From"
                                inputLabel="Period From" req="required" value="{{ $policydtl->period_from }}" />
                        @else
                            <x-DateInput name="period_from" id="period_from" placeholder="Enter Period From"
                                inputLabel="Period From" req="required" value="" />
                        @endif
                    </x-QuotationInputDiv>
                    <x-QuotationInputDiv>
                        @if ($policydtl != '')
                            <x-DateInput name="period_to" id="period_to" placeholder="Enter Period To"
                                inputLabel="Period To" req="required" value="{{ $policydtl->period_to }}" />
                        @else
                            <x-DateInput name="period_to" id="period_to" placeholder="Enter Period To"
                                inputLabel="Period To" req="required" value="" />
                        @endif
                    </x-QuotationInputDiv>
                    <x-QuotationInputDiv>
                        @if ($policydtl != '')
                            <x-DateInput name="renewal_date" id="renewal_date" placeholder="Enter Renewal Date"
                                inputLabel="Renewal Date" req="required" value="{{ $policydtl->renewal_date }}"
                                disabled />
                        @else
                            <x-DateInput name="renewal_date" id="renewal_date" placeholder="Enter Renewal Date"
                                inputLabel="Renewal Date" req="required" value="" disabled />
                        @endif
                    </x-QuotationInputDiv>
                    <x-QuotationInputDiv>
                        @if ($policydtl != '')
                            <x-NumberInput name="cover_days" id="cover_days" placeholder="Cover days"
                                inputLabel="Cover Days" req="required" value="{{ $policydtl->days_covered }}"
                                disabled />
                        @else
                            <x-NumberInput name="cover_days" id="cover_days" placeholder="Cover days"
                                inputLabel="Cover Days" req="required" disabled />
                        @endif
                    </x-QuotationInputDiv>


                    <x-QuotationInputDiv>
                        <x-SelectInput class="select2" name="currency" id="currency" req="required"
                            inputLabel="Currency">
                            <option disabled>Select Currency...</option>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->currency_code }}"
                                    @if ($currency->base_currency == 'Y') selected @endif
                                    shortcode="{{ $currency->short_description }}">
                                    {{ $currency->description }}</option>
                            @endforeach
                        </x-SelectInput>
                    </x-QuotationInputDiv>

                    <x-QuotationInputDiv>
                        <x-SearchableSelect class="select2" name="vat" id="vat" req="required"
                            inputLabel="VAT">
                            <option value="">Select VAT</option>
                            @foreach ($vats as $vat)
                                @if ($policydtl != '')
                                    @if ($policydtl->vat_code == $vat->vat_code)
                                        <option value="{{ $vat->vat_code }}" selected>
                                            {{ $vat->vat_description }}</option>
                                    @endif
                                @else
                                    <option value="{{ $vat->vat_code }}">
                                        {{ $vat->vat_description }}</option>
                                @endif
                            @endforeach
                        </x-SearchableSelect>
                    </x-QuotationInputDiv>





                </div>
            </form>
            <div class="card-footer">
                <x-button.back type="button" class="save_car col-2" id="backdiv"></x-button>
                    <x-button.submit type="button" class="save_car col-2 float-end" id="add_veh">Submit</x-button>
            </div>
        </div>

    </div>


    @component('components.modal', [
        'id' => 'benefits',
        'class' => 'modal-lg',
    ])
        @slot('title', 'Risk Details')

        @slot('body')
            <!-- Body of the modal -->
            <div class="row">
                <div class="col-md-6 col-sm-12 mt-2 ">
                    <div class="form-check">
                        <label class="form-check-label" for="">Apply on all vehicles</label>
                        <input class="form-check-input add_all_benefits" type="checkbox" value="">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 mt-2 ">
                    <select class="selectpicker form-control" id="multicar" multiple data-live-search="true" name="asso_id">
                        <option disabled>Select Vehicles</option>

                    </select>

                </div>
            </div>


        @endslot

        @slot('footer')
            <!-- Footer of the modal -->

            <x-button.back type="button" id="markcomplete">Close</x-button>
                <x-button.submit type="button" id="markcomplete">Save changes</x-button>
                @endslot
            @endcomponent
            @component('components.modal', [
                'id' => 'benefits',
                'class' => 'modal-lg',
            ])
                @slot('title', 'Risk Details')

                @slot('body')
                    <!-- Body of the modal -->
                    <div class="row">
                        <div class="col-md-6 col-sm-12 mt-2 ">
                            <div class="form-check">
                                <label class="form-check-label" for="">Apply on all vehicles</label>
                                <input class="form-check-input add_all_benefits" type="checkbox" value="">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 mt-2 ">
                            <select class="selectpicker form-control" id="multicar" multiple data-live-search="true"
                                name="asso_id">
                                <option disabled>Select Vehicles</option>

                            </select>

                        </div>
                    </div>


                @endslot

                @slot('footer')
                    <!-- Footer of the modal -->

                    <x-button.back type="button" id="markcomplete">Close</x-button>
                        <x-button.submit type="button" id="markcomplete">Save changes</x-button>
                        @endslot
                    @endcomponent
                    @component('components.modal', [
                        'id' => 'uploadmod',
                        'class' => 'modal-lg',
                    ])
                        @slot('title', 'Re upload Risk Details')

                        @slot('body')
                            <div class="card" id="reupload_card">

                                <div class="card-body">
                                    <div class="row">
                                        <form id="reupload" method="POST" enctype="multipart/form-data"
                                            action="javascript:void(0)">

                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col"></th>
                                                        <th scope="col"></th>
                                                        <th scope="col"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>

                                                            <x-input.text type='file' id="actual-btn" name="quote_fleet"
                                                                class="mt-2" required />
                                                        </td>
                                                        <td></td>
                                                        <td>
                                                            <x-button.submit type="button" class="mt-3"
                                                                id="resubmit">Submit</x-button>
                                                        </td>
                                                    </tr>


                                                </tbody>
                                            </table>
                                        </form>

                                    </div>

                                </div>




                            </div>

                        @endslot

                        @slot('footer')
                            <!-- Footer of the modal -->

                            <x-button.back type="button" id="markcomplete">Close</x-button>
                            @endslot
                        @endcomponent



                    @endsection
                    @section('page_scripts')
                        <script type="text/javascript">
                            function getCoverType() {
                                let val = $('#covtype option:selected').attr('value')
                                let cls = $('#classbs option:selected').attr('value')
                                let est = $('#est_value').val()
                                let usage = $('#usage').val();

                                $.ajax({
                                    type: 'GET',
                                    data: {
                                        'id': val,
                                        'class': cls,
                                        'usage': usage
                                    },
                                    url: "{!! route('get.rate') !!}",
                                    success: function(data) {
                                        if (data.status == 1) {
                                            let rate = data.rate;
                                            let mimimun = data.mimimun;
                                            let basis = data.basis;
                                            if ($('[name="fleetswitch"]').is(':checked')) {


                                            } else {
                                                if (basis == "A") {
                                                    $('#premium_rate').val(100)
                                                    $('#est_value').val(0)
                                                    $('.non_tpo_div').fadeOut();
                                                    $('.tpo_div').fadeIn();
                                                    $(".add_benefit").prop("disabled", true);
                                                    $('.non_tpo_div').find('.checkempty').each(function() {
                                                        $(this).removeClass('checkempty');
                                                    });
                                                } else {
                                                    $('#premium_rate').val(rate)
                                                    $('.non_tpo_div').fadeIn();
                                                    $('.tpo_div').fadeOut();
                                                    $('.tpo_div').find('.checkempty').each(function() {
                                                        $(this).removeClass('checkempty');
                                                    });

                                                }
                                            }



                                        }
                                    }
                                });

                            }
                            $("#currency").change(function() {
                                var curr = $(this).find('option:selected');
                                var myTag = curr.attr("shortcode");

                                $('#selcurr').text(myTag);
                            });



                            $('#add_car_div').on('click', function() {
                                $('.add_vehicle_div').show()
                                $('#add_veh').show()
                                $('#backdiv').show()
                                $('.next').hide()
                                $('.display_vehicle').hide()

                            });
                            $('#backdiv').on('click', function() {
                                clearfields()
                                $('.add_vehicle_div').hide()
                                $('#add_veh').hide()
                                $('#backdiv').hide()
                                $('.next').show()
                                $('.display_vehicle').show()

                            })

                            $('#add_veh').on('click', function() {
                                let isValid = true;
                                let errorMessage = '';

                                $('#vehicle_details').find('input[required]').each(function() {
                                    if ($(this).val() === '') {
                                        isValid = false;
                                        errorMessage = 'Please fill in all required fields.';
                                        return false; // Exit the loop
                                    }
                                });
                                if (!isValid) {
                                    toastr.error(errorMessage)

                                } else {

                                    let data = $('#vehicle_details').serialize()
                                    $.ajax({
                                        type: 'GET',
                                        data: data,
                                        url: "{!! route('agent.stage_single_process') !!}",
                                        success: function(data) {
                                            // console.log(data)
                                            if (data.status == 1) {
                                                //toastr.success("Policy "+data.policy_no +" added successfully")
                                                window.location = "{{ route('policy.addrisk', '') }}" + "/" + data
                                                    .policy_no;


                                            } else if (data.status == 2) {
                                                $('.add_vehicle_div').hide()
                                                $('.display_vehicle').show()
                                                $('#add_veh').hide()
                                                $('.next').show()
                                                swal.fire({
                                                    icon: "error",
                                                    title: "Error",
                                                    html: "<h6 class='text-success'>Vehicle Already Exists </h6>"
                                                })
                                            } else {
                                                $('.add_vehicle_div').hide()
                                                $('.display_vehicle').show()
                                                $('#add_veh').hide()
                                                $('.next').show()

                                                toastr.warning(data.message, {
                                                    timeOut: 5000
                                                });

                                            }
                                        }

                                    });
                                }
                            });

                            function clearfields() {
                                $('#make,#model').select2();
                                $('#make').val("").trigger('change');
                                $('#model').val("").trigger('change');
                                $('#body_type').val("");
                                $('#reg_no').val("");
                                $('#est_value').val("");
                                $('#tpo_prem').val("");
                                $('#chasis_no').val("");
                                $('#engine_no').val("");
                                $('#seat_cap').val("");
                                $('#cc').val("");
                                $('#motive').val("");
                                $('#met_color').val("");
                                $('#owner').val("");

                                // alert(1);

                            }


                            function removeCommas(str) {
                                while (str.search(",") >= 0) {
                                    str = (str + "").replace(',', '');
                                }
                                return str;
                            };

                            function numberWithCommas(num) {
                                var amt = num.toFixed(2);
                                return amt.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
                            }




                            $(document).ready(function() {
                                var today = new Date().toISOString().split('T')[0];

                                // Set the value of the input field
                                $('#eff_date').val(today);

                                // Make the input field read-only
                                $('#eff_date').prop('readonly', true);
                                $('#period_from').on('change', function() {
                                    var periodFrom = new Date($(this).val());

                                    if (!isNaN(periodFrom)) {
                                        var periodTo = new Date(periodFrom);
                                        periodTo.setFullYear(periodTo.getFullYear() + 1);

                                        // Ensure the date format is yyyy-mm-dd
                                        var day = String(periodTo.getDate()).padStart(2, '0');
                                        var month = String(periodTo.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                                        var year = periodTo.getFullYear();

                                        var formattedDate = year + '-' + month + '-' + day;
                                        $('#period_to').val(formattedDate);
                                        var timeDiff = periodTo.getTime() - periodFrom.getTime();
                                        var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)); // Convert milliseconds to days
                                        $('#cover_days').val(daysDiff);
                                        // Set renewal date to one day after periodTo
                                        var renewalDate = new Date(periodTo);
                                        renewalDate.setDate(renewalDate.getDate() + 1);

                                        var renewalDay = String(renewalDate.getDate()).padStart(2, '0');
                                        var renewalMonth = String(renewalDate.getMonth() + 1).padStart(2,
                                        '0'); // Months are zero-based
                                        var renewalYear = renewalDate.getFullYear();

                                        var formattedRenewalDate = renewalYear + '-' + renewalMonth + '-' + renewalDay;
                                        $('#renewal_date').val(formattedRenewalDate);

                                    }
                                });
                                $('#period_to').on('change', function() {
                                    var periodTo = new Date($(this).val());
                                    var periodFrom = new Date($("#period_from").val());

                                    if (!isNaN(periodFrom)) {
                                        var timeDiff = periodTo.getTime() - periodFrom.getTime();
                                        var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)); // Convert milliseconds to days
                                        $('#cover_days').val(daysDiff);
                                        var renewalDate = new Date(periodTo);
                                        renewalDate.setDate(renewalDate.getDate() + 1);

                                        var renewalDay = String(renewalDate.getDate()).padStart(2, '0');
                                        var renewalMonth = String(renewalDate.getMonth() + 1).padStart(2,
                                        '0'); // Months are zero-based
                                        var renewalYear = renewalDate.getFullYear();

                                        var formattedRenewalDate = renewalYear + '-' + renewalMonth + '-' + renewalDay;
                                        $('#renewal_date').val(formattedRenewalDate);
                                    } else {
                                        $("#period_to").val("")
                                        toastr.warning('Select Transaction Period From!', {
                                            timeOut: 5000
                                        });

                                    }


                                })
                                $('.fleet').css('display', 'none');

                                var quote_no = $('#quote_no').val()
                                if (quote_no !== null && quote_no !== undefined && quote_no !== "") {
                                    $('#classbs').trigger('change');
                                    $('#branch').trigger('change');


                                }
                                $('#dwn_template').css('display', 'none');
                                var currency = $("#currency option:selected");
                                var myTag = currency.attr("shortcode");
                                $('#selcurr').text(myTag)
                                $('.tpo_div').css('display', 'none');

                                $('.fleet').find('.checkempty').each(function() {
                                    $(this).removeClass('checkempty');
                                });
                                let batch_serial = "BN" + Math.floor(100000 + Math.random() * 900000)
                                $('#batch_no').val(batch_serial)

                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });

                                $('#risks_data_table,#risks_data_table2').DataTable({
                                    processing: true,
                                    serverSide: true,
                                    autoWidth: false,
                                    ajax: {
                                        'url': '{{ route('get.motor.risks', ['source' => $source]) }}',
                                        'data': function(d) {
                                            var quote_no = $('#quote_no').val()
                                            d.quote_no = quote_no
                                        },
                                    },

                                    columns: [{
                                            data: 'reg_no',
                                            name: 'reg_no'
                                        },
                                        {
                                            data: 'make',
                                            name: 'make'
                                        },
                                        {
                                            data: 'model',
                                            name: 'model'
                                        },
                                        // {data:'body_type',name:'body_type'},
                                        {
                                            data: 'annual_prem',
                                            name: 'annual_prem',
                                            render: $.fn.dataTable.render.number(',', '.', 2)
                                        },
                                        {
                                            data: 'action',
                                            name: 'action'
                                        },
                                    ]
                                });
                            });

                            var step = 1;
                            $(".next").on("click", function() {
                                var nextstep = false;
                                var table = $('#risks_data_table').DataTable()
                                var table_length = table.data().count();
                                if (table_length > 0) {
                                    $('#add_vehicle_div').find('.checkempty').each(function() {
                                        $(this).removeClass('checkempty');
                                    });

                                }

                                $('#rsk_count').text(table_length)
                                if (step == 1) {
                                    nextstep = checkForm("vehicle_details");
                                    // nextstep = true;
                                } else if (step == 3) {
                                    nextstep = checkForm("attach_docs");
                                } else {
                                    nextstep = true;
                                }

                                if (nextstep == true) {
                                    if (step < $(".step").length) {
                                        $(".step").show();
                                        $(".step")
                                            .not(":eq(" + step++ + ")")
                                            .hide();
                                    }
                                    hideButtons(step);
                                }
                            });

                            // ON CLICK BACK BUTTON
                            $(".back").on("click", function() {
                                console.log(step);
                                if (step > 1) {
                                    step = step - 2;
                                    console.log(step);
                                    $(".next").trigger("click");
                                }
                                hideButtons(step);
                            });

                            $("#resubmit").on("click", function() {
                                let is_success = checkForm("reupload")
                                if (is_success) {
                                    $("#uploadmod").modal('toggle');
                                    swal.fire({
                                        icon: "success",
                                        title: "Success",
                                        html: "<h6 class='text-success'>Upload successful</h6>"
                                    })
                                }
                                $('.updatedetails').css('display', 'none');

                            });

                            // DISPLAY AND HIDE "NEXT", "BACK" AND "SUMBIT" BUTTONS
                            hideButtons = function(step) {
                                var limit = parseInt($(".step").length);
                                console.log(step, limit);
                                $(".action").hide();
                                if (step < limit) {
                                    $(".next").show();
                                    $(".back").hide();
                                    $(".submit").hide();
                                }
                                if (step > 1) {
                                    $(".back").show();
                                }
                                if (step == limit) {
                                    $(".next").hide();
                                    $(".submit").show();
                                }
                            };


                            function checkForm(val) {

                                var valid = false;
                                if ($('[name="fleetswitch"]').is(':checked')) {
                                    let myForm = $('#' + val)[0];
                                    var batch = $('#batch_no').val()
                                    if (val == "reupload" || (val == "vehicle_details" && batch.length > 1)) {
                                        let formData = new FormData(myForm);
                                        $.ajax({
                                            type: "POST",
                                            url: "{{ route('fleet_upload') }}",
                                            dataType: "JSON",
                                            data: formData,
                                            contentType: false,
                                            processData: false,
                                            async: false,
                                            success: function(resp) {
                                                if (resp.status == 0) {

                                                    $('#est_value').val(resp.value)

                                                    $('#batch_no').val(resp.batch_no)
                                                    valid = true;

                                                    console.log(valid);

                                                    var table = $('#risks_data_table2').DataTable()
                                                    table.ajax.reload();

                                                } else {
                                                    let final = ""
                                                    for (const key in resp) {
                                                        final += `${key}: ${resp[key]}`
                                                        final += '<br><hr>'

                                                    }
                                                    swal.fire({
                                                        icon: "error",
                                                        title: "Fleet Error",
                                                        html: "<h6 class='text-danger'>" + final + "</h6>"
                                                    })
                                                }
                                            }
                                        })
                                    } else {
                                        valid = true
                                    }


                                } else {
                                    valid = true;

                                }



                                // CHECK IF ALL "REQUIRED" FIELD ALL FILLED IN

                                $("#" + val + " .checkempty").each(function() {
                                    if ($(this).val() === "") {
                                        $(this).addClass("is-invalid");
                                        valid = false;
                                    } else {
                                        $(this).removeClass("is-invalid");

                                    }
                                });

                                return valid;
                            }

                            $('.req_doc').on('change', function(e) {
                                var ext = this.value.match(/\.([^\.]+)$/)[1];
                                switch (ext) {
                                    case 'pdf':
                                    case 'png':
                                    case 'jpeg':
                                    case 'jpg':
                                        break;
                                    default:
                                        swal.fire({
                                            icon: 'warning',
                                            text: 'File type not allowed, kindly upload .pdf, .png or .jpg files only.'
                                        })
                                        this.value = ''
                                }
                            })

                            $('#buy_save').on('click', function(e) {
                                $(this).attr('disabled', 'disabled')
                                e.preventDefault();

                                let form_data = new FormData();

                                let vehicle_det = new FormData(document.getElementById('vehicle_details'));
                                let premium_det = new FormData(document.getElementById('premiumdetails'));
                                let docs = new FormData(document.getElementById('attach_docs'));

                                for (var [key, value] of vehicle_det.entries()) {
                                    form_data.append(key, value);
                                }
                                for (var [key, value] of docs.entries()) {
                                    form_data.append(key, value);
                                }
                                for (var [key, value] of premium_det.entries()) {
                                    form_data.append(key, value);
                                }

                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('quoteprocessing.save') }}",
                                    data: form_data,
                                    contentType: false,
                                    processData: false,
                                    success: function(resp) {
                                        if (resp.status == 1) {
                                            swal.fire({
                                                icon: "success",
                                                title: "Quotation details send",
                                                text: "Quotations has been registered pending approval"
                                            })

                                            window.location = "{{ route('Agent.view_quote', '') }}" + "/" + resp.quote_no +
                                                "?source={{ $source }}";
                                        }
                                    }
                                })
                            })

                            $('#classbs').on('change', function() {
                                var cls = $(this).val();
                                var company = $('#brk_company').val();


                                $("#cls").val(cls)
                                $.ajax({
                                    type: 'GET',
                                    data: {
                                        'cls': cls,
                                        'company': company
                                    },
                                    url: "{!! route('agent.checkcommission') !!}",
                                    success: function(data) {
                                        console.log(data)
                                        if (data.status == 1) {
                                            if (data.exists != true) {
                                                toastr.warning('Commission Not Set, Please Set Commission To Proceed!', {
                                                    timeOut: 5000
                                                });
                                                $('#add_veh').prop('disabled', true);



                                            } else {
                                                $('#add_veh').prop('disabled', false);

                                            }

                                        } else {
                                            toastr.error('Error Occurred!', {
                                                timeOut: 5000
                                            });
                                            $('#add_veh').prop('disabled', true);




                                        }

                                    }
                                });

                            });

                            $('#plan').on('change', function() {
                                let plan = $(this).val();
                                if (plan == 'A') {
                                    $('#cover_days').attr('readonly', 'readonly');
                                    $('#cover_days').val(365);
                                } else {
                                    $('#cover_days').removeAttr('readonly');
                                    $('#cover_days').val(0);
                                }
                            });


                            $('.make').on('change', function() {
                                var cls = $(".make option:selected").text();
                                var make = $(".make option:selected").val();
                                console.log(make);
                                $("#clss").val(cls)
                                $.ajax({
                                    type: 'GET',
                                    data: {
                                        'make': make
                                    },
                                    url: "{!! route('fetchmodels') !!}",
                                    success: function(data) {
                                        // $(".model").empty()
                                        $("#model").empty()
                                        // var dropdown1 = $(".model");
                                        // var $dropdown = $("#model");
                                        $("#model").append($("<option />").val('').text('Choose vehicle model'));
                                        $.each(data, function() {
                                            $(".model").append($("<option />").val(this.model).text(this.model));
                                        });
                                    }
                                });
                            });

                            $('.model').on('change', function() {
                                var cls = $(".model option:selected").text();
                                var model = $(".model option:selected").val();

                                $("#clss").val(cls)
                                $.ajax({
                                    type: 'GET',
                                    data: {
                                        'model': model
                                    },
                                    url: "{!! route('fetchbody') !!}",
                                    success: function(data) {
                                        var $dropdown = $("#body_type");
                                        var dropdown1 = $(".body_type");
                                        $('#body_type').empty()
                                        $dropdown.append($("<option />").val('').text('Choose body'));
                                        $.each(data, function() {
                                            dropdown1.append($("<option />").val(this.bodytype).text(this
                                            .bodytype));
                                        });
                                    }
                                });
                            });

                            $('#view_acc').on('click', function() {
                                var registration = $('#reg_no').val();
                                if (registration == 0 || typeof registration == 'undefined') {
                                    toastr.warning('You have not entered a valid vehicle registration number', {
                                        timeOut: 5000
                                    });
                                } else {
                                    $('#reg_acc').val(registration)
                                    $('#edit_reg_acc').val(registration)
                                    //getAccessoryDataTable();
                                    $('#accesorysdata_modal').modal({
                                        backdrop: 'static'
                                    });
                                    $('#accesorysdata_modal').modal('show');
                                }

                            });

                            $('#branch').on('change', function() {
                                let branch = $(this).val()

                                $.ajax({
                                    type: "GET",
                                    data: {
                                        'branch': branch
                                    },
                                    url: "{{ route('get_branch_agents') }}",
                                    success: function(resp) {
                                        if (resp.status == 1) {
                                            $("#agent").empty()
                                            $("#agent").append($("<option />").val('').text('Select Agent'));
                                            var quote_no = $('#quote_no').val()

                                            var agent = null
                                            if (quote_no !== null && quote_no !== undefined && quote_no !== "") {

                                                agent = "{{ $policydtl ? $policydtl->agent : null }}"

                                            }

                                            $.each(resp.agents, function() {
                                                if (agent == this.lob_intermediary_id) {
                                                    $("#agent").append($("<option />").val(this.lob_intermediary_id)
                                                        .text(this.full_name).prop('selected', true));

                                                } else {
                                                    $("#agent").append($("<option />").val(this.lob_intermediary_id)
                                                        .text(this.full_name));

                                                }
                                            });
                                        }
                                    }
                                })

                            })

                            $('#add_acc_btn').on('click', function() {
                                var acc_type = $('#acc_type').val()
                                //var acc_premium =$('#acc_premium').val()
                                var acc_make = $('#acc_make').val()
                                var acc_value = $('#acc_value').val()

                                if (localStorage.getItem("final_accessory") === null) {
                                    var final_accessory = [];
                                } else {
                                    var jsonString = localStorage.getItem("final_accessory");

                                    // Parse the JSON string back to JS object
                                    var final_accessory = JSON.parse(jsonString);
                                }
                                acc_array = {}
                                acc_array['accessory_type'] = acc_type
                                acc_array['accessory_make'] = acc_make
                                acc_array['accessory_value'] = acc_value

                                final_accessory.push(acc_array);
                                localStorage.setItem("final_accessory", JSON.stringify(final_accessory));

                                console.log(final_accessory);


                            });

                            $('#sumins').keyup(function() {
                                var amt = $("#sumins").val()
                                var rate = $("#rate").val()
                                var prem = amt * rate / 100
                                $("#car_premium").val(prem.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","))
                            });

                            $('#editbtn').on('click', function() {
                                $('#editbtn').hide()
                                $('#sumdiv').hide()
                                $('#editdiv').show()
                            });

                            $('#cancelbtn').on('click', function() {
                                $('#editbtn').show()
                                $('#sumdiv').show()
                                $('#editdiv').hide()
                            });

                            $('#savebtn').on('click', function() {
                                var val = $('#edit_value').val()
                                var prem = val * $('#premium_rate').val() / 100
                                var batch_no = $('#batch_no').val();
                                var reg_no = $('#edit_reg').val()
                                $('#savebtn').text('updating...').button("refresh");

                                $.ajax({
                                    type: 'GET',
                                    data: {
                                        'val': val,
                                        'reg_no': reg_no,
                                        'batch_no': batch_no
                                    },
                                    url: "{!! route('edit.risk') !!}",
                                    success: function(data) {
                                        console.log(data);
                                        if (+data.status == 1) {
                                            $('#rsk_value').text(val)
                                            $('#rsk_prem').text(prem)
                                            $('#edit_prem').val(prem)
                                            $('#rsk_tprem').text(Number(prem) + Number(data.ben_amount))
                                            var table = $('#risks_data_table').DataTable()
                                            table.ajax.reload();

                                        }
                                        $('#savebtn').text('save').button("refresh");
                                        $('#editbtn').show()
                                        $('#sumdiv').show()
                                        $('#editdiv').hide()


                                    }
                                });
                            })


                            $('#fleetswitch').on('click', function() {
                                if ($('[name="fleetswitch"]').is(':checked')) {

                                    $('.notfleet').fadeOut();
                                    $('.fleet').fadeIn();
                                    $('#dwn_template').fadeIn();
                                    $('.add_vehicle_div').show()
                                    $('.display_vehicle').hide()

                                    $('.notfleet').find('.checkempty').each(function() {
                                        $(this).removeClass('checkempty');
                                    });
                                } else {
                                    $('.notfleet').fadeIn();
                                    $('.fleet').fadeOut();
                                    $('#dwn_template').fadeOut();
                                    $('.fleet').find('.checkempty').each(function() {
                                        $(this).removeClass('checkempty');
                                    });
                                }
                            });
                            $('#applydisc').on('click', function() {
                                var discvalue = removeCommas($(this).closest('tr').find('td:eq(1)').text());
                                var totprem = removeCommas($('#total_premium').text());
                                $('#discount_total').text("");


                                //compute VAT amount
                                // let total_premium = removeCommas($('#total_premium').text())
                                // computeVAT(vat, total_premium)


                            });

                            function computeDiscount(discamt, totalamt) {
                                if ($('[name="applydisc"]').is(':checked')) {
                                    $('#discount_total').text(numberWithCommas(discamt));
                                    totprem = Number(totalamt) - Number(discamt);
                                    $('#total_premium').text(numberWithCommas(totprem))
                                    $('#total_prem_field').val(totprem)
                                    $('#discount').val(discamt)

                                } else {
                                    $('#discount_total').text(0);
                                    totprem = Number(totalamt) + Number(discamt);
                                    $('#total_premium').text(numberWithCommas(totprem));
                                    $('#discount').val(discamt)
                                    $('#total_prem_field').val(totprem)

                                }
                            }

                            // function computeVAT(vat, totalamt) {
                            //     let tot_vat = Number(totalamt) *Number(vat);
                            //     console.log(totalamt, vat, tot_vat);
                            //     $('#total_taxes').text(numberWithCommas(tot_vat));

                            //     // let premium_after_tax = Number(totalamt) + tot_vat
                            //     // $('#total_premium').text(numberWithCommas(premium_after_tax))
                            //     // $('#total_prem_field').val(premium_after_tax)


                            // }

                            $('#adbenefit').on('click', function() {
                                $(".colelmt").removeClass("collapse.show")
                                $(".colelmt").addClass("collapse")

                                var n = $(document).height();
                                $('html, body').animate({
                                    scrollTop: n
                                }, 200);

                            });
                            $("#finish").on('click', function() {
                                let i = 0
                                var quote_no = $('#quote_no').val()
                                var table = $('#risks_data_table').DataTable()
                                var recordCount = table.rows().count();
                                if (recordCount < 1) {
                                    toastr.error('You must add atleast One Vehicle', {
                                        timeOut: 5000
                                    });
                                } else {
                                    window.location = "{{ route('Agent.view_quote') }}" + "/" + quote_no +
                                        "?source={{ $source }}";

                                }

                            })

                            $('#reupvehicle').on('click', function() {
                                var n = $(document).height();
                                $('html, body').animate({
                                    scrollTop: n
                                }, 200);

                            });



                            $('#risks_data_table').on('click', '.updatedetails', function() {

                                var itemno = $(this).closest('tr').find('td:eq(0)').text();
                                var batch_no = $('#batch_no').val();
                                var value = $(this).closest('tr').find('td:eq(4)').text();
                                amt = value.replace(/\,/g, '')
                                amt = Number(amt)
                                var rskprem = amt * $('#premium_rate').val() / 100;
                                $.ajax({
                                    type: 'GET',
                                    data: {
                                        'itemno': itemno,
                                        'batch_no': batch_no
                                    },
                                    async: false,
                                    url: "{!! route('view.risk') !!}",
                                    success: function(data) {
                                        console.log(value)
                                        $('#rsk_make').text(data.make)
                                        $('.make').val(data.make)
                                        $('.make').trigger('change');
                                        $('#rsk_model').text(data.model)

                                        $('.body_type').val(data.body_type)
                                        $('#rsk_reg').text(data.reg_no)
                                        $('#edit_reg').val(data.reg_no)
                                        $('#rsk_value').text(value)
                                        $('#edit_value').val(amt)
                                        $('#rsk_prem').text(rskprem)
                                        $('#edit_prem').val(rskprem)
                                        $('#rsk_tprem').text(rskprem)
                                        console.log("check here");
                                        console.log(data.Benefits);
                                        $('#risk_extensions').empty()
                                        for (const ben of data.Benefits) {


                                            let tp = Number(ben.ben_amount) + Number(rskprem)
                                            $('#rsk_tprem').text(tp)



                                            //$('#ext_risk').css('display', 'block')
                                            $('#risk_extensions').children('.alert').addClass('d-none')
                                            $('#risk_extensions').append(
                                                '<div class="d-flex border border-bottom justify-content-between text-align-center added_benefit"><div><div class="added_desc">' +
                                                ben.ben_desc + '</div><div class="fw-bold text-success added_amount">' +
                                                ben.ben_amount +
                                                '</div></div><div class="float-right"><button class="btn btn-sm remove_risk_benefit" ben_id="' +
                                                ben.ben_id + '"><span class="fa fa-times"></span></button></div></div>')
                                        }




                                        $('#exampleModal').modal('show');

                                    }
                                });


                                // window.location="{{ route('view.risk', '') }}"+"/"+itemno;

                            })

                            const actualBtn = document.getElementById('actual-btn');

                            const fileChosen = document.getElementById('file-chosen');

                            actualBtn.addEventListener('change', function() {
                                fileChosen.textContent = this.files[0].name

                            })
                        </script>
                        <style>
                            td div {
                                float: left;
                            }

                            a {
                                /* color: #FFFFFF; */
                                text-decoration: none;
                            }

                            .upload_btn {
                                background-color: grey;
                                color: white;
                                padding: 0.5rem;
                                font-family: sans-serif;
                                border-radius: 0.3rem;
                                cursor: pointer;
                                margin-top: 1rem;
                            }

                            .dropup .hide-toggle.dropdown-toggle::after {
                                display: none !important;
                            }

                            #file-chosen {
                                margin-left: 0.3rem;
                                font-family: sans-serif;
                            }

                            body {
                                width: 100vw;
                                height: 100vh;
                                background: #fafbff;
                            }

                            .form-group.required:after {
                                content: "*";
                                color: red;
                            }

                            /* table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
    } */
                            th,
                            td {
                                padding: 5px;
                            }

                            th {
                                text-align: left;
                            }
                        </style>
                    @endsection
