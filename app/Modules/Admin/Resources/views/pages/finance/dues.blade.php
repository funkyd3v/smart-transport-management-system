@extends('admin::layouts.app')

@section('title', 'Admin - Dues')

@section('content')
<x-common.page-breadcrumb pageTitle="Dues" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-rose-100 bg-rose-50/60 p-5 shadow-sm">
            <p class="text-sm text-rose-700">Outstanding</p>
            <p class="mt-2 text-2xl font-semibold text-rose-700">BDT {{ number_format((float) $stats['outstanding'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm">
            <p class="text-sm text-emerald-700">Collected</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">BDT {{ number_format((float) $stats['collected'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm">
            <p class="text-sm text-amber-700">Open Records</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format($stats['open_records']) }}</p>
        </article>
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">Settled Records</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">{{ number_format($stats['settled_records']) }}</p>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Due Ledger</h2>
            <span class="text-xs text-slate-500">{{ $dues->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Trip</th>
                        <th class="px-3 py-3">Client</th>
                        <th class="px-3 py-3">Original Due</th>
                        <th class="px-3 py-3">Collected</th>
                        <th class="px-3 py-3">Remaining</th>
                        <th class="px-3 py-3">Due Date</th>
                        <th class="px-3 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dues as $due)
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $due->trip?->trip_code ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $due->client?->company_name ?? $due->client?->user?->name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">BDT {{ number_format((float) $due->original_due, 2) }}</td>
                            <td class="px-3 py-3 text-emerald-700">BDT {{ number_format((float) $due->collected_amount, 2) }}</td>
                            <td class="px-3 py-3 font-medium {{ (float) $due->remaining_due > 0 ? 'text-rose-700' : 'text-emerald-700' }}">BDT {{ number_format((float) $due->remaining_due, 2) }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $due->due_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-3 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $due->is_settled ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-rose-50 text-rose-700 ring-1 ring-rose-100' }}">{{ $due->is_settled ? 'Settled' : 'Open' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-slate-500">No due records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $dues->links() }}</div>
    </section>
</div>
@endsection
