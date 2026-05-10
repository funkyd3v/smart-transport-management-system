@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Trip Management" />

    <div class="space-y-6">
        <x-common.component-card title="Trip List" desc="Track, filter, and control trip operations.">
            <form method="GET" action="{{ route('manager.trips.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-8">
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
                <select name="truck_id" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">All Trucks</option>
                    @foreach ($trucks as $truck)
                        <option value="{{ $truck->id }}" @selected((string) ($filters['truck_id'] ?? '') === (string) $truck->id)>{{ $truck->truck_number }}</option>
                    @endforeach
                </select>
                <input name="search" value="{{ $filters['search'] ?? '' }}" type="text" placeholder="Search code/client" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                <input name="date_from" value="{{ $filters['date_from'] ?? '' }}" type="date" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                <input name="date_to" value="{{ $filters['date_to'] ?? '' }}" type="date" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                <div class="flex items-center gap-2">
                    <button class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white">Filter</button>
                    <a href="{{ route('manager.trips.create') }}" class="rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white">Create</a>
                </div>
            </form>
            <div id="trip-list-table">
                @include('manager::trips.partials._table', ['trips' => $trips])
            </div>
        </x-common.component-card>
    </div>
@endsection
