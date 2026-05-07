@extends('driver::layouts.app')

@section('content')
<div class="space-y-4">
    <h1 class="text-2xl font-semibold">Trip {{ $trip->trip_code }}</h1>
    <p>Pickup: {{ $trip->pickup_point }}</p>
    <p>Delivery: {{ $trip->delivery_point }}</p>
    <p>Status: {{ $trip->status?->name }}</p>

    <form method="POST" action="{{ route('driver.trips.status.update') }}" class="space-y-2">
        @csrf
        <input type="hidden" name="trip_ulid" value="{{ $trip->ulid }}" />
        <select name="status" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700"><option>active</option><option>in_transit</option><option>completed</option><option>cancelled</option></select>
        <button class="rounded bg-brand-500 px-4 py-2 text-white">Update Status</button>
    </form>

    <form method="POST" action="{{ route('driver.trips.reload.store', $trip->ulid) }}" class="space-y-2">
        @csrf
        <input name="reload_point" placeholder="Reload point" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
        <textarea name="note" placeholder="Note" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-700"></textarea>
        <button class="rounded bg-blue-600 px-4 py-2 text-white">Add Reload History</button>
    </form>
</div>
@endsection
