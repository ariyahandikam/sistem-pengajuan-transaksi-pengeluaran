<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SubmissionApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Submission $submission, protected string $role)
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

        return (new MailMessage)
            ->from(
                env('MAIL_FROM_ADDRESS', config('mail.from.address')),
                env('MAIL_FROM_NAME', config('mail.from.name'))
            )
            ->subject("[$appName] Pengajuan Anda telah disetujui: {$submission->submission_number}")
            ->view('emails.submission_approved', [
                'submission' => $submission,
                'url' => $url,
                'appName' => $appName,
                'role' => $this->role,
            ]);
    }
}
