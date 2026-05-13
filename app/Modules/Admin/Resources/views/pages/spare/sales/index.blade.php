@extends('admin::layouts.app')

@section('title', 'Admin - Spare Sales')

@section('content')
<x-common.page-breadcrumb pageTitle="Spare Sales" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">Revenue</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">BDT {{ number_format((float) $stats['revenue'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm">
            <p class="text-sm text-emerald-700">Profit</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">BDT {{ number_format((float) $stats['profit'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Transactions</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['transactions']) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm">
            <p class="text-sm text-amber-700">Sales Today</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format($stats['today_sales']) }}</p>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Sales Register</h2>
            <span class="text-xs text-slate-500">{{ $sales->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Date</th>
                        <th class="px-3 py-3">Part</th>
                        <th class="px-3 py-3">Type</th>
                        <th class="px-3 py-3">Buyer</th>
                        <th class="px-3 py-3">Quantity</th>
                        <th class="px-3 py-3">Sale Price</th>
                        <th class="px-3 py-3">Profit</th>
                        <th class="px-3 py-3">Sold By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $sale->sale_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $sale->sparePart?->part_name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $sale->saleType?->name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $sale->buyer_name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ number_format((int) $sale->quantity_sold) }}</td>
                            <td class="px-3 py-3 text-slate-700">BDT {{ number_format((float) $sale->sale_price, 2) }}</td>
                            <td class="px-3 py-3 font-medium {{ (float) $sale->profit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">BDT {{ number_format((float) $sale->profit, 2) }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $sale->soldBy?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-slate-500">No spare sales found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $sales->links() }}</div>
    </section>
</div>
@endsection
