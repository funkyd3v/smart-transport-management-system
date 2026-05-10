@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Trip Management" />

    <div class="space-y-6">
        <x-common.component-card title="Trip List" desc="Track, filter, and control trip operations.">
            <form method="GET" action="{{ route('manager.trips.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-6">
                <select name="status_id" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" @selected((string) ($filters['status_id'] ?? '') === (string) $status->id)>{{ ucfirst(str_replace('_', ' ', $status->name)) }}</option>
                    @endforeach
                </select>
                <select name="client_id" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">All Clients</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected((string) ($filters['client_id'] ?? '') === (string) $client->id)>{{ $client->company_name ?? $client->user?->name ?? ('Client #'.$client->id) }}</option>
                    @endforeach
                </select>
                <select name="driver_id" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">All Drivers</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}" @selected((string) ($filters['driver_id'] ?? '') === (string) $driver->id)>{{ $driver->user?->name ?? ('Driver #'.$driver->id) }}</option>
                    @endforeach
                </select>
                <input name="date_from" value="{{ $filters['date_from'] ?? '' }}" type="date" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                <input name="date_to" value="{{ $filters['date_to'] ?? '' }}" type="date" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                <div class="flex items-center gap-2">
                    <button class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white">Filter</button>
                    <a href="{{ route('manager.trips.create') }}" class="rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white">Create</a>
                </div>
            </form>

            <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1100px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Trip</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Client</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Driver</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Route</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Rate</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Due</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($trips as $trip)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4 text-theme-sm text-gray-800 dark:text-white/90">{{ $trip->trip_code }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $trip->client?->company_name ?? $trip->client?->user?->name ?? '-' }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $trip->driver?->user?->name ?? '-' }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $trip->pickup_point }} → {{ $trip->delivery_point }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ number_format((float) $trip->trip_rate, 2) }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ number_format((float) $trip->due_amount, 2) }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', (string) $trip->status?->name)) }}</td>
                                    <td class="px-5 py-4"><a href="{{ route('manager.trips.show', $trip->ulid) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">View</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No trips found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $trips->withQueryString()->links() }}</div>
        </x-common.component-card>
    </div>
@endsection
