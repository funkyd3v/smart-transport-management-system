<?php

declare(strict_types=1);

namespace App\Modules\Communication\Channels\SMS\Providers;

use App\Modules\Communication\Channels\SMS\Contracts\SmsProviderInterface;
use App\Modules\Communication\DTOs\CommunicationDispatchResultDTO;
use App\Modules\Communication\Models\Communication;
use Illuminate\Support\Facades\Http;
use Throwable;

class TwilioSmsProvider implements SmsProviderInterface
{
    public function key(): string
    {
        return 'twilio';
    }

    public function send(Communication $communication): CommunicationDispatchResultDTO
    {
        $sid = (string) config('communication.providers.sms.twilio.account_sid');
        $token = (string) config('communication.providers.sms.twilio.auth_token');
        $from = (string) config('communication.providers.sms.twilio.from');

        if ($sid === '' || $token === '' || $from === '') {
            return new CommunicationDispatchResultDTO(
                success: false,
                providerMessageId: null,
                status: 'failed',
                message: 'Twilio credentials are not configured.',
            );
        }

        try {
            $response = Http::asForm()
                ->withBasicAuth($sid, $token)
                ->post('https://api.twilio.com/2010-04-01/Accounts/'.$sid.'/Messages.json', [
                    'From' => $from,
                    'To' => $communication->recipient,
                    'Body' => $communication->body,
                ]);

            $ok = $response->successful();
            $json = $response->json();

            return new CommunicationDispatchResultDTO(
                success: $ok,
                providerMessageId: is_array($json) ? ($json['sid'] ?? null) : null,
                status: $ok ? 'sent' : 'failed',
                message: $ok ? 'Message sent via Twilio.' : 'Twilio send failed.',
                rawResponse: is_array($json) ? $json : ['response' => $response->body()],
            );
        } catch (Throwable $e) {
            return new CommunicationDispatchResultDTO(
                success: false,
                providerMessageId: null,
                status: 'failed',
                message: 'Twilio send exception.',
                rawResponse: [
                    'error' => $e->getMessage(),
                ],
            );
        }
    }
}
