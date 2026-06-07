<?php

declare(strict_types=1);

namespace App\Modules\Communication\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Communication\Actions\QueueCommunicationAction;
use App\Modules\Communication\DTOs\CommunicationRequestDTO;
use App\Modules\Communication\DTOs\OtpRequestDTO;
use App\Modules\Communication\DTOs\OtpVerificationDTO;
use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Communication\Enums\OtpPurpose;
use App\Modules\Communication\Http\Requests\GenerateOtpRequest;
use App\Modules\Communication\Http\Requests\SendCommunicationRequest;
use App\Modules\Communication\Http\Requests\VerifyOtpRequest;
use App\Modules\Communication\Services\OtpService;
use Illuminate\Http\JsonResponse;

class CommunicationController extends Controller
{
    public function __construct(
        private readonly QueueCommunicationAction $queueCommunication,
        private readonly OtpService $otpService,
    ) {}

    public function send(SendCommunicationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $communication = ($this->queueCommunication)(new CommunicationRequestDTO(
            channel: CommunicationChannel::from((string) $data['channel']),
            recipient: (string) $data['recipient'],
            subject: (string) ($data['subject'] ?? ''),
            body: (string) $data['body'],
            provider: $data['provider'] ?? null,
            templateKey: $data['template_key'] ?? null,
            templateData: (array) ($data['template_data'] ?? []),
            requestedBy: $request->user()?->id,
            referenceType: $data['reference_type'] ?? null,
            referenceId: $data['reference_id'] ?? null,
            scheduledAt: $data['scheduled_at'] ?? null,
            metadata: (array) ($data['metadata'] ?? []),
        ));

        return response()->json([
            'message' => 'Communication queued successfully.',
            'communication' => [
                'id' => $communication->id,
                'reference_no' => $communication->reference_no,
                'status' => $communication->status,
            ],
        ]);
    }

    public function generateOtp(GenerateOtpRequest $request): JsonResponse
    {
        $data = $request->validated();

        $otp = $this->otpService->generate(new OtpRequestDTO(
            purpose: OtpPurpose::from((string) $data['purpose']),
            recipient: (string) $data['recipient'],
            expiresInMinutes: (int) ($data['expires_in_minutes'] ?? 10),
            requestedBy: $request->user()?->id,
            referenceType: $data['reference_type'] ?? null,
            referenceId: $data['reference_id'] ?? null,
        ));

        return response()->json([
            'message' => 'OTP generated and queued.',
            'otp_reference' => $otp->reference_no,
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $data = $request->validated();

        $verified = $this->otpService->verify(new OtpVerificationDTO(
            purpose: OtpPurpose::from((string) $data['purpose']),
            recipient: (string) $data['recipient'],
            code: (string) $data['code'],
        ));

        return response()->json([
            'verified' => $verified,
        ], $verified ? 200 : 422);
    }
}
