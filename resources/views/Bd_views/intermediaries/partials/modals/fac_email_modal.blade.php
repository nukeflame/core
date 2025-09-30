<div class="modal fade effect-scale md-wrapper" id="sendBDEmail" tabindex="-1" aria-labelledby="sendBDEmailLabel"
    data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendBDEmailLabel">
                    <i class="bx bx-briefcase me-2"></i>
                    Business Development Notification
                    <span class="ms-1">- Proposal</span>
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

                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-12">
                            <!-- Navigation Tabs -->
                            <div class="card-header bg-light border-bottom">
                                <ul class="nav nav-tabs card-header-tabs" id="emailTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="compose-tab" data-bs-toggle="tab"
                                            data-bs-target="#compose" type="button" role="tab">
                                            <i class="bx bx-envelope me-2 fs-15"
                                                style="vertical-align: middle"></i>Compose
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="replies-tab" data-bs-toggle="tab"
                                            data-bs-target="#replies" type="button" role="tab">
                                            <i class="bx bx-reply me-2 fs-15" style="vertical-align: middle"></i>Reply
                                            to
                                            Messages
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content" id="bdEmailTabContent">
                                <div class="tab-pane fade show active" id="compose" role="tabpanel">
                                    @include('Bd_views.intermediaries.partials.bd.reinsurers.compose-form')
                                </div>

                                <div class="tab-pane fade" id="replies" role="tabpanel">
                                    {{-- @include('claim.emails.reinsurers.messages-list') --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-default btn-sm" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-paper-plane me-1"></i>Send Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    #bdEmailTabContent {
        min-height: 50vh;
    }

    #bdEmailTabContent .tab-pane {
        padding-top: 1rem;
        border: none;
    }
</style>
