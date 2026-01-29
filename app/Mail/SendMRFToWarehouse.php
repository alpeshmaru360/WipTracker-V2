<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;

class SendMRFToWarehouse extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;
    public $excelFilePath;

    /**
     * Create a new message instance.
     */
    public function __construct($emailData, $excelFilePath)
    {
        $this->emailData = $emailData;
        $this->excelFilePath = $excelFilePath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Request for MRF: ' . 'Project no: ' . $this->emailData['project_no'] . ' | ' . 'Project Name: ' . $this->emailData['project_name'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.mrf_to_warehouse',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->excelFilePath)
                ->as('MRF_' . $this->emailData['project_no'] . '_' . $this->emailData['full_article_number'] . '.xlsx')
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}
