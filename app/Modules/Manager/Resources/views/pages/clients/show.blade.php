@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Client Profile" />

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="xl:col-span-2">
                <x-common.component-card title="Client Information" desc="Detailed business profile and project data.">
                    <div class="space-y-6">
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Bashundhara Group</h3>
                            <x-ui.badge variant="light" color="warning">Mega Project Client</x-ui.badge>
                            <x-ui.badge variant="light" color="success">Active</x-ui.badge>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div><p class="text-theme-xs text-gray-500 dark:text-gray-400">Contact Number</p><p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">01811-345678</p></div>
                            <div><p class="text-theme-xs text-gray-500 dark:text-gray-400">Category</p><p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">Mega Project Client</p></div>
                            <div><p class="text-theme-xs text-gray-500 dark:text-gray-400">Project Name</p><p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">Bashundhara Residential Area Phase 4</p></div>
                            <div><p class="text-theme-xs text-gray-500 dark:text-gray-400">Agreement Number</p><p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">BG-2024-0042</p></div>
                            <div><p class="text-theme-xs text-gray-500 dark:text-gray-400">Project Value</p><p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">BDT 85,000,000</p></div>
                            <div><p class="text-theme-xs text-gray-500 dark:text-gray-400">Target Finishing Date</p><p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">31 December 2025</p></div>
                            <div><p class="text-theme-xs text-gray-500 dark:text-gray-400">Member Since</p><p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">15 April 2025</p></div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                            <a href="{{ route('manager.clients.edit') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                                Edit Client
                            </a>
                            <button
                                class="inline-flex items-center justify-center rounded-lg bg-red-50 px-5 py-3.5 text-sm font-medium text-red-600 transition hover:bg-red-100 dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/20">
                                Deactivate
                            </button>
                        </div>
                    </div>
                </x-common.component-card>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Total Trips</span>
                    <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">12</h4>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Total Business Amount</span>
                    <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">BDT 14,20,000</h4>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Total Outstanding Due</span>
                    <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">BDT 2,35,000</h4>
                </div>
            </div>
        </div>

        <x-common.component-card title="Recent Trips" desc="Latest completed trips for this client.">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1000px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Trip #</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Pickup Point</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Delivery Point</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Trip Date</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Trip Amount (BDT)</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Due Amount (BDT)</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">TRP-0041</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">Chittagong Port</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">Bashundhara Residential Area</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">28 Apr 2025</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">45,000</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">15,000</p></td>
                                <td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="success">Completed</x-ui.badge></td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">TRP-0038</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">Chittagong Port</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">Bashundhara Residential Area</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">14 Apr 2025</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">45,000</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">0</p></td>
                                <td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="success">Completed</x-ui.badge></td>
                            </tr>
                            <tr>
                                <td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">TRP-0035</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">Mongla Port</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">Bashundhara Residential Area</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">02 Apr 2025</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">52,000</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">20,000</p></td>
                                <td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="success">Completed</x-ui.badge></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </x-common.component-card>
    </div>
@endsection
