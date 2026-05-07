@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Driver Profile" />

    <div class="space-y-6">
        <x-common.component-card title="Driver Overview" desc="Driver details and performance snapshot.">
            <div class="flex flex-col gap-6">
                <div class="flex flex-col items-start gap-4 rounded-xl border border-gray-100 p-5 dark:border-gray-800 sm:flex-row sm:items-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-blue-100 text-3xl font-semibold text-blue-700">RA</div>
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Rafiq Ahmed</h3>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <x-ui.badge variant="light" color="primary">Permanent</x-ui.badge>
                            <x-ui.badge variant="light" color="success">Available</x-ui.badge>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="rounded-xl border border-gray-100 p-5 dark:border-gray-800">
                        <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Personal Information</h4>
                        <div class="space-y-3">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Full Name</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">Rafiq Ahmed</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">rafiq.ahmed@gmail.com</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">01711-223344</p>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-5 dark:border-gray-800">
                        <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">License & Employment Details</h4>
                        <div class="space-y-3">
                            <p class="text-sm text-gray-500 dark:text-gray-400">License Number</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">DL-DHK-441223</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">NID Number</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">1993267812345</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Driving Type</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">Permanent</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Joining Date</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">12 Jan 2023</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Trips</p>
                        <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">278</h3>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Profit Generated (BDT)</p>
                        <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">1,286,500</h3>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Average Rating</p>
                        <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">4.0 / 5</h3>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                    <a href="#" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">Edit Driver</a>
                    <a href="#" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-3.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">Back to List</a>
                </div>
            </div>
        </x-common.component-card>
    </div>
@endsection
