<?php

namespace App\Mail\Transport;

use Illuminate\Mail\Transport\Transport;
use Illuminate\Support\Facades\Http;
use Swift_Mime_SimpleMessage;

class SendGridTransport extends Transport
{
    protected $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Send the given message.
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @param  string[]  &$failedRecipients
     * @return void
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        try{
        dd('sendgrid');
        $this->beforeSendPerformed($message);

        // Build payload from the Swift message
        $payload = [
            "personalizations" => [
                [
                    "to" => $this->getRecipients($message),
                    "subject" => $message->getSubject(),
                ]
            ],
            "from" => [
                "email" => $this->getSender($message),
            ],
            "content" => [
                [
                    "type"  => "text/plain",
                    "value" => $message->getBody(),
                ]
            ]
        ];


            // Send the email using SendGrid API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://api.sendgrid.com/v3/mail/send', $payload);

            // Optionally, you could inspect $response to log errors or failures

            return $response;
        }catch(\Exception $e){
            dd($e);
        }
        
    }

    /**
     * Get the recipients from the message.
     */
    protected function getRecipients(Swift_Mime_SimpleMessage $message)
    {
        $recipients = [];
        foreach ((array) $message->getTo() as $email => $name) {
            $recipients[] = ['email' => $email, 'name' => $name];
        }
        return $recipients;
    }

    /**
     * Get the sender from the message.
     */
    protected function getSender(Swift_Mime_SimpleMessage $message)
    {
        $from = $message->getFrom();
        if ($from) {
            foreach ($from as $email => $name) {
                return $email; // use the first sender email
            }
        }
        return config('mail.from.address');
    }
}
