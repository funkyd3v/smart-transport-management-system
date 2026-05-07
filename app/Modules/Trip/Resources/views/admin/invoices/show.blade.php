@extends('admin::layouts.app')

@section('content')
<div class="mx-auto max-w-4xl rounded-xl border border-gray-200 bg-white p-8 dark:border-gray-800 dark:bg-gray-900">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Invoice {{ $invoice->invoice_number }}</h1>
            <p class="text-sm text-gray-500">Issued at: {{ $invoice->issued_at }}</p>
        </div>
        <div class="h-16 w-40 rounded border border-dashed border-gray-300 text-center text-xs leading-[64px]">Company Logo</div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
        <p><strong>Client:</strong> {{ $invoice->client?->name }}</p>
        <p><strong>Trip Code:</strong> {{ $invoice->trip?->trip_code }}</p>
        <p><strong>Truck:</strong> {{ $invoice->trip?->truck?->truck_number }}</p>
        <p><strong>Driver:</strong> {{ $invoice->trip?->driver?->user?->name }}</p>
    </div>

    <table class="mb-6 w-full text-sm"><thead><tr class="border-b"><th class="py-2 text-left">Item</th><th class="py-2 text-left">Unit</th><th class="py-2 text-left">Qty</th><th class="py-2 text-left">Unit Price</th><th class="py-2 text-left">Total</th></tr></thead><tbody>@foreach($invoice->trip?->goods ?? [] as $goods)<tr class="border-b border-gray-100"><td class="py-2">{{ $goods->item_name }}</td><td class="py-2">{{ $goods->unit }}</td><td class="py-2">{{ $goods->quantity }}</td><td class="py-2">{{ $goods->unit_price }}</td><td class="py-2">{{ $goods->total_price }}</td></tr>@endforeach</tbody></table>

    <div class="ml-auto max-w-xs space-y-1 text-sm">
        <p class="flex justify-between"><span>Subtotal</span><span>{{ $invoice->subtotal }}</span></p>
        <p class="flex justify-between"><span>Advance Paid</span><span>{{ $invoice->advance_paid }}</span></p>
        <p class="flex justify-between"><span>Due</span><span>{{ $invoice->due_amount }}</span></p>
        <p class="flex justify-between font-bold"><span>Total</span><span>{{ $invoice->total_amount }}</span></p>
    </div>

    <div class="mt-10 flex justify-end"><div class="h-16 w-48 rounded border border-dashed border-gray-300 text-center text-xs leading-[64px]">Authority Signature</div></div>
</div>
@endsection
