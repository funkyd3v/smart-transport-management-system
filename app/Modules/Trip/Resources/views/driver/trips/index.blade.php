@extends('driver::layouts.app')

@section('content')
<div class="space-y-4">
    <h1 class="text-2xl font-semibold">My Active Trips</h1>
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
        <table class="w-full">
            <thead><tr class="border-b"><th class="px-4 py-2 text-left">Trip Code</th><th class="px-4 py-2 text-left">Pickup</th><th class="px-4 py-2 text-left">Delivery</th><th class="px-4 py-2 text-left">Status</th><th class="px-4 py-2 text-left">Action</th></tr></thead>
            <tbody>@foreach($trips as $trip)<tr class="border-b border-gray-100 dark:border-gray-800"><td class="px-4 py-2">{{ $trip->trip_code }}</td><td class="px-4 py-2">{{ $trip->pickup_point }}</td><td class="px-4 py-2">{{ $trip->delivery_point }}</td><td class="px-4 py-2">{{ $trip->status?->name }}</td><td class="px-4 py-2"><a href="{{ route('driver.trips.show', $trip->ulid) }}" class="text-brand-600">Open</a></td></tr>@endforeach</tbody>
        </table>
    </div>
    {{ $trips->links() }}
</div>
@endsection
