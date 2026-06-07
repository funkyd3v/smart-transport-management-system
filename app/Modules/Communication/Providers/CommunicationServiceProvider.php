<?php

declare(strict_types=1);

namespace App\Modules\Communication\Providers;

use App\Modules\Communication\Channels\Email\Providers\EmailChannelService;
use App\Modules\Communication\Channels\InApp\Services\InAppChannelService;
use App\Modules\Communication\Channels\Push\Providers\PushChannelService;
use App\Modules\Communication\Channels\SMS\Providers\BulkSmsBdProvider;
use App\Modules\Communication\Channels\SMS\Providers\TwilioSmsProvider;
use App\Modules\Communication\Channels\SMS\Services\SmsChannelService;
use App\Modules\Communication\Channels\WhatsApp\Providers\WhatsAppChannelService;
use App\Modules\Communication\Factories\CommunicationChannelFactory;
use App\Modules\Communication\Factories\CommunicationProviderFactory;
use App\Modules\Communication\Repositories\CommunicationRepository;
use App\Modules\Communication\Repositories\CommunicationRepositoryInterface;
use App\Modules\Communication\Support\RecipientValidator;
use App\Modules\Communication\Support\TemplateRenderer;
use Illuminate\Support\ServiceProvider;

class CommunicationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CommunicationRepositoryInterface::class, CommunicationRepository::class);

        $this->app->singleton(TemplateRenderer::class, fn (): TemplateRenderer => new TemplateRenderer());
        $this->app->singleton(RecipientValidator::class, fn (): RecipientValidator => new RecipientValidator());

        $this->app->singleton(CommunicationProviderFactory::class, function (): CommunicationProviderFactory {
            $configured = (array) config('communication.providers_map', []);

            $defaults = [
                'sms' => [
                    'twilio' => TwilioSmsProvider::class,
                    'bulksmsbd' => BulkSmsBdProvider::class,
                ],
                'whatsapp' => [],
                'email' => [],
                'push' => [],
                'in_app' => [],
            ];

            return new CommunicationProviderFactory(array_replace_recursive($defaults, $configured));
        });

        $this->app->singleton(CommunicationChannelFactory::class, function (): CommunicationChannelFactory {
            $configured = (array) config('communication.channels_map', []);

            $defaults = [
                'sms' => SmsChannelService::class,
                'whatsapp' => WhatsAppChannelService::class,
                'email' => EmailChannelService::class,
                'push' => PushChannelService::class,
                'in_app' => InAppChannelService::class,
            ];

            return new CommunicationChannelFactory(array_replace($defaults, $configured));
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
    }
}
