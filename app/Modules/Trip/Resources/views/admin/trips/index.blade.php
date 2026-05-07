@extends('admin::layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Trips</h1>
    <form class="grid grid-cols-1 gap-3 md:grid-cols-5" method="GET">
        <select name="status_id" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700"><option value="">Status</option></select>
        <input name="date_from" type="date" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
        <input name="date_to" type="date" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
        <input name="client_id" placeholder="Client ID" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
        <input name="driver_id" placeholder="Driver ID" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    </form>
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
        <table class="w-full min-w-[980px]">
            <thead><tr class="border-b border-gray-200 dark:border-gray-800"><th class="px-4 py-2 text-left">Trip Code</th><th class="px-4 py-2 text-left">Client</th><th class="px-4 py-2 text-left">Driver</th><th class="px-4 py-2 text-left">Status</th><th class="px-4 py-2 text-left">Rate</th><th class="px-4 py-2 text-left">Actions</th></tr></thead>
            <tbody>
                @foreach($trips as $trip)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="px-4 py-2">{{ $trip->trip_code }}</td>
                        <td class="px-4 py-2">{{ $trip->client?->name }}</td>
                        <td class="px-4 py-2">{{ $trip->driver?->user?->name }}</td>
                        <td class="px-4 py-2"><span class="rounded px-2 py-1 text-xs bg-gray-100 dark:bg-gray-800">{{ $trip->status?->name }}</span></td>
                        <td class="px-4 py-2">{{ $trip->trip_rate }}</td>
                        <td class="px-4 py-2"><a href="{{ route('admin.trips.show', $trip->ulid) }}" class="text-brand-600">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $trips->links() }}
</div>
@endsection
