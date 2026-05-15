<div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="w-full min-w-[1120px]">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">#</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Truck Number</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Truck Type</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Capacity (Tons)</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status Action</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody id="truck-table-body">
                @forelse ($trucks as $truck)
                    @php
                        $status = (string) ($truck->status ?? 'idle');
                        $isOnTrip = $status === 'on_trip';
                    @endphp
                    <tr id="truck-row-{{ $truck->id }}" class="border-b border-gray-100 dark:border-gray-800" x-data="{ status: '{{ $status }}' }">
                        <td class="px-5 py-4 text-theme-sm text-gray-700 dark:text-gray-300">{{ ($trucks->firstItem() ?? 0) + $loop->index }}</td>
                        <td class="px-5 py-4 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $truck->truck_number }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $truck->truck_type ?? '-' }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ number_format((float) ($truck->capacity ?? 0), 2) }}</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="
                                    status === 'idle' ? 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300' :
                                    (status === 'on_trip' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-300')
                                "
                                x-text="status.replace('_', ' ')"
                            ></span>
                        </td>
                        <td class="px-5 py-4">
                            @can('updateStatus', $truck)
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="window.updateTruckStatus({{ $truck->id }}, 'idle', {{ $isOnTrip ? 'true' : 'false' }}, (nextStatus) => { status = nextStatus; })"
                                        @disabled($isOnTrip)
                                        title="{{ $isOnTrip ? 'Truck is currently on a trip' : 'Set status to idle' }}"
                                        class="rounded-lg border px-3 py-1.5 text-xs {{ $isOnTrip ? 'cursor-not-allowed border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500' : 'border-green-200 text-green-700 hover:bg-green-50 dark:border-green-500/40 dark:text-green-300 dark:hover:bg-green-500/10' }}"
                                    >Idle</button>
                                    <button
                                        type="button"
                                        @click="window.updateTruckStatus({{ $truck->id }}, 'under_workshop', {{ $isOnTrip ? 'true' : 'false' }}, (nextStatus) => { status = nextStatus; })"
                                        @disabled($isOnTrip)
                                        title="{{ $isOnTrip ? 'Truck is currently on a trip' : 'Set status to under workshop' }}"
                                        class="rounded-lg border px-3 py-1.5 text-xs {{ $isOnTrip ? 'cursor-not-allowed border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500' : 'border-yellow-200 text-yellow-700 hover:bg-yellow-50 dark:border-yellow-500/40 dark:text-yellow-300 dark:hover:bg-yellow-500/10' }}"
                                    >Under Workshop</button>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        disabled
                                        title="You are not allowed to change status for this truck"
                                        class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500"
                                    >Idle</button>
                                    <button
                                        type="button"
                                        disabled
                                        title="You are not allowed to change status for this truck"
                                        class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500"
                                    >Under Workshop</button>
                                </div>
                            @endcan
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('manager.trucks.show', $truck) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">View</a>

                                @can('update', $truck)
                                    @if ($isOnTrip)
                                        <button type="button" disabled title="Truck is currently on a trip" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500">Edit</button>
                                    @else
                                        <a href="{{ route('manager.trucks.edit', $truck) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">Edit</a>
                                    @endif
                                @else
                                    <button type="button" disabled title="You are not allowed to edit this truck" class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500">Edit</button>
                                @endcan

                                @can('delete', $truck)
                                    <button
                                        type="button"
                                        @click="window.deleteTruck({{ $truck->id }}, {{ $isOnTrip ? 'true' : 'false' }})"
                                        title="{{ $isOnTrip ? 'Truck is currently on a trip' : 'Delete truck' }}"
                                        class="rounded-lg border px-3 py-1.5 text-xs {{ $isOnTrip ? 'cursor-not-allowed border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500' : 'border-red-200 text-red-500 hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10' }}"
                                        @disabled($isOnTrip)
                                    >
                                        Delete
                                    </button>
                                @else
                                    <button type="button" disabled title="You are not allowed to delete this truck" class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500">Delete</button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="truck-empty-row">
                        <td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No trucks found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $trucks->withQueryString()->links() }}</div>
