<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendClaimReinsurerRequest extends FormRequest
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
            'claim_no' => 'required|string|max:255',
            'customer_id' => 'required|integer',
            'to_email' => 'required|max:255',
            'partner_email' => 'required|max:255',
            'contacts' => 'nullable|array',
            'contacts.*' => 'email|max:255',
            'cc_email' => 'nullable|array',
            'cc_email.*' => 'email|max:255',
            'bcc_email' => 'nullable|array',
            'bcc_email.*' => 'email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:65535',
            'priority' => 'nullable|in:low,normal,high',
            'category' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif'
        ];
    }

    public function messages(): array
    {
        return [
            'to_email.required' => 'Primary recipient email is required',
            'to_email.email' => 'Please provide a valid primary email addresses',
            'contacts.*.email' => 'All contact emails must be valid',
            'cc_email.*.email' => 'All CC emails must be valid',
            'bcc_email.*.email' => 'All BCC emails must be valid',
            'subject.required' => 'Email subject is required',
            'message.required' => 'Email message cannot be empty',
            'attachments.*.max' => 'Each attachment must be smaller than 10MB',
            'attachments.*.mimes' => 'Attachments must be: pdf, doc, docx, txt, jpg, jpeg, png, gif'

        ];
    }
}
