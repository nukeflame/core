@extends('layouts.app', [
    'pageTitle' => 'Import Budget Data - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Import Budget Allocation Data</h1>
            <p class="fs-semibold text-muted pt-1 fs-12">Upload your Excel file containing income statement data</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.budget_allocation') }}">Budget Allocation</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Import finacial budget data
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card custom-card mb-4">
            <div class="card-header">
                <div class="card-title">Download Sample Template</div>
            </div>
            <div class="card-body">
                <p>Please download our sample template to ensure your data is formatted correctly:</p>
                <a href="{{ route('admin.budget_allocation.download_template') }}" class="btn btn-outline-primary">
                    <i class="bi bi-file-earmark-excel"></i> Download Excel Template
                </a>

                <div class="mt-3">
                    <p class="mb-1"><strong>The template includes the following columns:</strong></p>
                    <ul>
                        <li>Category (e.g., New Business, Renewal Business)</li>
                        <li>Subcategory (e.g., Facultative, Special Lines)</li>
                        <li>Amount (in KES)</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card custom-card">
            <div class="card-header">
                <div class="card-title">Upload Budget Statement Excel</div>
            </div>
            <div class="card-body">
                <form id="importForm" action="{{ route('admin.budget_allocation.import') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="fiscal_year_id" class="form-label">Select Fiscal Year</label>
                            <div class="card-md">
                                <select name="fiscal_year_id" id="fiscal_year_id" class="form-inputs select2" required>
                                    <option value="">Select Fiscal Year</option>
                                    @foreach ($fiscalYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="excel_file" class="form-label">Upload Excel File</label>
                            <input type="file" name="excel_file" id="excel_file" class="form-control" required
                                accept=".xlsx,.xls">
                            <div class="form-text">Accepted formats: .xlsx, .xls</div>
                        </div>
                    </div>

                    <div id="importOptions" class="mb-4">
                        <div class="form-check form-check-md d-flex align-items-center">
                            <input class="form-check-input" type="checkbox"id="overwrite_existing" name="overwrite_existing"
                                value="1">
                            <label class="form-check-label" for="overwrite_existing">
                                Overwrite existing data for this fiscal year
                            </label>
                        </div>

                        <div class="form-check form-check-md d-flex align-items-center">
                            <input class="form-check-input" type="checkbox" id="validate_only" name="validate_only"
                                value="1">
                            <label class="form-check-label" for="validate_only">
                                Validate data only (no import)
                            </label>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" id="startImportBtn" class="btn btn-success">
                            <i class="bi bi-cloud-upload"></i> Start Import
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="importProgress" class="card custom-card mt-4 d-none">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title">Import Progress</div>
                <div>
                    <button id="cancelImportBtn" class="btn btn-sm btn-outline-danger px-3">
                        <i class="bx bx-x"></i> Cancel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="progress mb-3">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                        style="width: 0%">0%</div>
                </div>

                <div class="d-flex justify-content-between">
                    <div id="importStatus">Preparing import...</div>
                    <div id="importStats">
                        <span id="processedRows">0</span> / <span id="totalRows">0</span> rows
                    </div>
                </div>

                <div id="importMessages" class="mt-3">
                    <div class="card bg-light">
                        <div class="card-body p-2">
                            <small class="text-muted">Messages will appear here during import...</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <button id="resumeImportBtn" class="btn btn-primary d-none">
                        <i class="bi bi-play-fill"></i> Resume Import
                    </button>
                    <button id="completeImportBtn" class="btn btn-success d-none">
                        <i class="bi bi-check-circle"></i> Complete
                    </button>
                </div>
            </div>
        </div>

        <!-- Data Preview Section (For validation results) -->
        <div id="dataPreview" class="card custom-card mt-4 d-none">
            <div class="card-header">
                <div class="card-title">Data Preview</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Subcategory</th>
                                <th class="text-end">Amount (KES)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="previewTableBody">
                            <!-- Preview data will be inserted here via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <button id="editDataBtn" class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i> Edit Data
                    </button>
                    <button id="confirmImportBtn" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Confirm Import
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let itemCount = 0;

            $('#addItemBtn').click(function(e) {
                e.preventDefault();
                itemCount++;

                const newItem = `
                <div class="row mb-3 expense-item">
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="items[${itemCount}][category]" class="form-inputs" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Subcategory</label>
                        <input type="text" name="items[${itemCount}][subcategory]" class="form-inputs" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Amount (KES)</label>
                        <input type="text" name="items[${itemCount}][amount]" class="form-inputs amount-input"
                            onkeyup="this.value=numberWithCommas(this.value)"
                            onchange="this.value=numberWithCommas(this.value)" required>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex">
                            <div>
                                <label class="form-label">Is Total?</label>
                                <div class="form-check form-check-md pt-2">
                                    <input type="checkbox" name="items[${itemCount}][is_total]"
                                        class="form-check-input form-checked-dark" value="1">
                                </div>
                            </div>
                            <div style="padding-top: 27px;margin-left: 4rem;">
                                <button type="button" class="btn btn-sm btn-danger remove-item"
                                    title="Remove Item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                $('#expenseItemsContainer').append(newItem);
                attachValidation(itemCount);

            });

            $(document).on('click', '.remove-item', function() {
                if ($('.expense-item').length > 1) {
                    $(this).closest('.expense-item').remove();
                }
            });

            function attachValidation(index) {
                $(`input[name="items[${index}][category]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Category is required"
                    }
                });

                $(`input[name="items[${index}][subcategory]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Subcategory is required"
                    }
                });

                $(`input[name="items[${index}][amount]"]`).rules("add", {
                    required: true,
                });
            }

            $("#fiscal_year_id").on('change', function() {
                $('#fiscal_year_id').val();
            });

            $('#budgetIncomeForm').validate({
                errorClass: 'errorClass',
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                rules: {
                    'fiscal_year_id': {
                        required: true
                    },
                    'items[0][category]': {
                        required: true
                    },
                    'items[0][subcategory]': {
                        required: true
                    },
                    'items[0][amount]': {
                        required: true,
                    }
                },
                messages: {
                    'fiscal_year_id': {
                        required: "Please select a fiscal year"
                    },
                    'items[0][category]': {
                        required: "Category is required"
                    },
                    'items[0][subcategory]': {
                        required: "Subcategory is required"
                    },
                    'items[0][amount]': {
                        required: "Amount is required",
                    }
                },
                submitHandler: function(form) {
                    if ($('.expense-item').length === 0) {
                        return false;
                    }

                    $('.amount-input').each(function() {
                        let cleanValue = $(this).val().replace(/,/g, '');
                        $(this).val(cleanValue);
                    });

                    $.ajax({
                        url: $(form).attr('action'),
                        method: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            toastr.success('Budget expense records saved successfully');
                            setTimeout(() => {
                                window.location.href =
                                    "{{ route('admin.budget_allocation') }}";
                            }, 2000);
                        },
                        error: function(xhr) {
                            console.log(xhr)
                            toastr.error('An error occurred. Please try again.');
                        }
                    });

                    return false;
                }
            });

            const $importForm = $('#importForm');
            const $startImportBtn = $('#startImportBtn');
            const $cancelImportBtn = $('#cancelImportBtn');
            const $resumeImportBtn = $('#resumeImportBtn');
            const $completeImportBtn = $('#completeImportBtn');
            const $importProgress = $('#importProgress');
            const $dataPreview = $('#dataPreview');
            const $progressBar = $('#progressBar');
            const $importStatus = $('#importStatus');
            const $processedRows = $('#processedRows');
            const $totalRows = $('#totalRows');
            const $importMessages = $('#importMessages');
            const $validateOnly = $('#validate_only');
            const $confirmImportBtn = $('#confirmImportBtn');

            let importJobId = null;
            let importCancelled = false;
            let importPaused = false;

            $importForm.on('submit', function(e) {
                e.preventDefault();

                $startImportBtn.prop('disabled', true);
                $startImportBtn.html('<i class="bi bi-hourglass"></i> Processing...');

                const formData = new FormData(this);

                if ($validateOnly.is(':checked')) {
                    validateData(formData);
                } else {
                    startImport(formData);
                }
            });

            function validateData(formData) {
                $.ajax({
                    url: '{{ route('admin.budget_allocation.validate') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        console.log(data)
                        // if (data.success) {
                        //     showDataPreview(data.rows);
                        // } else {
                        //     showValidationErrors(data.errors);
                        // }
                    },
                    error: function(xhr, status, error) {
                        $importMessages.html(
                            `<div class="alert alert-danger">Error validating data: ${error}</div>`);
                    },
                    complete: function() {
                        $startImportBtn.prop('disabled', false);
                        $startImportBtn.html('<i class="bx bx-cloud-upload"></i> Start Import');
                    }
                });
            }

            // Show validation preview
            function showDataPreview(rows) {
                const $tableBody = $('#previewTableBody');
                $tableBody.empty();

                $.each(rows, function(i, row) {
                    let statusClass = row.valid ? 'text-success' : 'text-danger';
                    let statusIcon = row.valid ?
                        '<i class="bi bi-check-circle-fill"></i>' :
                        '<i class="bi bi-exclamation-circle-fill"></i>';

                    $tableBody.append(`
                    <tr>
                        <td>${row.category}</td>
                        <td>${row.subcategory}</td>
                        <td class="text-end">${row.amount.toLocaleString('en-KE', {minimumFractionDigits: 2})}</td>
                        <td class="${statusClass}">${statusIcon} ${row.valid ? 'Valid' : row.error}</td>
                    </tr>
                `);
                });

                $dataPreview.removeClass('d-none');

                // Scroll to preview
                $('html, body').animate({
                    scrollTop: $dataPreview.offset().top
                }, 500);
            }

            // Show validation errors
            function showValidationErrors(errors) {
                let errorHtml = '<div class="alert alert-danger"><ul class="mb-0">';
                $.each(errors, function(i, error) {
                    errorHtml += `<li>${error}</li>`;
                });
                errorHtml += '</ul></div>';

                $importMessages.html(errorHtml);
            }

            function startImport(formData) {
                $importProgress.removeClass('d-none');
                $.ajax({
                    url: '{{ route('admin.budget_allocation.start_import') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success) {
                            importJobId = data.job_id;
                            $totalRows.text(data.total_rows);

                            pollImportProgress();
                        } else {
                            $importMessages.html(
                                `<div class="alert alert-danger">${data.message}</div>`);
                            $startImportBtn.prop('disabled', false);
                            $startImportBtn.html('<i class="bi bi-cloud-upload"></i> Start Import');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        $importMessages.html(
                            `<div class="alert alert-danger">Error starting import: ${error}</div>`);
                        $startImportBtn.prop('disabled', false);
                        $startImportBtn.html('<i class="bi bi-cloud-upload"></i> Start Import');
                    }
                });
            }

            function pollImportProgress() {
                if (importCancelled) return;

                $.ajax({
                    url: `{{ url('admin/budget-allocation/import-progress') }}/${importJobId}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        updateProgressUI(data);

                        if (data.status === 'completed') {
                            completeImport();
                        } else if (data.status === 'failed') {
                            failImport(data.message);
                        } else if (!importPaused) {
                            // Continue polling
                            setTimeout(pollImportProgress, 1000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        $importMessages.append(
                            `<div class="alert alert-warning">Error checking progress: ${error}</div>`
                        );

                        setTimeout(pollImportProgress, 3000);
                    }
                });
            }

            function updateProgressUI(data) {
                const percent = Math.round((data.processed_rows / data.total_rows) * 100);
                $progressBar.css('width', `${percent}%`);
                $progressBar.text(`${percent}%`);

                $processedRows.text(data.processed_rows);
                $importStatus.text(data.status_message);

                if (data.messages && data.messages.length > 0) {
                    let messagesHtml = '';
                    $.each(data.messages, function(i, message) {
                        const alertClass = message.type === 'error' ? 'danger' : message.type;
                        messagesHtml += `
                    <div class="alert alert-${alertClass} alert-sm mb-2 py-2">
                        ${message.text}
                    </div>
                `;
                    });
                    $importMessages.html(messagesHtml);
                }
            }

            function completeImport() {
                $progressBar.removeClass('progress-bar-animated progress-bar-striped')
                    .addClass('bg-success');

                $importStatus.text('Import completed successfully!');
                $completeImportBtn.removeClass('d-none');

                $importMessages.append(`
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Import has been successfully completed.
                        You can now view the imported data in your income statements.
                    </div>
                `);
            }

            function failImport(message) {
                $progressBar.removeClass('progress-bar-animated progress-bar-striped')
                    .addClass('bg-danger');

                $importStatus.text('Import failed');

                $importMessages.append(`
                    <div class="alert alert-danger mt-3">
                        <i class="bi bi-x-circle-fill me-2"></i>
                        Import failed: ${message}
                    </div>
                `);

                $startImportBtn.prop('disabled', false);
                $startImportBtn.html('<i class="bi bi-cloud-upload"></i> Retry Import');
            }

            $cancelImportBtn.on('click', function() {
                if (confirm('Are you sure you want to cancel the import? This cannot be undone.')) {
                    importCancelled = true;

                    $.ajax({
                        url: `{{ url('budget-incomes/cancel-import') }}/${importJobId}`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            if (data.success) {
                                $progressBar.removeClass('progress-bar-animated')
                                    .addClass('bg-warning');
                                $importStatus.text('Import cancelled');

                                $importMessages.append(`
                                    <div class="alert alert-warning mt-3">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Import has been cancelled. No data was imported.
                                    </div>
                                `);

                                $startImportBtn.prop('disabled', false);
                                $startImportBtn.html(
                                    '<i class="bi bi-cloud-upload"></i> Start Import');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                }
            });

            // Pause/Resume import
            $resumeImportBtn.on('click', function() {
                importPaused = false;
                $resumeImportBtn.addClass('d-none');

                $.ajax({
                    url: `{{ url('admin/budget-allocation/resume-import') }}/${importJobId}`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success) {
                            $progressBar.addClass('progress-bar-animated');
                            $importStatus.text('Import resumed...');
                            pollImportProgress();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            // Complete button click handler
            $completeImportBtn.on('click', function() {
                // {{-- window.location.href = '{{ route('budget-incomes.index') }}'; --}}
            });

            // Confirm import after validation
            $confirmImportBtn.on('click', function() {
                $dataPreview.addClass('d-none');
                $validateOnly.prop('checked', false);

                // Auto submit the form
                $importForm.submit();
            });

        });
    </script>
@endpush
