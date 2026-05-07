@extends('admin::layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Trip {{ $trip->trip_code }}</h1>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
            <p><strong>Pickup:</strong> {{ $trip->pickup_point }}</p>
            <p><strong>Delivery:</strong> {{ $trip->delivery_point }}</p>
            <p><strong>Status:</strong> {{ $trip->status?->name }}</p>
            <p><strong>Rate:</strong> {{ $trip->trip_rate }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
            <p><strong>Expense:</strong> {{ $trip->total_expense }}</p>
            <p><strong>Due:</strong> {{ $trip->due_amount }}</p>
            <p><strong>Profit:</strong> {{ $trip->profit }}</p>
            <p><strong>Invoiced:</strong> {{ $trip->isInvoiced() ? 'Yes' : 'No' }}</p>
        </div>
    </div>
</div>
@endsection
