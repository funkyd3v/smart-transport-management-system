<?php

declare(strict_types=1);

namespace App\Modules\Communication\Factories;

use App\Modules\Communication\Contracts\CommunicationChannelInterface;
use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Communication\Exceptions\CommunicationException;

class CommunicationChannelFactory
{
    /**
     * @param  array<string, class-string<CommunicationChannelInterface>>  $channels
     */
    public function __construct(private readonly array $channels = []) {}

    public function make(CommunicationChannel $channel): CommunicationChannelInterface
    {
        $className = $this->channels[$channel->value] ?? null;

        if ($className === null) {
            throw new CommunicationException("Unsupported communication channel [{$channel->value}].");
        }

        $resolved = app($className);

        if (! $resolved instanceof CommunicationChannelInterface) {
            throw new CommunicationException("Invalid communication channel [{$channel->value}].");
        }

        return $resolved;
    }
}
