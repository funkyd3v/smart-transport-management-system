@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Driver Profile" />

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-4">
                    <img src="{{ $driver->getFirstMediaUrl('avatar', 'thumb') ?: $driver->getFirstMediaUrl('avatar') ?: asset('images/user/user-01.jpg') }}" alt="Driver Avatar" class="h-20 w-20 rounded-full object-cover">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $driver->name ?? '-' }}</h2>
                        <p class="mt-1 text-theme-sm text-gray-600 dark:text-gray-300">{{ $driver->mobile_number ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span @class([
                        'inline-flex rounded-full px-2.5 py-1 text-xs font-medium',
                        'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300' => $driver->status === 'active',
                        'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300' => $driver->status === 'inactive',
                    ])>{{ ucfirst((string) $driver->status) }}</span>
                    <div class="inline-flex items-center gap-2">
                        <span @class([
                            'inline-flex rounded-full px-2.5 py-1 text-xs font-medium',
                            'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300' => $driver->is_approved,
                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-300' => ! $driver->is_approved,
                        ])>{{ $driver->is_approved ? 'Approved' : 'Pending Approval' }}</span>

                        @if (! $driver->is_approved)
                            @can('toggleApproval', $driver)
                                <button
                                    type="button"
                                    onclick="window.approveDriver({{ $driver->id }})"
                                    class="inline-flex items-center rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-green-700"
                                >
                                    Approve Driver
                                </button>
                            @else
                                <button
                                    type="button"
                                    disabled
                                    title="Only admin or manager can approve drivers"
                                    class="inline-flex cursor-not-allowed items-center rounded-lg bg-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                >
                                    Approve Driver
                                </button>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Trips</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $stats['total_trips'] }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Trip Value</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">BDT {{ number_format((float) $stats['total_trip_value'], 2) }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Approval Status</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $stats['approval_status'] }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Joining Date</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ optional($driver->joining_date)->format('d M Y') ?? '-' }}</h3>
            </div>
        </div>

        <x-common.component-card title="Driver Details" desc="Core registration and legal information.">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-theme-xs text-gray-500 dark:text-gray-400">License Number</p>
                    <p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $driver->license_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-theme-xs text-gray-500 dark:text-gray-400">NID Number</p>
                    <p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $driver->nid_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-theme-xs text-gray-500 dark:text-gray-400">Driving Type</p>
                    <p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ ucfirst((string) $driver->driving_type) }}</p>
                </div>
                <div>
                    <p class="text-theme-xs text-gray-500 dark:text-gray-400">Rating</p>
                    <p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ number_format((float) ($stats['rating'] ?? 0), 2) }}</p>
                </div>
            </div>
        </x-common.component-card>

        <x-common.component-card title="Recent Trips" desc="Last 10 trips for this driver.">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[980px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Trip Code</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Client</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Load Date</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Value</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Profit</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($driver->trips as $trip)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $trip->trip_code ?? '-' }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $trip->client?->company_name ?? '-' }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ optional($trip->load_date)->format('d M Y') }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">BDT {{ number_format((float) $trip->trip_rate, 2) }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">BDT {{ number_format((float) $trip->profit, 2) }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', (string) $trip->status?->name)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No recent trips found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </x-common.component-card>
    </div>
@endsection

@push('scripts')
    <script>
        window.approveDriver = function(driverId) {
            Swal.fire({
                title: 'Approve Driver?',
                text: 'This driver will become assignable for trips.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, approve',
                cancelButtonText: 'Cancel'
            }).then(async (result) => {
                if (!result.isConfirmed) {
                    return;
                }

                const url = `{{ url('manager/drivers') }}/${driverId}/toggle-approval`;

                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                const data = await response.json();

                if (!response.ok) {
                    Toastify({ text: data.message ?? 'Failed to approve driver.', duration: 4000, gravity: 'top', position: 'right', backgroundColor: '#ef4444', stopOnFocus: true }).showToast();
                    return;
                }

                Toastify({ text: data.message ?? 'Driver approved successfully.', duration: 3000, gravity: 'top', position: 'right', backgroundColor: '#22c55e', stopOnFocus: true }).showToast();
                window.location.reload();
            });
        };
    </script>
@endpush
