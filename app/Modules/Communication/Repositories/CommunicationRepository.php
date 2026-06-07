<?php

declare(strict_types=1);

namespace App\Modules\Communication\Repositories;

use App\Modules\Communication\Models\Communication;
use App\Modules\Communication\Models\CommunicationAttempt;
use App\Modules\Communication\Models\CommunicationLog;
use App\Modules\Communication\Models\CommunicationTemplate;
use App\Modules\Communication\Models\OtpCode;
use App\Modules\Communication\Repositories\CommunicationRepositoryInterface;

class CommunicationRepository implements CommunicationRepositoryInterface
{
    public function createCommunication(array $attributes): Communication
    {
        return Communication::query()->create($attributes);
    }

    public function updateCommunication(Communication $communication, array $attributes): Communication
    {
        $communication->fill($attributes)->save();

        return $communication->fresh();
    }

    public function findCommunicationById(int $id): ?Communication
    {
        return Communication::query()->find($id);
    }

    public function createAttempt(array $attributes): CommunicationAttempt
    {
        return CommunicationAttempt::query()->create($attributes);
    }

    public function createLog(array $attributes): CommunicationLog
    {
        return CommunicationLog::query()->create($attributes);
    }

    public function findTemplate(string $key, string $channel): ?CommunicationTemplate
    {
        return CommunicationTemplate::query()
            ->where('key', $key)
            ->where('channel', $channel)
            ->where('is_active', true)
            ->first();
    }

    public function findLatestActiveOtp(string $purpose, string $recipient): ?OtpCode
    {
        return OtpCode::query()
            ->where('purpose', $purpose)
            ->where('recipient', $recipient)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();
    }

    public function createOtp(array $attributes): OtpCode
    {
        return OtpCode::query()->create($attributes);
    }

    public function updateOtp(OtpCode $otpCode, array $attributes): OtpCode
    {
        $otpCode->fill($attributes)->save();

        return $otpCode->fresh();
    }
}
