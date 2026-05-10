@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Record Trip Payment" />

    <x-common.component-card title="Payment Entry" desc="Register a payment against this trip.">
        <form method="POST" action="{{ route('manager.trips.payments.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="trip_ulid" value="{{ $trip->ulid }}" />
            <input type="hidden" name="client_id" value="{{ $trip->client_id }}" />

            <select name="payment_method_id" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700">
                <option value="">Select payment method</option>
                @foreach ($paymentMethods as $method)
                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                @endforeach
            </select>
            <input name="amount" type="number" step="0.01" min="0" placeholder="Amount" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <input name="transaction_reference" placeholder="Transaction reference" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <input name="payment_date" type="date" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <textarea name="note" rows="3" placeholder="Note (optional)" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-700"></textarea>

            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('manager.trips.show', $trip->ulid) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm">Cancel</a>
                <button class="rounded-lg bg-brand-500 px-4 py-2 text-sm text-white">Save Payment</button>
            </div>
        </form>
    </x-common.component-card>
@endsection
