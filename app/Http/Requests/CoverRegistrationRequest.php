<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CoverRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $transType = $this->input('trans_type');

        $baseRules = [
            'customer_id' => 'required|exists:customers,customer_id',
            'trans_type' => 'required|in:NEW,REN,EDIT,EXT,CNC,RFN,NIL,INS',
            'type_of_bus' => 'required|exists:business_types,bus_type_id',
            'covertype' => 'required|exists:cover_type,type_id',
            'branchcode' => 'required|exists:branch,branch_code',
            'broker_flag' => 'required|in:Y,N',
            'division' => 'required|exists:reins_division,division_code',
            'pay_method' => 'required|exists:pay_method,pay_method_code',
            'currency_code' => 'required|exists:currency,currency_code',
            'today_currency' => 'required|numeric|min:0',
            'premium_payment_term' => 'required|exists:premium_pay_terms,pay_term_code',
            'coverfrom' => 'required|date',
            'coverto' => 'required|date|after:coverfrom',
        ];

        // Conditional rules based on broker flag
        if ($this->input('broker_flag') === 'Y') {
            $baseRules['brokercode'] = 'required';
            // $baseRules['brokercode'] = 'required|exists:brokers,broker_code';
        }

        // Conditional rules based on cover type
        // if ($this->getCoverTypeDescription() === 'B') {
        //     $baseRules['bindercoverno'] = 'required|exists:binder_register,binder_cov_no';
        // }

        // Business type specific rules
        $businessType = $this->input('type_of_bus');

        if (in_array($businessType, ['FPR', 'FNP'])) {
            $baseRules = array_merge($baseRules, $this->getFacultativeRules());
        } elseif ($businessType === 'TPR') {
            $baseRules = array_merge($baseRules, $this->getTreatyProportionalRules());
        } elseif ($businessType === 'TNP') {
            $baseRules = array_merge($baseRules, $this->getTreatyNonProportionalRules());
        }

        // Installment rules
        // $payMethod = $this->getPaymentMethodDescription();
        // if ($payMethod === 'I') {
        //     $baseRules = array_merge($baseRules, $this->getInstallmentRules());
        // }

        return $baseRules;
    }

    /**
     * Get facultative specific rules
     */
    protected function getFacultativeRules(): array
    {
        return [
            'class_group' => 'required|exists:class_groups,group_code',
            'classcode' => 'required|exists:classes,class_code',
            'insured_name' => 'required|string|max:255',
            'fac_date_offered' => 'required|date',
            'sum_insured_type' => 'required|exists:type_of_sum_insured,sum_insured_code',
            'total_sum_insured' => 'required|string',
            'apply_eml' => 'required|in:Y,N',
            'eml_rate' => 'required_if:apply_eml,Y|nullable|numeric|min:0|max:100',
            'eml_amt' => 'required_if:apply_eml,Y|nullable|string',
            'effective_sum_insured' => 'required|string',
            'risk_details' => 'nullable|string',
            'cede_premium' => 'required|string',
            'rein_premium' => 'required|string',
            'fac_share_offered' => 'required|numeric|min:0|max:100',
            'comm_rate' => 'required|numeric|min:0|max:100',
            'comm_amt' => 'required|string',
            'reins_comm_type' => 'required|in:R,A',
            'reins_comm_rate' => 'required_if:reins_comm_type,R|nullable|numeric|min:0|max:100',
            'reins_comm_amt' => 'required|string',
            'brokerage_comm_type' => 'nullable|in:R,A',
            'brokerage_comm_rate' => 'nullable|numeric|min:0|max:100',
            'brokerage_comm_amt' => 'nullable|string',
        ];
    }

    /**
     * Get treaty proportional specific rules
     */
    protected function getTreatyProportionalRules(): array
    {
        return [
            'treatytype' => 'required|exists:treaty_types,treaty_code',
            'date_offered' => 'required|date',
            'share_offered' => 'required|numeric|min:0|max:100',
            'prem_tax_rate' => 'required|numeric|min:0|max:100',
            'ri_tax_rate' => 'required|numeric|min:0|max:100',
            'treaty_brokerage_comm_rate' => 'required|numeric|min:0|max:100',
            'port_prem_rate' => 'required|numeric|min:0|max:100',
            'port_loss_rate' => 'required|numeric|min:0|max:100',
            'profit_comm_rate' => 'required|numeric|min:0|max:100',
            // 'mgnt_exp_rate' => 'required|numeric|min:0|max:100',
            // 'deficit_yrs' => 'required|integer|min:0|max:10',
            // 'treaty_reinclass' => 'required|array|min:1',
            // 'treaty_reinclass.*' => 'required|exists:classes,class_code',
            // 'quota_share_total_limit' => 'nullable|array',
            // 'quota_share_total_limit.*' => 'nullable|string',
            // 'retention_per' => 'nullable|array',
            // 'retention_per.*' => 'nullable|numeric|min:0|max:100',
            // 'treaty_reice' => 'nullable|array',
            // 'treaty_reice.*' => 'nullable|numeric|min:0|max:100',
            // 'no_of_lines' => 'nullable|array',
            // 'no_of_lines.*' => 'nullable|integer|min:0',
            // 'estimated_income' => 'required|array',
            // 'estimated_income.*' => 'required|string',
            // 'cashloss_limit' => 'required|array',
            // 'cashloss_limit.*' => 'required|string',
            // 'prem_type_code' => 'required|array|min:1',
            // 'prem_type_code.*' => 'required|string',
            // 'prem_type_comm_rate' => 'required|array|min:1',
            // 'prem_type_comm_rate.*' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get treaty non-proportional specific rules
     */
    protected function getTreatyNonProportionalRules(): array
    {
        return [
            'treatytype' => 'required|exists:treaty_types,treaty_code',
            'date_offered' => 'required|date',
            'share_offered' => 'required|numeric|min:0|max:100',
            'prem_tax_rate' => 'required|numeric|min:0|max:100',
            'ri_tax_rate' => 'required|numeric|min:0|max:100',
            'brokerage_comm_rate' => 'required|numeric|min:0|max:100',
            'reinclass_code' => 'required|array|min:1',
            'reinclass_code.*' => 'required|exists:reinsurance_classes,class_code',
            'method' => 'required|in:B,F',
            'layer_no' => 'required|array|min:1',
            'layer_no.*' => 'required|integer|min:1',
            'nonprop_reinclass' => 'required|array',
            'nonprop_reinclass.*' => 'required|string',
            'limit_per_reinclass' => 'nullable|array',
            'limit_per_reinclass.*' => 'nullable|in:Y,N',
            'indemnity_treaty_limit' => 'required|array',
            'indemnity_treaty_limit.*' => 'required|string',
            'underlying_limit' => 'required|array',
            'underlying_limit.*' => 'required|string',
            'egnpi' => 'required|array',
            'egnpi.*' => 'required|string',
            'min_bc_rate' => 'required_if:method,B|nullable|array',
            'min_bc_rate.*' => 'nullable|numeric|min:0',
            'max_bc_rate' => 'required_if:method,B|nullable|array',
            'max_bc_rate.*' => 'nullable|numeric|min:0',
            'flat_rate' => 'required_if:method,F|nullable|array',
            'flat_rate.*' => 'nullable|numeric|min:0',
            'upper_adj' => 'required_if:method,B|nullable|array',
            'upper_adj.*' => 'nullable|numeric|min:0',
            'lower_adj' => 'required_if:method,B|nullable|array',
            'lower_adj.*' => 'nullable|numeric|min:0',
            'min_deposit' => 'required|array',
            'min_deposit.*' => 'required|string',
            'reinstatement_type' => 'nullable|array',
            'reinstatement_type.*' => 'nullable|in:NOR,AAL',
            'reinstatement_value' => 'nullable|array',
            'reinstatement_value.*' => 'nullable|string',
        ];
    }

    /**
     * Get installment rules
     */
    protected function getInstallmentRules(): array
    {
        return [
            'no_of_installments' => 'required|integer|min:1|max:100',
            'installment_no' => 'required|array|min:1',
            'installment_no.*' => 'required|integer|min:1',
            'installment_date' => 'required|array|min:1',
            'installment_date.*' => 'required|date',
            'installment_amt' => 'required|array|min:1',
            'installment_amt.*' => 'required|string',
        ];
    }

    /**
     * Get cover type description
     */
    protected function getCoverTypeDescription(): ?string
    {
        // $coverType = DB::table('binder_register')->where($this->input('covertype'))->first();
        // return $coverType?->insured_name;
        return '';
    }

    /**
     * Get payment method description
     */
    protected function getPaymentMethodDescription(): ?string
    {
        // $payMethod = \App\Models\PaymentMethod::where('pay_method_code', $this->input('pay_method'))->first();
        // return $payMethod?->short_description;
        return '';
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer is required',
            'type_of_bus.required' => 'Business type is required',
            'covertype.required' => 'Cover type is required',
            'branchcode.required' => 'Branch is required',
            'coverfrom.required' => 'Cover start date is required',
            'coverto.required' => 'Cover end date is required',
            'coverto.after' => 'Cover end date must be after start date',
            'brokercode.required' => 'Broker is required when broker flag is Yes',
            'bindercoverno.required' => 'Binder policy is required for binder cover type',
            'installment_date.*.required' => 'All installment dates are required',
            'installment_amt.*.required' => 'All installment amounts are required',
        ];
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        // Clean up numeric fields that may contain commas
        $numericFields = [
            'total_sum_insured',
            'eml_amt',
            'effective_sum_insured',
            'cede_premium',
            'rein_premium',
            'comm_amt',
            'reins_comm_amt',
            'brokerage_comm_amt',
            'quota_share_total_limit',
            'quota_retention_amt',
            'quota_treaty_limit',
            'surp_retention_amt',
            'surp_treaty_limit',
            'estimated_income',
            'cashloss_limit',
            'indemnity_treaty_limit',
            'underlying_limit',
            'egnpi',
            'min_deposit',
            'installment_amt',
            'reinstatement_value'
        ];

        $data = $this->all();

        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                if (is_array($data[$field])) {
                    $data[$field] = array_map(function ($value) {
                        return is_string($value) ? str_replace(',', '', $value) : $value;
                    }, $data[$field]);
                } elseif (is_string($data[$field])) {
                    $data[$field] = str_replace(',', '', $data[$field]);
                }
            }
        }

        $this->replace($data);
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
