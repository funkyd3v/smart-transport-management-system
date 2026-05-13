@extends('admin::layouts.app')

@section('title', 'Admin - Trips')

@section('content')
<x-common.page-breadcrumb pageTitle="Trips" />

@php
    $statusClasses = [
        'completed' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100',
        'in_progress' => 'bg-sky-50 text-sky-700 ring-1 ring-sky-100',
        'running' => 'bg-sky-50 text-sky-700 ring-1 ring-sky-100',
        'pending' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-100',
        'cancelled' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-100',
    ];
@endphp

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Trips</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['total']) }}</p>
        </article>
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">Running</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">{{ number_format($stats['running']) }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm">
            <p class="text-sm text-emerald-700">Completed</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ number_format($stats['completed']) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm">
            <p class="text-sm text-amber-700">Created Today</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format($stats['today']) }}</p>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Trip Operations</h2>
            <span class="text-xs text-slate-500">{{ $trips->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Trip Code</th>
                        <th class="px-3 py-3">Client</th>
                        <th class="px-3 py-3">Driver</th>
                        <th class="px-3 py-3">Truck</th>
                        <th class="px-3 py-3">Route</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trips as $trip)
                        @php
                            $statusKey = strtolower((string) ($trip->status?->name ?? 'pending'));
                        @endphp
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $trip->trip_code }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $trip->client?->company_name ?? $trip->client?->user?->name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $trip->driver?->user?->name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $trip->truck?->truck_number ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $trip->pickup_point }} to {{ $trip->delivery_point }}</td>
                            <td class="px-3 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$statusKey] ?? 'bg-slate-100 text-slate-700 ring-1 ring-slate-200' }}">{{ str_replace('_', ' ', ucfirst((string) $trip->status?->name)) }}</span>
                            </td>
                            <td class="px-3 py-3 font-medium text-slate-900">BDT {{ number_format((float) $trip->trip_rate, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-slate-500">No trips found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $trips->links() }}</div>
    </section>
</div>
@endsection
