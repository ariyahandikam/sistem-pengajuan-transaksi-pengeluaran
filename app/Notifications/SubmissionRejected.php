<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubmissionRejected extends Notification
{
    use Queueable;

    public function __construct(
        protected Submission $submission,
        protected string $rejectedBy,
        protected ?string $notes = null
    )
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $submission = $this->submission;
        $appName = config('app.name');
        $url = route('submissions.show', $submission->id);

        \Log::info('SubmissionRejected::toMail', [
            'submission_id' => $submission->id,
            'recipient_email' => $notifiable->email,
            'recipient_name' => $notifiable->name,
            'rejected_by' => $this->rejectedBy,
        ]);

        return (new MailMessage)
            ->from(
                env('MAIL_FROM_ADDRESS', config('mail.from.address')),
                env('MAIL_FROM_NAME', config('mail.from.name'))
            )
            ->subject("[$appName] Pengajuan ditolak: {$submission->submission_number}")
            ->view('emails.submission_rejected', [
                'submission' => $submission,
                'url' => $url,
                'appName' => $appName,
                'rejectedBy' => $this->rejectedBy,
                'notes' => $this->notes,
            ]);
    }
}
