<?php

declare(strict_types=1);

namespace App\Modules\Communication\Services;

use App\Modules\AuditLog\Services\AuditLogService;
use App\Modules\Communication\DTOs\CommunicationRequestDTO;
use App\Modules\Communication\DTOs\CommunicationSendResultDTO;
use App\Modules\Communication\Enums\CommunicationStatus;
use App\Modules\Communication\Events\MessageQueued;
use App\Modules\Communication\Exceptions\CommunicationException;
use App\Modules\Communication\Factories\CommunicationChannelFactory;
use App\Modules\Communication\Jobs\SendCommunicationJob;
use App\Modules\Communication\Models\Communication;
use App\Modules\Communication\Repositories\CommunicationRepositoryInterface;
use App\Modules\Communication\Support\RecipientValidator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CommunicationService
{
    public function __construct(
        private readonly CommunicationRepositoryInterface $repository,
        private readonly CommunicationTemplateService $templateService,
        private readonly CommunicationChannelFactory $channelFactory,
        private readonly RecipientValidator $recipientValidator,
        private readonly AuditLogService $auditLogService,
    ) {}

    public function queue(CommunicationRequestDTO $dto): Communication
    {
        if (! $this->recipientValidator->isValid($dto->channel, $dto->recipient)) {
            throw new CommunicationException('Invalid recipient for selected channel.');
        }

        $subject = $this->templateService->resolveSubject($dto->templateKey, $dto->channel, $dto->subject, $dto->templateData);
        $body = $this->templateService->resolveBody($dto->templateKey, $dto->channel, $dto->body, $dto->templateData);

        $communication = DB::transaction(function () use ($dto, $subject, $body): Communication {
            $communication = $this->repository->createCommunication([
                'ulid' => str()->ulid()->toBase32(),
                'reference_no' => (string) str()->upper(str()->random(12)),
                'reference_type' => $dto->referenceType,
                'reference_id' => $dto->referenceId,
                'channel' => $dto->channel->value,
                'provider' => $dto->provider,
                'recipient' => $dto->recipient,
                'subject' => $subject,
                'body' => $body,
                'status' => CommunicationStatus::Queued->value,
                'provider_message_id' => null,
                'template_key' => $dto->templateKey,
                'template_data' => $dto->templateData,
                'metadata' => $dto->metadata,
                'scheduled_at' => $dto->scheduledAt,
                'requested_by' => $dto->requestedBy,
                'sent_at' => null,
                'delivered_at' => null,
                'failed_at' => null,
                'failure_reason' => null,
            ]);

            $this->repository->createLog([
                'communication_id' => $communication->id,
                'event' => 'message_queued',
                'message' => 'Communication queued.',
                'context' => [
                    'channel' => $dto->channel->value,
                    'provider' => $dto->provider,
                ],
                'logged_at' => now(),
            ]);

            return $communication;
        });

        $this->auditLogService->log(
            userId: $dto->requestedBy,
            action: 'communication.queued',
            tableName: 'communications',
            recordId: $communication->id,
            oldValues: null,
            newValues: [
                'channel' => $communication->channel,
                'recipient' => $this->maskRecipient($communication->recipient),
                'status' => $communication->status,
            ],
        );

        $job = SendCommunicationJob::dispatch($communication->id);

        if ($dto->scheduledAt !== null && $dto->scheduledAt !== '') {
            $job->delay(Carbon::parse($dto->scheduledAt));
        }

        event(new MessageQueued($communication));

        return $communication;
    }

    public function markSending(int $communicationId): ?Communication
    {
        $communication = $this->repository->findCommunicationById($communicationId);

        if ($communication === null || $communication->status !== CommunicationStatus::Queued->value) {
            return null;
        }

        return $this->repository->updateCommunication($communication, [
            'status' => CommunicationStatus::Sending->value,
        ]);
    }

    public function sendNow(Communication $communication): CommunicationSendResultDTO
    {
        $channel = $this->channelFactory->make($communication->channelEnum());
        $dispatchResult = $channel->send($communication);

        $attemptNo = (int) $communication->attempts()->count() + 1;

        $this->repository->createAttempt([
            'communication_id' => $communication->id,
            'attempt_no' => $attemptNo,
            'provider' => $communication->provider,
            'status' => $dispatchResult->status,
            'provider_message_id' => $dispatchResult->providerMessageId,
            'response_payload' => $dispatchResult->rawResponse,
            'error_message' => $dispatchResult->success ? null : $dispatchResult->message,
            'attempted_at' => now(),
        ]);

        $communication = $this->repository->updateCommunication($communication, [
            'status' => $dispatchResult->success ? CommunicationStatus::Sent->value : CommunicationStatus::Failed->value,
            'provider_message_id' => $dispatchResult->providerMessageId,
            'sent_at' => $dispatchResult->success ? now() : null,
            'failed_at' => $dispatchResult->success ? null : now(),
            'failure_reason' => $dispatchResult->success ? null : $dispatchResult->message,
        ]);

        $this->repository->createLog([
            'communication_id' => $communication->id,
            'event' => $dispatchResult->success ? 'message_sent' : 'message_failed',
            'message' => $dispatchResult->message,
            'context' => [
                'provider_message_id' => $dispatchResult->providerMessageId,
            ],
            'logged_at' => now(),
        ]);

        $this->auditLogService->log(
            userId: $communication->requested_by,
            action: $dispatchResult->success ? 'communication.sent' : 'communication.failed',
            tableName: 'communications',
            recordId: $communication->id,
            oldValues: null,
            newValues: [
                'channel' => $communication->channel,
                'recipient' => $this->maskRecipient($communication->recipient),
                'status' => $communication->status,
            ],
        );

        return new CommunicationSendResultDTO($communication, $dispatchResult->success);
    }

    private function maskRecipient(string $recipient): string
    {
        if (str_contains($recipient, '@')) {
            [$name, $domain] = explode('@', $recipient, 2);

            return substr($name, 0, 2).'***@'.$domain;
        }

        return substr($recipient, 0, 3).'****'.substr($recipient, -2);
    }
}
