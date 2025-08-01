@extends('layouts.app', [
    'pageTitle' => 'Create Budget Expense - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Add Budget Expense Records</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.budget_allocation') }}">Budget Allocation</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Create Budget Expense
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-header">
            <div class="card-title">New Budget Expense Entry</div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.budget_allocation.expenses_store') }}" method="POST" id="budgetIncomeForm">
                @csrf
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="fiscal_year_id" class="form-label">Fiscal Year</label>
                        <div class="card-md">
                            <select name="fiscal_year_id" id="fiscal_year_id" class="form-inputs select2" required>
                                <option value="" selected disabled>Select Fiscal Year</option>
                                @foreach ($fiscalYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Expense Statement Items</h6>
                        <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                            <i class="bi bi-plus"></i> Add Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="expenseItemsContainer">
                            <div class="row mb-3 expense-item">
                                <div class="col-md-3">
                                    <label class="form-label">Category</label>
                                    <input type="text" name="items[0][category]" class="form-inputs" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Subcategory</label>
                                    <input type="text" name="items[0][subcategory]" class="form-inputs" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Amount (KES)</label>
                                    <input type="text" name="items[0][amount]" class="form-inputs amount-input"
                                        onkeyup="this.value=numberWithCommas(this.value)"
                                        onchange="this.value=numberWithCommas(this.value)" required>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex">
                                        <div>
                                            <label class="form-label">Is Total?</label>
                                            <div class="form-check form-check-md pt-2">
                                                <input type="checkbox" name="items[0][is_total]"
                                                    class="form-check-input form-checked-dark" value="1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Save Records
                    </button>
                </div>
            </form>
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
        });
    </script>
@endpush
