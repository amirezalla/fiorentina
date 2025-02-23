<?php

namespace Botble\ACL\Notifications;

use Botble\Base\Facades\EmailHandler;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ResetPasswordNotification extends Notification
{
    public string $token;
    public $mailer='sendgrid';

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        try {
            // Set up the email content using your EmailHandler
            EmailHandler::setModule('acl')
                ->setVariableValue('reset_link', route('access.password.reset', ['token' => $this->token]));
    
            $template = 'password-reminder';
            $content = EmailHandler::prepareData(EmailHandler::getTemplateContent($template, 'core'));
    
            // Build and return the mail message, forcing the sendgrid mailer
            return (new MailMessage())
                ->mailer('sendgrid') // Force using SendGrid mailer for this notification
                ->view(['html' => new HtmlString($content)])
                ->subject(EmailHandler::getTemplateSubject($template));
        } catch (\Exception $e) {
            \Log::error('Reset password email error: ' . $e->getMessage());
            dd('Error sending reset email: ' . $e->getMessage());
        }
    }
    
}
