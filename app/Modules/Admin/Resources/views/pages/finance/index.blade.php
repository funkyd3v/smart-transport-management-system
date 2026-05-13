@extends('admin::layouts.app')

@section('title', 'Admin - Finance Overview')

@section('content')
<x-common.page-breadcrumb pageTitle="Finance Overview" />

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
            <p class="text-sm text-amber-700">Open Cashbook Days</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format($stats['open_days']) }}</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <a href="{{ route('admin.finance.dues') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow">
            <h2 class="text-lg font-semibold text-slate-900">Due Management</h2>
            <p class="mt-2 text-sm text-slate-600">Track outstanding client dues, collection progress, and settlements.</p>
            <span class="mt-4 inline-block text-sm font-medium text-slate-700">Open dues ledger</span>
        </a>
        <a href="{{ route('admin.finance.cashbook') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow">
            <h2 class="text-lg font-semibold text-slate-900">Daily Cashbook</h2>
            <p class="mt-2 text-sm text-slate-600">Review daily income, expense, and net balance from recorded entries.</p>
            <span class="mt-4 inline-block text-sm font-medium text-slate-700">Open cashbook register</span>
        </a>
    </section>
</div>
@endsection
