@extends('admin::layouts.app')

@section('title', 'Admin - Spare Inventory')

@section('content')
<x-common.page-breadcrumb pageTitle="Spare Inventory" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Parts</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['total_parts']) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm">
            <p class="text-sm text-amber-700">Low Stock Items</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format($stats['low_stock']) }}</p>
        </article>
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">Inventory Value</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">BDT {{ number_format((float) ($stats['inventory_value'] ?? 0), 2) }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm">
            <p class="text-sm text-emerald-700">Sold Units</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ number_format($stats['sold_items']) }}</p>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Inventory Register</h2>
            <span class="text-xs text-slate-500">{{ $parts->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Part Name</th>
                        <th class="px-3 py-3">Category</th>
                        <th class="px-3 py-3">Condition</th>
                        <th class="px-3 py-3">Source Truck</th>
                        <th class="px-3 py-3">Stock</th>
                        <th class="px-3 py-3">Purchase Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($parts as $part)
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $part->part_name }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $part->category?->name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ ucfirst((string) $part->condition) }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $part->sourcedFromTruck?->truck_number ?? '-' }}</td>
                            <td class="px-3 py-3 font-medium {{ (int) $part->quantity_in_stock <= 5 ? 'text-amber-700' : 'text-slate-700' }}">{{ number_format((int) $part->quantity_in_stock) }}</td>
                            <td class="px-3 py-3 text-slate-700">BDT {{ number_format((float) $part->purchase_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-slate-500">No spare parts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $parts->links() }}</div>
    </section>
</div>
@endsection
