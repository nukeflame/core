<div class="modal fade effect-scale md-wrapper" id="sendBDEmail" tabindex="-1" aria-labelledby="sendBDEmailLabel"
    data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendBDEmailLabel">
                    <i class="bx bx-briefcase me-2"></i>
                    Business Development Notification
                    <span class="badge bg-secondary ms-2">
                        Proposal
                    </span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="bdNotificationForm" action="{{ route('bd.notification.send') }}" method="POST">
                @csrf
                {{-- <input type="hidden" name="opportunity_id" value="{{ $bdOpportunity->id }}">
                <input type="hidden" name="stage" value="{{ $bdOpportunity->stage }}">
                <input type="hidden" name="client_id" value="{{ $bdOpportunity->client_id }}">
                <input type="hidden" name="user_id" value="{{ auth()->id() }}"> --}}

                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body py-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="mb-1">
                                                <i class="bx bx-building me-1"></i>
                                                {{-- {{ $bdOpportunity->client->name }} --}}
                                                Britam Insurance
                                            </h6>
                                            <small class="text-muted">
                                                Jam Shah
                                                {{-- Opportunity:') }} {{ $bdOpportunity->opportunity_name }} --}}
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="stage-progress">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    @php
                                                        $stages = [
                                                            'lead',
                                                            'proposal',
                                                            'negotiation',
                                                            'win',
                                                            'lost',
                                                            'final',
                                                        ];
                                                        $stageIcons = [
                                                            'lead' => 'bx-search-alt',
                                                            'proposal' => 'bx-file-blank',
                                                            'negotiation' => 'bx-chat',
                                                            'win' => 'bx-trophy',
                                                            'lost' => 'bx-x-circle',
                                                            'final' => 'bx-check-circle',
                                                        ];
                                                        $currentStage = 'proposal'; //
                                                    @endphp

                                                    @foreach ($stages as $stage)
                                                        @if ($stage !== 'lost' || $currentStage === 'lost')
                                                            <div
                                                                class="stage-item {{ $currentStage === $stage ? 'active' : ($currentStage === 'lost' && $stage !== 'lost' ? 'skipped' : '') }}">
                                                                <i class="bx {{ $stageIcons[$stage] }}"></i>
                                                                <span
                                                                    class="stage-label">{{ __(ucfirst($stage)) }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-bold">
                            <i class="fas fa-user me-1"></i>From:') }}
                        </label>
                        <div class="col-sm-9">
                            <div class="sender-info d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <img src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.png') }}"
                                        alt="{{ auth()->user()->name }}" class="avatar-img rounded-circle">
                                </div>
                                <div>
                                    <strong>{{ auth()->user()->name }}</strong>
                                    <br>
                                    <small class="text-muted">Business Development</small>
                                    {{-- <small class="text-muted">{{ auth()->user()->email }} •
                                        {{ auth()->user()->department ?? __('Business Development') }}</small> --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="recipients" class="col-sm-3 col-form-label fw-bold">
                            <i class="bx bx-users me-1"></i>Stakeholders:') }}
                        </label>
                        <div class="col-sm-9">
                            <div class="stakeholder-groups">
                                {{-- Internal Stakeholders --}}
                                <div class="stakeholder-group mb-3">
                                    <h6 class="text-primary mb-2">
                                        <i class="bx bx-building me-1"></i>Internal Team
                                    </h6>
                                    <select class="form-select select2" name="internal_recipients[]" multiple
                                        data-placeholder="Select internal stakeholders">
                                        {{-- @foreach ($internalStakeholders as $stakeholder)
                                            @if (in_array($stakeholder->role, $stageConfig[$bdOpportunity->stage]['internal_roles']))
                                                <option value="{{ $stakeholder->email }}"
                                                    {{ in_array($stakeholder->role, $stageConfig[$bdOpportunity->stage]['required_internal'] ?? []) ? 'selected' : '' }}>
                                                    {{ $stakeholder->name }} - {{ $stakeholder->role }}
                                                </option>
                                            @endif
                                        @endforeach --}}
                                    </select>
                                </div>

                                {{-- External Stakeholders --}}
                                <div class="stakeholder-group mb-3">
                                    <h6 class="text-success mb-2">
                                        <i class="bx bx-group me-1"></i>External Contacts
                                    </h6>
                                    <select class="form-select select2" name="external_recipients[]" multiple
                                        data-placeholder="Select external contacts') }}">
                                        {{-- @foreach ($bdOpportunity->client->contacts as $contact)
                                            @if (in_array($contact->contact_type, $stageConfig[$bdOpportunity->stage]['external_types']))
                                                <option value="{{ $contact->email }}"
                                                    {{ in_array($contact->contact_type, $stageConfig[$bdOpportunity->stage]['required_external'] ?? []) ? 'selected' : '' }}>
                                                    {{ $contact->name }} - {{ $contact->contact_type }}
                                                </option>
                                            @endif
                                        @endforeach --}}
                                    </select>
                                </div>

                                {{-- Reinsurer Stakeholders (if applicable) --}}
                                {{-- @if (in_array($bdOpportunity->stage, ['negotiation', 'win', 'final'])) --}}
                                <div class="stakeholder-group mb-3">
                                    <h6 class="text-warning mb-2">
                                        <i class="bx bx-shield-alt me-1"></i>Reinsurer Contacts
                                    </h6>
                                    <select class="form-select select2" name="reinsurer_recipients[]" multiple
                                        data-placeholder="Select reinsurer contacts') }}">
                                        {{-- @foreach ($reinsurerContacts as $contact)
                                            <option value="{{ $contact->email }}">
                                                {{ $contact->name }} - {{ $contact->company }}
                                                ({{ $contact->role }})
                                            </option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                {{-- @endif --}}
                            </div>

                            @error('recipients')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="subject" class="col-sm-3 col-form-label fw-bold">
                            <i class="bx bx-tag me-1"></i>Subject:') }}
                        </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                id="subject" name="subject" value="" {{-- value="{{ old('subject', $stageConfig[$bdOpportunity->stage]['default_subject']) }}" --}} maxlength="255"
                                required />
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="message" class="col-sm-3 col-form-label fw-bold">
                            <i class="bx bx-comment me-1"></i>Message:') }}
                        </label>
                        <div class="col-sm-9">
                            <div class="message-templates mb-2">
                                <small class="text-muted">Quick Templates</small>
                                <div class="btn-group btn-group-sm" role="group">
                                    {{-- @foreach ($stageConfig[$bdOpportunity->stage]['templates'] as $template)
                                        <button type="button" class="btn btn-outline-secondary template-btn"
                                            data-template="{{ $template['key'] }}">
                                            {{ $template['name'] }}
                                        </button>
                                    @endforeach --}}
                                </div>
                            </div>

                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="12"
                                maxlength="5000" required></textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Maximum 5000 characters') }}</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-bold">
                            <i class="bx bx-info-circle me-1"></i>Opportunity Details:') }}
                        </label>
                        <div class="col-sm-9">
                            <div class="opportunity-summary card bg-light">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Business Line</small>
                                            <p class="mb-2"></p>

                                            <small class="text-muted">Expected Premium</small>
                                            <p class="mb-2"></p>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Target Date</small>
                                            <p class="mb-2"></p>

                                            <small class="text-muted"></small>
                                            <p class="mb-2">0%</p>
                                        </div>
                                    </div>

                                    {{-- @if ($bdOpportunity->stage === 'win')
                                        <div class="alert alert-success mb-0 mt-2">
                                            <i class="bx bx-trophy me-1"></i>
                                            Congratulations! This opportunity has been won.') }}
                                        </div>
                                    @elseif($bdOpportunity->stage === 'lost')
                                        <div class="alert alert-warning mb-0 mt-2">
                                            <i class="bx bx-info-circle me-1"></i>
                                            Reason:') }}
                                            {{ $bdOpportunity->loss_reason ?? __('Not specified') }}
                                        </div>
                                    @endif --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-bold">
                            <i class="bx bx-paperclip me-1"></i>Attachments:
                        </label>
                        <div class="col-sm-9">
                            <div class="attachment-categories">
                                {{-- Stage-specific documents --}}
                                {{-- @foreach ($stageConfig[$bdOpportunity->stage]['document_types'] as $docType)
                                    <div class="document-category mb-3">
                                        <h6 class="text-secondary mb-2">
                                            <i class="bx bx-folder me-1"></i>{{ __($docType['name']) }}
                                            @if ($docType['required'])
                                                <span class="badge bg-danger ms-1">Required') }}</span>
                                            @endif
                                        </h6>

                                        <div class="document-list">
                                            @forelse($bdOpportunity->documents->where('document_type', $docType['key']) as $document)
                                                @include('business-development.partials.document-item', [
                                                    'document' => $document,
                                                ])
                                            @empty
                                                <div class="no-documents text-muted small">
                                                    <i class="bx bx-file me-1"></i>No documents available') }}
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endforeach --}}
                            </div>

                            {{-- File upload for additional documents --}}
                            <div class="upload-section mt-3">
                                <input type="file" class="form-control" name="additional_files[]" multiple
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                <small class="form-text text-muted">
                                    Upload additional documents (PDF, DOC, XLS, PPT files only)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="document-item d-flex align-items-center mb-2 p-2 border rounded">
                        <div class="document-icon me-3">
                            {{-- <i
                                class="bx bx-file-{{ $document->getIconType() }} text-{{ $document->getIconColor() }}"></i> --}}
                        </div>
                        <div class="document-info flex-grow-1">
                            <h6 class="mb-1"></h6>
                            <div class="document-meta text-muted small">
                                {{-- {{ strtoupper($document->file_extension) }} •
                                {{ $document->formatted_size }} • --}}
                                Updated
                                {{-- {{ $document->updated_at->diffForHumans() }} --}}
                            </div>
                        </div>
                        <div class="document-actions">
                            <div class="form-check">
                                {{-- <input class="form-check-input" type="checkbox" name="attached_documents[]"
                                    value="{{ $document->id }}" id="doc_{{ $document->id }}"
                                    {{ $document->auto_attach_for_stage($bdOpportunity->stage) ? 'checked' : '' }}> --}}
                                <label class="form-check-label">
                                    Include
                                </label>
                            </div>
                            {{-- <a href="{{ route('bd.documents.view', $document) }}"
                                class="btn btn-sm btn-outline-secondary ms-2" target="_blank">
                                <i class="bx bx-show"></i>
                            </a> --}}
                        </div>
                    </div>


                </div>

                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100">
                        <div class="footer-info">
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>
                                {{-- Stage:') }} {{ __(ucfirst($bdOpportunity->stage)) }} •
                                Last Updated:') }} {{ $bdOpportunity->updated_at->diffForHumans() }} --}}
                            </small>
                        </div>
                        <div class="footer-actions">
                            <button type="button" class="btn btn-outline-secondary btn-sm me-2"
                                data-bs-dismiss="modal">
                                <i class="bx bx-x me-1"></i>Cancel
                            </button>

                            {{-- @if ($bdOpportunity->stage !== 'final')
                                <button type="button" class="btn btn-outline-primary btn-sm me-2" id="saveDraft">
                                    <i class="bx bx-save me-1"></i>Save Draft') }}
                                </button>
                            @endif --}}

                            <button type="submit" class="btn btn-primary" id="sendNotification">
                                <i class="bx bx-send me-1"></i>
                                Send Notification
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
