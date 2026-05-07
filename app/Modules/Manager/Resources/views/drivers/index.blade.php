@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Driver Management" />

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Drivers</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">124</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Available Drivers</p>
                <h3 class="mt-2 text-2xl font-semibold text-green-600 dark:text-green-400">97</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Permanent Drivers</p>
                <h3 class="mt-2 text-2xl font-semibold text-blue-600 dark:text-blue-400">83</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Backup Drivers</p>
                <h3 class="mt-2 text-2xl font-semibold text-yellow-600 dark:text-yellow-400">41</h3>
            </div>
        </div>

        <x-common.component-card title="Driver List" desc="Manage all drivers and their current availability.">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3 xl:w-3/4">
                    <input type="text" placeholder="Search by name or license"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    <select
                        class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                        <option>All Driving Types</option>
                        <option>Permanent</option>
                        <option>Backup</option>
                    </select>
                    <select
                        class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                        <option>All Status</option>
                        <option>Available</option>
                        <option>Unavailable</option>
                    </select>
                </div>

                <a href="#"
                    class="inline-flex items-center justify-center rounded-lg bg-green-600 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-green-700">
                    Add New Driver
                </a>
            </div>

            <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1280px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Driver</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">License Number</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">NID Number</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Driving Type</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Joining Date</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Total Trips</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Rating</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p></th>
                                <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Actions</p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-3"><div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700">RA</div><div><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">Rafiq Ahmed</p><p class="text-gray-500 text-theme-xs dark:text-gray-400">rafiq.ahmed@gmail.com</p></div></div></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">DL-DHK-441223</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">1993267812345</p></td>
                                <td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="primary">Permanent</x-ui.badge></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">12 Jan 2023</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">278</p></td>
                                <td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-1 text-yellow-500"><span>*</span><span>*</span><span>*</span><span>*</span><span class="text-gray-300">*</span><span class="ml-1 text-gray-600 text-theme-xs dark:text-gray-400">4.0</span></div></td>
                                <td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="success">Available</x-ui.badge></td>
                                <td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">V</button><button class="rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">E</button><button class="rounded-lg border border-red-200 p-2 text-red-500 hover:bg-red-50 dark:border-red-800">D</button></div></td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-3"><div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100 text-sm font-semibold text-yellow-700">MK</div><div><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">Masud Karim</p><p class="text-gray-500 text-theme-xs dark:text-gray-400">masud.karim@gmail.com</p></div></div></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">DL-CTG-883201</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">1989156723421</p></td>
                                <td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="warning">Backup</x-ui.badge></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">05 Mar 2024</p></td>
                                <td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">143</p></td>
                                <td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-1 text-yellow-500"><span>*</span><span>*</span><span>*</span><span class="text-gray-300">*</span><span class="text-gray-300">*</span><span class="ml-1 text-gray-600 text-theme-xs dark:text-gray-400">3.0</span></div></td>
                                <td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="error">Unavailable</x-ui.badge></td>
                                <td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">V</button><button class="rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">E</button><button class="rounded-lg border border-red-200 p-2 text-red-500 hover:bg-red-50 dark:border-red-800">D</button></div></td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-800"><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-3"><div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-sm font-semibold text-green-700">SK</div><div><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">Shahidul Khan</p><p class="text-gray-500 text-theme-xs dark:text-gray-400">shahidul.khan@gmail.com</p></div></div></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">DL-KHU-556778</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">1978845612309</p></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="primary">Permanent</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">21 Aug 2022</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">352</p></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-1 text-yellow-500"><span>*</span><span>*</span><span>*</span><span>*</span><span>*</span><span class="ml-1 text-gray-600 text-theme-xs dark:text-gray-400">5.0</span></div></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="success">Available</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">V</button><button class="rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">E</button><button class="rounded-lg border border-red-200 p-2 text-red-500 hover:bg-red-50 dark:border-red-800">D</button></div></td></tr>
                            <tr class="border-b border-gray-100 dark:border-gray-800"><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-3"><div class="flex h-10 w-10 items-center justify-center rounded-full bg-pink-100 text-sm font-semibold text-pink-700">NJ</div><div><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">Noman Jamil</p><p class="text-gray-500 text-theme-xs dark:text-gray-400">noman.jamil@gmail.com</p></div></div></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">DL-RAJ-112239</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">1991043356120</p></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="warning">Backup</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">02 Nov 2023</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">119</p></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-1 text-yellow-500"><span>*</span><span>*</span><span>*</span><span>*</span><span class="text-gray-300">*</span><span class="ml-1 text-gray-600 text-theme-xs dark:text-gray-400">4.0</span></div></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="error">Unavailable</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">V</button><button class="rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">E</button><button class="rounded-lg border border-red-200 p-2 text-red-500 hover:bg-red-50 dark:border-red-800">D</button></div></td></tr>
                            <tr class="border-b border-gray-100 dark:border-gray-800"><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-3"><div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">TI</div><div><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">Tanvir Islam</p><p class="text-gray-500 text-theme-xs dark:text-gray-400">tanvir.islam@gmail.com</p></div></div></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">DL-SYL-778122</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">1987345623998</p></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="primary">Permanent</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">10 Feb 2021</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">501</p></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-1 text-yellow-500"><span>*</span><span>*</span><span>*</span><span>*</span><span class="text-gray-300">*</span><span class="ml-1 text-gray-600 text-theme-xs dark:text-gray-400">4.0</span></div></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="success">Available</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">V</button><button class="rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">E</button><button class="rounded-lg border border-red-200 p-2 text-red-500 hover:bg-red-50 dark:border-red-800">D</button></div></td></tr>
                            <tr><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-3"><div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-sm font-semibold text-orange-700">FU</div><div><p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">Faruk Uddin</p><p class="text-gray-500 text-theme-xs dark:text-gray-400">faruk.uddin@gmail.com</p></div></div></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">DL-BAR-102200</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">1995123489012</p></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="warning">Backup</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">18 Jul 2024</p></td><td class="px-5 py-4 sm:px-6"><p class="text-gray-600 text-theme-sm dark:text-gray-300">88</p></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-1 text-yellow-500"><span>*</span><span>*</span><span>*</span><span class="text-gray-300">*</span><span class="text-gray-300">*</span><span class="ml-1 text-gray-600 text-theme-xs dark:text-gray-400">3.0</span></div></td><td class="px-5 py-4 sm:px-6"><x-ui.badge variant="light" color="success">Available</x-ui.badge></td><td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">V</button><button class="rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">E</button><button class="rounded-lg border border-red-200 p-2 text-red-500 hover:bg-red-50 dark:border-red-800">D</button></div></td></tr>
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
