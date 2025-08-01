@extends('layouts.intermediaries.base')

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>NON-MOTOR POLICY</h4>
        </div>
        <div class="card-body p-4">
            <div id="employee_schedule">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="text-start my-2">Employee schedule</h5>
                    </div>
                </div>
                <hr>
                @if($class->gpa == "Y" || $class->wiba_policy == "Y")
                    <div>
                        <h6>Upload Schedule</h6>
                        <div>
                            <a href="{{ route('schedule_temp')}}">
                                <button class="btn btn-sm btn-secondary"><i class="fa fa-arrow-down"></i> Download template</button>
                            </a>
                            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#uploadschedmodal">Upload schedule</button>
                        </div>
                    </div>
                    <hr>
                @endif
                <form id="employee_schedule_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="quote_no" value="{{ $quote->quote_no }}" id="quote">
                    <div class="card-body" id="employeediv">
                        <div class="row my-2">
                            <x-QuotationInputDiv>
                                <x-Input class="wiba_dec"   name="staff_no[]" id="staff_no_0"  inputLabel="Staff/Pay Number" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-Input class="wiba_dec"  name="employee_name[]" id="employee_name_0"  onkeyup="this.value = this.value.toUpperCase()"  inputLabel="Employee Name" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="wiba_dec" class="position" name="position[]" id="position_0"  onkeyup="this.value = this.value.toUpperCase()" inputLabel="Employee Position" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="wiba_dec" class="earning" name="earning[]" id="earning_0"  onkeyup="this.value=numberWithCommas(this.value)" inputLabel="Earnings" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="wiba_dec" class="multiple" name="multiple[]" id="multiple_0" inputLabel="Multiple of Salary" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <label class="required">Benefit</label>
                                <div class="input-group">
                                    <input  name="benefit[]" id="benefit_0" required class="wiba_dec form-control benefit"  />
                                    <span class="btn btn-primary" id="add_employee">&plus;</span>
                                </div>
                            </x-QuotationInputDiv>
                        </div>
                    </div> 
                    <hr>
                    <div>
                        <x-button.submit class="col-md-2 float-end" id="save_schedule">Save</x-button>
                        <x-button.back class="col-md-2 float-end mx-3" id="sections_back">Back</x-button>
                    </div>
                    
                </form>
            </div>
        </div> 
    </div>

    <div id="uploadschedmodal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #cfd7e0 ">
                    <h5 class="modal-title">Upload Schedule</h5>
                </div>

                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <form enctype="multipart/form-data" id="upload_schedule_form"> 
                            <input type="hidden" name="quote_no" value="{{ $quote->quote_no }}" id="quote">
                                @csrf
                                <div class="form-group row mb-0">
                                    <label for="">Employee Schedule file <i> (csv, xls)</i></label>
                                    <input type="file" name="emp_sched" id="emp_sched" class="form-control" required>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="submit_upload_sched" class="btn btn-outline-success col-4">Save</button>
                </div>
                
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
<script>
$(document).ready(function(){
    
    let quote_no = $("#quote").val();

    $("#sections_back").on('click', function(){
        window.location="{{ route('add_location_sections')}}"+"?qstring={{Crypt::encrypt("quote_no=$quote->quote_no&source=$source&location=1")}}"
       
    })

    var counter = 0;
    $('#add_employee').on('click',function(){
        var staff_no = $('#staff_no_'+counter).val()
        var employee_name = $('#employee_name_'+counter).val()
        var position = $('#position_'+counter).val()
        var earning = $('#earning_' + counter).val()

        if (staff_no == '' || employee_name == '' || position == '' || earning == '') {
            toastr.warning('Kindly fill the fields before moving on to the next row', {timeOut: 5500});
        } else {
            counter += 1;

            $('#employeediv').append(
                `<div class="row my-2">
                    <div class="col-md-3">
                        <x-Input class="wiba_dec"   name="staff_no[]" id="staff_no_${counter}"  inputLabel="Staff/Pay Number" req="required"/>
                    </div >
                    
                    <div class="col-md-3">
                        <x-Input class="wiba_dec"  name="employee_name[]" id="employee_name_${counter}"  onkeyup="this.value = this.value.toUpperCase()"  inputLabel="Employee Name" req="required"/>
                    </div >

                    <div class="col-md-3">
                        <x-Input class="wiba_dec" class="position" name="position[]" id="position_${counter}"  onkeyup="this.value = this.value.toUpperCase()" inputLabel="Employee Position" req="required"/>
                    </div >

                    <div class="col-md-3">
                        <x-Input class="wiba_dec" class="earning" name="earning[]" id="earning_${counter}"  onkeyup="this.value=numberWithCommas(this.value)" inputLabel="Earnings" req="required"/>
                    </div >

                    <div class="col-md-3">
                        <x-Input class="wiba_dec" class="multiple" name="multiple[]" id="multiple_${counter}" inputLabel="Multiple of Salary" req="required"/>
                    </div >

                    <div class="col-md-3">
                        <label class="required">Benefit</label>
                        <div class="input-group">
                            <input  name="benefit[]" id="benefit_${counter}" required class="wiba_dec form-control benefit"  />
                            <span class="btn btn-danger" id="remove_employee">&minus;</span>
                        </div>
                    </div>
                </div>`
            );
        }
    });

    $('#save_schedule').on('click', function(e){
        e.preventDefault()
        let form = $("#employee_schedule_form")
        form.validate({
            errorElement: 'span',
            errorClass: 'text-danger fst-italic',
            highlight: function(element, errorClass) {
            },
            unhighlight: function(element, errorClass) {
            }
        });
        if (form.valid() === true){
            let data = $('#employee_schedule_form').serialize()
            $.ajax({
                type: 'POST',
                data:data,
                url: "{!! route('add_employee_schedule')!!}",
                success:function(data){
                    if (data.status == 1) {
                        toastr.success('Details saved.', {
                            timeOut: 5000
                        });
                        window.location = ""
                    }else{
                        swal.fire({
                            icon: "error",
                            title: "Failed",
                            html:"<h6>Check details and try again</h6>"
                        });
                    }

                }
            })
        }
    })

    $('#submit_upload_sched').on('click', function(e){
        e.preventDefault()
        console.log("ian");
        let formdata = document.getElementById('upload_schedule_form')
        formdata  = new FormData(formdata)
        $.ajax({
            type: 'POST',
            data:formdata,
            url: "{!! route('upload_emp_schedule')!!}",
            contentType: false,
            processData: false,
            success:function(data){
                if (data.status == 1) {
                    toastr.success('Details saved.', {
                        timeOut: 5000
                    });
                    window.location="{{ route('add_location_sections')}}"+"?qstring={{Crypt::encrypt("quote_no=$quote->quote_no&source=$source&location=1")}}"
                }else{
                    swal.fire({
                        icon: "error",
                        title: "Failed",
                        html:data.message
                    });
                }

            }
        })
    })
})
</script>
@endsection