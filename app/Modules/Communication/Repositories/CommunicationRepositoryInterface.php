<?php

declare(strict_types=1);

namespace App\Modules\Communication\Repositories;

use App\Modules\Communication\Models\Communication;
use App\Modules\Communication\Models\CommunicationAttempt;
use App\Modules\Communication\Models\CommunicationLog;
use App\Modules\Communication\Models\CommunicationTemplate;
use App\Modules\Communication\Models\OtpCode;

interface CommunicationRepositoryInterface
{
    public function createCommunication(array $attributes): Communication;

    public function updateCommunication(Communication $communication, array $attributes): Communication;

    public function findCommunicationById(int $id): ?Communication;

    public function createAttempt(array $attributes): CommunicationAttempt;

    public function createLog(array $attributes): CommunicationLog;

    public function findTemplate(string $key, string $channel): ?CommunicationTemplate;

    public function findLatestActiveOtp(string $purpose, string $recipient): ?OtpCode;

    public function createOtp(array $attributes): OtpCode;

    public function updateOtp(OtpCode $otpCode, array $attributes): OtpCode;
}
