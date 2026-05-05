@extends('manager::layouts.app')

@section('content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Assigned Runs</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">42 Open Loads</h2>
            <p class="mt-2 text-sm text-sky-600">6 awaiting truck confirmation</p>
        </article>
        <article class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Dispatch Readiness</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">91%</h2>
            <p class="mt-2 text-sm text-emerald-600">Vehicles and documents verified</p>
        </article>
        <article class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Escalations</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">3 Active</h2>
            <p class="mt-2 text-sm text-amber-600">Fuel variance and late-delivery reviews</p>
        </article>
    </section>
@endsection
