<?php

namespace App\Mail;

use App\Models\Payment;
use App\Services\PaymentReceiptService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment receipt '.$this->payment->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-receipt',
            with: ['payment' => $this->payment],
        );
    }

    public function attachments(): array
    {
        $receipts = app(PaymentReceiptService::class);

        return [
            Attachment::fromData(
                fn () => $receipts->pdf($this->payment),
                $receipts->filename($this->payment),
            )->withMime('application/pdf'),
        ];
    }
}
