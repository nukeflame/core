@extends('layouts.app')

@section('content')
    <div>
        <nav class="breadcrumb">
            <a class="breadcrumb-item" href="{{ route('customer.info') }}">{{ $customer->name }} </a><span> ➤ </span>
            <span>
                {{ 'NEW CLAIM' }}
            </span>
        </nav>
    </div>
    <div class="container">
        <div class="form-group" style="padding:21px">
            <form id="register_claim" action="{{ route('claim.register') }}" method="post">
                <div class="form-group">
                    <div class="row">
                        <input type="text" name="customer_id" id="customer_id" value="{{ $customer->customer_id }} "
                            hidden>

                        {{-- choose cover to claim --}}
                        <div class="col-sm-4" id="coversec">
                            <label class="required" id="coverlabel">Cover</label>
                            <select class="form-select select2" name="cover_no" id="cover_no" @required(true)>
                                <option selected value="">Choose Cover</option>
                                @foreach ($covers as $cover)
                                    <option value="{{ $cover->cover_no }}">{{ $cover->cover_no }} -
                                        {{ $cover->insured_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- date of loss --}}
                        <div class="col-sm-3">
                            <label class="required">Date of Loss</label>
                            <input type="date" class="form-inputs" name="loss_date" id="loss_date" @required(true)>
                        </div>


                        {{-- Endorsement Number --}}
                        <div class="col-sm-3">
                            <label class="required">Endorsement Number</label>
                            <select class="form-select select2" name="endorsement_no" id="endorsement_no" @required(true)>
                            </select>
                            <span class="text-danger">{{ $errors->first('endorsement_no') }}</span>
                        </div>

                    </div>

                    <div class="row">

                        {{-- Cover From --}}
                        <div class="col-sm-3">
                            <label class="required">Cover From</label>
                            <input type="date" class="form-control" name="cover_from" id="cover_from" @required(true)
                                @readonly(true)>
                        </div>

                        {{-- Cover To --}}
                        <div class="col-sm-3">
                            <label class="required">Cover To</label>
                            <input type="date" class="form-control" name="cover_to" id="cover_to" @required(true)
                                @readonly(true)>
                        </div>

                        {{-- Insured Name --}}
                        <div class="col-sm-3">
                            <label class="required">Type Of Business</label>
                            <input type="text" class="form-control" name="type_of_bus" id="type_of_bus" @required(true)
                                @readonly(true)>
                        </div>

                        {{-- Insured Name --}}
                        <div class="col-sm-3">
                            <label class="required">Insured Name</label>
                            <input type="text" class="form-control" name="insured_name" id="insured_name"
                                @required(true) @readonly(true)>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Date Notified to Insurer --}}
                        <div class="col-sm-3">
                            <label class="required">Date Notified to Insurer</label>
                            <input type="date" class="form-inputs" name="date_notify_insurer" id="date_notify_insurer"
                                @required(true)>
                        </div>

                        {{-- Date Notified to Reinsurer --}}
                        <div class="col-sm-3">
                            <label class="required">Date Notified to Reinsurer</label>
                            <input type="date" class="form-inputs" name="date_notify_reinsurer"
                                id="date_notify_reinsurer" @required(true)>
                        </div>

                        {{-- Cause of Loss --}}
                        <div class="col-sm-6">
                            <label class="required">Cause of Loss</label>
                            <input type="text" class="form-inputs" name="cause_of_loss" id="cause_of_loss"
                                @required(true)>
                        </div>

                    </div>

                    <div class="row">

                        {{-- loss description --}}
                        <div class="col-sm-5">
                            <label class="required">Loss Description</label>
                            <textarea type="text" class="form-inputs" name="loss_desc" id="loss_desc" @required(true)> </textarea>
                        </div>

                    </div>


                </div>

                <div class="row">
                    <div class="col-sm-3">
                        <button type="submit" id="save_claim" class="btn-dark form-control">SAVE</button>
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

            $("#loss_date").change(function() {
                var cover_no = $("select#cover_no option:selected").attr('value');
                var loss_date = $("#loss_date").val();
                $('#endorsement_no').empty();

                if ($(this).val() != '') {
                    $('#endorsement_no').prop('disabled', false)
                    $.ajax({
                        url: "{{ route('claim.get_loss_endorsements') }}",
                        data: {
                            'cover_no': cover_no,
                            'loss_date': loss_date
                        },
                        type: "get",
                        dataType: 'json',
                        success: function(resp) {
                            const loss_endorses = resp.endorsements || [];
                            const endorsementSelect = $('#endorsement_no');

                            endorsementSelect.empty()
                                .append($('<option>', {
                                    value: '',
                                    text: '-- Select Endorsement --'
                                }));

                            loss_endorses.forEach(function(value) {
                                const year = value.cover_from ? value.cover_from
                                    .substring(0, 4) : '';
                                const optionText = `${value.endorsement_no} - ${year}`;

                                endorsementSelect.append($('<option>', {
                                    value: value.endorsement_no,
                                    text: optionText
                                }));
                            });

                            $('.section').trigger("chosen:updated");
                        },
                        error: function(resp) {
                            console.error;
                        }
                    })
                }
            });

            $("select#endorsement_no").change(function() {
                var endorsement_no = $(this).val();

                $.ajax({
                    url: "{{ route('claim.get_endorsement_info') }}",
                    data: {
                        'endorsement_no': endorsement_no
                    },
                    type: "get",
                    dataType: 'json',
                    success: function(resp) {
                        const loss_endorses = resp.endorsement_info || [];

                        $.each(loss_endorses, function(i, valcov) {
                            $('#cover_from').val(valcov.cover_from);
                            $('#cover_to').val(valcov.cover_to);
                            $('#insured_name').val(valcov.insured_name);
                            $('#type_of_bus').val(valcov.type_of_bus);
                        });

                        $('.section').trigger("chosen:updated");
                    },
                    error: function(resp) {
                        console.error;
                    }
                })

            });




            // <!-- END -->
        });
    </script>
@endpush
