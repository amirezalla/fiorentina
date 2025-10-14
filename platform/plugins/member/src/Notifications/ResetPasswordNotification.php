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
            // Generate the password reset link
            $resetLink = route('password.reset', ['token' => $this->token]);
            
            // Define a simple subject and message body
            $subject = "Reimposta la tua password su Laviola";
            $content = "Salve,\n\nAbbiamo ricevuto una richiesta per reimpostare la tua password. " .
                       "Clicca sul link sottostante per reimpostare la tua password:\n\n" .
                       $resetLink . "\n\n" .
                       "Se non hai richiesto la reimpostazione della password, ignora questa email.\n\n" .
                       "Cordiali saluti,\nLaviola";
            
            
            // Build and return the mail message, forcing the SendGrid mailer
            return (new MailMessage())
                 // Force using your custom SendGrid transport
                ->subject($subject)
                ->view('emails.template', [
                    'subject' => $subject,
                    'content' => $content,
                ]);
        } catch (\Exception $e) {
            \Log::error('Reset password email error: ' . $e->getMessage());
            dd('Error sending reset email: ' . $e->getMessage());
        }
    }
    
    
}
