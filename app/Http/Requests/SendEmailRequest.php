<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'recipient_email' => 'required|email|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,zip,rar',
            'priority' => 'nullable|in:low,medium,high',
            'scheduled_at' => 'nullable|date|after:now'
        ];
    }

    public function messages()
    {
        return [
            'recipient_email.required' => 'Please enter a recipient email address.',
            'recipient_email.email' => 'Please enter a valid email address.',
            'subject.required' => 'Please enter a subject for your email.',
            'body.required' => 'Please enter a message body.',
            'attachments.*.max' => 'Each attachment must be less than 10MB.',
            'attachments.*.mimes' => 'Invalid file type. Please upload supported file formats.',
        ];
    }
}
