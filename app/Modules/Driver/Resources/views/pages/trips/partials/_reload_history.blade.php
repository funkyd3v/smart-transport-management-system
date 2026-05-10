<x-common.component-card title="Reload History" desc="Record reload stops during the trip.">
    <div class="flex items-center justify-between gap-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">Track every reload stop with amount and time.</p>
        <button type="button" @click="openReloadModal()" class="inline-flex items-center rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">Add Reload</button>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-white/[0.03]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Reloaded At</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Location</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Note</th>
                </tr>
            </thead>
            <tbody id="driver-reload-body" class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-transparent">
                @forelse ($trip->reloadHistory as $reload)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ optional($reload->reloaded_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $reload->reload_point ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">BDT {{ number_format((float) ($reload->reload_amount ?? 0), 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $reload->note_text ?? '-' }}</td>
                    </tr>
                @empty
                    <tr id="driver-reload-empty">
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No reload history recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-common.component-card>