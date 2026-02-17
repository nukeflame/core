@extends('layouts.app')
@section('content')
    <!-- <div>
                                                                                                                                                                                                                                                                                                                                                                    <nav class="breadcrumb">
                                                                                                                                                                                                                                                                                                                                                                        <a class="breadcrumb-item" href="#">Tenders</a><span> ➤ List Of Tenders</span>
                                                                                                                                                                                                                                                                                                                                                                    </nav>
                                                                                                                                                                                                                                                                                                                                                                </div> -->
    <div class="card">
        <div class="table-responsive card-body">
            <div class="tab-content" style="margin-top:20px">
                <div>
                    @if(!isset($approver_review))
                    <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#tenderModal">
                        <i class="bx bx-plus"></i> Add Tender Details
                    </button>
                    @endif
                   

                    {{-- <form method="post" action="" id="tender_form"> --}}
                    {{-- {{ csrf_field() }} --}}
                    <table class="table table-striped" id="tenders_table" style="width:100%">
                        <thead>
                            <tr>
                                <th>Date Added</th>
                                <th>Tender No</th>
                                <th>Tender Name</th>
                                <th>Tender Description</th>
                                <th>Closing Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tenders as $tender)
                                <tr>
                                    <td>{{ formatDate($tender->created_at) }}</td>
                                    <td>{{ $tender->tender_no }}</td>
                                    <td>{{ $tender->tender_name }}</td>
                                    <td>{{ $tender->tender_description }}</td>
                                    <td>{{ $tender->closing_date }}</td>
                                    <td>{{ $tender->tender_status }}</td>
                                    <td>
                                        <button class="btn btn-success btn-sm" type="button" id="view_tender_info"
                                            value="{{ $tender->tender_no }}+-{{ $tender->tender_name }}">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center">
                        {!! $tenders->links() !!}
                    </div>

                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="tenderModal" tabindex="-1" aria-labelledby="tenderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="tenderModalLabel">Add Tender</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-bs-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="tenderForm">
                        @csrf
                       
                        <input type="hidden" name="prospect_id" id="prospect_id"
                            value="{{ $prospect_id ?? '' }}">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="tenderNo">Tender No</label>
                                <input type="text" class="form-control" id="tenderNo" name="tender_no" maxlength="50"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tenderName">Tender Name</label>
                                <input type="text" class="form-control" id="tenderName" name="tender_name"
                                    maxlength="200" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="client_name">Cedant Name</label>
                                <input type="text" class="form-control" id="client_name" name="client_name"
                                    maxlength="50" value="{{isset($cedant) && isset($cedant->customer) ? $cedant->customer->name : ''}}" required>
                            </div>
                            {{-- <div class="form-group col-md-6">
                                <select name="proposal_category" id="proposal_category" class="form-control" required>
                                    <option value="">Select Category Of Proposal</option>
                                    <option value="Underwritter">Underwritter</option>
                                    <option value="Broker">Broker</option>
                                </select>

                            </div> --}}
                            <div class="form-group col-md-6">
                                <label for="proposal_nature">Nature Of Proposal</label>
                                <select name="proposal_nature" id="proposal_nature" class="form-control" required>
                                    <option value="">Select nature of proposal</option>
                                    <option value="TENDER">Tender</option>
                                    <option value="Request For Proposal">Request For Proposal</option>
                                    <option value="Request For Quotation">Request For Quotation</option>
                                    <option value="Prequalification">Prequalification</option>
                                </select>

                            </div>
                            <div class="form-group col-md-6">
                                <label for="closingDate">Closing Date</label>
                                <input type="date" class="form-control" id="closingDate" name="closing_date" required>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="tenderDescription">Tender Brief Description</label>
                                <textarea class="form-control" id="tenderDescription" name="tender_description" maxlength="300" required></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitTenderForm">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form to handle the POST request -->
    <form id="tender-details-form" method="POST" action="{{ route('tender.tenderdetails') }}" style="display:none;">
        @csrf
        <input type="hidden" name="prospect_id" id="prospect_id" value="{{ $prospect_id ?? ''}}">
        <input type="hidden" name="tender_ref" id="tender_ref">
        <input type="hidden" name="tender_namex" id="tender_namex">
    </form>
@endsection

@push('script')
    <script>
        /**
         * Capitalizes the first letter of each word in the input value while preserving cursor position
         * @param {Event} event - The input event object
         */
        function capitalizeInput(event) {
            const inputField = event.target;
            const originalValue = inputField.value;
            const cursorPosition = inputField.selectionStart;

            // Skip if the field is empty
            if (!originalValue) return;

            // Capitalize first letter of each word, keeping the rest lowercase
            const capitalizedValue = originalValue
                .toLowerCase()
                .replace(/(?:^|\s)\w/g, match => match.toUpperCase());

            // Only update if the value would actually change
            if (capitalizedValue !== originalValue) {
                inputField.value = capitalizedValue;

                // Restore cursor position
                inputField.setSelectionRange(cursorPosition, cursorPosition);
            }
        }

        /**
         * Initialize capitalization listeners on all text inputs and textareas
         */
        function initializeCapitalization() {
            // Select both input and textarea elements that aren't readonly or disabled
            const inputFields = document.querySelectorAll(`
        input[type="text"]:not([readonly]):not([disabled]),
        textarea:not([readonly]):not([disabled])
    `);

            inputFields.forEach(field => {
                // Remove any existing listeners to prevent duplicates
                field.removeEventListener('input', capitalizeInput);
                // Add the new listener
                field.addEventListener('input', capitalizeInput);
            });
        }

        // Initialize when the DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeCapitalization);
        } else {
            initializeCapitalization();
        }
   
    
        $(document).ready(function() {
            $('#tenders_table').on('click', '#view_tender_info', function() {
                var value = $(this).val().split('+-');
                console.log(value);
                $('#tender_ref').val(value[0]);
                $('#tender_namex').val(value[1]);
                $('#tender-details-form').submit();
            });

            $('#submitTenderForm').click(function() {
                var formData = $('#tenderForm').serialize();

                $.ajax({
                    url: "{{ route('tender.add') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {

                        // Check the response code
                        if (response.status === 200) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Tender Saved',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        } else if (response.status === 201) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Tender Exists',
                                text: response.message,
                                confirmButtonText: 'OK'
                            })
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Save Tender',
                            text: 'There was an error saving the tender. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

        });
    </script>
@endpush
