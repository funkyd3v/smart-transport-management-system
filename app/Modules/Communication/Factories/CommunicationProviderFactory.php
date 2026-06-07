<?php

declare(strict_types=1);

namespace App\Modules\Communication\Factories;

use App\Modules\Communication\Contracts\CommunicationProviderInterface;
use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Communication\Exceptions\ProviderUnavailableException;

class CommunicationProviderFactory
{
    /**
     * @param  array<string, array<string, class-string<CommunicationProviderInterface>>>  $providers
     */
    public function __construct(private readonly array $providers = []) {}

    public function make(CommunicationChannel $channel, ?string $provider): CommunicationProviderInterface
    {
        $providerKey = strtolower((string) ($provider ?: config('communication.default_providers.'.$channel->value)));
        $className = $this->providers[$channel->value][$providerKey] ?? null;

        if ($className === null) {
            throw new ProviderUnavailableException("Provider [{$providerKey}] is not configured for [{$channel->value}].");
        }

        $resolved = app($className);

        if (! $resolved instanceof CommunicationProviderInterface) {
            throw new ProviderUnavailableException("Provider [{$providerKey}] is invalid for [{$channel->value}].");
        }

        return $resolved;
    }
}
