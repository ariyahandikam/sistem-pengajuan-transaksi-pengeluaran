<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SubmissionForApproval extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Submission $submission, protected string $targetRole)
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
        $url = route('approvals.show', $submission->id);

        \Log::info('SubmissionForApproval::toMail', [
            'submission_id' => $submission->id,
            'recipient_email' => $notifiable->email,
            'recipient_name' => $notifiable->name,
            'role' => $this->targetRole,
        ]);

        return (new MailMessage)
            ->from(
                env('MAIL_FROM_ADDRESS', config('mail.from.address')),
                env('MAIL_FROM_NAME', config('mail.from.name'))
            )
            ->subject("[$appName] Pengajuan membutuhkan approval: {$submission->submission_number}")
            ->view('emails.submission_for_approval', [
                'submission' => $submission,
                'url' => $url,
                'appName' => $appName,
                'targetRole' => $this->targetRole,
            ]);
    }
}
