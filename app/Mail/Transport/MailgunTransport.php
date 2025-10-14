<?php

namespace App\Mail\Transport;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MailgunTransport extends AbstractTransport
{
    private string $apiKey;
    private string $domain;
    private string $endpoint;
    private HttpClientInterface $client;

    /**
     * @param string $apiKey   Mailgun private API key (starting with "key-...")
     * @param string $domain   Mailgun sending domain, e.g. "mg.example.com"
     * @param HttpClientInterface $client
     * @param EventDispatcherInterface|null $dispatcher
     * @param string $endpoint Mailgun API base (US: api.mailgun.net, EU: api.eu.mailgun.net)
     */
    public function __construct(
        string $apiKey,
        string $domain,
        HttpClientInterface $client,
        ?EventDispatcherInterface $dispatcher = null,
        string $endpoint = 'https://api.mailgun.net'
    ) {
        $this->apiKey  = $apiKey;
        $this->domain  = $domain;
        $this->client  = $client;
        $this->endpoint = rtrim($endpoint, '/');
        parent::__construct($dispatcher);
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var Email $email */
        $email = $message->getOriginalMessage();

        // Required fields
        $from = $email->getFrom()[0] ?? null;
        if (!$from) {
            throw new TransportException('MailgunTransport: Missing "From" address.');
        }

        $to   = $email->getTo();
        if (empty($to)) {
            throw new TransportException('MailgunTransport: Missing "To" recipients.');
        }

        // Build form-data payload (Mailgun uses multipart/form-data)
        $form = [
            'from'    => $this->formatAddress($from),
            'to'      => array_map(fn($a) => $this->formatAddress($a), $to),
            'subject' => (string) $email->getSubject(),
        ];

        // Optional: cc / bcc / reply-to
        if ($cc = $email->getCc()) {
            $form['cc'] = array_map(fn($a) => $this->formatAddress($a), $cc);
        }
        if ($bcc = $email->getBcc()) {
            $form['bcc'] = array_map(fn($a) => $this->formatAddress($a), $bcc);
        }
        if ($replyTo = $email->getReplyTo()) {
            // Mailgun supports a single Reply-To header string
            $form['h:Reply-To'] = $this->formatAddress($replyTo[0]);
        }

        // Bodies
        $html = $email->getHtmlBody();
        $text = $email->getTextBody();

        if ($html) {
            $form['html'] = $html;
        }
        if ($text) {
            $form['text'] = $text;
        }

        if (!$html && !$text) {
            $form['text'] = '';
        }

        // Attachments (inline & regular)
        $files = [];
        foreach ($email->getAttachments() as $part) {
            /** @var DataPart $part */
            $filename = $part->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename') ?? $part->getFilename() ?? 'attachment';
            $disposition = $part->asInline() ? 'inline' : 'attachment';

            $files[] = [
                'name'     => $disposition,          // 'attachment' OR 'inline'
                'filename' => $filename,
                'content'  => $part->getBody(),
                'headers'  => [
                    'Content-Type' => $part->getMediaType() . '/' . $part->getMediaSubtype(),
                ],
            ];

            // For inline attachments, Mailgun needs the CID to match the Content-ID
            if ($part->asInline() && $part->getContentId()) {
                $form['cid'][] = $part->getContentId();
            }
        }

        // Endpoint
        $url = "{$this->endpoint}/v3/{$this->domain}/messages";

        try {
            $response = $this->client->request('POST', $url, [
                'auth_basic' => ['api', $this->apiKey],
                'body'       => $form,
                'files'      => $files, // Symfony HttpClient supports 'files' for multipart
            ]);

            if ($response->getStatusCode() >= 400) {
                $payload = $response->getContent(false);
                throw new TransportException("Mailgun send failed ({$response->getStatusCode()}): " . $payload);
            }
        } catch (\Throwable $e) {
            throw new TransportException('Error sending via Mailgun: ' . $e->getMessage(), 0, $e);
        }
    }

    public function __toString(): string
    {
        return 'mailgun';
    }

    private function formatAddress($address): string
    {
        $email = $address->getAddress();
        $name  = trim((string) $address->getName());
        return $name ? sprintf('"%s" <%s>', $name, $email) : $email;
    }
}
