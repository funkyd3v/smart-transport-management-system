@extends('admin::layouts.app')

@section('content')
<form method="POST" action="{{ route('admin.trips.payments.store') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="trip_ulid" value="{{ $trip->ulid }}" />
    <input type="hidden" name="client_id" value="{{ $trip->client_id }}" />
    <h1 class="text-2xl font-semibold">Record Payment</h1>
    <input name="payment_method_id" placeholder="Payment method ID" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    <input name="amount" type="number" step="0.01" placeholder="Amount" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    <input name="transaction_reference" placeholder="Transaction reference" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    <input name="payment_date" type="date" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    <button class="rounded bg-brand-500 px-4 py-2 text-white">Save</button>
</form>
@endsection
