<?php

namespace App\Mail;

use App\Models\ProgressReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProgressReportDelivered extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ProgressReport $report)
    {
    }

    public function envelope(): Envelope
    {
        $studentName = $this->report->student->full_name;
        $period = $this->report->period_label;

        return new Envelope(
            subject: "Monthly Progress Report - {$studentName} ({$period})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'elpisview.emails.progress-report-notification',
        );
    }
}
