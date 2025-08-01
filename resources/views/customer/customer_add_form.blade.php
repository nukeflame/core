@extends('layouts.app', [
    'pageTitle' => 'Create Partner - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Customer</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('customer.info') }}">Customer</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="mt-3">
        <form action="{{ route('customer.store') }}" method="POST" id="partnerForm">
            @csrf
            <div class="card border border-dark custom-card mb-3">
                <div class="card-header">
                    <div class="card-title">Partner Details</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="partnerName" class="form-label">Partner Name</label>
                            <input type="text" class="form-inputs @error('partnerName') is-invalid @enderror"
                                id="partnerName" name="partnerName" value="{{ old('partnerName') }}"
                                placeholder="Customer name">
                            @error('partnerName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="customerType" class="form-label">Type of Customer</label>
                            <div class="card-md">
                                <select class="form-inputs select2 @error('customerType') is-invalid @enderror"
                                    id="customerType" name="customerType[]" multiple>
                                    <option value="" disabled>-- Select --</option>
                                    @foreach ($type_of_cust as $cust_type)
                                        <option value="{{ $cust_type->type_id }}">
                                            {{ $cust_type->type_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('customerType')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="financialRating" class="form-label">Financial Rating</label>
                            <input type="text" class="form-inputs @error('financialRating') is-invalid @enderror"
                                id="financialRating" name="financialRating" value="{{ old('financialRating') }}"
                                placeholder="Financial Rating">
                            @error('financialRating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="agencyRating" class="form-label">Agency Rating</label>
                            <input type="text" class="form-inputs @error('agencyRating') is-invalid @enderror"
                                id="agencyRating" name="agencyRating" value="{{ old('agencyRating') }}"
                                placeholder="Agency Rating">
                            @error('agencyRating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-inputs @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" placeholder="customer@email.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-inputs @error('website') is-invalid @enderror" id="website"
                                name="website" value="{{ old('website') }}" placeholder="https://www.website.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="taxNo" class="form-label">Tax No</label>
                            <input type="text" class="form-inputs @error('taxNo') is-invalid @enderror" id="taxNo"
                                name="taxNo" value="{{ old('taxNo') }}" placeholder="Tax No">
                            @error('taxNo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="incorporationNo" class="form-label">Incorporation No</label>
                            <input type="text" class="form-inputs @error('incorporationNo') is-invalid @enderror"
                                id="incorporationNo" name="incorporationNo" placeholder="Registration No"
                                value="{{ old('incorporationNo') }}">
                            @error('incorporationNo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="street" class="form-label">Street</label>
                            <input type="text" class="form-inputs @error('street') is-invalid @enderror"
                                id="street" name="street" value="{{ old('street') }}"
                                placeholder="Enter street name">
                            @error('street')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- <div class="col-md-3 mb-3">
                            <label for="postal_town" class="form-label">Town</label>
                            <input type="text" class="form-inputs @error('postal_town') is-invalid @enderror"
                                id="postal_town" name="postal_town" value="{{ old('postal_town') }}"
                                placeholder="Enter town name">
                            @error('postal_town')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}


                        <div class="col-md-3 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-inputs @error('city') is-invalid @enderror" id="city"
                                name="city" value="{{ old('city') }}" placeholder="City">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="identityType" class="form-label">Identity Type</label>
                            <div class="card-md">
                                <select class="form-inputs select2 @error('identityType') is-invalid @enderror"
                                    id="identityType" name="identityType">
                                    <option value="" selected disabled>-- Select ID Type --</option>
                                    @foreach ($partnersId as $pId)
                                        <option value="{{ $pId->identification_type }}">
                                            {{ $pId->identification_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('identityType')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="identityNo" class="form-label">Identity No.</label>
                            <input type="text" class="form-inputs @error('identityNo') is-invalid @enderror"
                                id="identityNo" name="identityNo" placeholder="ID No" value="{{ old('identityNo') }}">
                            @error('identityNo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="telephone" class="form-label">Telephone</label>
                            <input type="tel" class="form-inputs @error('telephone') is-invalid @enderror"
                                id="telephone" name="telephone" value="{{ old('telephone') }}"
                                placeholder="+254700000000" />
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="postalCode" class="form-label">Postal Code</label>
                            <input type="text" class="form-inputs @error('postalCode') is-invalid @enderror"
                                id="postalCode" name="postalCode" value="{{ old('postalCode') }}"
                                placeholder="Enter postal code">
                            @error('postalCode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="country" class="form-label">Country</label>
                            <div class="card-md">
                                <select class="form-inputs select2 @error('country') is-invalid @enderror" id="country"
                                    name="country">
                                    <option value="" selected disabled>-- Select Country --</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->country_iso }}"
                                            {{ old('country') == $country->country_iso ? 'selected' : '' }}>
                                            {{ $country->country_iso }} - {{ $country->country_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border border-dark custom-card mb-3">
                <div class="card-header justify-content-between">
                    <div class="card-title">Contact Information</div>
                    <button type="button" class="btn btn-outline-dark" id="addContactBtn">
                        <i class="bx bx-plus me-1"></i> Add Contact
                    </button>
                </div>
                <div class="card-body">
                    <div id="contactsContainer">
                        <div class="contact-item card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                <h6 class="mb-0">Primary Contact</h6>
                                {{-- <button type="button" class="btn btn-sm btn-outline-danger remove-contact">
                                    <i class="bi bi-x"></i>
                                </button> --}}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Contact Name</label>
                                        <input type="text" class="form-inputs" name="contacts[0][name]"
                                            placeholder="Enter contact name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Contact Position</label>
                                        <input type="text" class="form-inputs" name="contacts[0][position]"
                                            placeholder="Enter contact position">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Contact Mobile No</label>
                                        <input type="tel" class="form-inputs" name="contacts[0][mobile]"
                                            placeholder="Enter mobile number">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Contact Email</label>
                                        <input type="email" class="form-inputs" name="contacts[0][email]"
                                            placeholder="Enter contact email">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Department</label>
                                        <div class="card-md">
                                            <select class="form-inputs select2" name="contacts[0][department]">
                                                <option value="" selected disabled>-- Select Department --</option>
                                                <option value="sales">Sales</option>
                                                <option value="marketing">Marketing</option>
                                                <option value="finance">Finance</option>
                                                <option value="technical">Technical</option>
                                                <option value="operations">Operations</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Is Primary Contact</label>
                                        <div class="card-md">
                                            <select class="form-inputs select2" name="contacts[0][isPrimary]">
                                                <option value="1" selected>Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="contacts[0][order]" value="0" class="contact-order">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3 mb-5  text-end">
                <div class="col">
                    <a href="{{ route('customer.info') }}" class="btn btn-light me-2 px-4">Cancel</a>
                    <button type="submit" class="btn btn-dark px-4"><i class="bi bi-save me-1"></i> Submit</button>
                </div>
            </div>
        </form>
    </div>

    <div class="row row-cols-12 d-none">
        <div class="col">
            <form id="store_customer" action="{{ route('customer.store') }}" method="post">
                <div class="form-group">
                    <h4>Partner Details</h4>
                    <div class="row gy-4 partner_info">
                        <div class="col-md-4 mb-3">
                            <div class="col-xl-11">
                                <label class="form-label">Partner Name</label>
                                <input type="text" class="form-inputs" placeholder="Customer name"
                                    aria-label="Customer name" id="name" name="name">
                            </div>
                            <div class="col-xl-11">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-inputs" placeholder="Email" aria-label="email"
                                    id="email" name="email">
                            </div>

                            <div class="col-xl-11 ">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-inputs" placeholder="Street" aria-label="Street"
                                    id="street" name="street">
                            </div>
                            <div class="col-xl-11">
                                <input type="text" class="form-inputs" placeholder="City" aria-label="City"
                                    id="city" name="city">
                            </div>
                            <div class="col-xl-11 mb-3">
                                <input type="text" class="form-inputs" placeholder="Postal Code"
                                    aria-label="Postal Code/ Address" id="postal_address" name="postal_address">
                            </div>
                            <div class="col-xl-11 mb-3">
                                <div class="card-md">
                                    <select class="form-inputs select2" id="country_iso" name="country_iso">
                                        <option value="">-- Select Country --</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->country_iso }}">{{ $country->country_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="col-xl-11">
                                <label class="form-label">Type of Customer</label>
                                <div class="card-md">
                                    <select class="form-inputs section typeof_select" id="type_of_cust"
                                        name="type_of_cust[]" multiple="multiple">
                                        @foreach ($type_of_cust as $cust_type)
                                            <option value="{{ $cust_type->type_id }}">
                                                {{ $cust_type->type_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-11">
                                <label class="form-label">Incorporation No</label>
                                <input type="text" class="form-inputs" placeholder="Registration No"
                                    aria-label="Registration No" id="customer_reg_no" name="customer_reg_no">
                            </div>
                            <div class="col-xl-11">
                                <label class="form-label">Tax No</label>
                                <input type="text" class="form-inputs" placeholder="Tax No" aria-label="Tax No"
                                    id="customer_tax_no" name="customer_tax_no">
                            </div>
                            <div class="col-xl-11">
                                <label class="form-label">Identity Type</label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="id_type" name="identity_no_type">
                                        <option value="">-- Select ID Type --</option>
                                        @foreach ($partnersId as $pId)
                                            <option value="{{ $pId->identification_type }}">
                                                {{ $pId->identification_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-11">
                                <label class="form-label">Identity No.</label>
                                <input type="text" class="form-inputs" placeholder="ID No" aria-label="ID No"
                                    id="identity_no" name="identity_no">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <!-- Add content for the third column here -->
                            <div class="col-xl-11">
                                <label class="form-label">Financial Rating</label>
                                <input type="text" class="form-inputs" placeholder="Financial Rating"
                                    aria-label="Financial Rating" id="financial_rate" name="financial_rate">
                            </div>
                            <div class="col-xl-11">
                                <label class="form-label">Agency Rating</label>
                                <input type="text" class="form-inputs" placeholder="Agency Rating"
                                    aria-label="Agency Rating" id="agency_rate" name="agency_rate">
                            </div>
                            <div class="col-xl-11">
                                <label class="form-label">Website</label>
                                <input type="text" class="form-inputs" aria-label="Website" placeholder="Website"
                                    id="website" name="website">
                            </div>
                            <div class="col-xl-11">
                                <label class="form-label">Telephone</label>
                                <input type="text" class="form-inputs" aria-label="Telephone" placeholder="Telephone"
                                    id="telephone" name="telephone">
                            </div>
                        </div>
                        <div class="form-group contact_info" style="border-left:0px;border-right:0px;">
                            <h6>Section B: Contact Information</h6>
                            <div class="row">
                                <div class="col-md-4 contact_info">
                                    <label id="code_name"> Contact Name</label>
                                    <input class="form-inputs contact_info" name="contact_name"
                                        placeholder="Contact Name" id="contact_name" type="text">
                                </div>
                                <div class="col-md-4 contact_info">
                                    <label id="code_name"> Contact Position</label>
                                    <input class="form-inputs contact_info" name="contact_position"
                                        placeholder="Contact Position" id="contact_position" type="text">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 contact_info">
                                    <label id="code_name"> Contact Mobile No</label>
                                    <input class="form-inputs contact_info" name="contact_mobile_no"
                                        placeholder="Contact Mobile No" id="contact_mobile_no" type="text">
                                </div>
                                <div class="col-md-4 contact_info">
                                    <label id="code_name"> Contact Email</label>
                                    <input class="form-inputs contact_info" name="contact_email"
                                        placeholder="Contact Email" id="contact_email" type="text">
                                </div>
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

            // $("#store_customer").validate({
            //     rules: {
            //         name: {
            //             required: true,
            //             maxlength: 100,
            //         },
            //         street: {
            //             required: true,
            //             maxlength: 50
            //         },
            //         email: {
            //             required: true,
            //             email: true
            //         },
            //         city: {
            //             required: true
            //         },
            //         country_iso: {
            //             required: true
            //         },
            //         customer_reg_no: {
            //             required: true
            //         },
            //         customer_tax_no: {
            //             required: true
            //         },
            //         type_of_cust: {
            //             required: true
            //         },
            //         postal_address: {
            //             required: true
            //         },
            //         agency_rate: {
            //             required: true
            //         },
            //         financial_rate: {
            //             required: true
            //         },
            //         telephone: {
            //             required: true
            //         },
            //         identity_type: {
            //             required: true
            //         },
            //         identity_no: {
            //             required: true
            //         },
            //     },
            //     messages: {
            //         name: {
            //             required: "Customer name is required",
            //             maxlength: "Customer name must be at most 100 characters",
            //             // pattern: "Customer name should contain letters only"
            //         },
            //         street: {
            //             required: "Street is required",
            //             maxlength: "Street must be at most 50 characters"
            //         },
            //         email: {
            //             required: "Email is required",
            //             email: "Invalid Email format !!!"
            //         },
            //         city: {
            //             required: "City is required"
            //         },
            //         country_iso: {
            //             required: "Country is required"
            //         },
            //         customer_reg_no: {
            //             required: "Registration Number is required"
            //         },
            //         customer_tax_no: {
            //             required: "Tax Number is required"
            //         },
            //         type_of_cust: {
            //             required: "Customer Type is required"
            //         },
            //         postal_address: {
            //             required: "Postal Code is required"
            //         },
            //         agency_rate: {
            //             required: "Agency is required"
            //         },
            //         financial_rate: {
            //             required: "Financial rate is required"
            //         },
            //         telephone: {
            //             required: "Telephone is required"
            //         },
            //         identity_type: {
            //             required: "Identity Type is required"
            //         },
            //         identity_no: {
            //             required: "Identity No. is required"
            //         },
            //     },
            //     errorPlacement: function(error, element) {
            //         error.addClass("text-danger");
            //         error.insertAfter(element);
            //     },
            //     highlight: function(element) {
            //         $(element).addClass('error').removeClass('valid');
            //     },
            //     unhighlight: function(element) {
            //         $(element).removeClass('error').addClass('valid');
            //     },
            //     submitHandler: function(form, e) {
            //         e.preventDefault();
            //         var isConfirmed = confirm("Are you sure you want to submit the form?");
            //         if (isConfirmed) {
            //             $.ajax({
            //                 url: $(form).attr('action'),
            //                 type: 'POST',
            //                 data: new FormData(form),
            //                 processData: false,
            //                 contentType: false,
            //                 success: function(response) {
            //                     console.log(response)
            //                     // if (response.success) {
            //                     //     window.location.href = '{{ route('customer.info') }}';
            //                     // } else {
            //                     //     alert(response.message || 'Error submitting form');
            //                     // }
            //                 },
            //                 error: function(xhr) {
            //                     if (xhr.status === 422) {
            //                         const errors = xhr.responseJSON.errors;
            //                         $('.is-invalid').removeClass('is-invalid');
            //                         $('.invalid-feedback').remove();

            //                         console.log(errors)
            //                         Object.keys(errors).forEach(field => {
            //                             const input = $(`[name="${field}"]`);
            //                             input.addClass('is-invalid');
            //                             input.after(
            //                                 `<div class="invalid-feedback">${errors[field][0]}</div>`
            //                             );
            //                         });

            //                         toastr.error('Please check the form for errors');
            //                     } else {
            //                         toastr.error(
            //                             'An error occurred while submitting the form');
            //                     }
            //                 }
            //             });
            //         } else {
            //             return false;
            //         }
            //     }
            // });

            let contactIndex = 0;

            $('#addContactBtn').on('click', function() {
                contactIndex++;

                // Determine the title based on the number of contacts
                let contactTitle = (contactIndex === 0) ? 'Primary Contact' :
                    (contactIndex === 1) ? 'Secondary Contact' :
                    `Additional Contact ${contactIndex-1}`;

                // Create the new contact HTML
                const newContact = `
                    <div class="contact-item card mb-3">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                            <h6 class="mb-0">${contactTitle}</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-contact">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Name</label>
                                    <input type="text" class="form-control" name="contacts[${contactIndex}][name]">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Position</label>
                                    <input type="text" class="form-control" name="contacts[${contactIndex}][position]">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Mobile No</label>
                                    <input type="tel" class="form-control" name="contacts[${contactIndex}][mobile]">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Email</label>
                                    <input type="email" class="form-control" name="contacts[${contactIndex}][email]">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Department</label>
                                    <select class="form-select" name="contacts[${contactIndex}][department]">
                                        <option value="" selected disabled>-- Select Department --</option>
                                        <option value="sales">Sales</option>
                                        <option value="marketing">Marketing</option>
                                        <option value="finance">Finance</option>
                                        <option value="technical">Technical</option>
                                        <option value="operations">Operations</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Is Primary Contact</label>
                                    <select class="form-select primary-contact-select" name="contacts[${contactIndex}][isPrimary]">
                                        <option value="1">Yes</option>
                                        <option value="0" selected>No</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="contacts[${contactIndex}][order]" value="${contactIndex}" class="contact-order">
                        </div>
                    </div>
                `;

                // Append the new contact
                $('#contactsContainer').append(newContact);
            });

            $(document).on('click', '.remove-contact', function() {
                if ($('.contact-item').length > 1) {
                    $(this).closest('.contact-item').remove();
                    updateContactIndices();
                } else {
                    alert('At least one contact is required.');
                }
            });

            $(document).on('change', '.primary-contact-select', function() {
                if ($(this).val() == '1') {
                    $('.primary-contact-select').not(this).val('0');
                }
            });

            function updateContactIndices() {
                $('.contact-item').each(function(index) {
                    let title = (index === 0) ? 'Primary Contact' :
                        (index === 1) ? 'Secondary Contact' :
                        `Additional Contact ${index-1}`;

                    $(this).find('h6').text(title);
                    $(this).find('input, select').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            const newName = name.replace(/contacts\[\d+\]/, `contacts[${index}]`);
                            $(this).attr('name', newName);
                        }
                    });
                    $(this).find('.contact-order').val(index);
                });
            }

            $('#partnerForm').validate({
                rules: {
                    partnerName: {
                        required: true
                    },
                    customerType: {
                        required: true
                    },
                    financialRating: {
                        required: true
                    },
                    agencyRating: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    street: {
                        required: true
                    },
                    taxNo: {
                        required: true
                    },
                    incorporationNo: {
                        required: true
                    },
                    website: {
                        required: true,
                        url: true
                    },
                    city: {
                        required: true
                    },
                    identityType: {
                        required: true
                    },
                    telephone: {
                        required: true
                    },
                    postalCode: {
                        required: true
                    },
                    identityNo: {
                        required: true
                    },
                    country: {
                        required: true
                    }
                },
                messages: {
                    partnerName: "Partner name is required",
                    customerType: "Customer type is required",
                    financialRating: "Financial rating is required",
                    agencyRating: "Agency rating is required",
                    email: {
                        required: "Email is required",
                        email: "Please enter a valid email address"
                    },
                    street: "Street is required",
                    taxNo: "Tax number is required",
                    incorporationNo: "Incorporation number is required",
                    website: {
                        required: "Website is required",
                        url: "Please enter a valid URL"
                    },
                    city: "City is required",
                    identityType: "Identity type is required",
                    telephone: "Telephone is required",
                    postalCode: "Postal code is required",
                    identityNo: "Identity number is required",
                    country: "Country is required"
                },
                errorPlacement: function(error, element) {
                    error.addClass("invalid-feedback");
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                },
                submitHandler: function(form, e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to submit this form?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, submit it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: $(form).attr('action'),
                                type: 'POST',
                                data: new FormData(form),
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.success) {
                                        toastr.success(response.message ||
                                            'Partner details have been successfully saved'
                                        );
                                        window.location.href =
                                            '{{ route('customer.info') }}';
                                    } else {
                                        toastr.error(response.message ||
                                            'Error submitting form');
                                    }
                                },
                                error: function(xhr) {
                                    if (xhr.status === 422) {
                                        const errors = xhr.responseJSON.errors;
                                        $('.is-invalid').removeClass('is-invalid');
                                        $('.invalid-feedback').remove();

                                        Object.keys(errors).forEach(field => {
                                            const input = $(
                                                `[id="${field}"]`);
                                            input.addClass('is-invalid');
                                            input.after(
                                                `<div class="invalid-feedback">${errors[field][0]}</div>`
                                            );
                                        });

                                        toastr.error(
                                            'Please check the form for errors');
                                    } else {
                                        toastr.error(
                                            'An error occurred while submitting the form'
                                        );
                                    }
                                }
                            });
                        }
                    });

                }
            });
        });
    </script>
@endpush
