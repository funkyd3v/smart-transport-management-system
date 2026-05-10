<x-common.component-card title="Goods" desc="Read-only goods details for this trip.">
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-white/[0.03]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Unit</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Quantity</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Unit Price</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-transparent">
                @forelse ($trip->goods as $goods)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $goods->item_name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $goods->unit ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ number_format((float) $goods->quantity, 3) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">BDT {{ number_format((float) $goods->unit_price, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">BDT {{ number_format((float) $goods->total_price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No goods items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-common.component-card>