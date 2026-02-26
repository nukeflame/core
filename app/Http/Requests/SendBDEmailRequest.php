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
        return [
            'opportunity_id' => 'required|string|max:100',
            'customer_id' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'reference' => 'required|string|max:100',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'category' => 'nullable|in:lead,proposal,negotiation,won,lost,final',

            // "to_email" is submitted as a comma-separated string in this form.
            'to_email' => 'nullable|string',
            'contacts' => 'nullable|array',
            'contacts.*' => 'email',
            'cc_email' => 'nullable|array',
            'cc_email.*' => 'email',
            'bcc_email' => 'nullable|array',
            'bcc_email.*' => 'email',

            'is_reply' => 'nullable|boolean',
            'include_reply_attachments' => 'nullable|boolean',
            'message_id' => 'required_if:is_reply,1|nullable|string',
            'conversation_id' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'subject.required' => 'Email subject is required',
            'message.required' => 'Email message body is required',
            'opportunity_id.required' => 'Opportunity is required',
            'customer_id.required' => 'Customer is required',
            'reference.required' => 'Reference is required',
            'contacts.*.email' => 'Each contact must be a valid email address',
            'cc_email.*.email' => 'Each CC contact must be a valid email address',
            'bcc_email.*.email' => 'Each BCC contact must be a valid email address',
            'priority.in' => 'Priority must be one of: low, normal, high, urgent',
            'message_id.required' => 'Message ID is required when replying to an email',
            'message_id.required_if' => 'Message ID is required when replying to an email',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $toEmails = collect(explode(',', (string) $this->input('to_email', '')))
                ->map(static fn(string $email) => trim($email))
                ->filter()
                ->values()
                ->all();

            foreach ($toEmails as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add('to_email', "Invalid recipient email: {$email}");
                }
            }

            $contacts = array_filter((array) $this->input('contacts', []));
            $cc = array_filter((array) $this->input('cc_email', []));
            $bcc = array_filter((array) $this->input('bcc_email', []));
            $isReply = $this->boolean('is_reply');

            if ($isReply && empty($toEmails)) {
                $validator->errors()->add('to_email', 'Reply recipient is required.');
            }

            if (!$isReply && empty($toEmails) && empty($contacts) && empty($cc) && empty($bcc)) {
                $validator->errors()->add('contacts', 'At least one recipient is required.');
            }
        });
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

        if ($this->has('include_reply_attachments')) {
            $this->merge([
                'include_reply_attachments' => filter_var(
                    $this->include_reply_attachments,
                    FILTER_VALIDATE_BOOLEAN,
                ),
            ]);
        }
    }
}
