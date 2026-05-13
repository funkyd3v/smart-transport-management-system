@extends('admin::layouts.app')

@section('title', 'Admin - Reports')

@section('content')
<x-common.page-breadcrumb pageTitle="Reports" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Trips</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['trips']) }}</p>
        </article>
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">Total Payments</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">BDT {{ number_format((float) $stats['payments'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-rose-100 bg-rose-50/60 p-5 shadow-sm">
            <p class="text-sm text-rose-700">Total Expenses</p>
            <p class="mt-2 text-2xl font-semibold text-rose-700">BDT {{ number_format((float) $stats['expenses'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm">
            <p class="text-sm text-amber-700">Outstanding Dues</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">BDT {{ number_format((float) $stats['dues'], 2) }}</p>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <h2 class="mb-4 text-lg font-semibold text-slate-900">Generate Report</h2>

        <form action="{{ route('admin.reports.generate') }}" method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @csrf
            <div class="md:col-span-2">
                <label for="report_type" class="mb-1 block text-sm font-medium text-slate-700">Report Type</label>
                <select id="report_type" name="report_type" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none">
                    @foreach ($reportTypes as $type)
                        <option value="{{ $type }}">{{ str_replace('-', ' ', ucwords($type, '-')) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Generate Preview</button>
            </div>
        </form>

        <div class="mt-6 border-t border-slate-200 pt-4">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Quick Downloads</h3>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($reportTypes as $type)
                    <a href="{{ route('admin.reports.download', $type) }}" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 transition hover:border-slate-400 hover:text-slate-900">{{ str_replace('-', ' ', ucwords($type, '-')) }}</a>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection
