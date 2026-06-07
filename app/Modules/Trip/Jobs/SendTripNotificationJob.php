<?php

declare(strict_types=1);

namespace App\Modules\Trip\Jobs;

use App\Modules\Communication\Actions\QueueCommunicationAction;
use App\Modules\Communication\DTOs\CommunicationRequestDTO;
use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Trip\Models\TripNotification;
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

    public function handle(QueueCommunicationAction $queueCommunication): void
    {
        $notification = TripNotification::query()->find($this->notificationId);

        if ($notification === null || $notification->status !== 'pending') {
            return;
        }

        $channel = match ($notification->type) {
            'sms' => CommunicationChannel::Sms,
            'whatsapp' => CommunicationChannel::WhatsApp,
            default => CommunicationChannel::InApp,
        };

        $recipient = $notification->recipient_phone;

        if ($channel === CommunicationChannel::InApp) {
            $recipient = (string) $notification->user_id;
        }

        try {
            ($queueCommunication)(new CommunicationRequestDTO(
                channel: $channel,
                recipient: (string) $recipient,
                subject: 'Trip Notification',
                body: (string) $notification->message,
                provider: null,
                templateKey: null,
                templateData: [
                    'trip_id' => $notification->trip_id,
                ],
                requestedBy: (int) $notification->user_id,
                referenceType: TripNotification::class,
                referenceId: (string) $notification->id,
                scheduledAt: null,
                metadata: [
                    'legacy_trip_notification_ulid' => $notification->ulid,
                    'channel' => $notification->channel,
                ],
            ));

            $notification->status = 'sent';
            $notification->sent_at = now();
            $notification->save();
        } catch (\Throwable) {
            $notification->status = 'failed';
            $notification->sent_at = null;
            $notification->save();
        }
    }
}
