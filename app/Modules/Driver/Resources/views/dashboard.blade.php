@extends('driver::layouts.app')

@section('content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Today’s Trips</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">5 Stops</h2>
            <p class="mt-2 text-sm text-sky-600">Next departure in 35 minutes</p>
        </article>
        <article class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Fuel Allowance</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">$320</h2>
            <p class="mt-2 text-sm text-emerald-600">$110 submitted, balance available</p>
        </article>
        <article class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Compliance Checks</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">2 Pending</h2>
            <p class="mt-2 text-sm text-amber-600">Upload trip sheet and delivery receipt</p>
        </article>
    </section>
@endsection
