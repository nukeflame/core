<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterCoverRequest extends FormRequest
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
            'region' => 'nullable|string',
            'period' => 'nullable|string',
            'line_of_business' => 'nullable|string',
            'tab' => 'nullable|string|in:covers-placement,covers-by-type,covers-ending,renewed-covers',
            'page' => 'nullable|integer|min:1',
        ];
    }
}
