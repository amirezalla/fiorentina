<?php

namespace Botble\Member\Notifications;

use Botble\Base\Facades\EmailHandler;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ResetPasswordNotification extends Notification
{
    public function __construct(public string $token)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        try {
            // Create a simple reset link
            $resetLink = route('access.password.reset', ['token' => $this->token]);
            $text = "You requested a password reset. Click here to reset your password: " . $resetLink;
    
            // Build a simple raw email message using the SendGrid mailer
            return (new MailMessage())
                ->mailer('sendgrid') // Force using your custom SendGrid transport
                ->subject('Reset Your Password')
                ->line($text);
        } catch (\Exception $e) {
            \Log::error('Reset password email error: ' . $e->getMessage());
            dd('Error sending reset email: ' . $e->getMessage());
        }
    }
    
}
