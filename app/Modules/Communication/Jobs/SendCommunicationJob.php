<?php

declare(strict_types=1);

namespace App\Modules\Communication\Jobs;

use App\Modules\Communication\Events\MessageFailed;
use App\Modules\Communication\Events\MessageSending;
use App\Modules\Communication\Events\MessageSent;
use App\Modules\Communication\Services\CommunicationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCommunicationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function backoff(): array
    {
        return [10, 30, 120];
    }

    public function __construct(private readonly int $communicationId) {}

    public function handle(CommunicationService $communicationService): void
    {
        $communication = $communicationService->markSending($this->communicationId);

        if ($communication === null) {
            return;
        }

        event(new MessageSending($communication));

        $result = $communicationService->sendNow($communication);

        if ($result->success) {
            event(new MessageSent($result->communication));

            return;
        }

        event(new MessageFailed($result->communication));

        if ($this->attempts() < $this->tries) {
            throw new \RuntimeException('Communication send failed; retrying.');
        }
    }
}
