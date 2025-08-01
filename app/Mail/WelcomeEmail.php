<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $temporaryPassword
    ) {
        // $this->onQueue('emails');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            subject: "Welcome to " . config('app.name') . "! Here's Your Login Info",
            tags: ['welcome-email'],
            metadata: [
                'user_id' => $this->user->id,
                'user_email' => $this->user->email,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'loginUrl' => $this->getLoginUrl(),
                'companyLogo' => $this->getCompanyLogoUrl(),
                'userFirstname' => $this->user->first_name ?? 'User',
                'temporaryPassword' => $this->temporaryPassword,
                'username' => $this->getUsernameField(),
                'email' => $this->user->email,
                'supportPhone' => config('mail.support_phone', '+1-234-567-8900'),
                'supportEmail' => config('mail.support_email', 'support@acentriagroup.com'),
                'requiredPasswordReset' => $this->getPasswordResetRequirement(),
                'appName' => config('app.name', 'Acentriagroup'),
                'companyWebsite' => config('app.url'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get the login URL safely.
     */
    private function getLoginUrl(): string
    {
        try {
            return route('login');
        } catch (\Exception $e) {
            return config('app.url') . '/login';
        }
    }

    /**
     * Get the company logo URL.
     */
    private function getCompanyLogoUrl(): string
    {
        $logoPath = config('mail.company_logo', '/images/logo.png');
        return config('app.url') . $logoPath;
    }

    /**
     * Get the username field value with fallback.
     */
    private function getUsernameField(): string
    {
        return $this->user->username
            ?? $this->user->user_name
            ?? $this->user->email;
    }

    /**
     * Get password reset requirement with fallback.
     */
    private function getPasswordResetRequirement(): bool
    {
        return $this->user->require_password_change
            ?? $this->user->requires_password_reset
            ?? true;
    }
}
