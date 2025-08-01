@extends('layouts.app')
@section('content')
    <style>

    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 ">
                <div class="card px-0 pt-4 pb-0 mb-3">
                    <h6 class="text-center text-lg-start mb-0 mx-2">PQ Proposal
                    </h6>

                    <hr>
                    <div class="card-body">

                        <small class="primary-color">Proposal attachments and other related files</small>
                        <br><br>

                        <button class="btn btn-primary btn-sm" id="addFileInputBtn"><i class="fa fa-plus"></i> Add
                            document</button>
                        <br><br>
                        <form id="dynamicForm" method="POST" action="{{ route('PQ_proposal_documents') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="prospectId" value="{{ $prospectId }}">
                            <input type="hidden" name="client_name" value="{{ $client_name }}">

                            <div class="file-input-wrapper d-flex align-items-center mb-2">
                                <label for="marketingMail" class="me-2">Marketing EMail </label>
                                <input type="text" class="form-control" name="marketingMail"
                                    placeholder="Enter marketing email" required value="{{ $email }}">
                            </div>

                            <div id="fileInputsContainer">
                                <div class="file-input-wrapper d-flex align-items-center mb-2">
                                    <label for="file_1" class="me-2">Attach Document</label>
                                    <input type="file" name="files[]" id="file_1" class="form-control me-2">
                                </div>
                            </div>

                            <div id="fileInputsContainer">
                                <label for="PQ Comments" class="me-2">PQ Comments</label>
                                <textarea name="pqcomment" id="pqcomment" cols="30" rows="10" class="form-control"
                                    placeholder="Enter Comments" required></textarea>
                            </div>
                            <br>

                            <br><br>
                            <button type="submit" class="btn btn-info btn-sm">Submit document(s)</button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addFileInputBtn = document.getElementById('addFileInputBtn');
            const fileInputsContainer = document.getElementById('fileInputsContainer');

            // Counter for input IDs
            let fileInputCount = 1;

            // Add new file input dynamically
            addFileInputBtn.addEventListener('click', function() {
                fileInputCount++;

                // Create wrapper div with flex layout
                const fileInputDiv = document.createElement('div');
                fileInputDiv.classList.add('file-input-wrapper', 'd-flex', 'align-items-center', 'mb-2');
                fileInputDiv.setAttribute('id', `file-input-wrapper-${fileInputCount}`);

                // Create label
                const label = document.createElement('label');
                label.textContent = 'Attach Document';
                label.classList.add('me-2');

                // Create file input
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = 'files[]';
                fileInput.classList.add('form-control', 'me-2');

                // Create remove button
                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.classList.add('btn', 'btn-danger', 'btn-sm', 'remove-button');
                removeButton.textContent = 'Remove';
                removeButton.addEventListener('click', function() {
                    // Remove the wrapper div
                    fileInputDiv.remove();
                });

                // Append elements to wrapper div
                fileInputDiv.appendChild(label);
                fileInputDiv.appendChild(fileInput);
                fileInputDiv.appendChild(removeButton);

                // Append wrapper div to container
                fileInputsContainer.appendChild(fileInputDiv);
            });
        });
    </script>
@endpush
