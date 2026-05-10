@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Trip Details" />

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        <x-common.component-card title="{{ $trip->trip_code }}" desc="Monitor and control this trip lifecycle.">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500">Route</p>
                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $trip->pickup_point }} → {{ $trip->delivery_point }}</p>
                    <p class="mt-3 text-sm text-gray-500">Client</p>
                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $trip->client?->company_name ?? $trip->client?->user?->name ?? '-' }}</p>
                    <p class="mt-3 text-sm text-gray-500">Driver</p>
                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $trip->driver?->user?->name ?? '-' }}</p>
                    <p class="mt-3 text-sm text-gray-500">Truck</p>
                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $trip->truck?->truck_number ?? '-' }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500">Financials</p>
                    <p class="mt-1 text-sm">Income: <strong>{{ number_format((float) $trip->total_income, 2) }}</strong></p>
                    <p class="text-sm">Expense: <strong>{{ number_format((float) $trip->total_expense, 2) }}</strong></p>
                    <p class="text-sm">Due: <strong>{{ number_format((float) $trip->due_amount, 2) }}</strong></p>
                    <p class="text-sm">Profit: <strong>{{ number_format((float) $trip->profit, 2) }}</strong></p>
                    <p class="mt-3 text-sm">Current Status: <strong>{{ ucfirst(str_replace('_', ' ', (string) $trip->status?->name)) }}</strong></p>
                    <p class="text-sm">Invoiced: <strong>{{ $trip->isInvoiced() ? 'Yes' : 'No' }}</strong></p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <form method="POST" action="{{ route('manager.trips.status.update') }}" class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    @csrf
                    <input type="hidden" name="trip_ulid" value="{{ $trip->ulid }}" />
                    <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white/90">Update Status</h3>
                    <select name="status" class="mb-3 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700">
                        @foreach (\App\Modules\Trip\Enums\TripStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <textarea name="note" rows="2" placeholder="Optional note" class="mb-3 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-700"></textarea>
                    <button class="rounded bg-brand-500 px-4 py-2 text-sm text-white">Save</button>
                </form>

                <form method="POST" action="{{ route('manager.trips.invoices.store') }}" class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    @csrf
                    <input type="hidden" name="trip_ulid" value="{{ $trip->ulid }}" />
                    <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white/90">Invoice</h3>
                    <p class="mb-3 text-xs text-gray-500">Generate invoice when the trip billing is ready.</p>
                    <button class="rounded bg-green-600 px-4 py-2 text-sm text-white">Generate Invoice</button>
                    @if ($trip->invoice)
                        <a href="{{ route('manager.trips.invoices.show', $trip->invoice->ulid) }}" class="mt-3 block text-sm text-brand-600">View Latest Invoice</a>
                    @endif
                </form>

                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white/90">Transactions</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('manager.trips.payments.create', $trip->ulid) }}" class="rounded bg-indigo-600 px-4 py-2 text-sm text-white">Record Payment</a>
                        <a href="{{ route('manager.trips.expenses.create', $trip->ulid) }}" class="rounded bg-orange-600 px-4 py-2 text-sm text-white">Record Expense</a>
                    </div>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800">
                <table class="w-full min-w-[700px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Item</th>
                            <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Unit</th>
                            <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Qty</th>
                            <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Unit Price</th>
                            <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trip->goods as $item)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->item_name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->unit }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->quantity }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ number_format((float) $item->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No goods items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-common.component-card>
    </div>
@endsection
