<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

interface AdminOperationsRepositoryInterface
{
    public function usersPaginated(int $perPage = 15): LengthAwarePaginator;

    public function usersStats(): array;

    public function tripsPaginated(int $perPage = 15): LengthAwarePaginator;

    public function tripsStats(): array;

    public function driversPaginated(int $perPage = 15): LengthAwarePaginator;

    public function driversStats(): array;

    public function trucksPaginated(int $perPage = 15): LengthAwarePaginator;

    public function trucksStats(): array;

    public function clientsPaginated(int $perPage = 15): LengthAwarePaginator;

    public function clientsStats(): array;

    public function duesPaginated(int $perPage = 15): LengthAwarePaginator;

    public function duesStats(): array;

    public function cashbookPaginated(int $perPage = 15): LengthAwarePaginator;

    public function cashbookStats(): array;

    public function sparePartsPaginated(int $perPage = 15): LengthAwarePaginator;

    public function sparePartsStats(): array;

    public function spareSalesPaginated(int $perPage = 15): LengthAwarePaginator;

    public function spareSalesStats(): array;

    public function auditLogsPaginated(int $perPage = 20): LengthAwarePaginator;

    public function reportStats(): array;

    public function settingsSnapshot(): array;
}
