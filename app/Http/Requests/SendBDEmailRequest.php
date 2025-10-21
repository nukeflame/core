<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendBDEmailRequest extends FormRequest
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
        $rules = [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'to_email' => 'required_without:contacts|string',
            'contacts' => 'array',
            'contacts.*' => 'email',
            'cc_email' => 'array',
            'cc_email.*' => 'email',
            'bcc_email' => 'array',
            'bcc_email.*' => 'email',
            'priority' => 'nullable|in:low,normal,high',
            'opportunity_id' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'reference' => 'nullable|string|max:100',
            'is_reply' => 'nullable|boolean',
            'attachments' => 'nullable|array',
            'customer_id' => 'required',
            'attachments.*' => 'file|max:51200000', // 50 GB per file
        ];

        if ($this->input('is_reply') === true || $this->input('is_reply') === '1') {
            $rules['message_id'] = 'required|string';
            $rules['conversation_id'] = 'required|string';
        } else {
            $rules['message_id'] = 'nullable|string';
            $rules['conversation_id'] = 'nullable|string';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'subject.required' => 'Email subject is required',
            'message.required' => 'Email message body is required',
            'to_email.required_without' => 'At least one recipient (to_email or contacts) is required',
            'contacts.*.email' => 'Each contact must be a valid email address',
            'cc_email.*.email' => 'Each CC contact must be a valid email address',
            'bcc_email.*.email' => 'Each BCC contact must be a valid email address',
            'priority.in' => 'Priority must be one of: low, normal, high',
            'message_id.required' => 'Message ID is required when replying to an email',
            'conversation_id.required' => 'Conversation Message ID is required when replying to an email',
            'attachments.*.max' => 'Each attachment must not exceed 4MB',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('to_email') && is_string($this->to_email)) {
            $emails = array_map('trim', explode(',', $this->to_email));
            $this->merge(['to_email' => implode(',', $emails)]);
        }

        if ($this->has('is_reply')) {
            $this->merge(['is_reply' => filter_var($this->is_reply, FILTER_VALIDATE_BOOLEAN)]);
        }
    }
}
