<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Expense\Models\ExpenseCategory;
use App\Modules\Payment\Models\PaymentMethod;
use App\Modules\Spare\Models\SpareCategory;
use App\Modules\Spare\Models\SpareSaleType;
use App\Modules\Trip\Enums\TripStatus as TripStatusEnum;
use App\Modules\Trip\Models\TripStatus;
use App\Modules\Truck\Models\TruckStatus;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTruckStatuses();
        $this->seedTripStatuses();
        $this->seedExpenseCategories();
        $this->seedPaymentMethods();
        $this->seedSpareCategories();
        $this->seedSpareSaleTypes();

        $this->call(ClientCategorySeeder::class);
    }

    private function seedTruckStatuses(): void
    {
        foreach (['Idle', 'On Trip', 'Under Workshop'] as $name) {
            TruckStatus::query()->firstOrCreate(['name' => $name]);
        }
    }

    private function seedTripStatuses(): void
    {
        $definitions = [
            TripStatusEnum::Created->value => 'Trip has been created and is awaiting dispatch.',
            TripStatusEnum::InProgress->value => 'Trip is currently active.',
            TripStatusEnum::Completed->value => 'Trip has been completed successfully.',
            TripStatusEnum::Cancelled->value => 'Trip has been cancelled.',
        ];

        foreach ($definitions as $name => $description) {
            TripStatus::query()->firstOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }
    }

    private function seedExpenseCategories(): void
    {
        $definitions = [
            'Fuel' => 'Fuel expenses for trip operations.',
            'Driver Allowance' => 'Daily allowances paid to drivers.',
            'Toll' => 'Road and bridge toll charges.',
            'Maintenance' => 'Repairs and maintenance during trips.',
            'Parking' => 'Truck parking charges.',
            'Other' => 'Any other miscellaneous expense.',
        ];

        foreach ($definitions as $name => $description) {
            ExpenseCategory::query()->firstOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }
    }

    private function seedPaymentMethods(): void
    {
        $definitions = [
            'Cash' => 'Cash payment.',
            'Bank Transfer' => 'Direct bank transfer payment.',
            'Cheque' => 'Payment by cheque.',
            'Mobile Banking' => 'Payment via mobile banking services.',
        ];

        foreach ($definitions as $name => $description) {
            PaymentMethod::query()->firstOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }
    }

    private function seedSpareCategories(): void
    {
        $definitions = [
            'Engine' => 'Engine related spare parts.',
            'Electrical' => 'Electrical system spare parts.',
            'Body' => 'Truck body and cabin spare parts.',
            'Tire' => 'Tires and tube related spare parts.',
            'Other' => 'Other uncategorized spare parts.',
        ];

        foreach ($definitions as $name => $description) {
            SpareCategory::query()->firstOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }
    }

    private function seedSpareSaleTypes(): void
    {
        $definitions = [
            'spare_part' => 'Physical spare part sale from inventory stock.',
            'security_solution' => 'Security product or solution sale.',
            'monthly_maintenance' => 'Recurring monthly maintenance contract sale.',
        ];

        foreach ($definitions as $name => $description) {
            SpareSaleType::query()->updateOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }
    }
}
