@props([
    'autoShow' => true,
    'showCancelButton' => true,
    'redirectOnCancel' => null,
    'onSuccess' => null,
    'fetchEmailsOnConnect' => false,
    'showToastMessage' => true,
    'enableLoadingSpinner' => false,
])

<div {{ $attributes->merge(['class' => 'outlook-connection-wrapper']) }}>
    <!-- Connection Modal -->
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
                                <path
                                    d="m334.99 124.379h-48.122l-13.894 20.625 13.894 20.623 48.122 41.247h41.247v-41.247z"
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
                    <h2 id="outlookModalTitle">Connect to Microsoft Outlook</h2>
                    <p id="outlookModalDescription">
                        To access your emails and sync with this system, please connect your Microsoft Outlook account.
                    </p>
                </div>

                <div class="modal-actions text-center">
                    @if ($showCancelButton)
                        <button class="btn btn-light text-center" id="outlookCancelBtn">
                            Cancel
                        </button>
                    @endif
                    <button class="btn btn-primary text-center" id="outlookConnectBtn">
                        <span id="outlookConnectText" class="text-center">Connect to Outlook</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner Modal -->
    <div class="modal effect-scale modal-overlay-shadow" id="outlookLoadingModal" tabindex="-1"
        data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mb-0" id="outlookLoadingText">Processing...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="outlookSuccessToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header text-default">
                <i class="bx bx-check-circle text-success fs-16 me-2"></i>
                <strong class="me-auto">Outlook Connection</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body text-fixed-white">
                <span id="outlookToastMessage">Successfully connected to Microsoft Outlook!</span>
            </div>
        </div>
    </div>

    <!-- Error Toast -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="outlookErrorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header text-danger">
                <i class="ri-error-warning-fill me-2"></i>
                <strong class="me-auto">Connection Error</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <span id="outlookErrorMessage">Connection failed. Please try again.</span>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.OutlookConnectionManager = new(function() {
                this.config = {
                    endpoints: {
                        connect: @json(route('admin.outlook.connect')),
                        status: @json(route('admin.outlook.status')),
                        disconnect: @json(route('admin.outlook.disconnect')),
                        fetchEmails: @json(route('admin.emails.fetch')),
                    },
                    csrf: @json(csrf_token()),
                    debug: @json(config('app.debug', false)),
                    options: {
                        autoShow: @json($autoShow),
                        showCancelButton: @json($showCancelButton),
                        redirectOnCancel: @json($redirectOnCancel),
                        onSuccess: @json($onSuccess),
                        fetchEmailsOnConnect: @json($fetchEmailsOnConnect),
                        showToastMessage: @json($showToastMessage),
                        enableLoadingSpinner: @json($enableLoadingSpinner),
                    }
                };

                this.state = {
                    isConnecting: false,
                    isFetching: false,
                    retryCount: 0,
                    maxRetries: 3
                };

                console.log('initiated', this.config.options.fetchEmailsOnConnect)


                this.init = function() {
                    this.bindEvents();
                    if (this.config.options.autoShow) {
                        this.checkInitialStatus();
                    }
                };

                this.bindEvents = function() {
                    const cancelBtn = document.getElementById('outlookCancelBtn');
                    const connectBtn = document.getElementById('outlookConnectBtn');
                    const modal = document.getElementById('outlookConnectionModal');

                    if (cancelBtn) {
                        cancelBtn.addEventListener('click', this.handleCancel.bind(this));
                    }

                    if (connectBtn) {
                        connectBtn.addEventListener('click', this.handleConnect.bind(this));
                    }

                    if (modal) {
                        modal.addEventListener('hidden.bs.modal', this.handleModalClose.bind(this));
                    }
                };

                this.show = function() {
                    const modal = new bootstrap.Modal(document.getElementById(
                        'outlookConnectionModal'));
                    modal.show();
                };

                this.hide = function() {
                    const modal = bootstrap.Modal.getInstance(document.getElementById(
                        'outlookConnectionModal'));
                    if (modal) {
                        modal.hide();
                    }
                };

                this.checkStatus = function() {
                    return this.checkInitialStatus();
                };

                this.checkInitialStatus = async function() {
                    try {
                        const response = await this.makeRequest('GET', this.config.endpoints
                            .status);

                        if (response.connected) {
                            this.handleConnectionSuccess(response);
                            return response;
                        }

                        if (this.config.options.autoShow) {
                            this.show();
                        }

                        return response;

                    } catch (error) {
                        this.log('Initial status check failed', error);

                        if (this.config.options.autoShow) {
                            this.show();
                        }

                        return {
                            connected: false,
                            error: error.message
                        };
                    }
                };

                this.handleConnect = async function() {
                    if (this.state.isConnecting) return;

                    try {
                        this.state.isConnecting = true;
                        this.updateUI('connecting');
                        this.showLoading('Connecting to Outlook...');

                        const response = await this.makeRequest('POST', this.config.endpoints
                            .connect, {
                                scopes: []
                            });

                        if (response.success && response.auth_url) {
                            window.location.href = response.auth_url;
                        } else if (response.connected) {
                            this.handleConnectionSuccess(response);
                        } else {
                            throw new Error(response.message || 'Connection failed');
                        }
                    } catch (error) {
                        await this.handleError(error);
                    }
                };

                this.handleConnectionSuccess = function(response) {
                    this.state.isConnecting = false;
                    this.hideLoading();
                    this.updateUI('success');
                    this.hide();

                    this.showToast('success', 'Successfully connected to Microsoft Outlook!');

                    if (response.email) {
                        this.updateUserInfo(response);
                    }

                    if (this.config.options.fetchEmailsOnConnect) {
                        this.fetchEmails();
                    }

                    if (this.config.options.onSuccess && typeof window[this.config.options
                            .onSuccess] === 'function') {
                        window[this.config.options.onSuccess](response);
                    }
                };

                this.handleCancel = function() {
                    this.cleanup();

                    if (this.config.options.redirectOnCancel) {
                        window.location.href = this.config.options.redirectOnCancel;
                    } else {
                        this.hide();
                    }
                };

                this.handleModalClose = function() {
                    if (!this.state.isConnecting) {
                        this.cleanup();
                    }
                };

                this.handleError = async function(error) {
                    this.state.isConnecting = false;
                    this.hideLoading();

                    this.log('Connection error', error);

                    if (this.state.retryCount < this.state.maxRetries && this.isRetryableError(
                            error)) {
                        this.state.retryCount++;
                        this.updateUI('retrying', this.state.retryCount);

                        await this.delay(2000);
                        return this.handleConnect();
                    }

                    this.state.retryCount = 0;
                    this.updateUI('error');
                    this.showToast('error', this.getErrorMessage(error));
                };

                this.fetchEmails = function(folder = 'inbox', limit = 100, forceFetch = false) {
                    return new Promise(async (resolve, reject) => {
                        if (this.state.isFetching) {
                            reject(new Error('Already fetching emails'));
                            return;
                        }

                        try {
                            this.state.isFetching = true;
                            if (this.config.options.showToastMessage) {
                                this.showLoading('Fetching emails...');
                            }

                            const response = await this.makeRequest('POST', this.config
                                .endpoints.fetchEmails, {
                                    folder: folder,
                                    limit: limit,
                                    forceFetch: forceFetch
                                });

                            if (response.success) {
                                this.hideLoading();
                                console.log('response', response)
                                // if (response.action === 'fetched_and_stored') {
                                //     toastr.success(
                                //         `Successfully fetched ${response.fetched_count} emails!`
                                //     );
                                //     setTimeout(() => window.location.reload(), 1500);
                                // } else {
                                //     toastr.success(
                                //         `Fetched ${response.fetched_count || response.existing_count} emails`
                                //     );
                                // }
                                resolve(response);
                            } else {
                                throw new Error(response.message ||
                                    'Failed to fetch emails');
                            }
                        } catch (error) {
                            this.hideLoading();
                            this.showToast('error', 'Failed to fetch emails: ' + error
                                .message);
                            reject(error);
                        } finally {
                            this.state.isFetching = false;
                        }
                    });
                };

                this.updateUI = function(state, extra = null) {
                    const button = document.getElementById('outlookConnectBtn');
                    const text = document.getElementById('outlookConnectText');

                    if (!button || !text) return;

                    button.className = 'btn';
                    button.disabled = false;

                    switch (state) {
                        case 'connecting':
                            button.className += ' btn-secondary';
                            button.disabled = true;
                            text.innerHTML =
                                '<span class="spinner-border spinner-border-sm me-2"></span>Connecting...';
                            break;

                        case 'retrying':
                            button.className += ' btn-secondary';
                            button.disabled = true;
                            text.innerHTML =
                                `<span class="spinner-border spinner-border-sm me-2"></span>Retrying (${extra}/${this.state.maxRetries})...`;
                            break;

                        case 'success':
                            button.className += ' btn-success';
                            button.disabled = true;
                            text.innerHTML = '<i class="ri-check-line me-2"></i>Connected!';
                            break;

                        case 'error':
                            button.className += ' btn-danger';
                            text.innerHTML = '<i class="ri-alert-line me-2"></i>Try Again';
                            break;

                        default:
                            button.className += ' btn-primary';
                            text.textContent = 'Connect to Outlook';
                    }
                };

                this.showLoading = function(message = 'Processing...') {
                    const loadingText = document.getElementById('outlookLoadingText');
                    if (loadingText) {
                        loadingText.textContent = message;
                    }

                    const modal = new bootstrap.Modal(document.getElementById('outlookLoadingModal'));
                    modal.show();
                };

                this.hideLoading = function() {
                    const modal = bootstrap.Modal.getInstance(document.getElementById(
                        'outlookLoadingModal'));
                    if (modal) {
                        modal.hide();
                    }
                };

                this.showToast = function(type, message) {
                    const toastId = type === 'success' ? 'outlookSuccessToast' : 'outlookErrorToast';
                    const messageId = type === 'success' ? 'outlookToastMessage' :
                        'outlookErrorMessage';

                    const messageElement = document.getElementById(messageId);
                    if (messageElement) {
                        messageElement.textContent = message;
                    }

                    const toastElement = document.getElementById(toastId);
                    if (toastElement) {
                        const toast = new bootstrap.Toast(toastElement);
                        if (this.config.options.showToastMessage) {
                            toast.show();
                        }
                    }
                };

                this.updateUserInfo = function(response) {
                    const displayName = document.getElementById('displayName');
                    const displayEmail = document.getElementById('displayEmail');

                    if (displayName && response.displayName) {
                        displayName.textContent = response.displayName;
                    }

                    if (displayEmail && response.email) {
                        displayEmail.textContent = response.email;
                    }
                };

                this.makeRequest = async function(method, url, data = null) {
                    const options = {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': this.config.csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    };

                    if (data) {
                        options.body = JSON.stringify(data);
                    }

                    const response = await fetch(url, options);

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.message ||
                            `HTTP ${response.status}: ${response.statusText}`);
                    }

                    return await response.json();
                };

                this.isRetryableError = function(error) {
                    const message = error.message.toLowerCase();
                    return message.includes('timeout') ||
                        message.includes('network') ||
                        message.includes('500') ||
                        message.includes('502') ||
                        message.includes('503') ||
                        message.includes('504');
                };

                this.getErrorMessage = function(error) {
                    if (error.message) return error.message;

                    const message = error.toString().toLowerCase();

                    if (message.includes('401')) {
                        return 'Authentication failed. Please try again.';
                    } else if (message.includes('403')) {
                        return 'Access denied. Please check your permissions.';
                    } else if (message.includes('404')) {
                        return 'Service not found. Please contact support.';
                    } else if (message.includes('timeout')) {
                        return 'Request timed out. Please check your connection.';
                    } else if (message.includes('429')) {
                        return 'Too many requests. Please wait and try again.';
                    } else if (message.includes('5')) {
                        return 'Server error. Please try again later.';
                    }

                    return 'Connection failed. Please try again.';
                };

                this.cleanup = function() {
                    this.state.isConnecting = false;
                    this.state.isFetching = false;
                    this.state.retryCount = 0;
                };

                this.delay = function(ms) {
                    return new Promise(resolve => setTimeout(resolve, ms));
                };

                this.log = function(message, data = null) {
                    if (this.config.debug) {
                        console.log(`[Outlook Connection] ${message}`, data);
                    }
                };

                this.init();
            })();

            document.addEventListener('visibilitychange', function() {
                if (document.hidden && window.OutlookConnectionManager) {
                    window.OutlookConnectionManager.cleanup();
                }
            });

            window.addEventListener('beforeunload', function() {
                if (window.OutlookConnectionManager) {
                    window.OutlookConnectionManager.cleanup();
                }
            });
        });
    </script>
@endpush
