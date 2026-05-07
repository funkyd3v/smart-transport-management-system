@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Truck" />

    <div class="space-y-6">
        <x-common.component-card title="Update Truck" desc="Edit truck information, assignment, and operational status.">
            <div class="mb-4">
                <a href="{{ route('manager.trucks.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white/90"><- Back to Truck List</a>
            </div>

            <form class="space-y-6">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="space-y-4">
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Truck Number</label><input type="text" value="DHAKA-METRO-TA-1123" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Brand</label><input type="text" value="Isuzu" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Model</label><input type="text" value="FVR 34" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Manufacturing Year</label><input type="number" value="2021" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                    </div>
                    <div class="space-y-4">
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Capacity (tons)</label><input type="text" value="8.50" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label><select class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"><option selected>Active</option><option>Under Maintenance</option><option>Out of Service</option><option>Idle</option></select></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Assigned Driver</label><select class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"><option>Unassigned</option><option selected>Rafiq Ahmed</option><option>Masud Karim</option><option>Shahidul Khan</option><option>Noman Jamil</option><option>Tanvir Islam</option><option>Faruk Uddin</option></select></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Last Service Date</label><input type="date" value="2026-05-04" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                    </div>
                </div>

                <p class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700 dark:border-blue-800/40 dark:bg-blue-900/20 dark:text-blue-300">Total trips will be tracked automatically by the system.</p>

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                    <button type="button" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">Cancel</button>
                    <button type="button" class="inline-flex items-center justify-center rounded-lg bg-green-600 px-5 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-green-700">Update Truck</button>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection
