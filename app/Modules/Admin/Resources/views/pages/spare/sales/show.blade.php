@extends('admin::layouts.app')

@section('title', 'Admin - Spare Sale Details')

@section('content')
<x-common.page-breadcrumb pageTitle="Spare Sales / Details" />

<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-slate-900">Sale Details</h2>
        <a href="{{ route('admin.spare.sales.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Back</a>
    </div>

    <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="rounded-lg border border-slate-200 p-4">
            <dt class="text-xs uppercase text-slate-500">Buyer Name</dt>
            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $sale->buyer_name }}</dd>
        </div>
        <div class="rounded-lg border border-slate-200 p-4">
            <dt class="text-xs uppercase text-slate-500">Sale Type</dt>
            <dd class="mt-1 text-sm font-medium text-slate-900">{{ ucwords(str_replace('_', ' ', (string) $sale->saleType?->name)) }}</dd>
        </div>
        <div class="rounded-lg border border-slate-200 p-4">
            <dt class="text-xs uppercase text-slate-500">Spare Part</dt>
            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $sale->sparePart?->name ?? '-' }}</dd>
        </div>
        <div class="rounded-lg border border-slate-200 p-4">
            <dt class="text-xs uppercase text-slate-500">Quantity</dt>
            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $sale->quantity ?? '-' }}</dd>
        </div>
        <div class="rounded-lg border border-slate-200 p-4">
            <dt class="text-xs uppercase text-slate-500">Sale Price</dt>
            <dd class="mt-1 text-sm font-medium text-slate-900">BDT {{ number_format((float) $sale->sale_price, 2) }}</dd>
        </div>
        <div class="rounded-lg border border-slate-200 p-4">
            <dt class="text-xs uppercase text-slate-500">Purchase Price Snapshot</dt>
            <dd class="mt-1 text-sm font-medium text-slate-900">BDT {{ number_format((float) $sale->purchase_price_snapshot, 2) }}</dd>
        </div>
        <div class="rounded-lg border border-slate-200 p-4">
            <dt class="text-xs uppercase text-slate-500">Profit</dt>
            <dd class="mt-1 text-sm font-medium {{ (float) $sale->profit > 0 ? 'text-emerald-700' : 'text-red-700' }}">BDT {{ number_format((float) $sale->profit, 2) }}</dd>
        </div>
        <div class="rounded-lg border border-slate-200 p-4">
            <dt class="text-xs uppercase text-slate-500">Sold At</dt>
            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $sale->sold_at?->format('d M Y') ?? '-' }}</dd>
        </div>
        <div class="rounded-lg border border-slate-200 p-4 md:col-span-2">
            <dt class="text-xs uppercase text-slate-500">Note</dt>
            <dd class="mt-1 text-sm text-slate-900">{{ $sale->note ?? '-' }}</dd>
        </div>
    </dl>
</section>
@endsection
