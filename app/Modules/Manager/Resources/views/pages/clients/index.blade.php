@extends('manager::layouts.app')

@section('content')
    @php
        $clients = [
            ['id' => 'CL-24001', 'name' => 'Rahim Traders', 'email' => 'rahim.traders@gmail.com', 'initials' => 'RT', 'avatarBg' => 'bg-blue-100', 'avatarColor' => 'text-blue-600', 'category' => 'Port Client', 'value' => '$18,500', 'shipment' => '2026-04-28', 'status' => 'Active'],
            ['id' => 'CL-24002', 'name' => 'Bashundhara Group', 'email' => 'transport@bashundhara.com', 'initials' => 'BG', 'avatarBg' => 'bg-[#fdf2fa]', 'avatarColor' => 'text-[#dd2590]', 'category' => 'Mega Project', 'value' => '$64,000', 'shipment' => '2026-05-02', 'status' => 'Active'],
            ['id' => 'CL-24003', 'name' => 'Meghna Cement', 'email' => 'logistics@meghnacement.com', 'initials' => 'MC', 'avatarBg' => 'bg-[#f0f9ff]', 'avatarColor' => 'text-[#0086c9]', 'category' => 'Contractual', 'value' => '$27,300', 'shipment' => '2026-04-21', 'status' => 'Pending'],
            ['id' => 'CL-24004', 'name' => 'Padma Bridge Authority', 'email' => 'ops@padmabridge.gov.bd', 'initials' => 'PB', 'avatarBg' => 'bg-[#fff6ed]', 'avatarColor' => 'text-[#ec4a0a]', 'category' => 'Mega Project', 'value' => '$92,800', 'shipment' => '2026-05-04', 'status' => 'Active'],
            ['id' => 'CL-24005', 'name' => 'Karim Shipping', 'email' => 'dispatch@karimshipping.com', 'initials' => 'KS', 'avatarBg' => 'bg-green-50', 'avatarColor' => 'text-green-700', 'category' => 'Port Client', 'value' => '$14,200', 'shipment' => '2026-04-11', 'status' => 'Inactive'],
            ['id' => 'CL-24006', 'name' => 'Navana Steel', 'email' => 'fleet@navanasteel.com', 'initials' => 'NS', 'avatarBg' => 'bg-purple-100', 'avatarColor' => 'text-purple-700', 'category' => 'Contractual', 'value' => '$39,450', 'shipment' => '2026-05-01', 'status' => 'Pending'],
        ];
    @endphp

    <x-common.page-breadcrumb pageTitle="Client Management" />

    <div class="space-y-6">
        <x-common.component-card title="Client List" desc="Manage all transport business clients from one place.">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3 xl:w-3/4">
                    <div>
                        <input type="text" placeholder="Search by client name or contact"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>
                    <div>
                        <select
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                            <option>All Categories</option>
                            <option>Port Client</option>
                            <option>Contractual Client</option>
                            <option>Mega Project Client</option>
                        </select>
                    </div>
                    <div>
                        <select
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                            <option>All Status</option>
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div>
                </div>

                <a href="{{ route('manager.clients.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    Add New Client
                </a>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-white/[0.05] dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto">
                    <table class="w-full min-w-[1024px]">
                        <thead class="border-y border-gray-100 bg-gray-50 dark:border-white/[0.05] dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-start font-medium text-gray-500 text-theme-xs dark:text-gray-400">Client ID</th>
                                <th class="px-6 py-3 text-start font-medium text-gray-500 text-theme-xs dark:text-gray-400">Client</th>
                                <th class="px-6 py-3 text-start font-medium text-gray-500 text-theme-xs dark:text-gray-400">Category</th>
                                <th class="px-6 py-3 text-start font-medium text-gray-500 text-theme-xs dark:text-gray-400">Deal Value</th>
                                <th class="px-6 py-3 text-start font-medium text-gray-500 text-theme-xs dark:text-gray-400">Last Shipment</th>
                                <th class="px-6 py-3 text-start font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</th>
                                <th class="px-6 py-3 text-start font-medium text-gray-500 text-theme-xs dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                <tr class="border-b border-gray-100 dark:border-white/[0.05]">
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-gray-700 text-theme-sm dark:text-gray-300">{{ $client['id'] }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-medium {{ $client['avatarBg'] }} {{ $client['avatarColor'] }}">
                                                {{ $client['initials'] }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $client['name'] }}</p>
                                                <p class="text-gray-500 text-theme-xs dark:text-gray-400">{{ $client['email'] }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-gray-700 text-theme-sm dark:text-gray-400">{{ $client['category'] }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-gray-700 text-theme-sm dark:text-gray-400">{{ $client['value'] }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-gray-700 text-theme-sm dark:text-gray-400">{{ $client['shipment'] }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($client['status'] === 'Active')
                                            <x-ui.badge variant="light" color="success">Active</x-ui.badge>
                                        @elseif ($client['status'] === 'Pending')
                                            <x-ui.badge variant="light" color="warning">Pending</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="light" color="error">Inactive</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-common.table-dropdown>
                                            <x-slot name="button">
                                                <button type="button" aria-haspopup="true" aria-expanded="false" class="text-gray-500 dark:text-gray-400">
                                                    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99902 10.245C6.96552 10.245 7.74902 11.0285 7.74902 11.995V12.005C7.74902 12.9715 6.96552 13.755 5.99902 13.755C5.03253 13.755 4.24902 12.9715 4.24902 12.005V11.995C4.24902 11.0285 5.03253 10.245 5.99902 10.245ZM17.999 10.245C18.9655 10.245 19.749 11.0285 19.749 11.995V12.005C19.749 12.9715 18.9655 13.755 17.999 13.755C17.0325 13.755 16.249 12.9715 16.249 12.005V11.995C16.249 11.0285 17.0325 10.245 17.999 10.245ZM13.749 11.995C13.749 11.0285 12.9655 10.245 11.999 10.245C11.0325 10.245 10.249 11.0285 10.249 11.995V12.005C10.249 12.9715 11.0325 13.755 11.999 13.755C12.9655 13.755 13.749 12.9715 13.749 12.005V11.995Z" fill="currentColor" />
                                                    </svg>
                                                </button>
                                            </x-slot>
                                            <x-slot name="content">
                                                <a href="{{ route('manager.clients.show') }}" class="flex w-full rounded-lg px-3 py-2 text-left font-medium text-gray-500 text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">View</a>
                                                <a href="{{ route('manager.clients.edit') }}" class="flex w-full rounded-lg px-3 py-2 text-left font-medium text-gray-500 text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">Edit</a>
                                                <button class="flex w-full rounded-lg px-3 py-2 text-left font-medium text-red-500 text-theme-xs hover:bg-red-50 dark:hover:bg-red-500/10">Deactivate</button>
                                            </x-slot>
                                        </x-common.table-dropdown>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-common.component-card>
    </div>
@endsection
