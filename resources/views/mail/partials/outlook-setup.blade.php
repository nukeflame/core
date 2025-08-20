<!-- Outlook Connection Setup -->
<div class="hidden outlook-setup-overlay position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
    style="background: rgba(0,0,0,0.7); z-index: 1060;">
    <div class="card" style="width: 400px;">
        <div class="card-header text-center">
            {{-- <i class="ri-microsoft-line fs-48 text-primary mb-2"></i> --}}
            <div class="sv-icon">
                <svg clip-rule="evenodd" width="200" height="200" fill-rule="evenodd" stroke-linejoin="round"
                    stroke-miterlimit="1.41421" viewBox="0 0 560 400" xmlns="http://www.w3.org/2000/svg">
                    <g fill-rule="nonzero">
                        <path
                            d="m366.585 103.756h-118.187c-5.295 0-9.652 4.357-9.652 9.652v10.971l66.614 20.625 70.877-20.625v-10.971c0-5.295-4.357-9.652-9.652-9.652z"
                            fill="#0364b8" />
                        <path
                            d="m387.58 209.659c1.007-3.165 1.811-6.391 2.406-9.659.001-1.635-.873-3.15-2.289-3.967l-.089-.048-.028-.013-74.507-42.444c-.321-.208-.654-.399-.996-.571-2.885-1.43-6.279-1.43-9.164 0-.342.172-.675.362-.997.571l-74.506 42.444-.028.013-.09.048c-1.415.817-2.289 2.332-2.288 3.967.595 3.268 1.399 6.494 2.406 9.659l79.002 57.78z"
                            fill="#0a2767" />
                        <path d="m334.99 124.379h-48.122l-13.894 20.625 13.894 20.623 48.122 41.247h41.247v-41.247z"
                            fill="#28a8ea" />
                    </g>
                    <path d="m238.746 124.379h48.122v41.247h-48.122z" fill="#0078d4" />
                    <path d="m334.99 124.379h41.247v41.247h-41.247z" fill="#50d9ff" />
                    <path d="m334.99 206.874-48.122-41.247h-48.122v41.247l48.122 41.248 74.465 12.154z" fill="#0364b8"
                        fill-rule="nonzero" />
                    <path d="m238.959 124.379h137.278" fill="none" />
                    <path d="m286.868 165.627h48.122v41.247h-48.122z" fill="#0078d4" />
                    <path d="m238.746 206.874h48.122v41.247h-48.122z" fill="#064a8c" />
                    <path d="m334.99 206.874h41.247v41.247h-41.247z" fill="#0078d4" />
                    <g fill-rule="nonzero">
                        <path
                            d="m308.805 263.369-81.079-59.121 3.396-5.974s73.867 42.072 74.994 42.705c.934.375 1.984.345 2.895-.083 1.051-.591 75.152-42.828 75.152-42.828l3.41 5.974z"
                            fill="#0a2767" fill-opacity=".498039" />
                        <path
                            d="m387.697 203.966-.089.055-.021.014-74.506 42.444c-3.006 1.938-6.814 2.175-10.037.625l25.951 34.792 56.743 12.354v.028c2.675-1.935 4.263-5.044 4.262-8.346v-85.932c.001 1.634-.874 3.15-2.289 3.966z"
                            fill="#1490df" />
                        <path
                            d="m389.986 285.932v-5.073l-68.629-39.103-8.284 4.716c-3.005 1.938-6.813 2.176-10.036.625l25.951 34.793 56.743 12.353v.028c2.675-1.936 4.262-5.044 4.262-8.346z"
                            fill-opacity=".047059" />
                        <path
                            d="m389.643 288.565-75.229-42.856-1.341.763c-3.005 1.938-6.813 2.176-10.036.625l25.951 34.793 56.743 12.353v.028c1.925-1.396 3.31-3.415 3.918-5.713z"
                            fill-opacity=".098039" />
                        <path
                            d="m227.402 204.056v-.069h-.068l-.207-.137c-1.334-.821-2.144-2.284-2.131-3.85v85.946c0 5.649 4.649 10.298 10.299 10.298h144.38c.858-.009 1.713-.124 2.543-.344.431-.075.848-.214 1.238-.412.146-.015.286-.062.412-.138.562-.23 1.094-.53 1.581-.894l.275-.206z"
                            fill="#28a8ea" />
                        <path
                            d="m293.742 259.582v-112.282c-.015-5.022-4.142-9.149-9.164-9.164h-45.619v51.256l-11.557 6.586-.027.014-.089.048c-1.416.818-2.29 2.333-2.29 3.966v68.767-.028h59.582c5.022-.014 9.149-4.142 9.164-9.163z"
                            fill-opacity=".098039" />
                        <path
                            d="m286.868 266.456v-112.282c-.015-5.021-4.143-9.148-9.164-9.164h-38.745v44.382l-11.557 6.586-.027.014-.089.048c-1.416.818-2.29 2.333-2.29 3.966v75.642-.028h52.708c5.021-.015 9.149-4.142 9.164-9.164zm0-13.749v-98.533c-.015-5.021-4.143-9.148-9.164-9.164h-38.745v44.382l-11.557 6.586-.027.014-.089.048c-1.416.818-2.29 2.333-2.29 3.966v61.892-.027h52.708c5.021-.015 9.149-4.142 9.164-9.164zm-6.875 0v-98.533c-.015-5.021-4.142-9.148-9.163-9.164h-31.871v44.382l-11.557 6.586-.027.014-.089.048c-1.416.818-2.29 2.333-2.29 3.966v61.892-.027h45.834c5.021-.015 9.148-4.142 9.163-9.164z"
                            fill-opacity=".2" />
                        <path
                            d="m179.164 145.004h91.658c5.027 0 9.164 4.136 9.164 9.163v91.659c0 5.027-4.137 9.164-9.164 9.164h-91.658c-5.027 0-9.164-4.137-9.164-9.164v-91.659c0-5.027 4.137-9.163 9.164-9.163z"
                            fill="#0078d4" />
                        <path
                            d="m196.584 182.593c2.435-5.189 6.367-9.532 11.288-12.47 5.453-3.122 11.662-4.677 17.943-4.496 5.817-.128 11.559 1.347 16.595 4.262 4.739 2.822 8.556 6.962 10.985 11.914 2.646 5.456 3.966 11.46 3.85 17.523.129 6.337-1.23 12.617-3.966 18.335-2.483 5.127-6.415 9.416-11.309 12.333-5.232 3.007-11.188 4.522-17.221 4.379-5.943.141-11.812-1.35-16.966-4.311-4.776-2.827-8.639-6.971-11.123-11.934-2.663-5.378-3.998-11.317-3.891-17.317-.118-6.283 1.189-12.511 3.822-18.218zm12.03 29.272c1.299 3.281 3.502 6.128 6.353 8.208 2.901 2.032 6.379 3.08 9.919 2.991 3.772.149 7.491-.932 10.594-3.08 2.816-2.08 4.961-4.942 6.167-8.229 1.356-3.664 2.024-7.547 1.973-11.453.042-3.94-.586-7.86-1.856-11.59-1.12-3.358-3.188-6.322-5.954-8.531-3.021-2.256-6.73-3.402-10.497-3.245-3.617-.094-7.173.96-10.154 3.011-2.904 2.087-5.156 4.958-6.49 8.277-2.95 7.599-2.967 16.031-.048 23.641z"
                            fill="#fff" />
                    </g>
                    <path d="m170 90.006h219.986v219.986h-219.986z" fill="none" />
                </svg>
            </div>
            <h5 class="mb-0">Connect to Microsoft Outlook</h5>
        </div>
        <div class="card-body text-center">
            <p class="text-muted mb-4">
                Connect your Microsoft Outlook account to start managing your emails seamlessly.
            </p>

            <div class="mb-3">
                <div class="d-flex justify-content-center mb-3">
                    <div class="me-3">
                        <i class="ri-mail-line fs-24 text-primary"></i>
                        <p class="fs-12 text-muted mt-1">Sync Emails</p>
                    </div>
                    <div class="me-3">
                        <i class="ri-contacts-line fs-24 text-success"></i>
                        <p class="fs-12 text-muted mt-1">Import Contacts</p>
                    </div>
                    <div>
                        <i class="ri-calendar-line fs-24 text-warning"></i>
                        <p class="fs-12 text-muted mt-1">Calendar Access</p>
                    </div>
                </div>
            </div>

            <div class="alert alert-info" role="alert">
                <small>
                    <i class="ri-information-line me-1"></i>
                    We'll securely connect to your Outlook account using Microsoft Graph API.
                </small>
            </div>

            <button class="btn btn-primary w-100 mb-2" id="connectOutlookAccount">
                <i class="ri-microsoft-line me-2"></i>Connect Outlook Account
                <span class="spinner-border spinner-border-sm d-none ms-2" role="status"></span>
            </button>

            <button class="btn btn-outline-secondary w-100" id="skipOutlookSetup">
                Skip for now
            </button>
        </div>

        <div class="card-footer text-center">
            <small class="text-muted">
                <i class="ri-shield-check-line me-1"></i>
                Your data is secure and encrypted
            </small>
        </div>
    </div>
