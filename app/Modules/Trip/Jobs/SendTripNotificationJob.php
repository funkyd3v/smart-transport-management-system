<?php

declare(strict_types=1);

namespace App\Modules\Trip\Jobs;

use App\Modules\Trip\Enums\NotificationType;
use App\Modules\Trip\Models\TripNotification;
use App\Modules\Trip\Notifications\SmsNotificationStrategy;
use App\Modules\Trip\Notifications\WhatsAppNotificationStrategy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTripNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $notificationId) {}

    public function handle(SmsNotificationStrategy $sms, WhatsAppNotificationStrategy $whatsapp): void
    {
        $notification = TripNotification::query()->find($this->notificationId);

        if ($notification === null || $notification->status !== 'pending') {
            return;
        }

        $isSent = match (NotificationType::from($notification->type)) {
            NotificationType::Sms => $sms->send($notification),
            NotificationType::WhatsApp => $whatsapp->send($notification),
            NotificationType::System => true,
        };

        $notification->status = $isSent ? 'sent' : 'failed';
        $notification->sent_at = $isSent ? now() : null;
        $notification->save();
    }
}
