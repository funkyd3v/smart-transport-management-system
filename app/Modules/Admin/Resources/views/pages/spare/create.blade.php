@extends('admin::layouts.app')

@section('title', 'Admin - Spare / Create')

@section('content')
 <x-common.page-breadcrumb pageTitle="Spare / Create" />

 <section class="relative overflow-hidden rounded-[26px] border border-slate-200 bg-gradient-to-b from-slate-50 to-white p-6 shadow-[0_18px_40px_-24px_rgba(15,23,42,0.25)] md:p-8">
 <div class="pointer-events-none absolute -top-20 -right-20 h-56 w-56 rounded-full bg-sky-100/70 blur-3xl"></div>
 <div class="pointer-events-none absolute -bottom-20 -left-20 h-56 w-56 rounded-full bg-amber-100/60 blur-3xl"></div>

 <div class="relative grid grid-cols-1 gap-5 lg:grid-cols-3">
 <article class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
 <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Module</p>
 <h2 class="mt-2 text-xl font-semibold text-slate-900">Spare / Create</h2>
 <p class="mt-2 text-sm text-slate-600">This page now follows the same premium light visual language as the Admin dashboard.</p>
 </article>

 <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
 <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status</p>
 <p class="mt-2 inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">Ready for implementation</p>
 <p class="mt-3 text-sm text-slate-600">Backend wiring and module-specific UI can now be added on top of this layout.</p>
 </article>

 <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
 <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Quick Actions</p>
 <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
 <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">Dashboard</a>
 <a href="{{ route('admin.reports.index') }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">Reports</a>
 </div>
 </article>
 </div>
 </section>
@endsection
