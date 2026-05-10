@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Client Profile" />

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $client->company_name ?? $client->user?->name }}</h2>
                <span
                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                    @class([
                        'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300' => $client->category?->name === 'port',
                        'bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300' => $client->category?->name === 'contractual',
                        'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300' => $client->category?->name === 'mega_project',
                    ])
                >
                    {{ $client->category?->name ?? 'Unknown' }}
                </span>
                <span
                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize"
                    @class([
                        'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300' => true,
                    ])
                >
                    Active
                </span>
            </div>
            <p class="mt-3 text-theme-sm text-gray-600 dark:text-gray-300">Contact: {{ $client->user?->phone ?? '-' }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Trips</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $stats['total_trips'] }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Business Amount</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">BDT {{ number_format($stats['total_business_amount'], 2) }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Due</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">BDT {{ number_format($stats['total_due'], 2) }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Payments Made</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $stats['payment_count'] }}</h3>
            </div>
        </div>

        @if ($client->category?->name !== 'port')
            <x-common.component-card title="Project Details" desc="Contract/project metadata for this client.">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-theme-xs text-gray-500 dark:text-gray-400">Project Name</p>
                        <p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $client->project_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-theme-xs text-gray-500 dark:text-gray-400">Agreement Number</p>
                        <p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $client->agreement_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-theme-xs text-gray-500 dark:text-gray-400">Project Value</p>
                        <p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">BDT {{ number_format((float) ($client->project_value ?? 0), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-theme-xs text-gray-500 dark:text-gray-400">Target Finishing Date</p>
                        <p @class([
                            'mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90',
                            'text-red-500 dark:text-red-300' => filled($client->project_end_date) && (string) $client->project_end_date < now()->toDateString(),
                        ])>
                            {{ filled($client->project_end_date) ? \Illuminate\Support\Carbon::parse((string) $client->project_end_date)->format('d M Y') : '-' }}
                        </p>
                    </div>
                </div>
            </x-common.component-card>
        @endif

        <x-common.component-card title="Recent Trips" desc="Last 10 trips for this client.">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1080px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Trip Date</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Route</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Amount</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Due</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($client->trips as $trip)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ optional($trip->load_date)->format('d M Y') }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $trip->pickup_point }} → {{ $trip->delivery_point }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">BDT {{ number_format((float) $trip->trip_rate, 2) }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">BDT {{ number_format((float) $trip->due_amount, 2) }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', (string) $trip->status?->name)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No recent trips found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </x-common.component-card>
    </div>
@endsection
