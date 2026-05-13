<?php

declare(strict_types=1);

namespace App\Modules\Admin\Services;

use App\Modules\Admin\Repositories\AdminOperationsRepositoryInterface;

final class AdminOperationsService
{
    public function __construct(private readonly AdminOperationsRepositoryInterface $repository) {}

    public function usersPageData(): array
    {
        return [
            'title' => 'Users',
            'stats' => $this->repository->usersStats(),
            'users' => $this->repository->usersPaginated(),
        ];
    }

    public function tripsPageData(): array
    {
        return [
            'title' => 'Trips',
            'stats' => $this->repository->tripsStats(),
            'trips' => $this->repository->tripsPaginated(),
        ];
    }

    public function driversPageData(): array
    {
        return [
            'title' => 'Drivers',
            'stats' => $this->repository->driversStats(),
            'drivers' => $this->repository->driversPaginated(),
        ];
    }

    public function trucksPageData(): array
    {
        return [
            'title' => 'Trucks',
            'stats' => $this->repository->trucksStats(),
            'trucks' => $this->repository->trucksPaginated(),
        ];
    }

    public function clientsPageData(): array
    {
        return [
            'title' => 'Clients',
            'stats' => $this->repository->clientsStats(),
            'clients' => $this->repository->clientsPaginated(),
        ];
    }

    public function financeOverviewData(): array
    {
        return [
            'title' => 'Finance Overview',
            'stats' => $this->repository->cashbookStats(),
        ];
    }

    public function financeDuesData(): array
    {
        return [
            'title' => 'Dues',
            'stats' => $this->repository->duesStats(),
            'dues' => $this->repository->duesPaginated(),
        ];
    }

    public function financeCashbookData(): array
    {
        return [
            'title' => 'Cashbook',
            'stats' => $this->repository->cashbookStats(),
            'rows' => $this->repository->cashbookPaginated(),
        ];
    }

    public function spareInventoryData(): array
    {
        return [
            'title' => 'Spare Parts Inventory',
            'stats' => $this->repository->sparePartsStats(),
            'parts' => $this->repository->sparePartsPaginated(),
        ];
    }

    public function spareSalesData(): array
    {
        return [
            'title' => 'Spare Sales',
            'stats' => $this->repository->spareSalesStats(),
            'sales' => $this->repository->spareSalesPaginated(),
        ];
    }

    public function reportsData(array $reportTypes): array
    {
        return [
            'title' => 'Reports',
            'stats' => $this->repository->reportStats(),
            'reportTypes' => $reportTypes,
        ];
    }

    public function auditLogsData(): array
    {
        return [
            'title' => 'Audit Logs',
            'logs' => $this->repository->auditLogsPaginated(),
        ];
    }

    public function settingsData(): array
    {
        return [
            'title' => 'Settings',
            'settings' => $this->repository->settingsSnapshot(),
        ];
    }
}
