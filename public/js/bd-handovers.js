/**
 * BD Handovers Management
 * @author @pk305
 */

(function () {
    "use strict";

    const CONFIG = {
        STATUS_CODES: {
            SUCCESS: 201,
            ACCEPTED: 202,
            VALIDATION_ERROR: 422,
            SERVER_ERROR: 500,
        },
        DATATABLE: {
            PAGE_LENGTH: 15,
            LENGTH_MENU: [15, 30, 50, 100],
        },
        DELAYS: {
            RELOAD: 3000,
            SUCCESS_MESSAGE: 2000,
            DEBOUNCE: 500,
        },
        COLORS: {
            PRIMARY: "#0d6efd",
            SUCCESS: "#198754",
            WARNING: "#ffc107",
            DANGER: "#dc3545",
            INFO: "#0dcaf0",
            PURPLE: "#6f42c1",
            ORANGE: "#fd7e14",
            TEAL: "#20c997",
        },
        CACHE: {
            STATS_TTL: 300000,
            ENABLED: true,
        },
    };

    const state = {
        bdTable: null,
        divisionChart: null,
        statusChart: null,
        currentFilter: "all",
        statsCache: null,
        statsCacheTime: null,
    };

    async function apiFetch(url, options = {}) {
        try {
            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]',
            )?.content;

            const defaultOptions = {
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    ...(csrfToken && { "X-CSRF-TOKEN": csrfToken }),
                },
                credentials: "same-origin",
            };

            const response = await fetch(url, {
                ...defaultOptions,
                ...options,
                headers: {
                    ...defaultOptions.headers,
                    ...options.headers,
                },
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new FetchError(
                    errorData.message ||
                        `HTTP ${response.status}: ${response.statusText}`,
                    response.status,
                    errorData,
                );
            }

            return await response.json();
        } catch (error) {
            if (error instanceof FetchError) {
                throw error;
            }

            throw new FetchError("Network error or invalid response", 0, {
                originalError: error.message,
            });
        }
    }

    class FetchError extends Error {
        constructor(message, status, data = {}) {
            super(message);
            this.name = "FetchError";
            this.status = status;
            this.data = data;
        }
    }

    async function apiGet(url, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const fullUrl = queryString ? `${url}?${queryString}` : url;
        return apiFetch(fullUrl, { method: "GET" });
    }

    async function apiPost(url, data = {}) {
        return apiFetch(url, {
            method: "POST",
            body: JSON.stringify(data),
        });
    }

    async function loadSummaryStats(forceRefresh = false) {
        try {
            if (!forceRefresh && CONFIG.CACHE.ENABLED && state.statsCache) {
                const cacheAge = Date.now() - state.statsCacheTime;
                if (cacheAge < CONFIG.CACHE.STATS_TTL) {
                    updateStatsUI(state.statsCache);
                    return;
                }
            }
            showStatsLoading();
            const response = await apiGet(ROUTES.bdHandoversStats);
            if (response.status && response.data) {
                state.statsCache = response.data;
                state.statsCacheTime = Date.now();

                updateStatsUI(response.data);
            } else {
                throw new Error(response.message || "Invalid response format");
            }
        } catch (error) {
            showStatsError(error.message);

            if (error.status === 401) {
                toastr.error(
                    "Session expired. Please refresh the page.",
                    "Authentication Error",
                );
            } else if (error.status === 403) {
                toastr.error(
                    "You do not have permission to view statistics.",
                    "Permission Denied",
                );
            } else {
                toastr.error("Failed to load dashboard statistics", "Error");
            }
        }
    }

    function updateStatsUI(data) {
        updateSummaryCards(data);
        updateDivisionChart(data.divisions || []);
        updateStatusChart(data.statuses || []);
        updateTopCedants(data.top_cedants || []);
    }

    async function submitApprovalAction(data) {
        try {
            const response = await apiPost(ROUTES.bdApprovalAction, data);
            if (response.status === CONFIG.STATUS_CODES.SUCCESS) {
                toastr.success(response.message, "Successful");
                state.bdTable.ajax.reload(null, false);
                await loadSummaryStats(true);
            } else if (
                response.status === CONFIG.STATUS_CODES.VALIDATION_ERROR
            ) {
                handleValidationErrors(response.errors);
            } else {
                throw new Error(
                    response.message || "Failed to process request",
                );
            }
        } catch (error) {
            toastr.error(
                error.message ||
                    "An internal error occurred. Please try again.",
                "Error",
            );
        }
    }

    async function clearCedantData(cedantId) {
        try {
            const response = await apiPost(ROUTES.clearCedantData, {
                id: cedantId,
            });

            if (
                response.status === CONFIG.STATUS_CODES.ACCEPTED ||
                response.status === CONFIG.STATUS_CODES.SUCCESS
            ) {
                toastr.success(
                    response.message || "Action was successful",
                    "Successful",
                );

                setTimeout(() => {
                    location.reload();
                }, CONFIG.DELAYS.RELOAD);
            } else if (
                response.status === CONFIG.STATUS_CODES.VALIDATION_ERROR
            ) {
                handleValidationErrors(response.errors);
            } else {
                throw new Error(response.message || "Failed to clear data");
            }
        } catch (error) {
            console.error("Clear cedant error:", error);
            toastr.error(
                error.message ||
                    "An internal error occurred. Please try again.",
                "Error",
            );
        }
    }

    async function createCoverDocument(prospectId) {
        try {
            Swal.fire({
                title: "Processing",
                text: "Creating your cover document...",
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading(),
            });

            const response = await apiPost(ROUTES.createCover, {
                id: prospectId,
            });

            if (response.status) {
                Swal.fire({
                    title: "Success!",
                    text: "Cover document has been generated successfully.",
                    icon: "success",
                    timer: CONFIG.DELAYS.SUCCESS_MESSAGE,
                    showConfirmButton: false,
                });

                state.bdTable.ajax.reload(null, false);
                await loadSummaryStats(true);
                processCoverForm(response);
            } else {
                throw new Error(
                    response.message || "Failed to generate cover document",
                );
            }
        } catch (error) {
            Swal.fire({
                title: "Error",
                text:
                    error.message ||
                    "An unexpected error occurred while generating the cover document",
                icon: "error",
            });
        }
    }

    function showStatsLoading() {
        const spinner =
            '<span class="spinner-border spinner-border-sm" role="status"></span>';
        $("#total-handovers").html(spinner);
        $("#pending-approval").html(spinner);
        $("#approved-count").html(spinner);
        $("#total-premium").html(spinner);
    }

    function updateSummaryCards(data) {
        animateValue("total-handovers", 0, data.total_count || 0, 1000);
        $("#total-handovers-subtitle").text(
            `${data.this_month_count || 0} this month`,
        );

        animateValue("pending-approval", 0, data.pending_count || 0, 1000);
        const pendingPercentage =
            data.total_count > 0
                ? ((data.pending_count / data.total_count) * 100).toFixed(1)
                : 0;
        $("#pending-approval-subtitle").text(`${pendingPercentage}% of total`);

        animateValue("approved-count", 0, data.approved_count || 0, 1000);
        const approvedPercentage =
            data.total_count > 0
                ? ((data.approved_count / data.total_count) * 100).toFixed(1)
                : 0;
        $("#approved-subtitle").text(`${approvedPercentage}% approval rate`);

        const formattedPremium = formatCurrency(
            data.total_premium || 0,
            data.primary_currency || "USD",
        );
        $("#total-premium").html(formattedPremium);
        $("#premium-subtitle").text(
            `Avg: ${formatCurrency(
                data.average_premium || 0,
                data.primary_currency || "USD",
            )}`,
        );
    }

    function animateValue(elementId, start, end, duration) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const startTime = Date.now();
        const endTime = startTime + duration;

        function update() {
            const now = Date.now();
            const progress = Math.min((now - startTime) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);

            element.textContent = value.toLocaleString();

            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }

        requestAnimationFrame(update);
    }

    function updateDivisionChart(divisions) {
        const container = document.getElementById("division-chart");
        if (!container) return;

        if (state.divisionChart) {
            state.divisionChart.destroy();
        }

        if (!divisions || divisions.length === 0) {
            container.innerHTML =
                '<p class="text-muted text-center py-5">No division data available</p>';
            return;
        }

        const canvas = document.createElement("canvas");
        container.innerHTML = "";
        container.appendChild(canvas);

        const labels = divisions.map((d) => d.name);
        const data = divisions.map((d) => d.count);
        const colors = generateColors(divisions.length);

        state.divisionChart = new Chart(canvas, {
            type: "doughnut",
            data: {
                labels: labels,
                datasets: [
                    {
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: "#fff",
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            padding: 15,
                            font: { size: 11 },
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || "";
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce(
                                    (a, b) => a + b,
                                    0,
                                );
                                const percentage = (
                                    (value / total) *
                                    100
                                ).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            },
                        },
                    },
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000,
                    easing: "easeOutQuart",
                },
            },
        });
    }

    function updateStatusChart(statuses) {
        const container = document.getElementById("status-breakdown");
        if (!container) return;

        if (state.statusChart) {
            state.statusChart.destroy();
        }

        if (!statuses || statuses.length === 0) {
            container.innerHTML =
                '<p class="text-muted text-center py-5">No status data available</p>';
            return;
        }

        const canvas = document.createElement("canvas");
        canvas.height = 300;
        container.innerHTML = "";
        container.appendChild(canvas);

        const statusColors = {
            Pending: CONFIG.COLORS.WARNING,
            Approved: CONFIG.COLORS.SUCCESS,
            Rejected: CONFIG.COLORS.DANGER,
            Processing: CONFIG.COLORS.INFO,
        };

        const labels = statuses.map((s) => s.status);
        const data = statuses.map((s) => s.count);
        const colors = labels.map(
            (label) => statusColors[label] || CONFIG.COLORS.PRIMARY,
        );

        state.statusChart = new Chart(canvas, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Count",
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 0,
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `Count: ${context.parsed.y}`;
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                        },
                    },
                },
                animation: {
                    duration: 1000,
                    easing: "easeOutQuart",
                },
            },
        });
    }

    function updateTopCedants(cedants) {
        const container = document.getElementById("top-cedants-list");
        if (!container) return;

        if (!cedants || cedants.length === 0) {
            container.innerHTML =
                '<p class="text-muted text-center py-3">No data available</p>';
            return;
        }

        const html = cedants
            .slice(0, 5)
            .map(
                (cedant, index) => `
            <div class="cedant-item">
                <div>
                    <span class="text-muted fs-12">#${index + 1}</span>
                    <span class="cedant-name ms-2">${escapeHtml(
                        cedant.name,
                    )}</span>
                </div>
                <span class="cedant-count">${cedant.count}</span>
            </div>
        `,
            )
            .join("");

        container.style.height = "480px";
        container.style.overflowY = "auto";
        container.classList.add("customScrollBar");
    }

    function updateTableFooter() {
        if (!state.bdTable) return;

        const data = state.bdTable.rows({ page: "current" }).data();

        let totalSumInsured = 0;
        let totalPremium = 0;

        data.each(function (row) {
            totalSumInsured += parseFloat(row.effective_sum_insured || 0);
            totalPremium += parseFloat(row.cedant_premium || 0);
        });

        $("#footer-sum-insured").text(formatNumber(totalSumInsured));
        $("#footer-premium").text(formatNumber(totalPremium));
    }

    function showStatsError(message = "Failed to load statistics") {
        const errorIcon = '<i class="ri-error-warning-line text-danger"></i>';
        $("#total-handovers").html(errorIcon);
        $("#pending-approval").html(errorIcon);
        $("#approved-count").html(errorIcon);
        $("#total-premium").html(errorIcon);

        const errorHtml = `<p class="text-danger text-center py-3">${escapeHtml(
            message,
        )}</p>`;
        $("#division-chart").html(errorHtml);
        $("#status-breakdown").html(errorHtml);
        $("#top-cedants-list").html(errorHtml);
    }

    function initializeDataTable() {
        state.bdTable = $("#bd_handovers_table").DataTable({
            pageLength: CONFIG.DATATABLE.PAGE_LENGTH,
            lengthMenu: CONFIG.DATATABLE.LENGTH_MENU,
            processing: true,
            serverSide: true,
            responsive: true,
            order: [[1, "desc"]],
            ajax: {
                url: ROUTES.bdHandoversDatatable,
                data: function (d) {
                    d.status_filter = state.currentFilter;
                },
                error: function (xhr, error, code) {
                    console.error("DataTable Error:", error, code);
                    toastr.error(
                        "Failed to load data. Please refresh the page.",
                        "Error",
                    );
                },
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "highlight-index",
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                },
                {
                    data: "opportunity_id",
                    name: "opportunity_id",
                },
                {
                    data: "cedant",
                    searchable: true,
                    render: function (data, type, row) {
                        return `<span class="cedant-link" data-id="${
                            row.opportunity_id
                        }">${escapeHtml(data)}</span>`;
                    },
                },
                {
                    data: "insured_name",
                    searchable: true,
                },
                { data: "division_name" },
                { data: "business_class", searchable: true },
                {
                    data: "currency_code",
                    orderable: false,
                },
                {
                    data: "effective_sum_insured",
                    searchable: true,
                    render: $.fn.dataTable.render.number(",", ".", 2),
                },
                {
                    data: "cedant_premium",
                    searchable: true,
                    render: $.fn.dataTable.render.number(",", ".", 2),
                },
                { data: "effective_date", searchable: true },
                { data: "closing_date", searchable: true },
                {
                    data: "bd_status",
                    searchable: true,
                    orderable: false,
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                },
            ],
            drawCallback: function () {
                var dropdownElementList = [].slice.call(
                    document.querySelectorAll('[data-bs-toggle="dropdown"]'),
                );

                dropdownElementList.map(function (dropdownToggleEl) {
                    return new bootstrap.Dropdown(dropdownToggleEl);
                });

                updateTableFooter();
            },
        });

        return state.bdTable;
    }

    function handleStatusFilter(status) {
        state.currentFilter = status;
        $(".filter-status").removeClass("active");
        $(`.filter-status[data-status="${status}"]`).addClass("active");
        state.bdTable.ajax.reload();
    }

    function handleExport(format) {
        toastr.info(`Preparing ${format.toUpperCase()} export...`);

        const params = new URLSearchParams({
            export: format,
            status_filter: state.currentFilter,
        });

        window.location.href = `${ROUTES.exportData}?${params.toString()}`;
    }

    function handleClearCedantData(cedantId, cedantName) {
        Swal.fire({
            title: "WARNING: Clear All Covers",
            html: `You are about to permanently delete all insurance covers and related data for <strong>${escapeHtml(
                cedantName,
            )}</strong>.<br><br>
                   This action cannot be undone.<br><br>
                   Please confirm to proceed.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete everything",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
        }).then((result) => {
            if (result.isConfirmed) {
                clearCedantData(cedantId);
            }
        });
    }

    function handleCreateCover(prospectId) {
        Swal.fire({
            title: "Create Cover Document",
            text: "Are you sure you want to generate a cover document for this handover?",
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "Yes, Generate Cover",
            cancelButtonText: "Not Now",
            confirmButtonColor: "#198754",
            cancelButtonColor: "#d33",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                createCoverDocument(prospectId);
            }
        });
    }

    function handleApprovalAction(approvalId, actionType) {
        const isApproval = actionType === "approve";

        Swal.fire({
            title: isApproval ? "Approval Confirmation" : "Reject Confirmation",
            input: "textarea",
            inputLabel: "Your Comment",
            inputPlaceholder: "Enter your comment",
            inputAttributes: {
                "aria-label": "Comment",
                rows: 4,
            },
            inputValidator: (value) => {
                if (!value || value.trim().length === 0) {
                    return "";
                }
                return null;
            },
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Submit",
            cancelButtonText: "Cancel",
            confirmButtonColor: isApproval ? "#198754" : "#d33",
            showLoaderOnConfirm: true,
            preConfirm: async (comment) => {
                const data = {
                    id: approvalId,
                    action: isApproval ? "1" : "0",
                    type: actionType,
                    comment: comment.trim(),
                };

                await submitApprovalAction(data);
            },
            allowOutsideClick: () => !Swal.isLoading(),
        });
    }

    function processCoverForm(response) {
        if (!response.status) {
            return;
        }

        const data = response.data;
        const token =
            $('meta[name="csrf-token"]').attr("content") ||
            $('input[name="_token"]').val();

        if (!data.customerId || !data.prospectId) {
            console.error("❌ Missing required data!");
            return;
        }

        const params = new URLSearchParams({
            _token: token,
            _method: "POST",
            trans_type: "NEW",
            customer_id: data.customerId,
            prospect_id: data.prospectId,
        });

        if (data.typeOfBus) {
            params.append("type_of_bus", data.typeOfBus);
        }

        const finalUrl = `/cover/cover-form?${params.toString()}`;
        window.location.href = finalUrl;
    }

    function showRejectionComment(reason) {
        const messageElement = document.getElementById("rejection_message");
        if (messageElement) {
            messageElement.textContent = reason;
        }

        const modal = new bootstrap.Modal(
            document.getElementById("rejectedCommentModal"),
        );
        modal.show();
    }

    function openReviewUrl(url) {
        if (!url || typeof url !== "string") {
            toastr.error("Review URL not available");
            return;
        }
        window.open(url, "_blank", "noopener,noreferrer");
    }

    function handleValidationErrors(errors) {
        if (typeof showServerSideValidationErrors === "function") {
            showServerSideValidationErrors(errors);
        } else {
            Object.values(errors)
                .flat()
                .forEach((error) => {
                    toastr.error(error);
                });
        }
    }

    function formatCurrency(amount, currency = "USD") {
        try {
            return new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: currency,
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(amount);
        } catch (error) {
            return `${currency} ${formatNumber(amount)}`;
        }
    }

    function formatNumber(number) {
        return new Intl.NumberFormat("en-US", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(number);
    }

    function generateColors(count) {
        const baseColors = [
            CONFIG.COLORS.PRIMARY,
            CONFIG.COLORS.SUCCESS,
            CONFIG.COLORS.WARNING,
            CONFIG.COLORS.DANGER,
            CONFIG.COLORS.INFO,
            CONFIG.COLORS.PURPLE,
            CONFIG.COLORS.ORANGE,
            CONFIG.COLORS.TEAL,
        ];

        return Array.from(
            { length: count },
            (_, i) => baseColors[i % baseColors.length],
        );
    }

    function escapeHtml(text) {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function initializeEventListeners() {
        $(".filter-status").on("click", function () {
            const status = $(this).data("status");
            handleStatusFilter(status);
        });

        $("#export-excel").on("click", (e) => {
            e.preventDefault();
            handleExport("excel");
        });
        $("#export-pdf").on("click", (e) => {
            e.preventDefault();
            handleExport("pdf");
        });
        $("#export-csv").on("click", (e) => {
            e.preventDefault();
            handleExport("csv");
        });

        $("#refresh-table").on("click", function () {
            state.bdTable.ajax.reload();
            loadSummaryStats(true);
            toastr.info("Refreshing data...");
        });

        $(document).on("click", ".remove_process_customer", function (e) {
            e.preventDefault();
            const cedantId = $(this).data("cedant_id");
            const cedantName = $(this).data("name");
            handleClearCedantData(cedantId, cedantName);
        });

        $(document).on("click", ".integrate-btn", function (e) {
            e.preventDefault();
            const prospectId = $(this).data("id");
            handleCreateCover(prospectId);
        });

        state.bdTable.on("click", ".approve-btn", function (e) {
            e.preventDefault();
            const approvalId = $(this).data("id");
            handleApprovalAction(approvalId, "approve");
        });

        state.bdTable.on("click", ".reject-bd-btn", function (e) {
            e.preventDefault();
            const approvalId = $(this).data("id");
            handleApprovalAction(approvalId, "decline");
        });

        state.bdTable.on("click", ".review-btn", function (e) {
            e.preventDefault();
            const url = $(this).data("url");
            openReviewUrl(url);
        });

        state.bdTable.on("click", ".rejected-bd-comment", function (e) {
            e.preventDefault();
            const reason = $(this).data("reason");
            showRejectionComment(reason);
        });

        state.bdTable.on("click", ".cedant-link", function (e) {
            e.preventDefault();
            const opportunityId = $(this).data("id");
            toastr.info("Loading details...");
        });
    }
    async function init() {
        try {
            initializeDataTable();

            await loadSummaryStats();

            initializeEventListeners();

            $('.filter-status[data-status="all"]').addClass("active");
        } catch (error) {
            toastr.error(
                "Failed to initialize the page. Please refresh.",
                "Error",
            );
        }
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
