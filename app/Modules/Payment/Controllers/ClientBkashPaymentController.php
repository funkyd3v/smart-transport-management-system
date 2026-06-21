<?php

declare(strict_types=1);

namespace App\Modules\Payment\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Requests\InitiateBkashPaymentRequest;
use App\Modules\Payment\Services\ClientBkashPaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ClientBkashPaymentController extends Controller
{
    public function __construct(private readonly ClientBkashPaymentService $service) {}

    public function index(Request $request): View
    {
        $dueRecords = $this->service->getPendingDueRecords($request->user());

        return view('payment::client.payments.index', [
            'dueRecords' => $dueRecords,
            'summary' => [
                'total_due' => $dueRecords->sum(fn ($record): float => (float) $record->remaining_due),
                'trip_count' => $dueRecords->count(),
            ],
            'paymentResult' => session('payment_result'),
        ]);
    }

    public function initiate(InitiateBkashPaymentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $result = $this->service->initiate(
                user: $request->user(),
                tripUlid: (string) $data['trip_ulid'],
                amount: (float) $data['amount'],
                note: isset($data['note']) ? (string) $data['note'] : null,
            );
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $throwable) {
            report($throwable);

            return back()->withInput()->withErrors([
                'payment' => 'Unable to start bKash payment right now. Please try again.',
            ]);
        }

        return redirect()->away((string) $result['redirect_url']);
    }

    public function callback(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'paymentID' => ['required', 'string', 'max:191'],
            'status' => ['nullable', 'string', 'max:40'],
        ]);

        try {
            $payment = $this->service->handleCallback(
                user: $request->user(),
                paymentId: (string) $validated['paymentID'],
                status: isset($validated['status']) ? (string) $validated['status'] : null,
                payload: $request->all(),
            );
        } catch (ValidationException $exception) {
            return redirect()->route('client.payments.index')->withErrors($exception->errors());
        } catch (\Throwable $throwable) {
            report($throwable);

            return redirect()->route('client.payments.index')->withErrors([
                'payment' => 'Payment verification failed due to an unexpected error. Please contact support.',
            ]);
        }

        return redirect()->route('client.payments.index')->with('payment_result', [
            'status' => (string) $payment->status,
            'message' => $this->resultMessage((string) $payment->status),
            'payment_ulid' => $payment->ulid,
            'reference' => $payment->provider_reference,
        ]);
    }

    private function resultMessage(string $status): string
    {
        return match (Str::lower($status)) {
            'succeeded' => 'Your bKash payment was completed successfully.',
            'cancelled' => 'Your bKash payment was cancelled before completion.',
            'failed' => 'Your bKash payment could not be completed.',
            default => 'Your payment is currently being processed.',
        };
    }
}
