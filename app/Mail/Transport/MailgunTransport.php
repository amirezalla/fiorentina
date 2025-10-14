<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
     * @param string $apiKey   Mailgun private API key (starts with "key-...")
     * @param string $domain   Your Mailgun sending domain, e.g. "mg.example.com"
     * @param HttpClientInterface $client
     * @param EventDispatcherInterface|null $dispatcher
     * @param string $endpoint Mailgun API base (US: https://api.mailgun.net, EU: https://api.eu.mailgun.net)
     */
    public function __construct(
        string $apiKey,
        string $domain,
        HttpClientInterface $client,
        ?EventDispatcherInterface $dispatcher = null,
        string $endpoint = 'https://api/mailgun.net'
    ) {
        // little guard in case someone passes api.mailgun.net without scheme
        if (!str_starts_with($endpoint, 'http')) {
            $endpoint = 'https://' . ltrim($endpoint, '/');
        }

        $this->apiKey   = $apiKey;
        $this->domain   = $domain;
        $this->client   = $client;
        $this->endpoint = rtrim($endpoint, '/');

        parent::__construct($dispatcher);
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var Email $email */
        $email = $message->getOriginalMessage();

        // Basic validations
        $from = $email->getFrom()[0] ?? null;
        if (!$from) {
            throw new TransportException('MailgunTransport: Missing "From" address.');
        }

        $to = $email->getTo();
        if (empty($to)) {
            throw new TransportException('MailgunTransport: Missing "To" recipients.');
        }

        // Build multipart form fields (Mailgun expects multipart/form-data)
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
            $filename    = $part->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename')
                ?? $part->getFilename()
                ?? 'attachment';
            $disposition = $part->asInline() ? 'inline' : 'attachment';

            $files[] = [
                'name'     => $disposition,          // 'attachment' or 'inline'
                'filename' => $filename,
                'content'  => $part->getBody(),
                'headers'  => [
                    'Content-Type' => $part->getMediaType() . '/' . $part->getMediaSubtype(),
                ],
            ];

            // For inline attachments, Mailgun uses CID referenced in HTML like <img src="cid:...">
            if ($part->asInline() && $part->getContentId()) {
                // Mailgun auto-maps inline parts by Content-ID; nothing else required here.
                // If you want, you can capture them in $form['cid'][] = $part->getContentId();
            }
        }

        $url = "{$this->endpoint}/v3/{$this->domain}/messages";

        // Log outgoing request (with bodies redacted/limited)
        $safeForm = $form;
        if (isset($safeForm['html'])) {
            $safeForm['html'] = '[redacted:html]';
        }
        if (isset($safeForm['text'])) {
            $safeForm['text'] = Str::limit((string) $safeForm['text'], 400);
        }

        Log::debug('Mailgun request', [
            'url'   => $url,
            'form'  => $safeForm,
            'files' => array_map(fn($f) => ['name' => $f['name'], 'filename' => $f['filename']], $files),
        ]);

        try {
            $response = $this->client->request('POST', $url, [
                'auth_basic' => ['api', $this->apiKey],
                'body'       => $form,
                'files'      => $files, // Symfony HttpClient uses this for multipart parts
            ]);

            $status  = $response->getStatusCode();
            $bodyRaw = $response->getContent(false); // get body without throwing
            $headers = $response->getHeaders(false);

            Log::info('Mailgun response', [
                'status'  => $status,
                'headers' => $headers,
                'body'    => $bodyRaw,
            ]);

            if ($status >= 400) {
                throw new TransportException("Mailgun send failed ({$status}): " . $bodyRaw);
            }

            // expected success: {"id":"<...>","message":"Queued. Thank you."}
            $decoded = json_decode($bodyRaw, true);
            if (!is_array($decoded) || empty($decoded['id'])) {
                Log::warning('Mailgun success without id field', ['body' => $bodyRaw]);
            }
        } catch (\Throwable $e) {
            Log::error('Mailgun transport exception', [
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
            ]);
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
