<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Mailer\Exception\TransportException;

class SendGridTransport extends AbstractTransport
{
    private string $apiKey;
    private HttpClientInterface $client;

    public function __construct(string $apiKey, HttpClientInterface $client, ?\Symfony\Contracts\EventDispatcher\EventDispatcherInterface $dispatcher = null)
    {
        $this->apiKey = $apiKey;
        $this->client = $client;
        parent::__construct($client, $dispatcher);
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var Email $email */
        $email = $message->getOriginalMessage();

        // Prepare the payload for SendGrid
        $payload = [
            'personalizations' => [
                [
                    'to' => array_map(function ($address) {
                        return ['email' => $address->getAddress()];
                    }, $email->getTo()),
                    'subject' => $email->getSubject(),
                ]
            ],
            'from' => [
                'email' => $email->getFrom()[0]->getAddress()
            ],
            'content' => [
                [
                    'type'  => 'text/plain',
                    'value' => $email->getTextBody() ?? '',
                ]
            ]
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
                throw new TransportException('Unable to send email via SendGrid.');
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
