<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Psr\EventDispatcher\EventDispatcherInterface;

class SendGridTransport extends AbstractTransport
{
    private string $apiKey;
    private HttpClientInterface $client;

    public function __construct(string $apiKey, HttpClientInterface $client, ?EventDispatcherInterface $dispatcher = null)
    {
        $this->apiKey = $apiKey;
        $this->client = $client;
        // Pass only the dispatcher to the parent
        parent::__construct($dispatcher);
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var Email $email */
        $email = $message->getOriginalMessage();
    
        // Build the content array based on what the email provides.
        $content = [];
    
        // If there's an HTML body, add it as the HTML part.
        if ($html = $email->getHtmlBody()) {
            $content[] = [
                'type'  => 'text/html',
                'value' => $html,
            ];
        }
    
        // If there's a plain text body, add it as the plain text part.
        if ($text = $email->getTextBody()) {
            $content[] = [
                'type'  => 'text/plain',
                'value' => $text,
            ];
        }
    
        // If neither exists, fallback to an empty plain text message.
        if (empty($content)) {
            $content[] = [
                'type'  => 'text/plain',
                'value' => '',
            ];
        }
    
        $payload = [
            'personalizations' => [
                [
                    'to'      => array_map(function ($address) {
                        return ['email' => $address->getAddress()];
                    }, $email->getTo()),
                    'subject' => $email->getSubject(),
                ]
            ],
            'from' => [
                'email' => $email->getFrom()[0]->getAddress(),
            ],
            'content' => $content,
        ];
    
        try {
            $response = $this->client->request('POST', 'https://api.sendgrid.com/v3/mail/send', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);
    
            if ($response->getStatusCode() >= 400) {
                $errorContent = $response->getContent(false); // Get the response without throwing an exception.
                throw new TransportException('Unable to send email via SendGrid. Response: ' . $errorContent);
            }
        } catch (\Exception $e) {
            throw new TransportException('Error sending email: ' . $e->getMessage(), 0, $e);
        }
    }
    

    public function __toString(): string
    {
        return 'sendgrid';
    }
}
