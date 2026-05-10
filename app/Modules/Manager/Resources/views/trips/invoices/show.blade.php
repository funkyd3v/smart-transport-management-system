@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Trip Invoice" />

    <div class="mx-auto max-w-4xl rounded-xl border border-gray-200 bg-white p-8 dark:border-gray-800 dark:bg-gray-900">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Invoice {{ $invoice->invoice_number }}</h1>
                <p class="text-sm text-gray-500">Issued at: {{ $invoice->issued_at }}</p>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-1 gap-3 text-sm md:grid-cols-2 text-gray-700 dark:text-gray-300">
            <p><strong>Client:</strong> {{ $invoice->client?->company_name ?? $invoice->trip?->client?->user?->name }}</p>
            <p><strong>Trip Code:</strong> {{ $invoice->trip?->trip_code }}</p>
            <p><strong>Truck:</strong> {{ $invoice->trip?->truck?->truck_number }}</p>
            <p><strong>Driver:</strong> {{ $invoice->trip?->driver?->user?->name }}</p>
        </div>

        <table class="mb-6 w-full text-sm text-gray-700 dark:text-gray-300">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="py-2 text-left">Item</th>
                    <th class="py-2 text-left">Unit</th>
                    <th class="py-2 text-left">Qty</th>
                    <th class="py-2 text-left">Unit Price</th>
                    <th class="py-2 text-left">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->trip?->goods ?? [] as $goods)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="py-2">{{ $goods->item_name }}</td>
                        <td class="py-2">{{ $goods->unit }}</td>
                        <td class="py-2">{{ $goods->quantity }}</td>
                        <td class="py-2">{{ number_format((float) $goods->unit_price, 2) }}</td>
                        <td class="py-2">{{ number_format((float) $goods->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ml-auto max-w-xs space-y-1 text-sm text-gray-700 dark:text-gray-300">
            <p class="flex justify-between"><span>Subtotal</span><span>{{ number_format((float) $invoice->subtotal, 2) }}</span></p>
            <p class="flex justify-between"><span>Advance Paid</span><span>{{ number_format((float) $invoice->advance_paid, 2) }}</span></p>
            <p class="flex justify-between"><span>Due</span><span>{{ number_format((float) $invoice->due_amount, 2) }}</span></p>
            <p class="flex justify-between font-bold"><span>Total</span><span>{{ number_format((float) $invoice->total_amount, 2) }}</span></p>
        </div>
    </div>
@endsection
