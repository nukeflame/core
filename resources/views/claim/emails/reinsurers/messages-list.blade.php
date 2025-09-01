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

        <div class="d-flex justify-content-between align-items-center mt-3" id="messageListInfo">
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
    <div class="d-flex align-items-center justify-content-between mb-3" id="filteredMessagesContainer">
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


    .badge--type-claim {
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
        class EmailManager {
            constructor() {
                this.currentPage = 1;
                this.messagesPerPage = 20;
                this.allMessages = [];
                this.filteredMessages = [];
                this.isConnected = false;
                this.currentFolder = 'inbox';
                this.isLoading = false;

                this.handleSearch = this.debounce(this.filterMessages.bind(this), 500);
                this.handleFilterChange = this.handleFilterChange.bind(this);

                this.initialize();
            }

            async initialize() {
                try {
                    this.bindEventListeners();
                    await this.checkConnection();
                    if (this.isConnected) {
                        await this.loadMessages();
                    }
                } catch (error) {
                    console.error('Email manager initialization failed:', error);
                    this.showErrorMessage('Failed to initialize email system');
                }
            }

            bindEventListeners() {
                $('#emailTabs button').on('shown.bs.tab', (e) => {
                    if (e.target.id === 'replies-tab') {
                        this.loadMessages();
                    }
                });

                $('#searchTerm').on('input', this.handleSearch);
                $('#filterPriority, #filterCategory').on('change', this.handleFilterChange);
                $('#filterFolder').on('change', (e) => {
                    this.currentFolder = $(e.target).val();
                    this.loadMessages();
                });

                $('#refreshEmailsBtn').on('click', () => this.refreshData());
                $('#checkConnectionBtn').on('click', () => this.checkConnection());
                $('#connectOutlookBtn').on('click', () => this.redirectToMail());
                $('#fetchMoreBtn').on('click', () => this.fetchMoreEmails());
                $('#clearFiltersBtn').on('click', () => this.clearFilters());
                $('#saveDraftBtn').on('click', () => this.saveDraft());
                $('#newEmailBtn, #newEmailInstead').on('click', () => this.handleNewEmail());
            }

            async checkConnection() {
                try {
                    const connectionStatus = @json(auth()->user()->hasOutlookConnection());
                    this.isConnected = Boolean(connectionStatus);
                    this.updateConnectionUI(this.isConnected);
                    return this.isConnected;
                } catch (error) {
                    console.error('Connection check failed:', error);
                    this.isConnected = false;
                    this.updateConnectionUI(false);
                    return false;
                }
            }

            updateConnectionUI(isConnected) {
                const $alert = $('#connectionAlert');
                const $checkBtn = $('#checkConnectionBtn');
                const $container = $('#filteredMessagesContainer');
                const $spinner = $('#loadingSpinner');

                if (isConnected) {
                    $alert.addClass('d-none');
                    $container.removeClass('d-none');
                    $checkBtn.removeClass('btn-outline-danger').addClass('btn-outline-success')
                        .html('<i class="bx bx-wifi me-1"></i> Connected');
                } else {
                    $alert.removeClass('d-none');
                    $container.addClass('d-none');
                    $spinner.addClass('d-none');
                    $checkBtn.removeClass('btn-outline-success').addClass('btn-outline-danger')
                        .html('<i class="bx bx-wifi-off me-1"></i> Disconnected');
                }
            }

            async loadMessages(forceRefresh = false) {
                if (this.isLoading) return;

                this.isLoading = true;
                this.showLoadingState();

                try {
                    if (!this.isConnected) {
                        await this.checkConnection();
                    }

                    if (this.isConnected) {
                        await this.loadOutlookMessages(forceRefresh, this.currentFolder);
                    } else {
                        this.showErrorMessage('Outlook not connected. Please connect to continue.');
                    }
                } catch (error) {
                    console.error('Load messages failed:', error);
                    this.showErrorMessage('Failed to load messages: ' + error.message);
                } finally {
                    this.isLoading = false;
                    this.hideLoadingState();
                }
            }

            async loadOutlookMessages(forceRefresh = false, folderFilter = null) {
                try {
                    const emailData = @json($emails);

                    if (!emailData || !Array.isArray(emailData)) {
                        throw new Error('Invalid email data received');
                    }

                    this.allMessages = emailData.map(email => this.transformOutlookMessage(email));

                    this.applyFolderFilter(folderFilter);

                    this.sortMessages(this.filteredMessages, 'date', 'desc');

                    this.currentPage = 1;

                    this.updateResultsInfo();
                    this.renderMessages();
                    this.renderPagination();

                } catch (error) {
                    console.error('Outlook fetch failed:', error);
                    throw error;
                }
            }

            applyFolderFilter(folderFilter) {
                const targetFolder = folderFilter || 'inbox';
                this.filteredMessages = this.allMessages.filter(message => {
                    if (!message.folder) return false;
                    return message.folder.toLowerCase() === targetFolder.toLowerCase();
                });
            }

            transformOutlookMessage(outlookMsg) {
                try {
                    let formattedList = '';
                    if (outlookMsg.to_recipients) {
                        try {
                            const recipients = typeof outlookMsg.to_recipients === 'string' ?
                                JSON.parse(outlookMsg.to_recipients) :
                                outlookMsg.to_recipients;

                            if (Array.isArray(recipients)) {
                                formattedList = recipients
                                    .map(item => `${item.name || item.email} <${item.email}>`)
                                    .join(', ');
                            }
                        } catch (e) {
                            console.warn('Failed to parse recipients for message:', outlookMsg.id);
                        }
                    }

                    return {
                        id: outlookMsg.id || `msg_${Date.now()}`,
                        subject: this.sanitizeText(outlookMsg.subject) || '(No Subject)',
                        from: this.sanitizeText(outlookMsg.from_email) || 'Unknown',
                        fromName: this.sanitizeText(outlookMsg.from_name) || '',
                        preview: this.sanitizeText(outlookMsg.body_preview) || '',
                        priority: this.detectPriority(outlookMsg),
                        category: this.detectCategory(outlookMsg),
                        reference: this.extractReference(outlookMsg),
                        date: this.formatDate(outlookMsg.date_received),
                        isRead: Boolean(outlookMsg.is_read),
                        thread: Boolean(outlookMsg.conversation_id),
                        conversationId: outlookMsg.conversation_id || '',
                        importance: outlookMsg.importance || 'normal',
                        hasAttachments: Boolean(outlookMsg.has_attachments),
                        bodyHtml: outlookMsg.body_html || '',
                        toList: formattedList,
                        messageId: outlookMsg.uid || outlookMsg.id || '',
                        folder: outlookMsg.folder || 'inbox'
                    };
                } catch (error) {
                    console.error('Message transformation failed:', error, outlookMsg);
                    return {
                        id: outlookMsg.id || `error_${Date.now()}`,
                        subject: '(Error loading message)',
                        from: 'Unknown',
                        fromName: '',
                        preview: 'Failed to load message content',
                        priority: 'normal',
                        category: 'general',
                        reference: '',
                        date: 'Unknown',
                        isRead: true,
                        thread: false,
                        conversationId: '',
                        importance: 'normal',
                        hasAttachments: false,
                        bodyHtml: '',
                        toList: '',
                        messageId: '',
                        folder: 'inbox'
                    };
                }
            }

            sanitizeText(text) {
                if (!text) return '';
                return String(text).replace(/[<>'"&]/g, match => {
                    const escapeMap = {
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#x27;',
                        '&': '&amp;'
                    };
                    return escapeMap[match];
                });
            }

            sortMessages(messages, sortBy = 'date', sortOrder = 'desc') {
                messages.sort((a, b) => {
                    let aValue, bValue;

                    switch (sortBy.toLowerCase()) {
                        case 'date':
                            aValue = new Date(a.date === 'Unknown' ? 0 : a.date);
                            bValue = new Date(b.date === 'Unknown' ? 0 : b.date);
                            break;
                        case 'subject':
                            aValue = (a.subject || '').toLowerCase();
                            bValue = (b.subject || '').toLowerCase();
                            break;
                        case 'from':
                            aValue = (a.from || '').toLowerCase();
                            bValue = (b.from || '').toLowerCase();
                            break;
                        case 'priority':
                            const priorityOrder = {
                                'urgent': 4,
                                'high': 3,
                                'normal': 2,
                                'low': 1
                            };
                            aValue = priorityOrder[a.priority] || 2;
                            bValue = priorityOrder[b.priority] || 2;
                            break;
                        default:
                            aValue = String(a[sortBy] || '').toLowerCase();
                            bValue = String(b[sortBy] || '').toLowerCase();
                    }

                    if (aValue instanceof Date && bValue instanceof Date) {
                        return sortOrder === 'desc' ? bValue - aValue : aValue - bValue;
                    } else if (typeof aValue === 'number' && typeof bValue === 'number') {
                        return sortOrder === 'desc' ? bValue - aValue : aValue - bValue;
                    } else {
                        const comparison = String(aValue).localeCompare(String(bValue));
                        return sortOrder === 'desc' ? -comparison : comparison;
                    }
                });
            }

            detectPriority(msg) {
                const importance = (msg.importance || 'normal').toLowerCase();
                const subject = (msg.subject || '').toLowerCase();
                const preview = (msg.body_preview || '').toLowerCase();
                const content = subject + ' ' + preview;

                if (importance === 'high' || content.includes('urgent') || content.includes('asap')) {
                    return 'urgent';
                } else if (importance === 'high') {
                    return 'high';
                } else if (importance === 'low') {
                    return 'low';
                }
                return 'normal';
            }

            detectCategory(msg) {
                const subject = (msg.subject || '').toLowerCase();
                const preview = (msg.body_preview || '').toLowerCase();
                const content = subject + ' ' + preview;

                if (content.includes('settlement')) return 'settlement';
                if (content.includes('claim')) return 'claim';
                if (content.includes('policy')) return 'policy';
                if (content.includes('risk') || content.includes('assessment')) return 'risk';
                return 'general';
            }

            extractReference(msg) {
                const subject = msg.subject || '';
                const preview = msg.body_preview || '';
                const content = subject + ' ' + preview;

                const refMatch = content.match(/(?:REF|CLAIM|POL|CASE)[-:\s]*([A-Z0-9]+)/i);
                return refMatch ? refMatch[0] : `MSG-${String(msg.id).substring(0, 8)}`;
            }

            formatDate(dateString) {
                if (!dateString) return 'Unknown';

                try {
                    const date = new Date(dateString);
                    if (isNaN(date.getTime())) return 'Invalid Date';

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
                } catch (error) {
                    console.warn('Date formatting failed:', dateString, error);
                    return 'Unknown';
                }
            }

            filterMessages() {
                const searchTerm = $('#searchTerm').val().toLowerCase().trim();
                const priorityFilter = $('#filterPriority').val();
                const categoryFilter = $('#filterCategory').val();

                this.filteredMessages = this.allMessages.filter(message => {
                    const folderMatch = !message.folder ||
                        message.folder.toLowerCase() === this.currentFolder.toLowerCase();
                    if (!folderMatch) return false;

                    const matchesSearch = !searchTerm || [
                        message.subject,
                        message.from,
                        message.fromName,
                        message.preview,
                        message.reference
                    ].some(field => (field || '').toLowerCase().includes(searchTerm));

                    const matchesPriority = priorityFilter === 'all' || message.priority === priorityFilter;
                    const matchesCategory = categoryFilter === 'all' || message.category === categoryFilter;

                    return matchesSearch && matchesPriority && matchesCategory;
                });

                this.sortMessages(this.filteredMessages, 'date', 'desc');

                this.currentPage = 1;
                this.updateResultsInfo();
                this.renderMessages();
                this.renderPagination();
            }

            handleFilterChange(e) {
                if (e.target.id === 'filterFolder') {
                    this.currentFolder = $(e.target).val();
                    this.loadMessages();
                } else {
                    this.filterMessages();
                }
            }

            clearFilters() {
                $('#searchTerm').val('');
                $('#filterPriority').val('all').trigger('change');
                $('#filterCategory').val('all').trigger('change');
                this.filterMessages();
            }

            updateResultsInfo() {
                const totalFiltered = this.filteredMessages.length;
                const totalAll = this.allMessages.length;

                $('#filteredCount').text(totalFiltered);
                $('#resultsInfo').text(`Showing ${totalFiltered} of ${totalAll} messages`);
            }

            renderMessages() {
                const startIndex = (this.currentPage - 1) * this.messagesPerPage;
                const currentMessages = this.filteredMessages.slice(startIndex, startIndex + this.messagesPerPage);

                if (currentMessages.length === 0) {
                    $('#messagesContainer').html(`
                        <div class="text-center py-5 bg-light rounded">
                            <i class="bx bx-envelope fa-6x text-muted mb-3"></i>
                            <p class="text-muted">No messages found matching your criteria</p>
                            <button type="button" class="btn btn-primary btn-sm" onclick="emailManager.clearFilters()">
                                Clear filters
                            </button>
                        </div>
                    `);
                    $('#paginationContainer').addClass('d-none');
                    return;
                }

                const messagesHtml = currentMessages.map(message => this.renderMessageCard(message)).join('');
                $('#messagesContainer').html(messagesHtml);
                $('#paginationContainer').removeClass('d-none');
            }

            /**
             * Render individual message card
             */
            renderMessageCard(message) {
                const badges = [
                    message.priority ?
                    `<span class="badge badge--priority-${message.priority}">${message.priority.toUpperCase()}</span>` :
                    '',
                    message.category ?
                    `<span class="badge badge--type-${message.category}">${message.category.toUpperCase()}</span>` :
                    '',
                    message.reference ? `<span class="badge badge--id">${message.reference}</span>` : '',
                    message.thread ? `<span class="badge badge--thread">THREAD</span>` : '',
                    !message.isRead ? `<span class="badge badge--unread">UNREAD</span>` :
                    `<span class="badge badge--read">READ</span>`,
                    message.hasAttachments ? `<span class="badge badge--thread">📎</span>` : ''
                ].filter(Boolean).join('');

                return `
                    <div class="settlement-card ${!message.isRead ? 'message-unread' : ''}"
                        data-message-id="${message.id}"
                        ondblclick="emailManager.handleReply('${message.id}')">
                        <div class="settlement-card__content">
                            <div class="settlement-card__main">
                                <div class="settlement-card__badges">${badges}</div>
                                <h3 class="settlement-card__title">${message.subject}</h3>
                                <p class="settlement-card__from">
                                    From: <span class="settlement-card__from-email">${message.fromName || message.from}</span>
                                    ${message.fromName && message.from !== message.fromName ?
                                        `<span class="text-muted">&lt;${message.from}&gt;</span>` : ''}
                                </p>
                                <p class="settlement-card__description">${message.preview}</p>
                            </div>
                            <div class="settlement-card__sidebar">
                                <p class="settlement-card__timestamp">${message.date}</p>
                                <button class="reply-button" onclick="event.stopPropagation(); emailManager.handleReply('${message.id}')">
                                    <svg class="reply-button__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 18v-2a4 4 0 0 0-4-4H4"></path>
                                        <path d="m9 17-5-5 5-5"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }

            renderPagination() {
                const totalPages = Math.ceil(this.filteredMessages.length / this.messagesPerPage);

                if (totalPages <= 1) {
                    $('#paginationContainer').addClass('d-none');
                    return;
                }

                $('#paginationInfo').text(`Page ${this.currentPage} of ${totalPages}`);

                let paginationHtml = '';

                paginationHtml += `
                    <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="emailManager.changePage(${this.currentPage - 1})">&laquo;</a>
                    </li>
                `;

                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= this.currentPage - 2 && i <= this.currentPage + 2)) {
                        paginationHtml += `
                    <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="emailManager.changePage(${i})">${i}</a>
                    </li>
                `;
                    } else if (i === this.currentPage - 3 || i === this.currentPage + 3) {
                        paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                paginationHtml += `
                    <li class="page-item ${this.currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="emailManager.changePage(${this.currentPage + 1})">&raquo;</a>
                    </li>
                `;

                $('#paginationList').html(paginationHtml);
                $('#paginationContainer').removeClass('d-none');
            }

            changePage(page) {
                const totalPages = Math.ceil(this.filteredMessages.length / this.messagesPerPage);
                if (page >= 1 && page <= totalPages && page !== this.currentPage) {
                    this.currentPage = page;
                    this.renderMessages();
                    this.renderPagination();
                }
            }

            handleReply(messageId) {
                const message = this.allMessages.find(m => String(m.id) === String(messageId));
                if (!message) {
                    console.error('Message not found:', messageId);
                    this.showToast('error', 'Message not found');
                    return;
                }

                try {
                    $('#to').val(message.from);
                    $('#subject').val(message.subject.startsWith('Re:') ? message.subject : `Re: ${message.subject}`);
                    $('#category').val(message.category);

                    $('#toggleEmailBodyBtn').css('display', 'block').html(
                        '<i class="bx bx-chevron-up me-1"></i>Hide Original Email');
                    $('#emailBody').removeClass('hidden');
                    $('#message').attr('rows', 5).val('');

                    $('#emailBody #originalFrom').text(`${message.fromName} <${message.from}>`);
                    $('#emailBody #originalSent').text(message.date);
                    $('#emailBody #originalTo').text(message.toList);
                    $('#emailBody #originalSubject').text(message.subject);

                    $('#originalContent #threadMessages').contents().find('body').html(message.bodyHtml);

                    $('#isReply').val('1');
                    $('#replyToId').val(message.messageId);
                    $('#originalMessageId').val(message.conversationId);
                    $('#composeTitle').text('Reply to Message');
                    $('#newEmailInstead').removeClass('d-none');

                    $('.compose_attachement').hide();

                    $('#compose-tab').tab('show');

                    const subject = message.subject || '';
                    const preview = subject.length > 50 ? subject.substring(0, 50) + '...' : subject;
                    this.showToast('success', `Replying to: ${preview}`);

                } catch (error) {
                    console.error('Reply setup failed:', error);
                    this.showToast('error', 'Failed to setup reply');
                }
            }

            handleNewEmail() {
                this.resetForm();
                $('#compose-tab').tab('show');
            }

            resetForm() {
                $('#emailForm')[0].reset();
                $('#isReply').val('0');
                $('#originalMessageId').val('');
                $('#composeTitle').text('Compose New Email');
                $('#newEmailInstead').addClass('d-none');
                $('#threadMessage').css('display', 'none');
                $('.compose_attachement').show();
            }

            async refreshData() {
                const $btn = $('#refreshEmailsBtn');
                const originalHtml = $btn.html();

                try {
                    $btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1"></span> Refreshing...');

                    await this.loadMessages(true);
                    this.showToast('success', 'Messages refreshed successfully!');

                } catch (error) {
                    console.error('Refresh failed:', error);
                    this.showToast('error', 'Failed to refresh messages');
                } finally {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            }

            async fetchMoreEmails() {
                const $btn = $('#fetchMoreBtn');
                const originalHtml = $btn.html();

                try {
                    $btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1"></span> Fetching...');

                    this.showToast('info', 'Fetch more functionality needs backend implementation');

                } catch (error) {
                    console.error('Fetch more failed:', error);
                    this.showToast('error', 'Failed to fetch more emails');
                } finally {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            }

            /**
             * Save draft (placeholder)
             */
            async saveDraft() {
                try {
                    const formData = new FormData($('#emailForm')[0]);
                    formData.append('save_as_draft', '1');

                    this.showToast('info', 'Draft save functionality needs backend implementation');

                } catch (error) {
                    console.error('Save draft failed:', error);
                    this.showToast('error', 'Failed to save draft');
                }
            }

            redirectToMail() {
                window.location.href = '/mail';
            }

            showLoadingState() {
                $('#loadingSpinner').show();
                $('#messagesContainer').html($('#loadingSpinner').parent().html());
            }

            hideLoadingState() {
                $('#loadingSpinner').hide();
            }

            showErrorMessage(message) {
                $('#messagesContainer').html(`
                    <div class="alert alert-danger">
                        <i class="bx bx-error-circle me-2"></i>
                        <strong>Error:</strong> ${this.sanitizeText(message)}
                        <button type="button" class="btn btn-outline-danger btn-sm ms-3" onclick="emailManager.loadMessages(true)">
                            Try Again
                        </button>
                    </div>
                `);
                $('#loadingSpinner').hide();
                $('#paginationContainer').addClass('d-none');
            }

            showToast(type, message) {
                if (typeof toastr !== 'undefined') {
                    toastr[type](message);
                } else {
                    console.log(`${type.toUpperCase()}: ${message}`);
                }
            }

            debounce(func, wait) {
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
        }

        $(document).ready(function() {
            window.emailManager = new EmailManager();
        });

        window.changePage = function(page) {
            if (window.emailManager) {
                window.emailManager.changePage(page);
            }
        };
    </script>
@endpush
