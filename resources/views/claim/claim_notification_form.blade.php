@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">New Claim Notification</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href>{{ $customer->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        New Claim Notification
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="form-group" style="padding:21px">
        <form id="register_claim" action="{{ route('claim.notification.register') }}" method="post">
            {{ csrf_field() }}
            <div class="form-group">
                <div class="row claim-notification-form">
                    <input type="text" name="customer_id" id="customer_id" value="{{ $customer->customer_id }} " hidden>
                    {{-- choose cover to claim --}}
                    <div class="col-sm-4" id="coversec">
                        <label class="form-label required" id="coverlabel">Cover</label>
                        <select class="form-inputs select2" name="cover_no" id="cover_no" @required(true)>
                            <option selected value="">Choose Cover</option>
                            @foreach ($covers->unique('cover_no') as $cover)
                                <option value="{{ $cover->cover_no }}">{{ $cover->cover_no }} -
                                    {{ $cover->insured_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label required">Date of Loss</label>
                        <input type="date" class="form-inputs" name="loss_date" id="loss_date" @required(true)>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label required">Endorsement Number</label>
                        <select class="form-inputs select2" name="endorsement_no" id="endorsement_no" @required(true)>
                            <option value="">--Select Endorsement No.</option>
                        </select>
                        <span class="text-danger">{{ $errors->first('endorsement_no') }}</span>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label required">Cedant Claim No.</label>
                        <input class="form-inputs" name="cedant_claim_no" id="cedant_claim_no" required>
                        <span class="text-danger">{{ $errors->first('endorsement_no') }}</span>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <label class="form-label required">Cover From</label>
                        <input type="date" class="form-inputs disable" name="cover_from" id="cover_from"
                            @required(true) @readonly(true)>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label required">Cover To</label>
                        <input type="date" class="form-inputs disable" name="cover_to" id="cover_to" @required(true)
                            @readonly(true)>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label required">Type Of Business</label>
                        <input type="text" class="form-inputs disable" name="type_of_bus" id="type_of_bus"
                            @required(true) @readonly(true)>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label required">Insured Name</label>
                        <input type="text" class="form-inputs disable" name="insured_name" id="insured_name"
                            @required(true) @readonly(true)>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <label class="form-label required">Date Notified to Reinsurer</label>
                        <input type="date" class="form-inputs" name="date_notify_insurer" id="date_notify_insurer"
                            @required(true)>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label required">Date Reported</label>
                        <input type="date" class="form-inputs" name="date_notify_reinsurer" id="date_notify_reinsurer"
                            @required(true)>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label required">Cause of Loss</label>
                        <input type="text" class="form-inputs" name="cause_of_loss" id="cause_of_loss"
                            @required(true)>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <label class="form-label required">Loss Description</label>
                        <textarea type="text" class="form-inputs resize-none" name="loss_desc" id="loss_desc" @required(true)> </textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <button type="submit" id="save_claim" class="btn btn-dark btn-block fs-14">Save</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            const lossDate = document.getElementById('loss_date');
            const insurerNotifyDate = document.getElementById('date_notify_insurer');
            const reinsurerNotifyDate = document.getElementById('date_notify_reinsurer');
            lossDate.setAttribute('max', formattedDate);
            insurerNotifyDate.setAttribute('max', formattedDate);
            reinsurerNotifyDate.setAttribute('max', formattedDate);

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
                        method: "GET",
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
                        error: function(xhr, status, error) {}
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
                    error: function(resp) {}
                })

            });
        });
    </script>
@endpush
