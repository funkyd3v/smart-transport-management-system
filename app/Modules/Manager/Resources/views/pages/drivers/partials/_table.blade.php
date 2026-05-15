<div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="w-full min-w-[1200px]">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">#</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Driver</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Mobile</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Type</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Approval</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Joining</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody id="driver-table-body">
                @forelse ($drivers as $driver)
                    <tr id="driver-row-{{ $driver->id }}" class="border-b border-gray-100 dark:border-gray-800" x-data="{ status: '{{ $driver->status }}', isApproved: {{ $driver->is_approved ? 'true' : 'false' }} }">
                        <td class="px-5 py-4 text-theme-sm text-gray-700 dark:text-gray-300">{{ ($drivers->firstItem() ?? 0) + $loop->index }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $driver->avatar_url }}" alt="Driver Avatar" class="h-10 w-10 rounded-full object-cover">
                                <span class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $driver->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $driver->mobile_number ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <span
                                @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-medium',
                                    'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300' => $driver->driving_type === 'permanent',
                                    'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300' => $driver->driving_type === 'backup',
                                ])
                            >
                                {{ ucfirst((string) $driver->driving_type) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                                :class="status === 'active'
                                    ? 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300'
                                    : 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300'"
                                x-text="status"
                            ></span>
                        </td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="isApproved
                                    ? 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300'
                                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-300'"
                                x-text="isApproved ? 'Approved' : 'Pending'"
                            ></span>
                        </td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ optional($driver->joining_date)->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('manager.drivers.show', $driver) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">View</a>
                                @can('update', $driver)
                                    <a href="{{ route('manager.drivers.edit', $driver) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">Edit</a>
                                @else
                                    <button type="button" disabled title="You are not allowed to edit this driver" class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500">Edit</button>
                                @endcan

                                @can('toggleStatus', $driver)
                                    <button
                                        type="button"
                                        @click="window.toggleDriverStatus({{ $driver->id }}, (nextStatus) => { status = nextStatus; })"
                                        class="rounded-lg border border-brand-200 px-3 py-1.5 text-xs text-brand-600 hover:bg-brand-50 dark:border-brand-500/40 dark:text-brand-300 dark:hover:bg-brand-500/10"
                                    >
                                        Toggle Status
                                    </button>
                                @else
                                    <button type="button" disabled title="You are not allowed to change status for this driver" class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500">Toggle Status</button>
                                @endcan

                                @can('toggleApproval', $driver)
                                    <button
                                        type="button"
                                        @click="window.toggleDriverApproval({{ $driver->id }}, isApproved, (nextValue) => { isApproved = nextValue; })"
                                        class="rounded-lg border border-indigo-200 px-3 py-1.5 text-xs text-indigo-600 hover:bg-indigo-50 dark:border-indigo-500/40 dark:text-indigo-300 dark:hover:bg-indigo-500/10"
                                    >
                                        Toggle Approval
                                    </button>
                                @else
                                    <button type="button" disabled title="You are not allowed to change approval for this driver" class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500">Toggle Approval</button>
                                @endcan

                                @can('delete', $driver)
                                    <button
                                        type="button"
                                        @click="window.deleteDriver({{ $driver->id }})"
                                        class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-500 hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10"
                                    >
                                        Delete
                                    </button>
                                @else
                                    <button type="button" disabled title="You are not allowed to delete this driver" class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500">Delete</button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="driver-empty-row">
                        <td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No drivers found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
