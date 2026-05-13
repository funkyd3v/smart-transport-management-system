@extends('admin::layouts.app')

@section('title', 'Admin - Trucks')

@section('content')
<x-common.page-breadcrumb pageTitle="Trucks" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Trucks</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['total']) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm">
            <p class="text-sm text-amber-700">In Workshop</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format($stats['workshop']) }}</p>
        </article>
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">With Driver</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">{{ number_format($stats['with_driver']) }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm">
            <p class="text-sm text-emerald-700">Added This Month</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ number_format($stats['new_this_month']) }}</p>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Fleet List</h2>
            <span class="text-xs text-slate-500">{{ $trucks->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Truck Number</th>
                        <th class="px-3 py-3">Model</th>
                        <th class="px-3 py-3">Brand</th>
                        <th class="px-3 py-3">Year</th>
                        <th class="px-3 py-3">Capacity (Tons)</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Current Driver</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trucks as $truck)
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $truck->truck_number }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $truck->model ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $truck->brand ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $truck->year ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ number_format((float) ($truck->capacity_tons ?? 0), 2) }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ ucfirst((string) $truck->status?->name) }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $truck->currentDriver?->user?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-slate-500">No trucks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $trucks->links() }}</div>
    </section>
</div>
@endsection
