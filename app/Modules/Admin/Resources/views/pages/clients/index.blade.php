@extends('admin::layouts.app')

@section('title', 'Admin - Clients')

@section('content')
<x-common.page-breadcrumb pageTitle="Clients" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Clients</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['total']) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm">
            <p class="text-sm text-amber-700">Clients with Due</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format($stats['with_due']) }}</p>
        </article>
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">Total Business</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">BDT {{ number_format((float) $stats['business'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-rose-100 bg-rose-50/60 p-5 shadow-sm">
            <p class="text-sm text-rose-700">Outstanding Due</p>
            <p class="mt-2 text-2xl font-semibold text-rose-700">BDT {{ number_format((float) $stats['due'], 2) }}</p>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Client Portfolio</h2>
            <span class="text-xs text-slate-500">{{ $clients->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Client</th>
                        <th class="px-3 py-3">Email</th>
                        <th class="px-3 py-3">Category</th>
                        <th class="px-3 py-3">Project</th>
                        <th class="px-3 py-3">Business Amount</th>
                        <th class="px-3 py-3">Total Due</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $client->company_name ?? $client->user?->name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $client->user?->email ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $client->category?->name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $client->project_name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">BDT {{ number_format((float) ($client->total_business_amount ?? 0), 2) }}</td>
                            <td class="px-3 py-3 font-medium {{ (float) ($client->total_due ?? 0) > 0 ? 'text-rose-700' : 'text-emerald-700' }}">BDT {{ number_format((float) ($client->total_due ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-slate-500">No clients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $clients->links() }}</div>
    </section>
</div>
@endsection
