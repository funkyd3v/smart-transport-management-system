@extends('admin::layouts.app')

@section('content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Fleet Status</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">128 Active Trips</h2>
            <p class="mt-2 text-sm text-emerald-600">+12% from last dispatch cycle</p>
        </article>
        <article class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Pending Approvals</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">14 Requests</h2>
            <p class="mt-2 text-sm text-amber-600">Driver onboarding and limit overrides</p>
        </article>
        <article class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Revenue Snapshot</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">$48.2K</h2>
            <p class="mt-2 text-sm text-sky-600">Updated from today’s completed invoices</p>
        </article>
    </section>
@endsection
