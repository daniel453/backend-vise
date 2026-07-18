<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NationalBulletinMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $pdf  Bytes del PDF del boletín nacional.
     */
    public function __construct(
        public string $pdf,
        public ?string $recipientName = null,
        public ?string $dateLabel = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Boletín de Seguridad Nacional'.($this->dateLabel ? ' — '.$this->dateLabel : ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.national_bulletin',
            with: ['name' => $this->recipientName, 'dateLabel' => $this->dateLabel],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdf, 'boletin-nacional.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
