<div class="modal modal-lg fade" id="mail-compose-modal" tabindex="-1" aria-labelledby="mail-compose-label"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="compose-email-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title" id="mail-compose-label">Compose Mail</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <div class="row">
                        <!-- From Field -->
                        <div class="col-xl-6 mb-2">
                            <label for="fromMail" class="form-label">
                                From<sup><i class="ri-star-s-fill text-success fs-8"></i></sup>
                            </label>
                            <input type="email" class="form-control" id="fromMail" name="from"
                                value="{{ auth()->user()->email }}" readonly>
                        </div>

                        <!-- To Field -->
                        <div class="col-xl-6 mb-2">
                            <label for="toMail" class="form-label">
                                To<sup><i class="ri-star-s-fill text-success fs-8"></i></sup>
                            </label>
                            <select class="form-control" name="to[]" id="toMail" multiple required>
                                @if (isset($contacts) && $contacts->count() > 0)
                                    @foreach ($contacts as $contact)
                                        <option value="{{ $contact->email }}">{{ $contact->name }}
                                            ({{ $contact->email }})</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback">Please select at least one recipient.</div>
                        </div>

                        <!-- CC Field -->
                        <div class="col-xl-6 mb-2">
                            <label for="mailCC" class="form-label text-dark fw-semibold">Cc</label>
                            <select class="form-control" name="cc[]" id="mailCC" multiple>
                                @if (isset($contacts) && $contacts->count() > 0)
                                    @foreach ($contacts as $contact)
                                        <option value="{{ $contact->email }}">{{ $contact->name }}
                                            ({{ $contact->email }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- BCC Field -->
                        <div class="col-xl-6 mb-2">
                            <label for="mailBcc" class="form-label text-dark fw-semibold">Bcc</label>
                            <select class="form-control" name="bcc[]" id="mailBcc" multiple>
                                @if (isset($contacts) && $contacts->count() > 0)
                                    @foreach ($contacts as $contact)
                                        <option value="{{ $contact->email }}">{{ $contact->name }}
                                            ({{ $contact->email }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Subject Field -->
                        <div class="col-xl-12 mb-2">
                            <label for="mailSubject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="mailSubject" name="subject"
                                placeholder="Enter email subject" required>
                            <div class="invalid-feedback">Please enter a subject.</div>
                        </div>

                        <!-- Priority -->
                        <div class="col-xl-6 mb-2">
                            <label for="mailPriority" class="form-label">Priority</label>
                            <select class="form-control" id="mailPriority" name="priority">
                                <option value="normal">Normal</option>
                                <option value="low">Low</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <!-- Attachments -->
                        <div class="col-xl-6 mb-2">
                            <label for="mailAttachments" class="form-label">Attachments</label>
                            <input type="file" class="form-control" id="mailAttachments" name="attachments[]"
                                multiple>
                            <div class="form-text">Maximum 10MB per file, 25MB total</div>
                        </div>

                        <!-- Content Editor -->
                        <div class="col-xl-12">
                            <label class="col-form-label">Content :</label>
                            <div class="mail-compose">
                                <div id="mail-compose-editor" name="body"></div>
                                <textarea id="mail-compose-content" name="body" class="d-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-light me-2" id="saveDraftBtn">
                            <i class="ri-save-line me-1"></i>Save Draft
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary" id="sendEmailBtn">
                            <i class="ri-send-plane-2-line me-1"></i>Send
                            <span class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template Selection Modal -->
<div class="modal fade" id="template-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Email Templates</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @php
                        $templates = [
                            ['name' => 'Meeting Request', 'preview' => 'Schedule a meeting with...'],
                            ['name' => 'Follow Up', 'preview' => 'Following up on our previous...'],
                            ['name' => 'Thank You', 'preview' => 'Thank you for your time...'],
                            ['name' => 'Proposal', 'preview' => 'We are pleased to present...'],
                        ];
                    @endphp
                    @foreach ($templates as $template)
                        <div class="col-md-6 mb-3">
                            <div class="card template-card"
                                data-template="{{ strtolower(str_replace(' ', '-', $template['name'])) }}">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $template['name'] }}</h6>
                                    <p class="card-text text-muted">{{ $template['preview'] }}</p>
                                    <button class="btn btn-sm btn-outline-primary select-template">Use
                                        Template</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
