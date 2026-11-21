<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;

class AccountStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $status,
        public readonly ?string $remarks = null,
    ) {
        if (! in_array($status, ['approved', 'rejected'], true)) {
            throw new InvalidArgumentException('Account status notification must be approved or rejected.');
        }
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $message = $this->status === 'approved'
            ? 'Your SINAG account has been approved. You can now sign in.'
            : 'Your SINAG account registration was rejected. '.($this->remarks ?: 'Please contact the GAD Office for assistance.');

        return [
            'kind' => 'account_status',
            'status' => $this->status,
            'message' => $message,
            'remarks' => $this->remarks,
            'url' => $this->status === 'approved' ? route('home') : route('login'),
        ];
    }
}
