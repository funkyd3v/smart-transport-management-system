<?php

declare(strict_types=1);

namespace App\Modules\Payment\Providers;

use App\Modules\Payment\Contracts\PaymentRepositoryInterface;
use App\Modules\Payment\Factories\PaymentGatewayFactory;
use App\Modules\Payment\Gateways\Bkash\BkashGateway;
use App\Modules\Payment\Gateways\Offline\OfflineGateway;
use App\Modules\Payment\Gateways\SSLCommerz\SSLCommerzGateway;
use App\Modules\Payment\Repositories\PaymentRepository;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);

        $this->app->singleton(PaymentGatewayFactory::class, function (): PaymentGatewayFactory {
            $configured = (array) config('payment.gateways', []);

            $gateways = [
                'offline' => OfflineGateway::class,
                'sslcommerz' => SSLCommerzGateway::class,
                'bkash' => BkashGateway::class,
                ...$configured,
            ];

            return new PaymentGatewayFactory($gateways);
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'payment');
    }
}
