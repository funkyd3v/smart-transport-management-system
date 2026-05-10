<x-common.component-card title="Expenses" desc="Expenses recorded by you for this trip.">
    <div class="flex items-center justify-between gap-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">Add fuel, toll, and other trip expenses as you travel.</p>
        <button type="button" @click="openExpenseModal()" class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700">Add Expense</button>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-white/[0.03]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Description</th>
                </tr>
            </thead>
            <tbody id="driver-expenses-body" class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-transparent">
                @forelse ($trip->expenses as $expense)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ optional($expense->expense_date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $expense->category?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">BDT {{ number_format((float) $expense->amount, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $expense->description ?? '-' }}</td>
                    </tr>
                @empty
                    <tr id="driver-expenses-empty">
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No expenses recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-common.component-card>