<?php

namespace App\Notifications;

use App\Models\Notification as ApprovalNotice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $approvalNotice;
    protected $action;

    /**
     * Create a new notification instance.
     */
    public function __construct(ApprovalNotice $approvalNotice, ?string $action)
    {
        $this->approvalNotice = $approvalNotice;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        //TODO: Implement toMail() method.
        return (new MailMessage)
            ->subject('Notification from "_"')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
