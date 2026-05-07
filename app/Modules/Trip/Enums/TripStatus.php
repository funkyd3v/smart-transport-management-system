<?php

declare(strict_types=1);

namespace App\Modules\Trip\Enums;

enum TripStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case InTransit = 'in_transit';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Active => 'Active',
            self::InTransit => 'In Transit',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * @return list<self>
     */
    public function allowedNextStatuses(): array
    {
        return match ($this) {
            self::Pending => [self::Active, self::Cancelled],
            self::Active => [self::InTransit, self::Cancelled],
            self::InTransit => [self::Completed, self::Cancelled],
            self::Completed => [],
            self::Cancelled => [],
        };
    }
}
