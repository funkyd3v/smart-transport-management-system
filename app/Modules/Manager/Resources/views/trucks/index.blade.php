@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Truck Management" />

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Trucks</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">68</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Active Trucks</p>
                <h3 class="mt-2 text-2xl font-semibold text-green-600 dark:text-green-400">42</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Under Maintenance</p>
                <h3 class="mt-2 text-2xl font-semibold text-orange-600 dark:text-orange-400">12</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Out of Service</p>
                <h3 class="mt-2 text-2xl font-semibold text-red-600 dark:text-red-400">5</h3>
            </div>
        </div>

        <x-common.component-card title="Truck List" desc="Monitor fleet status, assignment, and service history.">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3 xl:w-3/4">
                    <input type="text" placeholder="Search by truck number or brand"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    <select class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                        <option>All Status</option><option>Active</option><option>Under Maintenance</option><option>Out of Service</option><option>Idle</option>
                    </select>
                    <select class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                        <option>All Capacities</option><option>Under 5 Tons</option><option>5-10 Tons</option><option>Above 10 Tons</option>
                    </select>
                </div>

                <a href="{{ route('manager.trucks.create') }}" class="inline-flex items-center justify-center rounded-lg bg-green-600 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-green-700">Add New Truck</a>
            </div>

            <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1180px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Truck Number</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Brand & Model</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Year</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Capacity (tons)</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Assigned Driver</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Last Service Date</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Total Trips</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Actions</p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 dark:border-gray-800"><td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">DHAKA-METRO-TA-1123</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">Isuzu FVR 34</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">2021</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">8.50</p></td><td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">Rafiq Ahmed</p></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="success">Active</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">04 May 2026</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">472</p></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">View</button><button class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Edit</button><button class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs text-red-500 hover:bg-red-50 dark:border-red-800">Delete</button></div></td></tr>
                            <tr class="border-b border-gray-100 dark:border-gray-800"><td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">CTG-METRO-TA-7451</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">Tata LPT 1613</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">2019</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">6.00</p></td><td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">Masud Karim</p></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="warning">Idle</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">21 Apr 2026</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">351</p></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">View</button><button class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Edit</button><button class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs text-red-500 hover:bg-red-50 dark:border-red-800">Delete</button></div></td></tr>
                            <tr class="border-b border-gray-100 dark:border-gray-800"><td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">SYL-METRO-TA-3988</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">Hino 500 FG8J</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">2020</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">11.00</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">Unassigned</p></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="error">Out of Service</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">10 Mar 2026</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">229</p></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">View</button><button class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Edit</button><button class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs text-red-500 hover:bg-red-50 dark:border-red-800">Delete</button></div></td></tr>
                            <tr class="border-b border-gray-100 dark:border-gray-800"><td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">KHU-METRO-TA-6190</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">Ashok Leyland 1616</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">2018</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">9.00</p></td><td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">Shahidul Khan</p></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="info">Under Maintenance</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">01 May 2026</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">398</p></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">View</button><button class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Edit</button><button class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs text-red-500 hover:bg-red-50 dark:border-red-800">Delete</button></div></td></tr>
                            <tr class="border-b border-gray-100 dark:border-gray-800"><td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">RAJ-METRO-TA-0044</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">Mitsubishi Fuso Canter</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">2022</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">4.50</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-500 text-theme-sm dark:text-gray-400">Unassigned</p></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="warning">Idle</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">14 Apr 2026</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">187</p></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">View</button><button class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Edit</button><button class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs text-red-500 hover:bg-red-50 dark:border-red-800">Delete</button></div></td></tr>
                            <tr><td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">BAR-METRO-TA-8870</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">Volvo FMX 440</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">2023</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">14.00</p></td><td class="px-5 py-4 sm:px-6"><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">Tanvir Islam</p></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="success">Active</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">06 May 2026</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">96</p></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">View</button><button class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Edit</button><button class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs text-red-500 hover:bg-red-50 dark:border-red-800">Delete</button></div></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-800">
                <p class="text-sm text-gray-500 dark:text-gray-400">Showing 1 to 6 of 6 results.</p>
                <div class="flex items-center gap-2">
                    <button class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 dark:border-gray-700 dark:text-gray-300">Previous</button>
                    <button class="rounded-lg bg-brand-500 px-3 py-1.5 text-sm text-white">1</button>
                    <button class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 dark:border-gray-700 dark:text-gray-300">Next</button>
                </div>
            </div>
        </x-common.component-card>
    </div>
@endsection
