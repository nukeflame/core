    <div class="modal effect-scale md-wrapper" id="attachments-modal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticAttachemntLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="attachmentsForm" data-post-url="{{ route('cover.save_attachment') }}"
                    data-put-url="{{ route('cover.amend_attachment') }}">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="cover_no" value="{{ $cover->cover_no }}" />
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}" />
                    <input type="hidden" name="id" id="attachments_id" value="" />
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
                                <input type="text" class="form-control" id="title" name="title" required
                                    maxlength="100" />
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

    <div class="modal effect-scale md-wrapper" id="attachment-document-modal" aria-labelledby="attachmentPreviewLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="attachmentPreviewLabel">Attachment Preview</h6>
                    <button type="button" id="attachment-preview-close-btn" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="preview-container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="attachment-preview-close-footer-btn" class="btn btn-outline-secondary btn-sm"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
