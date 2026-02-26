<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch') || $this->route('customerId');
        $partnerNameRule = $isUpdate
            ? 'required|string|max:255'
            : 'required|string|max:255|unique:customers,name';

        return [
            'partnerName' => $partnerNameRule,
            'customerType' => 'required|array|min:1',
            'customerType.*' => 'required|exists:customer_types,type_id',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',

            'incorporationNo' => 'required|string|max:255',
            'taxNo' => 'required|string|max:255',
            'identityType' => 'nullable|string|max:50',
            'identityNo' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',

            'securityRating' => 'nullable|string|max:50',
            'ratingAgency' => 'nullable|string|max:100',
            'ratingDate' => 'nullable|date',
            'regulatorLicenseNo' => 'nullable|string|max:100',
            'licensingAuthority' => 'nullable|string|max:255',
            'licensingTerritory' => 'nullable|string|max:100',
            'amlDetails' => 'nullable|string|max:1000',
            'insuredType' => 'nullable|string|max:50',
            'industryOccupation' => 'nullable|string|max:255',
            'dateOfBirthIncorporation' => 'nullable|date',

            'country' => 'required|string|size:3|exists:countries,country_iso',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postalCode' => 'required|string|max:20',

            'financialRating' => 'nullable|string|max:10',
            'agencyRating' => 'nullable|string|max:10',

            'contacts' => 'required|array|min:1',
            'contacts.*.id' => 'nullable|integer|exists:customer_contacts,id',
            'contacts.*.department' => 'nullable|string|in:executive,underwriting,claims,sales,marketing,finance,technical,operations,legal,hr,other',
            'contacts.*.name' => 'required|string|max:255',
            'contacts.*.position' => 'required|string|max:100',
            'contacts.*.mobile' => 'required|string|max:20',
            'contacts.*.email' => 'required|email|max:255',
            'contacts.*.isPrimary' => 'nullable|boolean',
            'contacts.*.order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'partnerName.required' => 'Legal/Trading Name is required.',
            'partnerName.unique' => 'Legal/Trading Already added.',
            'customerType.required' => 'Entity Type is required.',
            'email.required' => 'Primary Email Address is required.',
            'telephone.required' => 'Primary Telephone is required.',
            'incorporationNo.required' => 'Registration/Incorporation Number is required.',
            'taxNo.required' => 'Tax Identification Number is required.',
            'country.required' => 'Country is required.',
            'street.required' => 'Street Address is required.',
            'city.required' => 'City/Town is required.',
            'postalCode.required' => 'Postal/ZIP Code is required.',
            'contacts.*.name.required' => 'Primary Contact Name is required.',
            'contacts.*.position.required' => 'Primary Contact Position is required.',
            'contacts.*.mobile.required' => 'Primary Contact Mobile Number is required.',
            'contacts.*.email.required' => 'Primary Contact Email is required.',
        ];
    }

    public function attributes(): array
    {
        return [
            'partnerName' => 'legal/trading name',
            'customerType' => 'entity type',
            'customerType.*' => 'entity type',
            'incorporationNo' => 'registration/incorporation number',
            'taxNo' => 'tax identification number',
            'identityType' => 'identity document type',
            'identityNo' => 'identity document number',
            'regulatorLicenseNo' => 'regulator license number',
            'licensingAuthority' => 'licensing authority',
            'licensingTerritory' => 'licensing territory',
            'amlDetails' => 'AML details',
            'insuredType' => 'insured type',
            'industryOccupation' => 'industry/occupation',
            'dateOfBirthIncorporation' => 'date of birth/incorporation',
            'securityRating' => 'security rating',
            'ratingAgency' => 'rating agency',
            'ratingDate' => 'rating date',
            'postalCode' => 'postal/ZIP code',
            'financialRating' => 'financial rating',
            'agencyRating' => 'agency rating',
            'contacts.*.name' => 'contact name',
            'contacts.*.position' => 'contact position',
            'contacts.*.mobile' => 'contact mobile',
            'contacts.*.email' => 'contact email',
            'contacts.*.department' => 'contact department',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('customerType') && !is_array($this->customerType)) {
            $this->merge([
                'customerType' => [$this->customerType],
            ]);
        }

        if ($this->has('contacts')) {
            $contacts = $this->contacts;
            foreach ($contacts as $index => $contact) {
                $contacts[$index]['isPrimary'] = filter_var(
                    $contact['isPrimary'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );
                $contacts[$index]['order'] = (int) ($contact['order'] ?? $index);
            }
            $this->merge(['contacts' => $contacts]);
        }

        $stringFields = [
            'partnerName',
            'email',
            'telephone',
            'incorporationNo',
            'taxNo',
            'identityNo',
            'website',
            'regulatorLicenseNo',
            'licensingAuthority',
            'licensingTerritory',
            'amlDetails',
            'industryOccupation',
            'street',
            'city',
            'state',
            'postalCode',
            'agencyRating',
        ];

        $trimmed = [];
        foreach ($stringFields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $trimmed[$field] = trim($this->input($field));
            }
        }

        if (!empty($trimmed)) {
            $this->merge($trimmed);
        }
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Validation\ValidationException($validator, response()->json([
                'success' => false,
                'message' => 'Please fill all required fields.',
                'errors' => $validator->errors(),
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