</div>

<!-- Connection Progress Modal -->
<div class="modal fade" id="outlook-connection-modal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="connection-step" id="step-connecting">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <h6>Connecting to Outlook...</h6>
                    <p class="text-muted mb-0">Please wait while we establish the connection.</p>
                </div>

                <div class="connection-step d-none" id="step-syncing">
                    <div class="spinner-border text-success mb-3" role="status"></div>
                    <h6>Syncing your emails...</h6>
                    <p class="text-muted mb-0">This may take a few moments.</p>
                </div>

                <div class="connection-step d-none" id="step-success">
                    <i class="ri-check-circle-line fs-48 text-success mb-3"></i>
                    <h6>Successfully Connected!</h6>
                    <p class="text-muted mb-0">Your Outlook account is now connected.</p>
                    <button class="btn btn-success mt-3" onclick="location.reload()">Continue</button>
                </div>

                <div class="connection-step d-none" id="step-error">
                    <i class="ri-error-warning-line fs-48 text-danger mb-3"></i>
                    <h6>Connection Failed</h6>
                    <p class="text-muted mb-3" id="error-message">Unable to connect to your Outlook account.</p>
                    <button class="btn btn-outline-primary me-2" id="retryConnection">Try Again</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade effect-scale modal-overlay" id="outlookConnectionModal" data-bs-backdrop="static"
    data-bs-keyboard="false" tabindex="-1" aria-labelledby="outlookConnectionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-heading">
                <div class="modal-icon">
                    <svg clip-rule="evenodd" width="200" height="200" fill-rule="evenodd"
                        stroke-linejoin="round" stroke-miterlimit="1.41421" viewBox="0 0 560 400"
                        xmlns="http://www.w3.org/2000/svg">
                        <g fill-rule="nonzero">
                            <path
                                d="m366.585 103.756h-118.187c-5.295 0-9.652 4.357-9.652 9.652v10.971l66.614 20.625 70.877-20.625v-10.971c0-5.295-4.357-9.652-9.652-9.652z"
                                fill="#0364b8" />
                            <path
                                d="m387.58 209.659c1.007-3.165 1.811-6.391 2.406-9.659.001-1.635-.873-3.15-2.289-3.967l-.089-.048-.028-.013-74.507-42.444c-.321-.208-.654-.399-.996-.571-2.885-1.43-6.279-1.43-9.164 0-.342.172-.675.362-.997.571l-74.506 42.444-.028.013-.09.048c-1.415.817-2.289 2.332-2.288 3.967.595 3.268 1.399 6.494 2.406 9.659l79.002 57.78z"
                                fill="#0a2767" />
                            <path d="m334.99 124.379h-48.122l-13.894 20.625 13.894 20.623 48.122 41.247h41.247v-41.247z"
                                fill="#28a8ea" />
                        </g>
                        <path d="m238.746 124.379h48.122v41.247h-48.122z" fill="#0078d4" />
                        <path d="m334.99 124.379h41.247v41.247h-41.247z" fill="#50d9ff" />
                        <path d="m334.99 206.874-48.122-41.247h-48.122v41.247l48.122 41.248 74.465 12.154z"
                            fill="#0364b8" fill-rule="nonzero" />
                        <path d="m238.959 124.379h137.278" fill="none" />
                        <path d="m286.868 165.627h48.122v41.247h-48.122z" fill="#0078d4" />
                        <path d="m238.746 206.874h48.122v41.247h-48.122z" fill="#064a8c" />
                        <path d="m334.99 206.874h41.247v41.247h-41.247z" fill="#0078d4" />
                        <g fill-rule="nonzero">
                            <path
                                d="m308.805 263.369-81.079-59.121 3.396-5.974s73.867 42.072 74.994 42.705c.934.375 1.984.345 2.895-.083 1.051-.591 75.152-42.828 75.152-42.828l3.41 5.974z"
                                fill="#0a2767" fill-opacity=".498039" />
                            <path
                                d="m387.697 203.966-.089.055-.021.014-74.506 42.444c-3.006 1.938-6.814 2.175-10.037.625l25.951 34.792 56.743 12.354v.028c2.675-1.935 4.263-5.044 4.262-8.346v-85.932c.001 1.634-.874 3.15-2.289 3.966z"
                                fill="#1490df" />
                            <path
                                d="m389.986 285.932v-5.073l-68.629-39.103-8.284 4.716c-3.005 1.938-6.813 2.176-10.036.625l25.951 34.793 56.743 12.353v.028c2.675-1.936 4.262-5.044 4.262-8.346z"
                                fill-opacity=".047059" />
                            <path
                                d="m389.643 288.565-75.229-42.856-1.341.763c-3.005 1.938-6.813 2.176-10.036.625l25.951 34.793 56.743 12.353v.028c1.925-1.396 3.31-3.415 3.918-5.713z"
                                fill-opacity=".098039" />
                            <path
                                d="m227.402 204.056v-.069h-.068l-.207-.137c-1.334-.821-2.144-2.284-2.131-3.85v85.946c0 5.649 4.649 10.298 10.299 10.298h144.38c.858-.009 1.713-.124 2.543-.344.431-.075.848-.214 1.238-.412.146-.015.286-.062.412-.138.562-.23 1.094-.53 1.581-.894l.275-.206z"
                                fill="#28a8ea" />
                            <path
                                d="m293.742 259.582v-112.282c-.015-5.022-4.142-9.149-9.164-9.164h-45.619v51.256l-11.557 6.586-.027.014-.089.048c-1.416.818-2.29 2.333-2.29 3.966v68.767-.028h59.582c5.022-.014 9.149-4.142 9.164-9.163z"
                                fill-opacity=".098039" />
                            <path
                                d="m286.868 266.456v-112.282c-.015-5.021-4.143-9.148-9.164-9.164h-38.745v44.382l-11.557 6.586-.027.014-.089.048c-1.416.818-2.29 2.333-2.29 3.966v75.642-.028h52.708c5.021-.015 9.149-4.142 9.164-9.164zm0-13.749v-98.533c-.015-5.021-4.143-9.148-9.164-9.164h-38.745v44.382l-11.557 6.586-.027.014-.089.048c-1.416.818-2.29 2.333-2.29 3.966v61.892-.027h52.708c5.021-.015 9.149-4.142 9.164-9.164zm-6.875 0v-98.533c-.015-5.021-4.142-9.148-9.163-9.164h-31.871v44.382l-11.557 6.586-.027.014-.089.048c-1.416.818-2.29 2.333-2.29 3.966v61.892-.027h45.834c5.021-.015 9.148-4.142 9.163-9.164z"
                                fill-opacity=".2" />
                            <path
                                d="m179.164 145.004h91.658c5.027 0 9.164 4.136 9.164 9.163v91.659c0 5.027-4.137 9.164-9.164 9.164h-91.658c-5.027 0-9.164-4.137-9.164-9.164v-91.659c0-5.027 4.137-9.163 9.164-9.163z"
                                fill="#0078d4" />
                            <path
                                d="m196.584 182.593c2.435-5.189 6.367-9.532 11.288-12.47 5.453-3.122 11.662-4.677 17.943-4.496 5.817-.128 11.559 1.347 16.595 4.262 4.739 2.822 8.556 6.962 10.985 11.914 2.646 5.456 3.966 11.46 3.85 17.523.129 6.337-1.23 12.617-3.966 18.335-2.483 5.127-6.415 9.416-11.309 12.333-5.232 3.007-11.188 4.522-17.221 4.379-5.943.141-11.812-1.35-16.966-4.311-4.776-2.827-8.639-6.971-11.123-11.934-2.663-5.378-3.998-11.317-3.891-17.317-.118-6.283 1.189-12.511 3.822-18.218zm12.03 29.272c1.299 3.281 3.502 6.128 6.353 8.208 2.901 2.032 6.379 3.08 9.919 2.991 3.772.149 7.491-.932 10.594-3.08 2.816-2.08 4.961-4.942 6.167-8.229 1.356-3.664 2.024-7.547 1.973-11.453.042-3.94-.586-7.86-1.856-11.59-1.12-3.358-3.188-6.322-5.954-8.531-3.021-2.256-6.73-3.402-10.497-3.245-3.617-.094-7.173.96-10.154 3.011-2.904 2.087-5.156 4.958-6.49 8.277-2.95 7.599-2.967 16.031-.048 23.641z"
                                fill="#fff" />
                        </g>
                        <path d="m170 90.006h219.986v219.986h-219.986z" fill="none" />
                    </svg>
                </div>
                <h2 class="mb-3" id="outlookModalTitle">Connect to Microsoft Outlook</h2>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <p class="text-dark mb-4">
                        Connect your Microsoft Outlook account to start managing your emails seamlessly.
                    </p>

                    <div class="mb-3">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="me-3">
                                <i class="ri-mail-line fs-24 text-primary"></i>
                                <p class="fs-12 text-muted mt-1">Sync Emails</p>
                            </div>
                            <div class="me-3">
                                <i class="ri-contacts-line fs-24 text-success"></i>
                                <p class="fs-12 text-muted mt-1">Import Contacts</p>
                            </div>
                            <div>
                                <i class="ri-calendar-line fs-24 text-warning"></i>
                                <p class="fs-12 text-muted mt-1">Calendar Access</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button class="btn btn-primary w-100 mb-2" id="connectOutlookAccount">
                    <i class="ri-microsoft-line me-2" style="vertical-align: -2px"></i>Connect Outlook Account
                    <span class="spinner-border spinner-border-sm d-none ms-2" role="status"></span>
                </button>

                <button class="btn btn-outline-secondary w-100" id="skipOutlookSetup">
                    Skip for now
                </button>
            </div>
            <div class="mt-3">
                <div class="text-center">
                    <small class="text-muted">
                        <i class="ri-shield-check-line me-1"></i>
                        Your data is secure and encrypted
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $("#outlookConnectionModal").modal('show')
            //     const connectBtn = document.getElementById('connectOutlookAccount');
            //     const skipBtn = document.getElementById('skipOutlookSetup');
            //     const retryBtn = document.getElementById('retryConnection');
            //     const connectionModal = new bootstrap.Modal(document.getElementById('outlook-connection-modal'));

            //     // Connect to Outlook
            //     connectBtn.addEventListener('click', function() {
            //         connectToOutlook();
            //     });

            //     // Skip setup
            //     skipBtn.addEventListener('click', function() {
            //         document.querySelector('.outlook-setup-overlay').style.display = 'none';
            //         // Store in session that user skipped
            //         sessionStorage.setItem('outlook_setup_skipped', 'true');
            //     });

            //     // Retry connection
            //     retryBtn.addEventListener('click', function() {
            //         hideAllSteps();
            //         showStep('step-connecting');
            //         connectToOutlook();
            //     });

            //     function connectToOutlook() {
            //         const spinner = connectBtn.querySelector('.spinner-border');
            //         spinner.classList.remove('d-none');
            //         connectBtn.disabled = true;

            //         // Show connection modal
            //         connectionModal.show();
            //         showStep('step-connecting');

            //         // Get auth URL from server
            //         fetch('{{ route('mail.outlook.connect') }}', {
            //                 method: 'POST',
            //                 headers: {
            //                     'Content-Type': 'application/json',
            //                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
            //                 }
            //             })
            //             .then(response => response.json())
            //             .then(data => {
            //                 if (data.auth_url) {
            //                     // Redirect to Microsoft OAuth
            //                     window.location.href = data.auth_url;
            //                 } else {
            //                     showError('Failed to get authorization URL');
            //                 }
            //             })
            //             .catch(error => {
            //                 console.error('Connection error:', error);
            //                 showError('Network error occurred');
            //             })
            //             .finally(() => {
            //                 spinner.classList.add('d-none');
            //                 connectBtn.disabled = false;
            //             });
            //     }

            //     function showStep(stepId) {
            //         hideAllSteps();
            //         document.getElementById(stepId).classList.remove('d-none');
            //     }

            //     function hideAllSteps() {
            //         document.querySelectorAll('.connection-step').forEach(step => {
            //             step.classList.add('d-none');
            //         });
            //     }

            //     function showError(message) {
            //         document.getElementById('error-message').textContent = message;
            //         showStep('step-error');
            //     }

            //     // Check if user previously skipped setup
            //     if (sessionStorage.getItem('outlook_setup_skipped') === 'true') {
            //         document.querySelector('.outlook-setup-overlay').style.display = 'none';
            //     }

            //     // Handle OAuth callback success (if redirected back)
            //     const urlParams = new URLSearchParams(window.location.search);
            //     if (urlParams.get('outlook_connected') === 'true') {
            //         connectionModal.show();
            //         showStep('step-syncing');

            //         // Trigger sync
            //         fetch('{{ route('mail.outlook.sync') }}', {
            //                 method: 'POST',
            //                 headers: {
            //                     'Content-Type': 'application/json',
            //                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
            //                 }
            //             })
            //             .then(response => response.json())
            //             .then(data => {
            //                 showStep('step-success');
            //                 // Remove the parameter from URL
            //                 window.history.replaceState({}, document.title, window.location.pathname);
            //             })
            //             .catch(error => {
            //                 showError('Failed to sync emails');
            //             });
            //     }
        });
    </script>
@endpush
