<?php

namespace Botble\Member\Notifications;

use Botble\Base\Facades\EmailHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class ConfirmEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
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
            // Generate the confirmation link
            $verifyLink = URL::signedRoute('public.member.confirm', ['user' => $notifiable->id]);
            
            // Define a simple subject and message body
            $subject = "Conferma il tuo indirizzo email su Laviola";
            $content = "Salve,\n\nPer favore, conferma il tuo indirizzo email cliccando sul link sottostante:\n" 
                       . $verifyLink 
                       . "\n\nCordiali saluti,\nLaviola";
            
            
            // Build and return the mail message, forcing the SendGrid mailer
            return (new MailMessage())
                ->mailer('sendgrid') // Force using your custom SendGrid transport
                ->subject($subject)
                ->view('emails.template', [
                    'subject' => $subject,
                    'content' => $content,
                ]);
        } catch (\Exception $e) {
            \Log::error('Confirm email error: ' . $e->getMessage());
            dd('Error sending confirm email: ' . $e->getMessage());
        }
    }
    
}
