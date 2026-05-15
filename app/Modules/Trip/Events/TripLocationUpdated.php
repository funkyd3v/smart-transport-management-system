<?php

declare(strict_types=1);

namespace App\Modules\Trip\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripLocationUpdated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $location
     */
    public function __construct(
        public string $tripUlid,
        public array $location,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('trips.'.$this->tripUlid.'.tracking');
    }

    public function broadcastAs(): string
    {
        return 'trip.location.updated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'trip_ulid' => $this->tripUlid,
            'location' => $this->location,
        ];
    }
}
