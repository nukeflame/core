@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="table-responsive card-body">
            <div class="tab-content" style="margin-top:20px">
                <div>
                    <button type="button" class="btn btn-info mb-3" data-toggle="modal" onclick="showTenderModal()">
                        Add Tender Document
                    </button>

                    {{-- <form method="post" action="" id="tender_form">
                        @csrf --}}
                    <table class="table table-black table-border data-table" id="tenders_table" style="width:100%">
                        <thead>
                            <tr>
                                <th>Date Added</th>
                                {{-- <th>Document No</th> --}}
                                <th>Document Name</th>
                                <th>Document Description</th>
                                <th>Expiry Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        @foreach ($tenderDocs as $tenderDoc)
                            <tr>
                                <td>{{ formatDate($tenderDoc->created_at) }}</td>
                                {{-- <td>{{ $tenderDoc->doc_id }}</td> --}}
                                <td>{{ $tenderDoc->doc_name }}</td>
                                <td>{{ $tenderDoc->doc_description }}</td>
                                <td>{{ $tenderDoc->expiry_date }}</td>
                                <td>
                                    <!-- View Document Button: Open document in a new tab -->
                                    <button type="button" class="btn btn-success view-attachment" id="view_doc_info"
                                        data-file="{{ asset('uploads/' . $tenderDoc->base64) }}" target="_blank">
                                        <i class="fa fa-eye"></i> View Attachment
                                    </button>

                                    <!-- Edit Document Button -->
                                    <button type="button" class="btn btn-info edit-attachment"
                                        data-id="{{ $tenderDoc->doc_id }}">
                                        <i class="fa fa-edit"></i> Edit Attachment
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center">
                        {!! $tenderDocs->links() !!}
                    </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="tenderModal" tabindex="-1" aria-labelledby="tenderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="tenderModalLabel">Tender Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="tenderForm">
                        @csrf

                        <input type="hidden" name="tenderStatus" id="tenderstatus">
                        <input type="hidden" name="tenderdocId" id="tenderdocId">
                        <div class="form-group">
                            <label for="tenderName">Document Name</label>
                            <input type="text" class="form-control" id="docName" name="docName" maxlength="200"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="expirydate">Expiry Date</label>
                            <input type="date" class="form-control" id="expirydate" name="expirydate" required>
                        </div>

                        <div class="form-group">
                            <label for="expirydate">Renewable Status</label>
                            <select name="renewableState" id="renewableState" class="form-control" required>
                                <option value="">Select Renewable status</option>
                                <option value="1">Document Is Renewable</option>
                                <option value="2">Document Not Renewable</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tenderDescription">Document Description</label>
                            <textarea class="form-control" id="docDescription" name="docDescription" maxlength="300" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="attachments">Attachments</label>
                            <input type="file" class="form-control" id="attachment" name="attachment">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="closeModal()">Close</button>

                    <button type="button" class="btn btn-primary" id="submitTenderForm">Save</button>
                </div>
            </div>
        </div>
    </div>
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
        function showTenderModal() {

            $('#tenderstatus').val('');
            $("#tenderdocId").val("")
            $('#docName').val("");
            $('#docDescription').val("");
            $('#tenderModal').modal("show")
        }


        function closeModal() {
            $("#tenderModal").modal("hide")
        }

        $(document).ready(function() {

            $('.edit-attachment').click(function() {
                // Get the document ID from the data-id attribute
                var docId = $(this).data('id');

                $.ajax({
                    url: "{{ route('docs-setup.tender.document.details', ':docId') }}".replace(':docId',
                        docId),
                    type: "GET",
                    success: function(response) {

                        // Populate the modal with the document data
                        $('#docName').val(response.doc_name); // Populate Document Name
                        $('#docDescription').val(response
                            .doc_description); // Populate Document Description


                        $("#tenderModal").modal("show")
                        $('#tenderstatus').val('U');
                        $("#tenderdocId").val(response.tender_doc_id)
                        // $("#docName").attr('readonly', true)
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert("Error fetching document data: " + textStatus);
                    }
                });
            });


            $('.view-attachment').click(function() {

                var fileUrl = $(this).data('file');

                window.open(fileUrl, '_blank');
            });

            $('#tenders_table').on('click', '#view_tender_info', function() {
                var value = $(this).val().split('+-');

                $('#tender_ref').val(value[0]);
                $('#tender_namex').val(value[1]);
                $('#tender-details-form').submit();
            });

            $('#submitTenderForm').click(function() {
                var form = $('#tenderForm')[0];
                var formData = new FormData(form);

                var allFieldsFilled = true;

                // Loop through all form fields except for #tenderstatus and #tenderdocId
                $('#tenderForm input, #tenderForm textarea, #tenderForm select')
                    .not('#tenderstatus, #tenderdocId') // Exclude #tenderstatus and #tenderdocId
                    .each(function() {
                        var field = $(this);
                        var fieldValue = field.val();

                        // Check if the field value is empty or undefined
                        if (fieldValue === '' || fieldValue === undefined) {
                            allFieldsFilled = false;
                            field.addClass('is-invalid');
                        } else {
                            field.removeClass('is-invalid');
                        }

                        // Special handling for file input (if exists)
                        if (field.attr('type') === 'file') {
                            if (field[0].files.length === 0) {
                                allFieldsFilled = false;
                                field.addClass('is-invalid');
                            } else {
                                field.removeClass('is-invalid');
                            }
                        }
                    });

                if (!allFieldsFilled) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Form',
                        text: 'Please fill in all required fields.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }


                $.ajax({
                    url: "{{ route('docs-setup.tender.doc_add') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tender Document Saved',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        } else if (response.status === 400) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Tender Document Exists',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Save Tender',
                            text: 'There was an error uploading files. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });


        });
    </script>
@endpush
