@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Driver" />

    <div class="space-y-6">
        <x-common.component-card title="Update Driver" desc="Edit existing driver information.">
            <div class="mb-4">
                <a href="#" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white/90"><- Back to Driver List</a>
            </div>

            <form class="space-y-6">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="space-y-4">
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Full Name</label><input type="text" value="Rafiq Ahmed" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label><input type="email" value="rafiq.ahmed@gmail.com" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Phone Number</label><input type="text" value="01711-223344" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password</label><input type="password" value="password123" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Confirm Password</label><input type="password" value="password123" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                    </div>

                    <div class="space-y-4">
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">License Number</label><input type="text" value="DL-DHK-441223" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">NID Number</label><input type="text" value="1993267812345" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Driving Type</label><select class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"><option selected>Permanent</option><option>Backup</option></select></div>
                        <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Joining Date</label><input type="date" value="2023-01-12" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" /></div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Profile Image</label>
                            <label class="flex h-32 w-full cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 text-center dark:border-gray-700 dark:bg-gray-900/50">
                                <span class="text-xl">^</span>
                                <span class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">Replace profile image</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Current: rafiq-ahmed.jpg</span>
                                <input type="file" class="hidden" />
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                    <button type="button" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">Cancel</button>
                    <button type="button" class="inline-flex items-center justify-center rounded-lg bg-green-600 px-5 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-green-700">Update Driver</button>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection
