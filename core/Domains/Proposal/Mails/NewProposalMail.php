<?php

namespace Core\Domains\Proposal\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewProposalMail extends Mailable
{
    /**
     * @param array<int, Attachment> $propAttachments
     */
    public function __construct(
        private readonly string $toEmail,
        private readonly string $fromName,
        private readonly array  $propAttachments,
    )
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        return new Envelope(
            to: $this->toEmail,
            subject: sprintf('Новое предложение%s', $this->fromName ? sprintf(' от %s', $this->fromName) : ''),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: '<span></span>',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return $this->propAttachments;
    }
}
