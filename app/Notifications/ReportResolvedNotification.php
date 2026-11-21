<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportResolvedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly int $reportId,
        public readonly string $incidentId,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'kind' => 'report_resolved',
            'report_id' => $this->reportId,
            'incident_id' => $this->incidentId,
            'message' => "Your report {$this->incidentId} has been resolved. You can now share feedback in your Evidence Vault.",
            'url' => route('student.vault'),
        ];
    }
}
