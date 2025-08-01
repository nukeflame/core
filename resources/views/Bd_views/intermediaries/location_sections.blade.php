@extends('layouts.app')

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>NON-MOTOR POLICY</h4>
        </div>
        <div class="card-body p-4">
            <div id="section_details">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-start my-2">Section details</h5>
                    </div>
                    <div>
                        @if ($class->gpa == 'Y' && $prem_basis != 'SCH')
                            <a
                                href="{{ route('employee_schedule', ['quote_no' => $quote->quote_no, 'source' => $source]) }}">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="add_schedule">
                                    <i class="fa fa-plus"></i>
                                    Employees schedule
                                </button>
                            </a>
                        @endif
                        <a
                            href="{{ route('location_benefit', ['qstring' => Crypt::encrypt('policy_no=' . $quote->policy_no . '&source=' . $source . '&location=' . $location)]) }}">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="add_benefit_button">
                                <i class="fa fa-plus"></i>
                                Add Benefits
                            </button>
                        </a>
                        <x-button.back class="mx-3 float-end btn-sm" id="loc_back"><i class="fa fa-up-left"></i></x-button>
                    </div>
                </div>
                <hr>
                <form id="section_details_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="policy_no" value="{{ $quote->policy_no }}" id="quote">
                    <input type="hidden" name="location" value="{{ $location }}" id="location">
                    <div class="row">
                        <div id="sectionrows">
                            <div class="row" style="margin-top: 10px;" id="section0">
                                <input class="form-control" type="hidden" name="source" id="source"
                                    value={{ $source }}>

                                <div class="col-md-3">
                                    <x-SearchableSelect name="classgrp[]" id="classgrp_0" req="required"
                                        inputLabel="Group Section" class="locsect classgroup">
                                        <option selected value="">Select Group Section</option>
                                        @foreach ($class_groups as $grp)
                                            <option value="{{ $grp->classgrp }}">{{ $grp->group_description }}</option>
                                        @endforeach
                                    </x-SearchableSelect>
                                </div>

                                <div class="col-md-3">
                                    <x-SearchableSelect name="section[]" id="section_0" req="required" inputLabel="Section"
                                        class="grpsection locsect">
                                        <option selected value="">Select Section</option>
                                    </x-SearchableSelect>
                                </div>
                                @if ($prem_basis != 'SCH')
                                    <div class="col-md-1">
                                        <x-NumberInput name="units[]" id="units_0" data-counter="0" value="1"
                                            inputLabel="Units" class="locsect units" req="required" />
                                    </div>
                                @endif

                                <div class="col-md-1">
                                    <x-NumberInput name="rate[]" id="rate_0" data-counter="0" value=""
                                        inputLabel="Rate" class="locsect rate" req="required" />
                                </div>

                                @if ($class->gpa == 'Y' && $prem_basis == 'SCH')
                                    <div class="col-md-2">
                                        <label for="">Template</label>
                                        <a class="btn btn-outline-secondary form-control down_template"
                                            href="{{ route('schedule_temp') }}"><i class="fa fa-arrow-down"></i>Download
                                            template</a>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">Upload Excel <i class="text-danger">*</i> </label>
                                        <input class="form-control" type="file" name="emp_sched" id="emp_sched_0"
                                            required>
                                    </div>
                                @endif

                                @if ($prem_basis != 'SCH')
                                    <div class="col-md-2">
                                        <x-Input name="sum_insured[]" id="sum_insured_0" inputLabel="Sum Insured"
                                            class="locsect sectionsum" req="required" />
                                    </div>

                                    <div class="premium col-md-2">
                                        <label for="premium">Premium</label>
                                        <div class="input-group">
                                            <input name="premium[]" id="premium_0" required value=""
                                                class="form-control premfield locsect" readonly />
                                            <span class="btn btn-secondary" id="add_section">&plus;</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <hr>
            <div>
                <x-button.submit class="save_loc col-md-2 float-end" id="save_sections">Save</x-button>
            </div>
        </div>

        <div class="card-footer">

            <nav>
                <div class="nav nav-tabs mt-3" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                        type="button" role="tab" aria-controls="nav-home" aria-selected="true">Sections</button>
                    @if ($class->gpa == 'Y')
                        <button class="nav-link" id="nav-sched-tab" data-bs-toggle="tab" data-bs-target="#schedule"
                            type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Employee
                            Schedule</button>
                    @endif
                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                        type="button" role="tab" aria-controls="nav-profile"
                        aria-selected="false">Benefits</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="mt-3 table-responsive">
                        <table class="table table-striped  table-hover" id="benefits_data_table" width="100%">
                            <thead class="">
                                <tr>
                                    <th>Benefit</th>
                                    <th>Rate</th>
                                    <th>Premium</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <div class="m-3 table-responsive">
                        <table class="table table-striped  table-hover" id="sections_data_table" width="100%">
                            <thead class="">
                                <tr>
                                    <th>Section Name</th>
                                    <th>Sum Insured</th>
                                    <th>Rate</th>
                                    <th>Premium</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>


                <div class="tab-pane fade" id="schedule" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="mt-3 table-responsive">
                        <table class="table table-striped  table-hover" id="employee_data_table" width="100%">
                            <thead class="">
                                <tr>
                                    <th>Item No.</th>
                                    @if ($prem_basis == 'SCH')
                                        <th>Section No.</th>
                                    @endif
                                    <th>Employee No.</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Earnings</th>
                                    <th>Benefit</th>
                                    <th>Period From</th>
                                    <th>Period to</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('page_scripts')
        <!-- <sript src="{{ asset('admincast/js/myfunctions.js') }}"></script> -->
        <script>
            $(document).ready(function() {
                let policy_no = $("#quote").val();
                let location = $("#location").val();
                let classbs = "{{ $quote->class }}"
                // let quote_si = {!! $quote->sum_insured !!}
                let quote_si = 10000;
                let selected_sections_array = new Array()
                let all_sections = {!! $classsect !!}
                let bond = "{{ $class->bond }}"
                let git = "{{ $class->git }}"
                let bond_sum = 0
                let git_sum = 0

                $.ajax({
                    type: "get",
                    url: "{{ route('get_selected_sections') }}",
                    data: {
                        'policy_no': policy_no,
                        'location': location
                    },
                    type: "get",
                    success: function(data) {
                        $.each(data, function(index, value) {
                            section_no = value.section_code
                            selected_sections_array.push(section_no)
                        })
                    }
                })

                if (quote_si <= 0) {
                    $("#add_benefit_button").hide()
                }

                if (bond == "Y") {
                    bond_sum = {!! $bond_sum_insured !!}
                }

                if (git == "Y") {
                    git_sum = {!! $git_sum_insured !!}
                }


                $('#sectionrows').on('change', '.classgroup', function() {
                    let grp = $(this).val()
                    var id = $(this).attr('id')
                    var id_length = id.length
                    var rowID = id.slice(8, id_length)

                    $.ajax({
                        type: "GET",
                        data: {
                            'class': classbs,
                            'classgrp': grp
                        },
                        url: "{{ route('get_class_sections') }}",
                        success: function(resp) {
                            if (resp.status == 1) {
                                $("#section" + rowID).empty()
                                $("#section" + rowID).append($("<option />").val('').text(
                                    'Select Section'));
                                $.each(resp.sections, function() {
                                    $("#section" + rowID).append($("<option />").val(this
                                            .section_no).text(this.section_description)
                                        .attr('rate', this.rate).attr('min_rate', this
                                            .minimum_rate));
                                });
                            }
                        }
                    })

                })

                var i = 0
                $('#add_section').on('click', function(e) {
                    i++;
                    if (i > 1) {
                        var classgrp = $('#classgrp_' + (i - 1)).val()
                        var section = $('#section_' + (i - 1)).val()
                        var rate = $('#rate_' + (i - 1)).val()
                        var sum_insured = $('#sum_insured_' + (i - 1)).val()
                    } else if (i == 1) {
                        var classgrp = $('#classgrp_0').val()
                        var section = $('#section_0').val()
                        var rate = $('#rate_0').val()
                        var sum_insured = $('#sum_insured_0').val()
                    }
                    if (classgrp == '' || section == '' || rate == '' || sum_insured == '') {
                        i--;
                        Swal.fire({
                            icon: 'warning',
                            text: 'Please fill all details'
                        });
                    } else {
                        $('#sectionrows').append(
                            '<div class="row" style="margin-top: 10px;" id="section' + i + '">' +
                            '<div class="col-md-3">' +
                            '<label>Group Section</label>' +
                            '<select name="classgrp[]" id="classgrp_' + i +
                            '" class="form-control locsect select2 classgroup" required>' +
                            '<option selected value="">Select Group Section</option>' +
                            '@foreach ($class_groups as $grp)' +
                            '<option value="{{ $grp->classgrp }}"  rate="{{ $grp->rate }}">{{ $grp->group_description }}</option>' +
                            '@endforeach' +
                            '</select>' +
                            '</div>' +
                            '<div class="col-md-3" >' +
                            '<label>Section</label>' +
                            '<select name="section[]" id="section_' + i +
                            '" class="form-control select2 grpsection locsect" required>' +
                            '<option selected value="">Select Section</option>' +
                            '</select>' +
                            '</div>' +
                            '<div  class="col-md-1">' +
                            '<label>Units</label>' +
                            '<input type="number" name="units[]" id="units_' + i +
                            '" data-counter="0" value="1" class="form-control units locsect" >' +
                            '</div>' +
                            '<div  class="col-md-1">' +
                            '<label>Rate</label>' +
                            '<input type="number" name="rate[]" id="rate_' + i +
                            '" data-counter="0" class="form-control rate locsect" >' +
                            '</div>' +
                            '<div class="col-md-2">' +
                            '<label>Sum Insured</label>' +
                            '<div class="input-group">' +
                            '<input type="text" name="sum_insured[]"  id="sum_insured_' + i +
                            '" class="form-control sectionsum locsect">' +
                            '</div>' +
                            '</div>' +
                            '<div class="premium col-md-2">' +
                            '<label>Premium</label>' +
                            '<div class="input-group">' +
                            '<input type="text" name="premium[]" id="premium_' + i +
                            '" value=""  class="premfield form-control" readonly>' +
                            '<span class="btn btn-danger" id="remove_section">&minus;</span>' +
                            '</div>' +
                            '</div>' +
                            '</div>'
                        )

                        $('.select2').select2()

                        for (x = 0; x <= i; x++) {
                            var sectionss = $("#section_" + i).val();
                            selected_sections_array.push(sectionss);
                            var all_added_section_array = selected_sections_array.filter(function(added_sect,
                                index, self) {
                                return index === self.indexOf(added_sect);
                            });
                        }

                        $('#section_' + i).empty();
                        $('#section_' + i).append('<option selected value="">Select Section</option>');

                        $.each(all_sections, function(index, value) {
                            if ((all_sections.length + 1 != all_added_section_array.length) &&
                                all_sections.length + 1 > all_added_section_array.length) {
                                if ((!($.inArray(value.section_no, all_added_section_array) >= 0))) {
                                    console.log(value.classgrp, classgrp);
                                    if (value.classgrp == classgrp) {
                                        $('#section_' + i).append($('<option>').val(value.section_no)
                                            .text(value.description).attr('rate', value.rate).attr(
                                                'min_rate', value.minimum_rate));

                                    }
                                }
                            }
                        });


                        $('#section_' + i).select2()


                    }
                });

                $('body').on('click', '#remove_section', function() {
                    $(this).parent().parent().parent().remove();
                    sumPremium()
                })

                $('#sectionrows').on('change', '.locsect', function() {
                    sumPremium()
                })

                $('#sectionrows').on('change', '.grpsection', function() {
                    let rate = $("option:selected", this).attr('rate')
                    let min_rate = $("option:selected", this).attr('min_rate')
                    // let min_rate = $(this).attr('min_rate')
                    var id = $(this).attr('id')
                    var id_length = id.length
                    var rowID = id.slice(7, id_length)

                    $('#rate' + rowID).val(rate)
                    $('#rate' + rowID).attr('min', min_rate)


                    if (bond == "Y") {
                        $("#sum_insured" + rowID).val(bond_sum)
                        $("#sum_insured" + rowID).attr('readonly', true)
                    }

                    if (git == "Y") {
                        $("#sum_insured" + rowID).val(git_sum)
                        $("#sum_insured" + rowID).attr('readonly', true)
                    }

                    $('#sum_insured' + rowID).trigger('change')

                })

                $('#sectionrows').on('change', '.rate', function() {
                    console.log("rate");
                    let rate = $(this).val()
                    let min_rate = $(this).attr('min')

                    if (parseFloat(rate) < parseFloat(min_rate)) {
                        swal.fire({
                            icon: "warning",
                            text: "Minimum rate is " + min_rate
                        })
                        $(this).val(min_rate)
                    }

                })



                $('#save_sections').on('click', function() {
                    $(this).attr('disabled', 'disabled')
                    let valid = checkForm("section_details_form")
                    if (valid == true) {
                        let data = new FormData(document.getElementById('section_details_form'));
                        $.ajax({
                            type: 'POST',
                            data: data,
                            url: "{!! route('save_loc_sections') !!}",
                            contentType: false,
                            processData: false,
                            success: function(data) {
                                if (data.status == 1) {
                                    toastr.success("Section(s) added successfully")
                                    window.location.reload();
                                } else {
                                    swal.fire({
                                        icon: "error",
                                        text: data.message
                                    });
                                }

                            }
                        })

                    } else {
                        swal.fire({
                            icon: "warning",
                            text: "Please fill all fields"
                        });
                    }
                    $(this).attr('disabled', false)

                })

                $("#loc_back").on('click', function() {
                    window.history.back();
                })



                $('#sections_data_table').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: {
                        'url': '{{ route('get_loc_sections') }}',
                        'data': function(d) {
                            d.policy_no = policy_no
                            d.location = location
                        },
                    },

                    columns: [{
                            data: 'section_description',
                            name: 'description'
                        },
                        {
                            data: 'sum_insured',
                            name: 'sum_insured'
                        },
                        {
                            data: 'rate',
                            name: 'rate'
                        },
                        {
                            data: 'premium',
                            name: 'premium'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        },
                    ]
                })


                $('#benefits_data_table').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: {
                        'url': '{{ route('get_loc_benefits', ['source' => $source]) }}',
                        'data': function(d) {
                            d.policy_no = policy_no
                            d.location = location
                        },
                    },

                    columns: [{
                            data: 'ext_description',
                            name: 'ext_description'
                        },
                        {
                            data: 'rate',
                            name: 'rate'
                        },
                        {
                            data: 'ben_amount',
                            name: 'ben_amount'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        },
                    ]
                })




                $('#employee_data_table').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: {
                        'url': '{{ route('get_loc_emp_schedule') }}',
                        'data': function(d) {
                            d.quote_no = quote
                        },
                    },

                    columns: [{
                            data: 'section_number',
                            name: 'section_number'
                        },
                        {
                            data: 'staff_no',
                            name: 'staff_no'
                        },
                        {
                            data: 'employee_name',
                            name: 'employee_name'
                        },
                        {
                            data: 'employee_role',
                            name: 'employee_role'
                        },
                        {
                            data: 'earning',
                            name: 'earnings'
                        },
                        {
                            data: 'benefit_amount',
                            name: 'benefit_amount'
                        },
                        {
                            data: 'period_from',
                            name: 'period_from'
                        },
                        {
                            data: 'period_to',
                            name: 'period_to'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        }
                    ],

                    createdRow: function(row, data, dataIndex) {
                        $(row).prepend('<td>' + (dataIndex + 1) + '</td>');
                        if ('{{ $prem_basis }}' != "SCH") {
                            $(row).find('td:eq(1)').hide();
                        }
                    }
                })

                $('#sections_data_table').on('click', '.deletesection', function(e) {
                    e.preventDefault()
                    var location = $(this).attr('data-location');
                    var quote_no = quote;
                    var section_no = $(this).attr('data-section');
                    var classgrp = $(this).attr('data-grp');
                    Swal.fire({
                        title: "Warning!",
                        html: "Are You Sure You Want to delete this section?",
                        icon: "warning",
                        confirmButtonText: "Yes"
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: 'GET',
                                data: {
                                    'location': location,
                                    'quote_no': quote_no,
                                    'section_no': section_no,
                                    'classgrp': classgrp
                                },
                                url: "{!! route('remove_loc_sect') !!}",
                                success: function(response) {
                                    if (response.status == 1) {
                                        toastr.success('Deleted Successfully', {
                                            timeOut: 5000
                                        });
                                        $('#sections_data_table').DataTable().ajax.reload();
                                        $('#benefits_data_table').DataTable().ajax.reload();
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    Swal.fire({
                                        title: "Error",
                                        text: textStatus,
                                        icon: "error"
                                    });
                                }
                            });
                        }
                    })


                });


                $('#benefits_data_table').on('click', '.deletebenefit', function(e) {
                    e.preventDefault()
                    var ben_id = $(this).attr('data-benefit');
                    Swal.fire({
                        title: "Warning!",
                        html: "Are You Sure You Want to delete this benefit?",
                        icon: "warning",
                        confirmButtonText: "Yes"
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: 'GET',
                                data: {
                                    'location': location,
                                    'quote_no': quote,
                                    'ben_id': ben_id
                                },
                                url: "{!! route('remove_loc_benefit') !!}",
                                success: function(response) {
                                    if (response.status == 1) {
                                        toastr.success('Deleted Successfully', {
                                            timeOut: 5000
                                        });
                                        $('#benefits_data_table').DataTable().ajax.reload();
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    Swal.fire({
                                        title: "Error",
                                        text: textStatus,
                                        icon: "error"
                                    });
                                }
                            });
                        }
                    })


                });
            });

            function sumPremium() {
                let sum = 0
                $('.sectionsum').each(function(i, obj) {

                    var id = $(this).attr('id')
                    var id_length = id.length
                    var rowID = id.slice(11, id_length)
                    let sinsured = $(this).val()
                    sinsured = sinsured.replaceAll(',', '')
                    console.log(sinsured)
                    let rate = $('#rate' + rowID).val();
                    let prem = parseFloat(rate) * parseFloat(sinsured) * ({!! $quote->days_covered !!} / 365) / 100
                    if (prem > 0) {
                        $('#premium' + rowID).val(numberWithCommas(prem))
                        sum = sum + prem
                    }

                });
            }

            function checkForm(val) {
                let valid = true;
                $("#" + val + " .checkempty").each(function() {
                    if ($(this).val() === "") {
                        $(this).addClass("is-invalid");
                        valid = false;
                    } else {
                        $(this).removeClass("is-invalid");
                    }
                });

                console.log(valid);
                return valid;
            }

            function numberWithCommas(str) {
                if (isNaN(str)) {
                    return '';
                }
                str = str.toString();
                var s = str;
                var s2 = '';

                if (str.indexOf('.') != -1) {
                    str = str.split('.');
                    s = str[0];

                    if (str[1] == 'NaN' || str[1] == "" || str[1] == null || isNaN(str[1])) {
                        s2 = '.';
                    } else {
                        s2 = '.' + str[1];
                    }
                }
                s = s.toString().replace(",", "");

                if (s.length > 6) {
                    s = s.replace(",", "");
                }

                if (s.length > 11) {
                    s = s.replace(",", "");
                }

                if (s.length > 14) {
                    s = s.replace(",", "");
                }

                if (s.length > 17) {
                    s = s.replace(",", "");
                }

                s = s.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
                formatedNumber = s + s2;
                if (formatedNumber == 'NaN' || formatedNumber == "") {
                    formatedNumber = '';
                }
                return formatedNumber;
            }
        </script>
    @endsection
