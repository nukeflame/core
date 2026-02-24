<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProspectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $prospectId = $this->route('prospect') ?? $this->input('prospect_id');

        return [
            // Cedant Details
            'type_of_bus' => 'required|string|in:FPR,FNP,TPR,TNP',
            'customer_id' => 'nullable|exists:customers,customer_id',
            'client_type' => 'required|string|max:50',
            'lead_name' => 'required|string|max:255',
            'lead_year' => 'required|exists:pipe_years,id',
            'client_category' => 'required|in:N,O',
            'country_code' => 'required|exists:countries,country_iso',
            'branchcode' => 'required|exists:branches,branch_code',

            // Contact Details (Arrays)
            'contact_name' => 'required|array|min:1',
            'contact_name.*' => 'required|string|max:255',
            'email' => 'required|array|min:1',
            'email.*' => 'required|email|max:255',
            'phone_number' => 'required|array|min:1',
            'phone_number.*' => 'required|string|max:20',
            'telephone' => 'nullable|array',
            'telephone.*' => 'nullable|string|max:20',

            // Insurance Details (Conditional based on business type)
            'division' => $this->getInsuranceFieldRule('nullable|exists:reinsdivisions,division_code'),
            'class_group' => $this->getInsuranceFieldRule('nullable|exists:class_groups,group_code'),
            'classcode' => $this->getInsuranceFieldRule('nullable|exists:classes,class_code'),
            'insured_name' => $this->getInsuranceFieldRule('nullable|string|max:255'),
            'industry' => $this->getInsuranceFieldRule('nullable|string|max:100'),
            'fac_date_offered' => $this->getInsuranceFieldRule('nullable|date|after:today'),

            // Currency and Financial Details
            'currency_code' => $this->getInsuranceFieldRule('nullable|exists:currencies,currency_code'),
            'today_currency' => $this->getInsuranceFieldRule('nullable|numeric|min:0'),
            'sum_insured_type' => $this->getInsuranceFieldRule('nullable|exists:types_of_sum_insured,sum_insured_code'),
            'total_sum_insured' => $this->getInsuranceFieldRule('nullable|numeric|min:0'),

            // EML Details
            'apply_eml' => $this->getInsuranceFieldRule('nullable|in:Y,N'),
            'eml_rate' => 'nullable|numeric|min:0|max:100|required_if:apply_eml,Y',
            'eml_amt' => 'nullable|numeric|min:0|required_if:apply_eml,Y',
            'effective_sum_insured' => $this->getInsuranceFieldRule('nullable|numeric|min:0'),

            // Premium and Commission Details
            'risk_details' => $this->getInsuranceFieldRule('nullable|string|max:1000'),
            'cede_premium' => $this->getInsuranceFieldRule('nullable|numeric|min:0'),
            'rein_premium' => $this->getInsuranceFieldRule('nullable|numeric|min:0'),
            'fac_share_offered' => $this->getInsuranceFieldRule('nullable|numeric|min:0|max:100'),
            'comm_rate' => $this->getInsuranceFieldRule('nullable|numeric|min:0|max:100'),
            'comm_amt' => $this->getInsuranceFieldRule('nullable|numeric|min:0'),

            // Reinsurance Commission
            'reins_comm_type' => $this->getInsuranceFieldRule('nullable|in:R,A'),
            'reins_comm_rate' => 'nullable|numeric|min:0|max:100|required_if:reins_comm_type,R',
            'reins_comm_amt' => $this->getInsuranceFieldRule('nullable|numeric|min:0'),

            // Brokerage Commission
            'brokerage_comm_type' => $this->getInsuranceFieldRule('nullable|in:R,A'),
            'brokerage_comm_amt' => 'nullable|numeric|min:0|required_if:brokerage_comm_type,A',
            'brokerage_comm_rate' => 'nullable|numeric',

            // Engagement Details
            'engage_type' => 'required|exists:engage_types,id',
            'lead_owner' => 'required|exists:users,id',
            'cover_dates_tba' => 'nullable|boolean',
            'effective_date' => 'nullable|date|required_unless:cover_dates_tba,1',
            'closing_date' => 'nullable|date|after_or_equal:effective_date|required_unless:cover_dates_tba,1',

            // Document Upload
            'document_name' => 'nullable|array',
            'document_name.*' => 'nullable|string|max:255',
            'document_file' => 'nullable|array',
            'document_file.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ];
    }

    /**
     * Get validation rules for insurance fields based on business type
     */
    private function getInsuranceFieldRule(string $baseRule): string
    {
        $businessType = $this->input('type_of_bus');

        if (in_array($businessType, ['FPR', 'FNP'])) {
            // For facultative business, make insurance fields required
            return str_replace('nullable', 'required', $baseRule);
        }

        return $baseRule;
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'type_of_bus.required' => 'Please select the type of business.',
            'type_of_bus.in' => 'Invalid business type selected.',
            'lead_name.required' => 'Lead name is required.',
            'lead_name.max' => 'Lead name cannot exceed 255 characters.',
            'lead_year.required' => 'Please select a year.',
            'lead_year.exists' => 'Invalid year selected.',
            'client_category.required' => 'Please select an insured category.',
            'client_category.in' => 'Invalid insured category selected.',
            'country_code.required' => 'Please select a country.',
            'country_code.exists' => 'Invalid country selected.',
            'branchcode.required' => 'Please select a branch.',
            'branchcode.exists' => 'Invalid branch selected.',

            // Contact validation messages
            'contact_name.required' => 'At least one contact is required.',
            'contact_name.*.required' => 'Contact name is required.',
            'email.required' => 'At least one email is required.',
            'email.*.required' => 'Email is required for each contact.',
            'email.*.email' => 'Please enter a valid email address.',
            'phone_number.required' => 'At least one phone number is required.',
            'phone_number.*.required' => 'Phone number is required for each contact.',

            // Insurance validation messages
            'division.required' => 'Division is required for facultative business.',
            'class_group.required' => 'Class group is required for facultative business.',
            'classcode.required' => 'Class code is required for facultative business.',
            'insured_name.required' => 'Insured name is required for facultative business.',
            'currency_code.required' => 'Currency is required for facultative business.',
            'total_sum_insured.required' => 'Sum insured is required for facultative business.',
            'cede_premium.required' => 'Cedant premium is required for facultative business.',
            'fac_share_offered.required' => 'Share offered is required for facultative business.',
            'comm_rate.required' => 'Commission rate is required for facultative business.',

            // EML validation messages
            'eml_rate.required_if' => 'EML rate is required when EML is applied.',
            'eml_rate.max' => 'EML rate cannot exceed 100%.',
            'eml_amt.required_if' => 'EML amount is required when EML is applied.',

            // Commission validation messages
            'reins_comm_rate.required_if' => 'Reinsurance commission rate is required when type is Rate.',
            'brokerage_comm_amt.required_if' => 'Brokerage amount is required when type is Amount.',

            // Date validation messages
            'fac_date_offered.after' => 'Expected closure date must be in the future.',
            'closing_date.after_or_equal' => 'Closing date must be on or after the effective date.',
            'effective_date.required_unless' => 'Cover start date is required unless marked as To Be Advised.',
            'closing_date.required_unless' => 'Cover end date is required unless marked as To Be Advised.',

            // Engagement validation messages
            'engage_type.required' => 'Please select the nature of engagement.',
            'engage_type.exists' => 'Invalid engagement type selected.',
            'lead_owner.required' => 'Please select a prospect lead.',
            'lead_owner.exists' => 'Invalid prospect lead selected.',

            // File validation messages
            'document_file.*.mimes' => 'Document must be a PDF, DOC, DOCX, JPG, JPEG, or PNG file.',
            'document_file.*.max' => 'Document size cannot exceed 10MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'type_of_bus' => 'type of business',
            'customer_id' => 'cedant',
            'client_type' => 'lead type',
            'lead_name' => 'lead name',
            'lead_year' => 'year',
            'client_category' => 'insured category',
            'country_code' => 'country',
            'branchcode' => 'branch',
            'contact_name.*' => 'contact name',
            'email.*' => 'email',
            'phone_number.*' => 'phone number',
            'telephone.*' => 'telephone',
            'insured_name' => 'insured name',
            'currency_code' => 'currency',
            'today_currency' => 'exchange rate',
            'sum_insured_type' => 'sum insured type',
            'total_sum_insured' => 'sum insured',
            'apply_eml' => 'apply EML',
            'eml_rate' => 'EML rate',
            'eml_amt' => 'EML amount',
            'effective_sum_insured' => 'effective sum insured',
            'risk_details' => 'risk details',
            'cede_premium' => 'cedant premium',
            'rein_premium' => 'reinsurance premium',
            'fac_share_offered' => 'share offered',
            'comm_rate' => 'commission rate',
            'comm_amt' => 'commission amount',
            'reins_comm_type' => 'reinsurance commission type',
            'reins_comm_rate' => 'reinsurance commission rate',
            'reins_comm_amt' => 'reinsurance commission amount',
            'brokerage_comm_type' => 'brokerage commission type',
            'brokerage_comm_amt' => 'brokerage commission amount',
            'brokerage_comm_rate' => 'brokerage commission rate',
            'engage_type' => 'engagement type',
            'lead_owner' => 'prospect lead',
            'cover_dates_tba' => 'cover dates to be advised',
            'effective_date' => 'effective date',
            'closing_date' => 'closing date',
            'fac_date_offered' => 'expected closure date',
            'document_name.*' => 'document name',
            'document_file.*' => 'document file',
        ];
    }

    /**
     * Handle a failed validation attempt
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'status' => 0,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->toArray()
            ], 422);

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        parent::failedValidation($validator);
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Custom validation logic
            $this->validateContactUniqueness($validator);
            $this->validateInsuranceConsistency($validator);
            $this->validateCommissionCalculations($validator);
        });
    }

    /**
     * Validate contact email uniqueness
     */
    private function validateContactUniqueness($validator)
    {
        $emails = array_filter($this->input('email', []));

        if (count($emails) !== count(array_unique($emails))) {
            $validator->errors()->add('email', 'Contact emails must be unique.');
        }
    }

    /**
     * Validate insurance details consistency
     */
    private function validateInsuranceConsistency($validator)
    {
        $applyEml = $this->input('apply_eml');
        $emlRate = $this->input('eml_rate');
        $totalSumInsured = $this->parseNumericValue($this->input('total_sum_insured'));
        $effectiveSumInsured = $this->parseNumericValue($this->input('effective_sum_insured'));

        if ($applyEml === 'Y' && $emlRate && $totalSumInsured && $effectiveSumInsured) {
            $calculatedEml = $totalSumInsured * ($emlRate / 100);

            if (abs($calculatedEml - $effectiveSumInsured) > 0.01) {
                $validator->errors()->add(
                    'effective_sum_insured',
                    'Effective sum insured does not match EML calculation.'
                );
            }
        }
    }

    /**
     * Validate commission calculations
     */
    private function validateCommissionCalculations($validator)
    {
        $commRate = $this->input('comm_rate');
        $cedePremium = $this->parseNumericValue($this->input('cede_premium'));
        $commAmt = $this->parseNumericValue($this->input('comm_amt'));

        if ($commRate && $cedePremium && $commAmt) {
            $calculatedComm = $cedePremium * ($commRate / 100);

            if (abs($calculatedComm - $commAmt) > 0.01) {
                $validator->errors()->add(
                    'comm_amt',
                    'Commission amount does not match rate calculation.'
                );
            }
        }
    }

    /**
     * Parse numeric value (remove commas)
     */
    private function parseNumericValue($value)
    {
        if (empty($value)) {
            return null;
        }

        $cleanValue = str_replace(',', '', $value);
        return is_numeric($cleanValue) ? (float) $cleanValue : null;
    }
}
