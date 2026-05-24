<?php

declare(strict_types=1);

namespace App\Modules\Admin\Services;

final class AdminReportService
{
    public function reportTypes(): array
    {
        return [
            'daily-trip',
            'monthly-business',
            'income-expense',
            'client-due',
            'driver-performance',
            'spare-profit',
        ];
    }

    public function isValidReportType(string $type): bool
    {
        return in_array($type, $this->reportTypes(), true);
    }

    /**
     * @return array{title: string, rows: array<int, array{label: string, value: float}>}
     */
    public function previewPayload(string $type, array $stats): array
    {
        return match ($type) {
            'daily-trip' => [
                'title' => 'Daily Trip Report',
                'rows' => [
                    ['label' => 'Trip Income', 'value' => (float) ($stats['daily_trip_income'] ?? 0)],
                    ['label' => 'Trip Expenses', 'value' => (float) ($stats['daily_trip_expenses'] ?? 0)],
                    ['label' => 'Trip Profit', 'value' => (float) ($stats['daily_trip_profit'] ?? 0)],
                ],
            ],
            'monthly-business' => [
                'title' => 'Monthly Business Report',
                'rows' => [
                    ['label' => 'Total Income', 'value' => (float) ($stats['monthly_total_income'] ?? 0)],
                    ['label' => 'Total Expenses', 'value' => (float) ($stats['monthly_trip_expenses'] ?? 0)],
                    ['label' => 'Total Profit', 'value' => (float) ($stats['monthly_total_profit'] ?? 0)],
                ],
            ],
            'income-expense' => [
                'title' => 'Income vs Expense Report',
                'rows' => [
                    ['label' => 'Trip Income', 'value' => (float) ($stats['payments'] ?? 0)],
                    ['label' => 'Spare Sales Revenue', 'value' => (float) ($stats['spare_sales_revenue'] ?? 0)],
                    ['label' => 'Trip Expenses', 'value' => (float) ($stats['expenses'] ?? 0)],
                    ['label' => 'Net Profit', 'value' => (float) ($stats['total_profit'] ?? 0)],
                ],
            ],
            'client-due' => [
                'title' => 'Client Due Report',
                'rows' => [
                    ['label' => 'Outstanding Dues', 'value' => (float) ($stats['dues'] ?? 0)],
                ],
            ],
            'driver-performance' => [
                'title' => 'Driver Performance Summary',
                'rows' => [
                    ['label' => 'Total Trips', 'value' => (float) ($stats['trips'] ?? 0)],
                    ['label' => 'Monthly Trip Profit', 'value' => (float) ($stats['monthly_trip_profit'] ?? 0)],
                ],
            ],
            'spare-profit' => [
                'title' => 'Spare Profit Report',
                'rows' => [
                    ['label' => 'Spare Sales Revenue', 'value' => (float) ($stats['spare_sales_revenue'] ?? 0)],
                    ['label' => 'Spare Profit', 'value' => (float) ($stats['spare_profit'] ?? 0)],
                    ['label' => 'Daily Spare Profit', 'value' => (float) ($stats['daily_spare_profit'] ?? 0)],
                    ['label' => 'Monthly Spare Profit', 'value' => (float) ($stats['monthly_spare_profit'] ?? 0)],
                ],
            ],
            default => [
                'title' => 'Report',
                'rows' => [],
            ],
        };
    }

    public function csvPayload(string $type, array $stats): string
    {
        $preview = $this->previewPayload($type, $stats);
        $stream = fopen('php://temp', 'w+');

        if ($stream === false) {
            return '';
        }

        fputcsv($stream, ['Report Type', $preview['title']]);
        fputcsv($stream, ['Generated At', now()->toDateTimeString()]);
        fputcsv($stream, []);
        fputcsv($stream, ['Metric', 'Amount (BDT)']);

        foreach ($preview['rows'] as $row) {
            fputcsv($stream, [$row['label'], number_format($row['value'], 2, '.', '')]);
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return $csv === false ? '' : $csv;
    }
}
