<div id="messageListForm">
    <div class="bg-light p-3 rounded border mb-4">
        <div class="row g-3 mb-2">
            <div class="col-md-12">
                <div class="position-relative">
                    <i
                        class="bx bx-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted fs-15"></i>
                    <input type="text" class="form-inputs ps-4" id="searchTerm"
                        placeholder="Search messages, senders, subjects, references...">
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-inputs select2" id="filterPriority">
                    <option value="all">All Priorities</option>
                    <option value="low">Low Priority</option>
                    <option value="normal">Normal Priority</option>
                    <option value="high">High Priority</option>
                    <option value="urgent">Urgent Priority</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-inputs select2" id="filterCategory">
                    <option value="all">All Categories</option>
                    <option value="claim">Claim Notification</option>
                    <option value="policy">Policy Communication</option>
                    <option value="risk">Risk Assessment</option>
                    <option value="settlement">Settlement</option>
                    <option value="general">General Correspondence</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-inputs select2" id="filterFolder">
                    <option value="inbox">Inbox</option>
                    <option value="sent">Sent Items</option>
                    <option value="drafts">Drafts</option>
                    <option value="deleteditems">Deleted Items</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary w-100" id="refreshEmailsBtn">
                    <i class="bx bx-refresh me-1"></i> Refresh
                </button>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted" id="resultsInfo">Loading messages...</small>
            <div class="d-flex gap-2">
                <small class="text-muted" id="totalInfo">Total: {{ $totalMessages ?? 0 }} messages in system</small>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="checkConnectionBtn">
                    <i class="bx bx-wifi me-1"></i> Check Connection
                </button>
            </div>
        </div>
    </div>

    {{-- Connection Status Alert --}}
    <div id="connectionAlert" class="alert alert-warning d-none mb-3">
        <div class="d-flex align-items-center">
            <i class="bx bx-wifi-off me-2"></i>
            <span>Outlook not connected. </span>
            <button type="button" class="btn btn-sm btn-warning ms-2" id="connectOutlookBtn">
                Connect Now
            </button>
        </div>
    </div>

    {{-- Filtered messages count --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center">
            <i class="bx bx-filter me-2"></i>
            <h3 class="mb-0" id="filteredMessagesCount">Select Message to Reply (<span id="filteredCount">0</span>
                results)</h3>
        </div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-primary" id="fetchMoreBtn">
                <i class="bx bx-download me-1"></i> Fetch More (50)
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearFiltersBtn">
                <i class="bx bx-x me-1"></i> Clear Filters
            </button>
        </div>
    </div>

    <div id="messagesContainer">
        <div class="text-center py-5" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading messages...</p>
        </div>
    </div>

    {{-- Pagination --}}
    <nav id="paginationContainer" class="d-none">
        <div class="d-flex justify-content-between align-items-center bg-white border rounded"
            style="padding: 8px 16px; margin-top: 16px;">
            <span class="text-muted fw-medium fs-13" id="paginationInfo">Page 1 of 1</span>
            <ul class="pagination mb-0" id="paginationList">
            </ul>
        </div>
    </nav>
</div>

<style>
    #filteredMessagesCount {
        margin-bottom: 0;
        font-size: 18px !important;
        font-weight: 600;
    }

    .form-label {
        font-weight: bold;
    }

    #messagesContainer {
        height: 680px;
        overflow-x: hidden;
        overflow-y: auto;
        padding-right: 7px;
    }

    .priority-low {
        @apply bg-success bg-opacity-10 text-success;
    }

    .priority-normal {
        @apply bg-primary bg-opacity-10 text-primary;
    }

    .priority-high {
        @apply bg-warning bg-opacity-10 text-warning;
    }

    .priority-urgent {
        @apply bg-danger bg-opacity-10 text-danger;
    }

    .fa-6x {
        font-size: 3rem !important;
    }

    :root {
        --color-gray-100: #f3f4f6;
        --color-gray-200: #e5e7eb;
        --color-gray-400: #9ca3af;
        --color-gray-500: #6b7280;
        --color-gray-600: #4b5563;
        --color-gray-900: #111827;
        --color-gray-50: #f9fafb;
        --color-green-50: #f0fdf4;
        --color-green-600: #16a34a;
        --color-blue-50: #eff6ff;
        --color-blue-600: #2563eb;
        --color-blue-800: #1e40af;
        --color-orange-50: #fff7ed;
        --color-orange-600: #ea580c;
        --color-white: #ffffff;
        --spacing-1: 0.25rem;
        --spacing-2: 0.5rem;
        --spacing-3: 0.75rem;
        --spacing-4: 1rem;
        --spacing-6: 1.5rem;
        --radius-sm: 0.125rem;
        --radius-md: 0.375rem;
        --radius-lg: 0.5rem;
        --radius-full: 9999px;
        --text-xs: 0.75rem;
        --text-sm: 0.875rem;
        --text-base: 1rem;
        --leading-relaxed: 1.625;
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --transition-all: all 0.2s ease-in-out;
        --transition-colors: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
    }

    .settlement-card {
        border: 1px solid var(--color-gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-4);
        cursor: pointer;
        transition: var(--transition-all);
        background-color: var(--color-white);
        margin: 0 0 18px;
    }

    .settlement-card:hover {
        background-color: var(--color-gray-50);
        box-shadow: var(--shadow-md);
    }

    .settlement-card__content {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    .settlement-card__main {
        flex: 1;
    }

    .settlement-card__sidebar {
        text-align: right;
        margin-left: var(--spacing-4);
        flex-shrink: 0;
    }

    .settlement-card__badges {
        display: flex;
        align-items: center;
        gap: var(--spacing-3);
        margin-bottom: var(--spacing-2);
    }

    .settlement-card__badges .badge {
        padding: var(--spacing-1) var(--spacing-2) !important;
        border-radius: var(--radius-full);
        font-size: var(--text-xs) !important;
        font-weight: 500;
        text-transform: uppercase;
        /* color: var(--color-gray-600); */
    }

    .badge--priority-low {
        color: var(--color-green-600);
        background-color: var(--color-green-50);
    }

    .badge--priority-normal {
        color: var(--color-blue-600);
        background-color: var(--color-blue-50);
    }

    .badge--priority-high {
        color: var(--color-orange-600);
        background-color: var(--color-orange-50);
    }

    .badge--priority-urgent {
        color: #dc3545;
        background-color: #f8d7da;
    }

    .badge--type-risk,
    .badge--type-general {
        color: var(--color-gray-600) !important;
    }

    .badge--type-settlement {
        color: var(--color-gray-500);
        background-color: var(--color-gray-100);
    }

    .badge--id {
        color: var(--color-blue-600);
        background-color: var(--color-blue-50);
        font-family: monospace;
        text-transform: none;
    }

    .badge--thread {
        color: var(--color-orange-600) !important;
        background-color: var(--color-orange-50);
    }

    .badge--read {
        color: var(--color-gray-500) !important;
        background-color: var(--color-gray-100);
    }

    .badge--unread {
        color: #ffffff !important;
        background-color: #007bff;
    }

    .settlement-card__title {
        font-weight: 600;
        color: var(--color-gray-900);
        margin: 12px auto;
        font-size: var(--text-base);
    }

    .settlement-card__from {
        font-size: var(--text-sm);
        color: var(--color-gray-600);
        margin-bottom: var(--spacing-2);
    }

    .settlement-card__from-email {
        font-weight: 500;
    }

    .settlement-card__description {
        font-size: var(--text-sm);
        color: var(--color-gray-500);
        line-height: var(--leading-relaxed);
    }

    .settlement-card__timestamp {
        font-size: var(--text-xs);
        color: var(--color-gray-400);
        margin-bottom: var(--spacing-2);
        white-space: nowrap;
    }

    .reply-button {
        color: var(--color-blue-600);
        transition: var(--transition-colors);
        padding: var(--spacing-2);
        border-radius: var(--radius-full);
        border: none;
        background: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .reply-button:hover {
        color: var(--color-blue-800);
        background-color: var(--color-blue-50);
    }

    .reply-button__icon {
        width: var(--spacing-4);
        height: var(--spacing-4);
    }

    .message-unread {
        border-left: 4px solid #007bff;
        font-weight: 600;
    }

    @media (max-width: 640px) {
        .settlement-card__content {
            flex-direction: column;
        }

        .settlement-card__sidebar {
            margin-left: 0;
            margin-top: var(--spacing-3);
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .settlement-card__badges {
            flex-wrap: wrap;
        }
    }
</style>

@push('script')
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let messagesPerPage = 20;
            let allMessages = [];
            let filteredMessages = [];
            let isConnected = false;
            let currentFolder = 'inbox';

            initializeMessageList();

            $('#emailTabs button').on('shown.bs.tab', function(e) {
                if (e.target.id === 'replies-tab') {
                    loadMessages();
                }
            });

            $('#searchTerm').on('input', debounce(filterMessages, 500));
            $('#filterPriority, #filterCategory, #filterFolder').on('change', function() {
                if (this.id === 'filterFolder') {
                    currentFolder = $(this).val();
                    loadMessages();
                } else {
                    filterMessages();
                }
            });

            $('#refreshEmailsBtn').on('click', () => refreshData());
            $('#checkConnectionBtn').on('click', checkConnection);
            $('#connectOutlookBtn').on('click', () => window.OutlookConnectionManager?.show());
            $('#fetchMoreBtn').on('click', () => fetchMoreEmails());
            $('#clearFiltersBtn').on('click', clearFilters);

            $('#saveDraftBtn').on('click', saveDraft);
            $('#newEmailBtn, #newEmailInstead').on('click', handleNewEmail);

            /**
             * Initialize the message list system
             */
            async function initializeMessageList() {
                try {
                    if (typeof window.OutlookConnectionManager === 'undefined') {
                        setTimeout(initializeMessageList, 500);
                        return;
                    }

                    await checkConnection();
                    await loadMessages();

                } catch (error) {
                    console.error('Initialization failed:', error);
                    showFallbackToMockData();
                }
            }

            /**
             * Check Outlook connection status
             */
            async function checkConnection() {
                try {
                    const status = await window.OutlookConnectionManager.checkStatus();
                    isConnected = status.connected;
                    updateConnectionUI(status);
                    return status;

                } catch (error) {
                    isConnected = false;
                    updateConnectionUI({
                        connected: false,
                        error: error.message
                    });
                    return {
                        connected: false,
                        error: error.message
                    };
                }
            }

            /**
             * Update connection status UI
             */
            function updateConnectionUI(status) {
                const $alert = $('#connectionAlert');
                const $checkBtn = $('#checkConnectionBtn');

                if (status.connected) {
                    $alert.addClass('d-none');
                    $checkBtn.removeClass('btn-outline-danger').addClass('btn-outline-success');
                    $checkBtn.html('<i class="bx bx-wifi me-1"></i> Connected');
                } else {
                    $alert.removeClass('d-none');
                    $checkBtn.removeClass('btn-outline-success').addClass('btn-outline-danger');
                    $checkBtn.html('<i class="bx bx-wifi-off me-1"></i> Disconnected');
                }
            }

            /**
             * Load messages from Outlook or fallback to mock data
             */
            async function loadMessages(forceRefresh = false) {
                $('#loadingSpinner').show();
                $('#messagesContainer').html($('#loadingSpinner').parent().html());

                try {
                    if (!isConnected) {
                        await checkConnection();
                    }

                    if (isConnected) {
                        await loadOutlookMessages(forceRefresh);
                    }

                } catch (error) {
                    showErrorMessage('Failed to load messages: ' + error.message);
                }
            }

            /**
             * Load messages from Outlook
             */
            async function loadOutlookMessages(forceRefresh = false) {
                try {
                    const result = await window.OutlookConnectionManager.fetchEmails(currentFolder, 100,
                        forceRefresh);

                    if (result.success && result.emails) {
                        allMessages = result.emails.map(transformOutlookMessage);
                    } else {
                        throw new Error(result.message || 'Failed to fetch emails');
                    }

                    filteredMessages = [...allMessages];
                    updateResultsInfo();
                    renderMessages();
                    renderPagination();

                } catch (error) {
                    console.error('Outlook fetch failed:', error);
                    toastr.error('Failed to load emails: ' + error.message);
                }
            }

            /**
             * Transform Outlook message to our internal format
             */
            function transformOutlookMessage(outlookMsg) {
                const to_recipients = outlookMsg.to_recipients?.length > 0 ? JSON.parse(outlookMsg.to_recipients) :
                    [];
                const formattedList = to_recipients.map(item => `${item.name} <${item.email}>`).join(', ');

                const transformed = {
                    id: outlookMsg.id,
                    subject: outlookMsg.subject || '(No Subject)',
                    from: outlookMsg.from_email || 'Unknown',
                    fromName: outlookMsg.from_name || '',
                    preview: outlookMsg.body_preview || '',
                    priority: detectPriority(outlookMsg),
                    category: detectCategory(outlookMsg),
                    reference: extractReference(outlookMsg),
                    date: formatDate(outlookMsg.date_received),
                    isRead: outlookMsg.is_read || false,
                    thread: outlookMsg.conversation_id ? true : false,
                    conversationId: outlookMsg.conversation_id,
                    importance: outlookMsg.importance || 'normal',
                    hasAttachments: outlookMsg.has_attachments || false,
                    bodyHtml: outlookMsg.body_html || '',
                    toList: formattedList ?? '',
                    conversationId: outlookMsg.conversation_id ?? '',
                    messageId: outlookMsg.uid ?? '',
                };

                return transformed;
            }

            /**
             * Detect priority from Outlook message
             */
            function detectPriority(msg) {
                const importance = (msg.importance || 'normal').toLowerCase();
                const subject = (msg.subject || '').toLowerCase();

                if (importance === 'high' || subject.includes('urgent') || subject.includes('asap')) {
                    return 'urgent';
                } else if (importance === 'high') {
                    return 'high';
                } else if (importance === 'low') {
                    return 'low';
                }
                return 'normal';
            }

            /**
             * Detect category from message content
             */
            function detectCategory(msg) {
                const subject = (msg.subject || '').toLowerCase();
                const preview = (msg.body_preview || '').toLowerCase();
                const content = subject + ' ' + preview;

                if (content.includes('claim') || content.includes('settlement')) return 'settlement';
                if (content.includes('policy')) return 'policy';
                if (content.includes('risk') || content.includes('assessment')) return 'risk';
                if (content.includes('claim')) return 'claim';
                return 'general';
            }

            /**
             * Extract reference number from message
             */
            function extractReference(msg) {
                const subject = msg.subject || '';
                const preview = msg.body_preview || '';
                const content = subject + ' ' + preview;

                const refMatch = content.match(/(?:REF|CLAIM|POL|CASE)[-:\s]*(\w+)/i);
                return refMatch ? refMatch[0] : `MSG-${msg.id.toString().substring(0, 8)}`;
            }

            /**
             * Format date for display
             */
            function formatDate(dateString) {
                if (!dateString) return 'Unknown';

                const date = new Date(dateString);
                const now = new Date();
                const diffMs = now - date;
                const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

                if (diffDays === 0) {
                    return date.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } else if (diffDays === 1) {
                    return 'Yesterday';
                } else if (diffDays < 7) {
                    return `${diffDays} days ago`;
                } else {
                    return date.toLocaleDateString();
                }
            }

            async function refreshData() {
                try {
                    $('#refreshEmailsBtn').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1"></span> Fetching...');

                    const result = await window.OutlookConnectionManager.fetchEmails(currentFolder, 100, true);
                    if (result.success) {
                        if (result.emails.length > 0) {
                            allMessages = result.emails.map(transformOutlookMessage);
                        }
                        toastr.success('Emails refreshed successfully!');
                    }

                    filteredMessages = [...allMessages];
                    updateResultsInfo();
                    renderMessages();
                    renderPagination();
                } catch (error) {
                    console.error('Refresh failed:', error);
                    toastr.error('Failed to refresh emails: ' + error.message);
                } finally {
                    $('#refreshEmailsBtn').prop('disabled', false).html(
                        '<i class="bx bx-refresh me-1"></i> Refresh');
                }
            }

            /**
             * Fetch more emails from current folder
             */
            async function fetchMoreEmails() {
                try {
                    $('#fetchMoreBtn').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1"></span> Fetching...');

                    const result = await window.OutlookConnectionManager.fetchEmails(currentFolder, 50);

                    if (result.success && result.emails) {
                        const newMessages = result.emails.map(transformOutlookMessage);

                        const existingIds = new Set(allMessages.map(m => m.id));
                        const uniqueNewMessages = newMessages.filter(m => !existingIds.has(m.id));

                        allMessages = [...allMessages, ...uniqueNewMessages];
                        filteredMessages = [...allMessages];

                        updateResultsInfo();
                        renderMessages();
                        renderPagination();

                        if (uniqueNewMessages.length === 0) {
                            toastr.info(`Fetched ${uniqueNewMessages.length} new messages`)
                        } else {
                            toastr.success('No new messages found')
                        }
                    }

                } catch (error) {
                    console.error('Fetch more failed:', error);
                    showToast('error', 'Failed to fetch more emails: ' + error.message);
                } finally {
                    $('#fetchMoreBtn').prop('disabled', false).html(
                        '<i class="bx bx-download me-1"></i> Fetch More (50)');
                }
            }

            /**
             * Filter messages based on search term and selected filters
             */
            function filterMessages() {
                const searchTerm = $('#searchTerm').val().toLowerCase().trim();
                const priorityFilter = $('#filterPriority').val();
                const categoryFilter = $('#filterCategory').val();

                filteredMessages = allMessages.filter(message => {
                    const matchesSearch = !searchTerm ||
                        message.subject.toLowerCase().includes(searchTerm) ||
                        message.from.toLowerCase().includes(searchTerm) ||
                        message.fromName.toLowerCase().includes(searchTerm) ||
                        message.preview.toLowerCase().includes(searchTerm) ||
                        message.reference.toLowerCase().includes(searchTerm);

                    const matchesPriority = priorityFilter === 'all' || message.priority === priorityFilter;
                    const matchesCategory = categoryFilter === 'all' || message.category === categoryFilter;

                    return matchesSearch && matchesPriority && matchesCategory;
                });

                currentPage = 1;
                updateResultsInfo();
                renderMessages();
                renderPagination();
            }

            /**
             * Update the results information display
             */
            function updateResultsInfo() {
                const totalFiltered = filteredMessages.length;
                const totalAll = allMessages.length;

                $('#filteredCount').text(totalFiltered);
                $('#resultsInfo').text(`Showing ${totalFiltered} of ${totalAll} messages`);
            }

            /**
             * Render the current page of messages
             */
            function renderMessages() {
                const startIndex = (currentPage - 1) * messagesPerPage;
                const currentMessages = filteredMessages.slice(startIndex, startIndex + messagesPerPage);

                $('#loadingSpinner').hide();

                if (currentMessages.length === 0) {
                    $('#messagesContainer').html(`
                        <div class="text-center py-5 bg-light rounded">
                            <i class="bx bx-envelope fa-6x text-muted mb-3"></i>
                            <p class="text-muted">No messages found matching your criteria</p>
                            <button type="button" class="btn btn-primary btn-sm" onclick="clearFilters()">
                                Clear filters
                            </button>
                        </div>
                    `);
                    $('#paginationContainer').addClass('d-none');
                    return;
                }

                const messagesHtml = currentMessages.map(message => `
                    <div class="settlement-card ${!message.isRead ? 'message-unread' : ''}" data-message-id="${message.id}" ondblclick="handleReply('${message.id}')">
                        <div class="settlement-card__content">
                            <div class="settlement-card__main">
                                <div class="settlement-card__badges">
                                    ${message.priority ? `<span class="badge badge--priority-${escapeHtml(message.priority)}">${message.priority.toUpperCase()}</span>` : ''}
                                    ${message.category ? `<span class="badge badge--type-${escapeHtml(message.category)}">${message.category.toUpperCase()}</span>` : ''}
                                    ${message.reference ? `<span class="badge badge--id">${escapeHtml(message.reference)}</span>` : ''}
                                    ${message.thread ? `<span class="badge badge--thread">THREAD</span>` : ''}
                                    ${!message.isRead ? `<span class="badge badge--unread">UNREAD</span>` : `<span class="badge badge--read">READ</span>`}
                                    ${message.hasAttachments ? `<span class="badge badge--thread">📎</span>` : ''}
                                </div>
                                <h3 class="settlement-card__title">${escapeHtml(message.subject)}</h3>
                                <p class="settlement-card__from">
                                    From: <span class="settlement-card__from-email">${escapeHtml(message.fromName || message.from)}</span>
                                    ${message.fromName && message.from !== message.fromName ? `<span class="text-muted">&lt;${escapeHtml(message.from)}&gt;</span>` : ''}
                                </p>
                                <p class="settlement-card__description">${escapeHtml(message.preview)}</p>
                            </div>
                            <div class="settlement-card__sidebar">
                                <p class="settlement-card__timestamp">${escapeHtml(message.date)}</p>
                                <button class="reply-button" onclick="event.stopPropagation(); handleReply('${message.id}')">
                                    <svg class="reply-button__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M20 18v-2a4 4 0 0 0-4-4H4"></path>
                                        <path d="m9 17-5-5 5-5"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');

                $('#messagesContainer').html(messagesHtml);
                $('#paginationContainer').removeClass('d-none');
            }

            /**
             * Render pagination controls
             */
            function renderPagination() {
                const totalPages = Math.ceil(filteredMessages.length / messagesPerPage);

                if (totalPages <= 1) {
                    $('#paginationContainer').addClass('d-none');
                    return;
                }

                $('#paginationInfo').text(`Page ${currentPage} of ${totalPages}`);

                let paginationHtml = '';
                paginationHtml += `
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">&laquo;</a>
                    </li>
                `;

                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                        paginationHtml += `
                            <li class="page-item ${i === currentPage ? 'active' : ''}">
                                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                            </li>
                        `;
                    } else if (i === currentPage - 3 || i === currentPage + 3) {
                        paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                paginationHtml += `
                    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">&raquo;</a>
                    </li>
                `;

                $('#paginationList').html(paginationHtml);
            }

            /**
             * Show error message
             */
            function showErrorMessage(message) {
                $('#messagesContainer').html(`
                    <div class="alert alert-danger">
                        <i class="bx bx-error-circle me-2"></i>
                        <strong>Error:</strong> ${escapeHtml(message)}
                        <button type="button" class="btn btn-outline-danger btn-sm ms-3" onclick="loadMessages(true)">
                            Try Again
                        </button>
                    </div>
                `);
                $('#loadingSpinner').hide();
            }

            /**
             * Show toast notification
             */
            function showToast(type, message) {
                const toastHtml = `
                    <div class="toast show align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">${escapeHtml(message)}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;

                let toastContainer = $('.toast-container');
                if (toastContainer.length === 0) {
                    toastContainer = $('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
                    $('body').append(toastContainer);
                }

                const $toast = $(toastHtml);
                toastContainer.append($toast);

                setTimeout(() => {
                    $toast.fadeOut(() => $toast.remove());
                }, 5000);
            }

            /**
             * Change to specific page
             */
            window.changePage = function(page) {
                const totalPages = Math.ceil(filteredMessages.length / messagesPerPage);
                if (page >= 1 && page <= totalPages && page !== currentPage) {
                    currentPage = page;
                    renderMessages();
                    renderPagination();
                }
            };

            /**
             * Handle reply to message
             */
            window.handleReply = function(messageId) {
                const message = allMessages.find(m => Number(m.id) === Number(messageId));
                if (!message) {
                    console.log(allMessages)
                    console.error('Message not found:', messageId);
                    return;

                }

                $('#to').val(message.from);
                $('#subject').val(message.subject);
                $('#category').val(message.category);
                // $('#reference').val(message.reference);

                $('#toggleEmailBodyBtn').css('display', 'block').html(
                    '<i class="bx bx-chevron-up me-1"></i>Hide Original Email');
                $('#emailBody').removeClass('hidden');
                $('#message').attr('rows', 5).val('');

                $("#emailBody #originalFrom").text(`${message.fromName} <${message.from}>`)
                $("#emailBody #originalSent").text(message.date)
                $("#emailBody #originalTo").text(message.toList)
                $("#emailBody #originalSubject").text(message.subject)

                $('#originalContent #threadMessages').contents().find('body').html(message.bodyHtml);
                $('#isReply').val('1');
                $('#replyToId').val(message.messageId);
                $('#originalMessageId').val(message.conversationId);

                $('#composeTitle').text('Reply to Message');
                $('#newEmailInstead').removeClass('d-none');

                $('.compose_attachement').hide();

                $('#compose-tab').tab('show');

                // Show success message
                let subject = message.subject || '';
                let preview = subject.length > 50 ? subject.substring(0, 50) + '...' : subject;
                toastr.success(`Replying to: ${preview}`, {
                    timeOut: 2000
                });
            };

            /**
             * Handle new email creation
             */
            function handleNewEmail() {
                resetForm();
                $('#compose-tab').tab('show');
            }

            function resetForm() {
                $('#emailForm')[0].reset();
                $('#isReply').val('0');
                $('#originalMessageId').val('');
                $('#composeTitle').text('Compose New Email');
                $('#newEmailInstead').addClass('d-none');
                $('#threadMessage').css('display', 'none');
            }

            /**
             * Clear all filters
             */
            function clearFilters() {
                $('#searchTerm').val('');
                $('#filterPriority').val('all').trigger('change');
                $('#filterCategory').val('all').trigger('change');
                filterMessages();
            };

            /**
             * Save draft
             */
            function saveDraft() {
                const $form = $('#emailForm');
                const formData = new FormData($form[0]);
                formData.append('save_as_draft', '1');


                //{{-- Uncomment and update this when you have the backend route
                // $.ajax({
                //     url: '{{ route('emails.draft') }}',
                //     method: 'POST',
                //     data: formData,
                //     processData: false,
                //     contentType: false,
                //     success: function(response) {
                //         console.log('Draft saved:', response);
                //         showToast('success', 'Draft saved successfully!');
                //     },
                //     error: function(xhr, status, error) {
                //         console.error('Draft save failed:', error);
                //         showToast('error', 'Failed to save draft. Please try again.');
                //     }
                // --}}});
            }

            /**
             * Debounce function for search input
             */
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func.apply(this, args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            /**
             * Escape HTML to prevent XSS
             */
            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        });
    </script>
@endpush
