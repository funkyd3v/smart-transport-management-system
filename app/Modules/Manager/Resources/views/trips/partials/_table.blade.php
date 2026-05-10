<div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="w-full min-w-[1200px]">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Trip</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Client</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Driver</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Truck</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Route</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Rate</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Due</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trips as $trip)
                    @php
                        $statusName = strtolower((string) ($trip->status?->name ?? ''));
                        $badgeClass = match ($statusName) {
                            'created', 'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                            'in_progress', 'active', 'in_transit' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                            'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                            'cancelled', 'canceled' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300',
                        };
                    @endphp
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="px-5 py-4 text-theme-sm text-gray-800 dark:text-white/90">{{ $trip->trip_code }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $trip->client?->company_name ?? $trip->client?->user?->name ?? '-' }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $trip->driver?->name ?? $trip->driver?->user?->name ?? '-' }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $trip->truck?->truck_number ?? '-' }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $trip->pickup_point }} -> {{ $trip->delivery_point }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ number_format((float) $trip->trip_rate, 2) }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ number_format((float) $trip->due_amount, 2) }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', (string) $trip->status?->name)) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('manager.trips.show', $trip) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">View</a>
                                <a href="{{ route('manager.trips.invoice.show', $trip) }}" class="rounded-lg border border-indigo-200 px-3 py-1.5 text-xs text-indigo-700 dark:border-indigo-700 dark:text-indigo-300">Invoice</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No trips found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $trips->withQueryString()->links() }}</div>
