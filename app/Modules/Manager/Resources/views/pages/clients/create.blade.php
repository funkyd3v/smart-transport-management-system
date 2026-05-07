@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Add New Client" />

    <div class="space-y-6" x-data="{ category: 'port', isActive: true }">
        <x-common.component-card title="Client Registration Form" desc="Register a new transport business client.">
            <form class="space-y-6">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Client Name</label>
                        <input type="text" value=""
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            placeholder="Enter client name" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Contact Number</label>
                        <input type="text" value=""
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            placeholder="01XXXXXXXXX" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Category</label>
                        <select x-model="category"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                            <option value="port">Port Client</option>
                            <option value="contractual">Contractual Client</option>
                            <option value="mega_project">Mega Project Client</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                        <label class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                            <div class="relative" x-data>
                                <input type="checkbox" class="sr-only" x-model="isActive" />
                                <div class="block h-6 w-11 rounded-full" :class="isActive ? 'bg-brand-500 dark:bg-brand-500' : 'bg-gray-200 dark:bg-white/10'"></div>
                                <div class="shadow-theme-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white duration-300 ease-linear"
                                    :class="isActive ? 'translate-x-full' : 'translate-x-0'"></div>
                            </div>
                            <span x-text="isActive ? 'Active' : 'Inactive'"></span>
                        </label>
                    </div>

                    <div x-show="category === 'contractual' || category === 'mega_project'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Project Name</label>
                        <input type="text"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            placeholder="Enter project name" />
                    </div>

                    <div x-show="category === 'contractual' || category === 'mega_project'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Project Agreement Number</label>
                        <input type="text"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            placeholder="Enter agreement number" />
                    </div>

                    <div x-show="category === 'contractual' || category === 'mega_project'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Project Value in BDT</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400">BDT</span>
                            <input type="number"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-14 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                placeholder="0" />
                        </div>
                    </div>

                    <div x-show="category === 'contractual' || category === 'mega_project'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Target Finishing Date</label>
                        <input type="date"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                    </div>

                    <div x-show="category === 'port'" x-cloak>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Project Type</label>
                        <input type="text"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            placeholder="Import or Export" />
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                    <button type="button"
                        class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                        Save Client
                    </button>
                    <a href="{{ route('manager.clients.index') }}"
                        class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-3.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                        Cancel
                    </a>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection
