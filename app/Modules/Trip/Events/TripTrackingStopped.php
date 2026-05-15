<?php

declare(strict_types=1);

namespace App\Modules\Trip\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripTrackingStopped implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public string $tripUlid,
        public string $message,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('trips.'.$this->tripUlid.'.tracking');
    }

    public function broadcastAs(): string
    {
        return 'trip.tracking.stopped';
    }

    /**
     * @return array<string, string>
     */
    public function broadcastWith(): array
    {
        return [
            'trip_ulid' => $this->tripUlid,
            'message' => $this->message,
        ];
    }
}
