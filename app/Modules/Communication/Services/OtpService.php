<?php

declare(strict_types=1);

namespace App\Modules\Communication\Services;

use App\Modules\Communication\Actions\QueueCommunicationAction;
use App\Modules\Communication\DTOs\CommunicationRequestDTO;
use App\Modules\Communication\DTOs\OtpRequestDTO;
use App\Modules\Communication\DTOs\OtpVerificationDTO;
use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Communication\Events\OtpGenerated;
use App\Modules\Communication\Events\OtpVerified;
use App\Modules\Communication\Exceptions\CommunicationException;
use App\Modules\Communication\Models\OtpCode;
use App\Modules\Communication\Repositories\CommunicationRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    public function __construct(
        private readonly CommunicationRepositoryInterface $repository,
        private readonly QueueCommunicationAction $queueCommunication,
    ) {}

    public function generate(OtpRequestDTO $dto): OtpCode
    {
        $rateKey = sprintf('otp:generate:%s:%s', $dto->purpose->value, $dto->recipient);
        $attempts = (int) Cache::get($rateKey, 0);

        if ($attempts >= (int) config('communication.otp.max_generate_per_window', 5)) {
            throw new CommunicationException('OTP generation limit exceeded. Please try later.');
        }

        Cache::put($rateKey, $attempts + 1, now()->addMinutes((int) config('communication.otp.generate_window_minutes', 10)));

        $code = (string) random_int(100000, 999999);

        $otpCode = $this->repository->createOtp([
            'ulid' => str()->ulid()->toBase32(),
            'reference_no' => (string) str()->upper(str()->random(10)),
            'purpose' => $dto->purpose->value,
            'recipient' => $dto->recipient,
            'channel' => CommunicationChannel::Sms->value,
            'provider' => config('communication.default_providers.sms'),
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'max_attempts' => (int) config('communication.otp.max_verify_attempts', 5),
            'expires_at' => now()->addMinutes($dto->expiresInMinutes),
            'requested_by' => $dto->requestedBy,
            'reference_type' => $dto->referenceType,
            'reference_id' => $dto->referenceId,
            'metadata' => ['masked_code' => '**'.substr($code, -2)],
        ]);

        ($this->queueCommunication)(new CommunicationRequestDTO(
            channel: CommunicationChannel::Sms,
            recipient: $dto->recipient,
            subject: 'OTP Verification',
            body: 'Your OTP is '.$code,
            provider: (string) config('communication.default_providers.sms'),
            templateKey: 'otp.generic',
            templateData: [
                'otp_code' => $code,
                'purpose' => $dto->purpose->value,
            ],
            requestedBy: $dto->requestedBy,
            referenceType: OtpCode::class,
            referenceId: (string) $otpCode->id,
            scheduledAt: null,
            metadata: ['otp' => true],
        ));

        event(new OtpGenerated($otpCode));

        return $otpCode;
    }

    public function verify(OtpVerificationDTO $dto): bool
    {
        $otpCode = $this->repository->findLatestActiveOtp($dto->purpose->value, $dto->recipient);

        if ($otpCode === null) {
            return false;
        }

        if ((int) $otpCode->attempts >= (int) $otpCode->max_attempts) {
            return false;
        }

        $matched = Hash::check($dto->code, (string) $otpCode->code_hash);

        $this->repository->updateOtp($otpCode, [
            'attempts' => (int) $otpCode->attempts + 1,
            'verified_at' => $matched ? now() : null,
        ]);

        if ($matched) {
            event(new OtpVerified($otpCode->fresh()));
        }

        return $matched;
    }
}
