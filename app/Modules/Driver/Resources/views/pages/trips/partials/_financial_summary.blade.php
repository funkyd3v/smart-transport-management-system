<x-common.component-card title="Financial Summary" desc="Driver-logged expense summary for this trip.">
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="rounded-2xl bg-gray-50 p-4 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Trip Rate</p>
            <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white/90" x-text="formatCurrency(financialSummary.trip_rate)"></p>
        </div>
        <div class="rounded-2xl bg-gray-50 p-4 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Advance Received</p>
            <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white/90" x-text="formatCurrency(financialSummary.advance_received)"></p>
        </div>
        <div class="rounded-2xl bg-gray-50 p-4 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Expenses</p>
            <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white/90" x-text="formatCurrency(financialSummary.total_expenses)"></p>
        </div>
        <div class="rounded-2xl bg-gray-50 p-4 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Net</p>
            <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white/90" x-text="formatCurrency(financialSummary.net)"></p>
        </div>
    </div>
</x-common.component-card>