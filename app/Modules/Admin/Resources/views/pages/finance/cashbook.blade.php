@extends('admin::layouts.app')

@section('title', 'Admin - Cashbook')

@section('content')
<x-common.page-breadcrumb pageTitle="Cashbook" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">Income This Month</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">BDT {{ number_format((float) $stats['income_this_month'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-rose-100 bg-rose-50/60 p-5 shadow-sm">
            <p class="text-sm text-rose-700">Expense This Month</p>
            <p class="mt-2 text-2xl font-semibold text-rose-700">BDT {{ number_format((float) $stats['expense_this_month'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm">
            <p class="text-sm text-emerald-700">Profit This Month</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">BDT {{ number_format((float) $stats['profit_this_month'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm">
            <p class="text-sm text-amber-700">Open Days</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format($stats['open_days']) }}</p>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Daily Entries</h2>
            <span class="text-xs text-slate-500">{{ $rows->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Date</th>
                        <th class="px-3 py-3">Recorded By</th>
                        <th class="px-3 py-3">Income</th>
                        <th class="px-3 py-3">Expense</th>
                        <th class="px-3 py-3">Net Profit</th>
                        <th class="px-3 py-3">Closing Balance</th>
                        <th class="px-3 py-3">Finalized</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $row->entry_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $row->recordedBy?->name ?? '-' }}</td>
                            <td class="px-3 py-3 text-sky-700">BDT {{ number_format((float) $row->total_income, 2) }}</td>
                            <td class="px-3 py-3 text-rose-700">BDT {{ number_format((float) $row->total_expense, 2) }}</td>
                            <td class="px-3 py-3 font-medium {{ (float) $row->net_profit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">BDT {{ number_format((float) $row->net_profit, 2) }}</td>
                            <td class="px-3 py-3 text-slate-700">BDT {{ number_format((float) $row->closing_balance, 2) }}</td>
                            <td class="px-3 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $row->is_finalized ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-amber-50 text-amber-700 ring-1 ring-amber-100' }}">{{ $row->is_finalized ? 'Yes' : 'No' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-slate-500">No cashbook entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $rows->links() }}</div>
    </section>
</div>
@endsection
