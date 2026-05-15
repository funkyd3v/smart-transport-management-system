@extends('manager::layouts.app')

@section('content')
    @php
        $statusRaw = $truck->status ?? null;
        $statusValue = is_object($statusRaw)
            ? strtolower(str_replace(' ', '_', (string) ($statusRaw->name ?? 'idle')))
            : (string) $statusRaw;

        if (! in_array($statusValue, ['idle', 'on_trip', 'under_workshop'], true)) {
            $statusValue = 'idle';
        }

        $isOnTrip = $statusValue === 'on_trip';
    @endphp

    <x-common.page-breadcrumb pageTitle="Truck Profile" />

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $truck->truck_number }}</h2>
                    <p class="mt-1 text-theme-sm text-gray-600 dark:text-gray-300">{{ $truck->truck_type ?? $truck->model ?? '-' }} • {{ number_format((float) ($truck->capacity ?? $truck->capacity_tons ?? 0), 2) }} tons</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span @class([
                        'inline-flex rounded-full px-2.5 py-1 text-xs font-medium',
                        'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300' => $statusValue === 'idle',
                        'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300' => $statusValue === 'on_trip',
                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-300' => $statusValue === 'under_workshop',
                    ])>{{ str_replace('_', ' ', ucfirst($statusValue)) }}</span>

                    @can('updateStatus', $truck)
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                onclick="window.updateTruckStatusFromProfile({{ $truck->id }}, 'idle', {{ $isOnTrip ? 'true' : 'false' }})"
                                @disabled($isOnTrip)
                                title="{{ $isOnTrip ? 'Truck is currently on a trip' : 'Set status to idle' }}"
                                class="rounded-lg border px-3 py-1.5 text-xs {{ $isOnTrip ? 'cursor-not-allowed border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500' : 'border-green-200 text-green-700 hover:bg-green-50 dark:border-green-500/40 dark:text-green-300 dark:hover:bg-green-500/10' }}"
                            >
                                Idle
                            </button>
                            <button
                                type="button"
                                onclick="window.updateTruckStatusFromProfile({{ $truck->id }}, 'under_workshop', {{ $isOnTrip ? 'true' : 'false' }})"
                                @disabled($isOnTrip)
                                title="{{ $isOnTrip ? 'Truck is currently on a trip' : 'Set status to under workshop' }}"
                                class="rounded-lg border px-3 py-1.5 text-xs {{ $isOnTrip ? 'cursor-not-allowed border-gray-200 text-gray-400 dark:border-gray-700 dark:text-gray-500' : 'border-yellow-200 text-yellow-700 hover:bg-yellow-50 dark:border-yellow-500/40 dark:text-yellow-300 dark:hover:bg-yellow-500/10' }}"
                            >
                                Under Workshop
                            </button>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                disabled
                                title="You are not allowed to change status for this truck"
                                class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500"
                            >
                                Idle
                            </button>
                            <button
                                type="button"
                                disabled
                                title="You are not allowed to change status for this truck"
                                class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500"
                            >
                                Under Workshop
                            </button>
                        </div>
                    @endcan
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Trips</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $stats['total_trips'] }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Income</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">BDT {{ number_format((float) $stats['total_income'], 2) }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Expense</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">BDT {{ number_format((float) $stats['total_expense'], 2) }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Profit</p>
                <h3 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">BDT {{ number_format((float) $stats['total_profit'], 2) }}</h3>
            </div>
        </div>

        <x-common.component-card title="Truck Details" desc="Core truck registration and operational status.">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-theme-xs text-gray-500 dark:text-gray-400">Truck Number</p>
                    <p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $truck->truck_number }}</p>
                </div>
                <div>
                    <p class="text-theme-xs text-gray-500 dark:text-gray-400">Truck Type</p>
                    <p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $truck->truck_type ?? $truck->model ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-theme-xs text-gray-500 dark:text-gray-400">Capacity</p>
                    <p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ number_format((float) ($truck->capacity ?? $truck->capacity_tons ?? 0), 2) }} tons</p>
                </div>
                <div>
                    <p class="text-theme-xs text-gray-500 dark:text-gray-400">Current Status</p>
                    <p class="mt-1 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ str_replace('_', ' ', ucfirst($statusValue)) }}</p>
                </div>
            </div>
        </x-common.component-card>

        <x-common.component-card title="Recent Trips" desc="Last 10 trips associated with this truck.">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[980px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Trip Date</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Client</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Route</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Amount</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($truck->trips as $trip)
                                @php
                                    $routeText = $trip->route_description;
                                    if (! filled($routeText)) {
                                        $routeText = trim(((string) ($trip->pickup_point ?? '')).' -> '.((string) ($trip->delivery_point ?? '')), ' ->');
                                    }
                                @endphp
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ optional($trip->load_date)->format('d M Y') }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $trip->client?->company_name ?? '-' }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">{{ $routeText !== '' ? $routeText : '-' }}</td>
                                    <td class="px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-300">BDT {{ number_format((float) $trip->trip_rate, 2) }}</td>
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

@push('scripts')
    <script>
        window.updateTruckStatusFromProfile = async function(truckId, status, isOnTrip) {
            if (isOnTrip) {
                Toastify({ text: 'Truck is currently on a trip.', duration: 4000, gravity: 'top', position: 'right', backgroundColor: '#ef4444', stopOnFocus: true }).showToast();
                return;
            }

            const url = `{{ url('manager/trucks') }}/${truckId}/status`;
            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ status }),
            });

            const data = await response.json();

            if (!response.ok) {
                Toastify({ text: data.message ?? 'Failed to update status.', duration: 4000, gravity: 'top', position: 'right', backgroundColor: '#ef4444', stopOnFocus: true }).showToast();
                return;
            }

            Toastify({ text: data.message ?? 'Truck status updated successfully.', duration: 3000, gravity: 'top', position: 'right', backgroundColor: '#22c55e', stopOnFocus: true }).showToast();
            window.location.reload();
        };
    </script>
@endpush
