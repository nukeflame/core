<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
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
        $rules = [
            'partnerName' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('customers', 'partner_name')->whereNull('deleted_at'),
            ],
            'customerType' => 'sometimes|array|min:1',
            'customerType.*' => 'required|exists:customer_types,type_id',
            'email' => [
                'sometimes',
                'email:rfc,dns',
                'max:255',
                Rule::unique('customers', 'email')->whereNull('deleted_at'),
            ],
            'telephone' => 'sometimes|string|max:50',

            'incorporationNo' => 'nullable|string|max:100',
            'taxNo' => 'nullable|string|max:100',
            'identityType' => 'nullable|string|max:100',
            'identityNo' => 'nullable|string|max:100',
            'website' => 'nullable|string|max:255',

            'securityRating' => 'nullable|string|max:50',
            'ratingAgency' => 'nullable|string|max:100',
            'ratingDate' => 'nullable|date|before_or_equal:today',
            'regulatorLicenseNo' => 'nullable|string|max:100',
            'licensingAuthority' => 'nullable|string|max:255',
            'licensingTerritory' => 'nullable|string|max:100',
            'amlDetails' => 'nullable|string|max:1000',
            'insuredType' => 'nullable|string|in:Individual,Corporate',
            'industryOccupation' => 'nullable|string|max:255',
            'dateOfBirthIncorporation' => 'nullable|date|before_or_equal:today',

            'country' => 'required|string|exists:countries,country_iso',
            'street' => 'sometimes|string|max:500',
            'city' => 'sometimes|string|max:100',
            'state' => 'nullable|string|max:100',
            'postalCode' => 'sometimes|string|max:20',

            'financialRating' => 'nullable|string|in:AAA,AA,A,BBB,BB,B,CCC,NR',
            'agencyRating' => 'nullable|string|max:50',

            'contacts' => 'required|array|min:1|max:10',
            'contacts.*.name' => 'sometimes|string|max:255',
            'contacts.*.position' => 'sometimes|string|max:100',
            'contacts.*.mobile' => 'sometimes|string|max:50',
            'contacts.*.email' => 'sometimes|email:rfc|max:255',
            'contacts.*.department' => 'nullable|string|in:executive,underwriting,claims,sales,marketing,finance,technical,operations,legal,hr,other',
            'contacts.*.isPrimary' => 'nullable|boolean',
            'contacts.*.order' => 'nullable|integer|min:0',
        ];

        // $this->applyTypeSpecificRules($rules);

        return $rules;
    }

    protected function applyTypeSpecificRules(array &$rules): void
    {
        $customerTypes = $this->input('customerType', []);
        $typeSlugs = $this->getTypeSlugs($customerTypes);

        if (in_array('reinsurer', $typeSlugs)) {
            $rules['incorporationNo'] = 'required|string|max:100';
            $rules['amlDetails'] = 'required|string|max:1000';
        }

        if (in_array('cedant', $typeSlugs)) {
            $rules['incorporationNo'] = 'required|string|max:100';
            $rules['taxNo'] = 'required|string|max:100';
            $rules['regulatorLicenseNo'] = 'required|string|max:100';
            $rules['licensingTerritory'] = 'required|string|max:100';
            $rules['amlDetails'] = 'required|string|max:1000';
        }

        if (in_array('reinsurance_broker', $typeSlugs)) {
            $rules['incorporationNo'] = 'required|string|max:100';
            $rules['taxNo'] = 'required|string|max:100';
            $rules['regulatorLicenseNo'] = 'required|string|max:100';
            $rules['licensingAuthority'] = 'required|string|max:255';
            $rules['licensingTerritory'] = 'required|string|max:100';
            $rules['amlDetails'] = 'required|string|max:1000';
            $rules['contacts'] = 'required|array|min:2|max:10';
        }

        if (in_array('insurance_broker', $typeSlugs)) {
            $rules['incorporationNo'] = 'required|string|max:100';
            $rules['taxNo'] = 'required|string|max:100';
            $rules['regulatorLicenseNo'] = 'required|string|max:100';
            $rules['licensingAuthority'] = 'required|string|max:255';
            $rules['licensingTerritory'] = 'required|string|max:100';
            $rules['amlDetails'] = 'required|string|max:1000';
            $rules['contacts'] = 'required|array|min:2|max:10';
        }

        if (in_array('insured', $typeSlugs)) {
            $rules['identityType'] = 'required|string|max:100';
            $rules['identityNo'] = 'required|string|max:100';
            $rules['insuredType'] = 'required|string|in:Individual,Corporate';
            $rules['industryOccupation'] = 'required|string|max:255';
            $rules['dateOfBirthIncorporation'] = 'required|date|before_or_equal:today';
            $rules['amlDetails'] = 'required|string|max:1000';
        }
    }

    protected function getTypeSlugs(array $typeIds): array
    {
        if (empty($typeIds)) {
            return [];
        }

        $typeMapping = [
            '1' => 'reinsurer',
            '2' => 'cedant',
            '3' => 'reinsurance_broker',
            '4' => 'insured',
            '5' => 'insurance_broker',
        ];

        $slugs = [];
        foreach ($typeIds as $typeId) {
            if (isset($typeMapping[$typeId])) {
                $slugs[] = $typeMapping[$typeId];
            }
        }

        return $slugs;
    }

    public function messages(): array
    {
        return [
            'partnerName.required' => 'The legal/trading name is required.',
            'partnerName.unique' => 'A customer with this name already exists.',
            'partnerName.max' => 'The name cannot exceed 255 characters.',
            'customerType.required' => 'Please select at least one entity type.',
            'customerType.*.exists' => 'The selected entity type is invalid.',
            'email.required' => 'The primary email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'A customer with this email already exists.',
            'telephone.required' => 'The primary telephone number is required.',

            'incorporationNo.required' => 'The registration/incorporation number is required for this entity type.',
            'taxNo.required' => 'The tax identification number is required for this entity type.',
            'identityType.required' => 'The identity document type is required for this entity type.',
            'identityNo.required' => 'The identity document number is required for this entity type.',
            'website.url' => 'Please enter a valid website URL.',

            'regulatorLicenseNo.required' => 'The regulator license number is required for this entity type.',
            'licensingAuthority.required' => 'The licensing authority is required for this entity type.',
            'licensingTerritory.required' => 'The licensing territory is required for this entity type.',
            'amlDetails.required' => 'AML details are required for this entity type.',
            'insuredType.required' => 'Please specify whether this is an Individual or Corporate insured.',
            'industryOccupation.required' => 'The industry/occupation is required for this entity type.',
            'dateOfBirthIncorporation.required' => 'The date of birth/incorporation is required for this entity type.',
            'ratingDate.before_or_equal' => 'The rating date cannot be in the future.',
            'dateOfBirthIncorporation.before_or_equal' => 'The date cannot be in the future.',

            'country.required' => 'Please select the country of incorporation/citizenship.',
            'country.exists' => 'The selected country is invalid.',
            'street.required' => 'The street address is required.',
            'city.required' => 'The city/town is required.',
            'postalCode.required' => 'The postal/ZIP code is required.',

            'financialRating.in' => 'The selected financial rating is invalid.',

            'contacts.required' => 'At least one contact person is required.',
            'contacts.min' => 'At least :min contact(s) are required for this entity type.',
            'contacts.max' => 'A maximum of :max contacts are allowed.',
            'contacts.*.name.required' => 'Contact name is required.',
            'contacts.*.position.required' => 'Contact position is required.',
            'contacts.*.mobile.required' => 'Contact mobile number is required.',
            'contacts.*.email.required' => 'Contact email is required.',
            'contacts.*.email.email' => 'Please enter a valid email address for the contact.',
            'contacts.*.department.in' => 'The selected department is invalid.',
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
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors(),
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
