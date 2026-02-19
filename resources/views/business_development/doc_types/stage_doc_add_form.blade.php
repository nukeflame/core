@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Stage Document</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Stage Document</a></li>
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
            <form id="store_schedule_header" action="{{ route('stage.doc.store') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ isset($StageDocuments) ? $StageDocuments->id : '' }}">

                <div class="form-group">
                    <h4>Stage Document Details</h4>
                    <div class="row gy-4 partner_info">
                        <div class="col-xl-4">
                            <label class="form-label">Stage</label>
                            @php
                                $selectedStage = isset($StageDocuments)
                                    ? strtolower((string) $StageDocuments->stage)
                                    : '';
                                $selectedStage = match ($selectedStage) {
                                    '0', 'all' => 'all',
                                    '1' => 'lead',
                                    '2' => 'proposal',
                                    '3' => 'negotiation',
                                    '4', 'final_stage' => 'final',
                                    default => $selectedStage,
                                };
                            @endphp
                            <select class="form-inputs select2" aria-label="stage" id="stage" name="stage" required>
                                <option value="">Select Stage</option>
                                <option value="all" {{ $selectedStage === 'all' ? 'selected' : '' }}>All Stages</option>
                                <option value="lead" {{ $selectedStage === 'lead' ? 'selected' : '' }}>Lead</option>
                                <option value="proposal" {{ $selectedStage === 'proposal' ? 'selected' : '' }}>Proposal
                                </option>
                                <option value="negotiation" {{ $selectedStage === 'negotiation' ? 'selected' : '' }}>
                                    Negotiation
                                </option>
                                <option value="final" {{ $selectedStage === 'final' ? 'selected' : '' }}>Final</option>
                            </select>
                        </div>
                        <div class="col-xl-4">
                            <label class="form-label">Doc Type</label>
                            <select class="form-inputs select2" id="doc_type" name="doc_type">
                                <option value="">-- Doc type --</option>
                                @foreach ($Documents as $doc)
                                    <option value="{{ $doc->id }}"
                                        {{ isset($StageDocuments) && $StageDocuments->doc_type == $doc->id ? 'selected' : '' }}>
                                        {{ $doc->doc_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-4">
                            <label class="form-label">Business type</label>
                            <select class="form-inputs select2" name="type_of_bus[]" id="type_of_bus" required multiple>
                                <option value="">Select</option>
                                @foreach ($types_of_bus as $type_of_bus)
                                    <option value="{{ $type_of_bus->bus_type_id }}"
                                        {{-- {{isset($StageDocuments) && $StageDocuments->type_of_bus == $type_of_bus->bus_type_id ? 'selected' : '' }}> --}}
                                        {{ isset($StageDocuments->type_of_bus) && in_array($type_of_bus->bus_type_id, $StageDocuments->type_of_bus) ? 'selected' : '' }}>

                                        {{ $type_of_bus->bus_type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-4">
                            <label class="form-label">mandatory</label>
                            <select class="form-inputs select2" name="mandatory" id="mandatory" required>
                                <option value="">Select</option>
                                <option value="Y"
                                    {{ isset($StageDocuments) && $StageDocuments->mandatory == 'Y' ? 'selected' : '' }}>Yes
                                </option>
                                <option value="N"
                                    {{ isset($StageDocuments) && $StageDocuments->mandatory == 'N' ? 'selected' : '' }}>No
                                </option>
                            </select>
                        </div>
                        <div class="col-xl-4">
                            <label class="form-label">category</label>
                            <select class="form-inputs select2" name="category_type" id="category_type" required>
                                <option value="">Select</option>
                                <option value="1"
                                    {{ isset($StageDocuments) && (string) $StageDocuments->category_type === '1' ? 'selected' : '' }}>
                                    Quotation
                                </option>
                                <option value="2"
                                    {{ isset($StageDocuments) && (string) $StageDocuments->category_type === '2' ? 'selected' : '' }}>
                                    Fac Offer
                                </option>
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
            $(".typeof_select").select2({
                placeholder: "-- Select --"
            });

            $("#store_schedule_header").validate({
                rules: {
                    stage: {
                        required: true,
                        maxlength: 100,
                    },
                    doc_type: {
                        required: true,
                    },
                    mandatory: {
                        required: true
                    },
                    category_type: {
                        required: true
                    },
                    type_of_bus: {
                        required: true
                    }
                },
                messages: {
                    stage: {
                        required: "document stage is required",
                        maxlength: "max length is 100",
                        // pattern: "Customer name should contain letters only"
                    },
                    doc_type: {
                        required: "doc_type is required",
                    },
                    mandatory: {
                        required: "amount field is required",
                    },
                    category_type: {
                        required: "category is required"
                    },
                    type_of_bus: {
                        required: "type of bus is required"
                    }
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
