    <div class="modal effect-scale md-wrapper" id="addAttachemntModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticAttachemntLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="attachmentsForm">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                    <input type="hidden" name="id" id="attachments_id" value="{{ $coverReg->endorsement_no }}" />
                    <div class="modal-header">
                        <h5 class="modal-title dc-modal-title" id="staticAttachemntLabel">File & Supporting
                            Docs
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label fs-14" for="title">Title</label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="title" name="title">
                                        <option>--Select title--</option>
                                        <option value="Policy Schedule">Policy Schedule</option>
                                        <option value="Closings">Closings</option>
                                        <option value="Insured Items">Insured Items</option>
                                        <option value="Survey Report">Survey Report</option>
                                    </select>
                                </div>
                                {{-- <input type="text" class="form-control" id="title" name="title" required /> --}}
                            </div>
                            <div class="mb-3">
                                <label class="form-label fs-14" for="file">File</label>
                                <input type="file" class="form-control" id="file" name="file"
                                    accept=".pdf, .doc, .docx,.png,.jpg" required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="attachments-save-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
