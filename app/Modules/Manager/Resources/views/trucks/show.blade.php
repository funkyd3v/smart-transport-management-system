@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Truck Detail" />

    <div class="space-y-6">
        <x-common.component-card title="Truck Overview" desc="Vehicle profile, assignment details, and performance summary.">
            <div class="rounded-xl border border-gray-100 p-5 dark:border-gray-800">
                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-800 dark:text-white/90">DHAKA-METRO-TA-1123</h3>
                        <div class="mt-2"><x-ui.badge variant="light" color="success">Active</x-ui.badge></div>
                    </div>
                    <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-300">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 7H15V14H3V7Z" stroke="currentColor" stroke-width="1.8"/><path d="M15 9H18.5L21 12V14H15V9Z" stroke="currentColor" stroke-width="1.8"/><circle cx="7" cy="16.5" r="1.5" fill="currentColor"/><circle cx="18" cy="16.5" r="1.5" fill="currentColor"/></svg>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-gray-100 p-5 dark:border-gray-800">
                    <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Truck Information</h4>
                    <div class="space-y-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Brand</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">Isuzu</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Model</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">FVR 34</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manufacturing Year</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">2021</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Capacity (tons)</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">8.50</p>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-100 p-5 dark:border-gray-800">
                    <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Assignment & Service Details</h4>
                    <div class="space-y-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Assigned Driver</p><p class="text-sm font-medium text-brand-600 dark:text-brand-400">Rafiq Ahmed</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">Active</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Last Service Date</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">04 May 2026</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Registered Since</p><p class="text-sm font-medium text-gray-800 dark:text-white/90">12 Jan 2023</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]"><p class="text-sm text-gray-500 dark:text-gray-400">Total Trips Completed</p><h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">472</h3></div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]"><p class="text-sm text-gray-500 dark:text-gray-400">Current Status Description</p><h3 class="mt-2 text-base font-semibold text-gray-800 dark:text-white/90">Truck is available and actively assigned for transport operations.</h3></div>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                <a href="{{ route('manager.trucks.edit') }}" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">Edit Truck</a>
                <a href="{{ route('manager.drivers.index') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-blue-700">Assign Driver</a>
                <a href="{{ route('manager.trucks.index') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-3.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">Back to List</a>
            </div>
        </x-common.component-card>
    </div>
@endsection
