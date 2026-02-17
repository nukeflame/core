<?php

namespace App\Http\Requests;

use App\Models\Classes;
use App\Models\PremiumPayTerm;
use App\Models\TreatyItemCode;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class GenerateDebitNoteRequest extends FormRequest
{
    protected ?array $validItemCodes = null;
    protected ?array $validClassCodes = null;
    protected ?array $validPayTerms = null;

    public function authorize(): bool
    {
        // return auth()->check() && auth()->user()->can('create-debit-notes');
        return true;
    }

    public function rules(): array
    {
        $currentYear = (int) date('Y');

        return [
            'cover_no' => [
                'required',
                'string',
                'max:50',
                Rule::exists('cover_register', 'cover_no')
            ],
            'endorsement_no' => [
                'required',
                'string',
                'max:50'
            ],
            'type_of_bus' => [
                'required',
                'string',
                // Rule::in(['FPR', 'FRP', 'TRP', 'TRN'])
            ],
            'installment' => [
                'required',
                'integer',
                'min:0',
                'max:99'
            ],
            'amount' => [
                'nullable',
                'string',
            ],

            'posting_year' => [
                'required',
                'integer',
                'min:' . ($currentYear - 2),
                'max:' . ($currentYear + 1)
            ],
            'posting_quarter' => [
                'required',
                Rule::in(['Q1', 'Q2', 'Q3', 'Q4'])
            ],
            'posting_date' => [
                'required',
                'date',
                'before_or_equal:today'
            ],

            'currency_code' => [
                'nullable',
                'string',
                'max:10',
            ],
            'today_currency' => [
                'nullable',
                'numeric',
                'min:0.000001',
            ],

            'brokerage_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'line_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'compute_premium_tax' => 'nullable|boolean',
            'compute_reinsurance_tax' => 'nullable|boolean',
            'compute_withholding_tax' => 'nullable|boolean',

            'loss_participation' => 'nullable|boolean',
            'sliding_commission' => 'nullable|boolean',

            'comments' => 'nullable|string|max:2000',

            'show_cedant' => 'nullable|boolean',
            'show_reinsurer' => 'nullable|boolean',

            'items' => 'required|array|min:1|max:50',
            'items.*.item_type' => [
                'nullable',
                'string'
            ],
            'items.*.item_code' => [
                'nullable',
                'string',
                // Rule::in($this->getValidItemCodes())
            ],
            'items.*.description' => [
                'required',
                'string',
                // Rule::in($this->getValidItemCodes())
            ],
            'items.*.class_name' => [
                'nullable',
                'string',
                // Rule::in($this->getValidClassCodes())
            ],
            'items.*.class_group' => [
                'nullable',
                'string',
            ],
            'items.*.ledger' => [
                'nullable',
                'string',
            ],
            'items.*.line_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],
            'items.*.amount' => [
                'nullable',
                'numeric',
                'min:0.01',
                'max:999999999999.99'
            ],
        ];
    }

    protected function getValidItemCodes(): array
    {
        if ($this->validItemCodes === null) {
            $this->validItemCodes = TreatyItemCode::getValidCodes();
        }
        return $this->validItemCodes;
    }

    protected function getValidClassCodes(): array
    {
        if ($this->validClassCodes === null) {
            $this->validClassCodes = Classes::getValidCodes();
        }
        return $this->validClassCodes;
    }

    protected function getValidPayTermCodes(): array
    {
        if ($this->validPayTerms === null) {
            $this->validPayTerms = PremiumPayTerm::getValidCodes();
        }
        return $this->validPayTerms;
    }

    public function messages(): array
    {
        return [
            'cover_no.required' => 'Cover number is required',
            'cover_no.exists' => 'The specified cover does not exist',
            'endorsement_no.required' => 'Endorsement number is required',
            'type_of_bus.required' => 'Business type is required',
            'type_of_bus.in' => 'Invalid business type selected',

            'posting_year.required' => 'Fiscal year is required',
            'posting_year.integer' => 'Invalid fiscal year',
            'posting_year.min' => 'Fiscal year is too far in the past',
            'posting_year.max' => 'Fiscal year cannot be more than one year ahead',
            'posting_quarter.required' => 'Accounting period is required',
            'posting_quarter.in' => 'Invalid accounting period',
            'posting_date.required' => 'Transaction date is required',
            'posting_date.date' => 'Invalid date format',
            'posting_date.before_or_equal' => 'Transaction date cannot be in the future',
            'brokerage_rate.numeric' => 'Brokerage rate must be a number',
            'brokerage_rate.min' => 'Brokerage rate cannot be negative',
            'brokerage_rate.max' => 'Brokerage rate cannot exceed 100%',
            'today_currency.numeric' => 'Exchange rate must be a valid number',
            'today_currency.min' => 'Exchange rate must be greater than zero',

            'comments.max' => 'Comments cannot exceed 2000 characters',

            'items.required' => 'At least one line item is required',
            'items.min' => 'At least one line item is required',
            'items.max' => 'Maximum of 50 line items allowed',
            'items.*.description.required' => 'Transaction type is required for line :position',
            'items.*.description.in' => 'Invalid transaction type for line :position',
            'items.*.item_code.in' => 'Invalid item code for line :position',
            'items.*.item_type.in' => 'Invalid item type for line :position',
            'items.*.class_name.in' => 'Invalid business class for line :position',
            'items.*.class_group.in' => 'Invalid business class group for line :position',
            'items.*.line_rate.numeric' => 'Fee rate must be a number for line :position',
            'items.*.line_rate.min' => 'Fee rate cannot be negative for line :position',
            'items.*.line_rate.max' => 'Fee rate cannot exceed 100% for line :position',
            'items.*.ledger.in' => 'Invalid ledger for line :position',
            'items.*.amount.required' => 'Amount is required for line :position',
            'items.*.amount.numeric' => 'Amount must be a valid number for line :position',
            'items.*.amount.min' => 'Amount must be greater than 0 for line :position',
            'items.*.amount.max' => 'Amount exceeds maximum allowed for line :position',
        ];
    }

    public function attributes(): array
    {
        return [
            'cover_no' => 'cover number',
            'endorsement_no' => 'endorsement number',
            'type_of_bus' => 'business type',
            'posting_year' => 'fiscal year',
            'posting_quarter' => 'accounting period',
            'posting_date' => 'transaction date',
            'currency_code' => 'currency',
            'today_currency' => 'exchange rate',
            'brokerage_rate' => 'brokerage rate',
            'items.*.item_code' => 'item code',
            'items.*.item_type' => 'item type',
            'items.*.description' => 'transaction type',
            'items.*.class_group' => 'class group',
            'items.*.class_name' => 'business class',
            'items.*.ledger' => 'item type',
            'items.*.line_rate' => 'fee rate',
            'items.*.amount' => 'amount',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'compute_premium_tax' => $this->boolean('compute_premium_tax'),
            'compute_reinsurance_tax' => $this->boolean('compute_reinsurance_tax'),
            'compute_withholding_tax' => $this->boolean('compute_withholding_tax'),
            'loss_participation' => $this->boolean('loss_participation'),
            'sliding_commission' => $this->boolean('sliding_commission'),
            'show_cedant' => $this->boolean('show_cedant'),
            'show_reinsurer' => $this->boolean('show_reinsurer'),
        ]);

        if ($this->has('brokerage_rate')) {
            $this->merge([
                'brokerage_rate' => $this->brokerage_rate !== ''
                    ? (float) $this->brokerage_rate
                    : null
            ]);
        }

        if ($this->has('today_currency')) {
            $this->merge([
                'today_currency' => $this->today_currency !== ''
                    ? (float) $this->today_currency
                    : null
            ]);
        }

        if ($this->has('items') && is_array($this->items)) {
            $items = collect($this->items)
                ->filter(fn($item) => !empty($item['amount']) || !empty($item['description']))
                ->map(function ($item) {
                    return [
                        'item_type' => $item['item_type'] ?? null,
                        'item_code' => $item['item_code'] ?? null,
                        'description' => $item['description'] ?? null,
                        'class_group' => $item['class_group'] ?? null,
                        'class_name' => $item['class_name'] ?? null,
                        'line_rate' => isset($item['line_rate']) && $item['line_rate'] !== ''
                            ? (float) $item['line_rate']
                            : null,
                        'ledger' => $item['ledger'] ?? null,
                        'amount' => isset($item['amount'])
                            ? (float) $item['amount']
                            : null,
                    ];
                })
                ->values()
                ->toArray();

            $this->merge(['items' => $items]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateTotalAmount($validator);
            // $this->validateDuplicateItems($validator);
            $this->validatePostingPeriod($validator);
        });
    }

    protected function validateTotalAmount(Validator $validator): void
    {
        if (!$this->has('items') || !is_array($this->items)) {
            return;
        }

        $totalAmount = collect($this->items)
            ->sum(fn($item) => (float) ($item['amount'] ?? 0));

        if ($totalAmount <= 0) {
            $validator->errors()->add(
                'items',
                'Total transaction amount must be greater than 0'
            );
        }
    }

    protected function validateDuplicateItems(Validator $validator): void
    {
        if (!$this->has('items') || !is_array($this->items)) {
            return;
        }

        $itemCodes = collect($this->items)
            ->pluck('item_code')
            ->filter()
            ->values();

        $duplicates = $itemCodes->duplicates();

        if ($duplicates->isNotEmpty()) {
            $validator->errors()->add(
                'items',
                'Duplicate item codes found: ' . $duplicates->unique()->implode(', ')
            );
        }
    }

    protected function validatePostingPeriod(Validator $validator): void
    {
        if (!$this->posting_date || !$this->posting_quarter || !$this->posting_year) {
            return;
        }

        try {
            $date = Carbon::parse($this->posting_date);
            $expectedQuarter = 'Q' . $date->quarter;

            if ($expectedQuarter !== $this->posting_quarter) {
                $validator->errors()->add(
                    'posting_date',
                    "Transaction date falls in {$expectedQuarter}, not {$this->posting_quarter}"
                );
            }

            if ($date->year !== (int) $this->posting_year) {
                $validator->errors()->add(
                    'posting_date',
                    'Transaction date must be within the selected fiscal year'
                );
            }
        } catch (\Exception $e) {
            // Date parsing failed, handled by date validation rule
        }
    }

    protected function failedAuthorization(): void
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'You do not have permission to generate debit notes.'
        );
    }
}
