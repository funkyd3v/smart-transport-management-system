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
        $timeout = (int) config('communication.providers.sms.twilio.timeout', 10);
        $connectTimeout = (int) config('communication.providers.sms.twilio.connect_timeout', 5);
        $retryTimes = (int) config('communication.providers.sms.twilio.retry_times', 2);
        $retrySleepMs = (int) config('communication.providers.sms.twilio.retry_sleep_ms', 250);

        if ($sid === '' || $token === '' || $from === '') {
            return new CommunicationDispatchResultDTO(
                false,
                $this->key(),
                null,
                'failed',
                'config_missing',
                'Twilio credentials are not configured.',
            );
        }

        try {
            $response = Http::asForm()
                ->timeout($timeout)
                ->connectTimeout($connectTimeout)
                ->retry($retryTimes, $retrySleepMs, throw: false)
                ->withBasicAuth($sid, $token)
                ->post('https://api.twilio.com/2010-04-01/Accounts/'.$sid.'/Messages.json', [
                    'From' => $from,
                    'To' => $communication->recipient,
                    'Body' => $communication->body,
                ]);

            $ok = $response->successful();
            $json = $response->json();

            return new CommunicationDispatchResultDTO(
                $ok,
                $this->key(),
                is_array($json) ? ($json['sid'] ?? null) : null,
                $ok ? 'sent' : 'failed',
                (string) $response->status(),
                $ok ? 'Message sent via Twilio.' : 'Twilio send failed.',
                is_array($json) ? $json : ['response' => $response->body()],
            );
        } catch (Throwable $e) {
            return new CommunicationDispatchResultDTO(
                false,
                $this->key(),
                null,
                'failed',
                'exception',
                'Twilio send exception.',
                [
                    'error' => $e->getMessage(),
                ],
            );
        }
    }
}
