@extends('client::layouts.app')

@section('content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Active Shipments</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">7 In Transit</h2>
            <p class="mt-2 text-sm text-sky-600">Latest ETA refresh available in real time</p>
        </article>
        <article class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Outstanding Balance</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">$12.8K</h2>
            <p class="mt-2 text-sm text-amber-600">Two invoices due this week</p>
        </article>
        <article class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Support Tickets</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">1 Open</h2>
            <p class="mt-2 text-sm text-emerald-600">Operations team responded 18 minutes ago</p>
        </article>
    </section>
@endsection
