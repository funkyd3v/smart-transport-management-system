<?php

declare(strict_types=1);

namespace App\Modules\Trip\DTOs;

use App\Modules\Trip\Http\Requests\CreateTripRequest;

readonly class CreateTripDTO
{
    /**
     * @param  array<int, array{item_name:string,unit:?string,quantity:numeric-string|int|float,unit_price:numeric-string|int|float}>  $goods
     */
    public function __construct(
        public int $clientId,
        public int $truckId,
        public int $driverId,
        public int $createdBy,
        public int $statusId,
        public string $pickupPoint,
        public string $deliveryPoint,
        public ?string $routeDescription,
        public ?string $goodsDescription,
        public string $loadDate,
        public ?string $expectedDeliveryDate,
        public float $tripRate,
        public float $advancePayment,
        public ?string $notes,
        public ?string $smsNote,
        public array $goods,
    ) {}

    public static function fromRequest(CreateTripRequest $request): self
    {
        $data = $request->validated();

        return new self(
            clientId: (int) $data['client_id'],
            truckId: (int) $data['truck_id'],
            driverId: (int) $data['driver_id'],
            createdBy: (int) $request->user()->id,
            statusId: (int) $data['status_id'],
            pickupPoint: (string) $data['pickup_point'],
            deliveryPoint: (string) $data['delivery_point'],
            routeDescription: $data['route_description'] ?? null,
            goodsDescription: $data['goods_description'] ?? null,
            loadDate: (string) $data['load_date'],
            expectedDeliveryDate: $data['expected_delivery_date'] ?? null,
            tripRate: (float) $data['trip_rate'],
            advancePayment: (float) ($data['advance_payment'] ?? 0),
            notes: $data['notes'] ?? null,
            smsNote: $data['sms_note'] ?? null,
            goods: $data['goods'],
        );
    }
}
