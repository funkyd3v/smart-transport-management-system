<div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="w-full min-w-[1080px]">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">#</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Name</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Contact</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Type</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Project</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Created</th>
                    <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody id="client-table-body">
                @forelse ($clients as $client)
                    <tr id="client-row-{{ $client->id }}" class="border-b border-gray-100 dark:border-gray-800" x-data="{ status: '{{ $client->status }}' }">
                        <td class="px-5 py-4 text-theme-sm text-gray-700 dark:text-gray-300">{{ ($clients->firstItem() ?? 0) + $loop->index }}</td>
                        <td class="px-5 py-4 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $client->name ?? '-' }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $client->contact_number ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="{
                                    'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300': '{{ $client->client_type }}' === 'port',
                                    'bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300': '{{ $client->client_type }}' === 'contractual',
                                    'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300': '{{ $client->client_type }}' === 'mega_project'
                                }"
                            >
                                {{ str_replace('_', ' ', ucfirst((string) $client->client_type)) }}
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
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $client->project ?? '-' }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ optional($client->created_at)->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('manager.clients.show', $client) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">View</a>
                                <a href="{{ route('manager.clients.edit', $client) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">Edit</a>
                                <button
                                    type="button"
                                    @click="window.toggleClientStatus({{ $client->id }}, (nextStatus) => { status = nextStatus; })"
                                    class="rounded-lg border border-brand-200 px-3 py-1.5 text-xs text-brand-600 hover:bg-brand-50 dark:border-brand-500/40 dark:text-brand-300 dark:hover:bg-brand-500/10"
                                >
                                    Toggle Status
                                </button>
                                <button
                                    type="button"
                                    @click="window.deleteClient({{ $client->id }})"
                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-500 hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="client-empty-row">
                        <td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No clients found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
