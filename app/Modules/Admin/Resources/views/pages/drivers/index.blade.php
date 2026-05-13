@extends('admin::layouts.app')

@section('title', 'Admin - Drivers')

@section('content')
<x-common.page-breadcrumb pageTitle="Drivers" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Drivers</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['total']) }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm">
            <p class="text-sm text-emerald-700">Available</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ number_format($stats['available']) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm">
            <p class="text-sm text-amber-700">Busy</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format($stats['busy']) }}</p>
        </article>
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">Avg. Rating</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">{{ number_format((float) $stats['avg_rating'], 2) }}</p>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Driver Registry</h2>
            <span class="text-xs text-slate-500">{{ $drivers->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Name</th>
                        <th class="px-3 py-3">Email</th>
                        <th class="px-3 py-3">License</th>
                        <th class="px-3 py-3">Type</th>
                        <th class="px-3 py-3">Availability</th>
                        <th class="px-3 py-3">Rating</th>
                        <th class="px-3 py-3">Total Trips</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($drivers as $driver)
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $driver->user?->name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $driver->user?->email ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $driver->license_number }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ ucfirst((string) $driver->driving_type) }}</td>
                            <td class="px-3 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $driver->is_available ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-amber-50 text-amber-700 ring-1 ring-amber-100' }}">{{ $driver->is_available ? 'Available' : 'Busy' }}</span>
                            </td>
                            <td class="px-3 py-3 text-slate-700">{{ number_format((float) ($driver->rating ?? 0), 2) }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ number_format((int) ($driver->total_trips ?? 0)) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-slate-500">No drivers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $drivers->links() }}</div>
    </section>
</div>
@endsection
