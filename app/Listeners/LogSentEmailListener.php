<?php declare(strict_types=1);

namespace App\Listeners;

use App\Models\Infra\SentEmail;
use Illuminate\Mail\Events\MessageSent;
use Symfony\Component\Mime\Address;

class LogSentEmailListener
{
    public function handle(MessageSent $event): void
    {
        $message = $event->message;

        // Получаем всех получателей (массив объектов Address)
        $recipients = $message->getTo();
        foreach ($recipients as $recipient) {
            if ( ! $recipient instanceof Address) {
                continue;
            }

            $email = $recipient->getAddress();
            $name  = $recipient->getName();

            $messageId   = $this->getMessageId($message);
            $subject     = $this->getSubject($message);
            $body        = $this->getEmailBody($message);
            $attachments = $this->getAttachments($message);
            $metadata    = $this->extractMetadata($message);

            SentEmail::create([
                SentEmail::MESSAGE_ID      => $messageId,
                SentEmail::RECIPIENT_EMAIL => $email,
                SentEmail::RECIPIENT_NAME  => $name,
                SentEmail::SUBJECT         => $subject,
                SentEmail::BODY            => $body,
                SentEmail::ATTACHMENTS     => $attachments,
                SentEmail::MAILER          => config('mail.default'),
                SentEmail::STATUS          => SentEmail::STATUS_SENT,
                SentEmail::METADATA        => $metadata,
                SentEmail::SENT_AT         => now(),
            ]);
        }
    }

    private function getMessageId($message): ?string
    {
        $messageId = $message->getHeaders()->get('Message-ID')?->getBody();
        if ($messageId) {
            return $messageId;
        }

        return $message->getHeaders()->get('X-Message-ID')?->getBody();
    }

    private function getSubject($message): ?string
    {
        return $message->getSubject();
    }

    private function getEmailBody($message): ?string
    {
        return $message->getHtmlBody() ?? $message->getTextBody();
    }

    private function getAttachments($message): array
    {
        $attachments = [];
        foreach ($message->getAttachments() as $attachment) {
            $attachments[] = [
                'filename'     => $attachment->getFilename(),
                'content_type' => $attachment->getContentType(),
                'size'         => strlen($attachment->getBody()),
            ];
        }

        return $attachments;
    }

    private function extractMetadata($message): array
    {
        $metadata      = [];
        $customHeaders = ['X-User-ID', 'X-Email-Type', 'X-Reference-ID'];
        foreach ($customHeaders as $header) {
            if ($value = $message->getHeaders()->get($header)?->getBody()) {
                $metadata[$header] = $value;
            }
        }

        return $metadata;
    }
}
