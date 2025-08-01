<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class PerformanceRecordRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_handler' => 'required|array',
            'account_handler.*' => 'exists:users,id',
            // 'account_handler.*' => 'unique:performance_records,user_id',
            'account_period' => 'required|string|regex:/^\d{4}\/\d{2}$/',
            'record_date' => 'required|date',

            'new_fac_gwp' => 'nullable|string',
            'new_special_gwp' => 'nullable|string',
            'new_treaty_gwp' => 'nullable|string',
            'new_market_gwp' => 'nullable|string',
            'new_fac_income' => 'nullable|string',
            'new_special_income' => 'nullable|string',
            'new_treaty_income' => 'nullable|string',
            'new_market_income' => 'nullable|string',

            'renewal_fac_gwp' => 'nullable|string',
            'renewal_special_gwp' => 'nullable|string',
            'renewal_treaty_gwp' => 'nullable|string',
            'renewal_market_gwp' => 'nullable|string',
            'renewal_fac_income' => 'nullable|string',
            'renewal_special_income' => 'nullable|string',
            'renewal_treaty_income' => 'nullable|string',
            'renewal_market_income' => 'nullable|string',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $input = $this->all();

        $numericFields = [
            'new_fac_gwp',
            'new_special_gwp',
            'new_treaty_gwp',
            'new_market_gwp',
            'new_fac_income',
            'new_special_income',
            'new_treaty_income',
            'new_market_income',
            'renewal_fac_gwp',
            'renewal_special_gwp',
            'renewal_treaty_gwp',
            'renewal_market_gwp',
            'renewal_fac_income',
            'renewal_special_income',
            'renewal_treaty_income',
            'renewal_market_income'
        ];

        foreach ($numericFields as $field) {
            if (isset($input[$field]) && !empty($input[$field])) {
                $input[$field] = str_replace(',', '', $input[$field]);
            }
        }

        $this->replace($input);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'account_handler.required' => 'Please select at least one staff member',
            'account_handler.*.exists' => 'One or more selected staff members do not exist or have been removed from the system',
            // 'account_handler.*.unique' => 'This staff member already has record on the system for this fiscal year',
            'account_period.required' => 'Account period is required',
            'account_period.regex' => 'Account period must be in format YYYY/MM',
            'record_date.required' => 'Record date is required',
            'record_date.date' => 'Record date must be a valid date',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    // public function failedValidation(Validator $validator)
    // {
    //     // $errors = $validator->errors()->toArray();
    //     // throw new \Illuminate\Validation\ValidationException(
    //     //     $validator,
    //     //     response()->json(['errors' => $errors], 422)
    //     // );
    //     parent::failedValidation($validator);
    // }
}
