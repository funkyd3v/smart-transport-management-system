@extends('driver::layouts.app')

@section('content')
    @php
        $statusFilter = (string) request('status', 'all');

        $filterTabs = [
            'all' => 'All',
            'created' => 'Pending',
            'in_progress' => 'Active',
            'completed' => 'Completed',
        ];

        $statusStyles = [
            'created' => 'bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-300',
            'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
            'completed' => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300',
            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
            'completion_pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
        ];
    @endphp

    <x-common.page-breadcrumb pageTitle="Driver Trips" />

    <div class="space-y-6" x-data="driverTripIndex()">
        <x-common.component-card title="Trip Management" desc="View your assigned trips and move active trips forward.">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-4 dark:border-gray-800">
                @foreach ($filterTabs as $value => $label)
                    <a href="{{ route('driver.trips.index', array_filter(['status' => $value !== 'all' ? $value : null])) }}"
                        class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium transition {{ $statusFilter === $value ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="grid gap-4 md:hidden">
                @forelse ($trips as $trip)
                    @php
                        $tripStatus = strtolower((string) $trip->status?->name);
                        $isCompletionPending = $tripStatus === 'in_progress' && $trip->completion_requested_at !== null;
                        $displayStatus = $isCompletionPending ? 'completion_pending' : $tripStatus;
                    @endphp
                    <div class="rounded-2xl border border-gray-200 p-4 dark:border-gray-800">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $trip->trip_code }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $trip->pickup_point }} &rarr; {{ $trip->delivery_point }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusStyles[$displayStatus] ?? $statusStyles['created'] }}">
                                {{ $isCompletionPending ? 'Completion Pending' : ($trip->status?->name ? str_replace('_', ' ', ucfirst($trip->status->name)) : 'Created') }}
                            </span>
                        </div>

                        <div class="mt-4 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                            <p><span class="font-medium text-gray-700 dark:text-gray-200">Load:</span> {{ optional($trip->load_date)->format('d M Y, h:i A') }}</p>
                            <p><span class="font-medium text-gray-700 dark:text-gray-200">Truck:</span> {{ $trip->truck?->truck_number ?? '-' }}</p>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('driver.trips.show', $trip) }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">View Details</a>

                            @if ($tripStatus === 'created')
                                <button type="button"
                                    @click="startTrip('{{ route('driver.trips.update-status', $trip) }}', '{{ $trip->trip_code }}')"
                                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-blue-700">
                                    Start Trip
                                </button>
                            @elseif ($tripStatus === 'in_progress' && ! $isCompletionPending)
                                <a href="{{ route('driver.trips.show', ['trip' => $trip, 'modal' => 'expense']) }}"
                                    class="inline-flex items-center rounded-lg bg-amber-500 px-3 py-2 text-xs font-medium text-white transition hover:bg-amber-600">
                                    Add Expense
                                </a>
                                <a href="{{ route('driver.trips.show', ['trip' => $trip, 'modal' => 'reload']) }}"
                                    class="inline-flex items-center rounded-lg bg-slate-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-slate-700">
                                    Add Reload
                                </a>
                                <button type="button"
                                    @click="completeTrip('{{ route('driver.trips.update-status', $trip) }}', '{{ $trip->trip_code }}')"
                                    class="inline-flex items-center rounded-lg bg-green-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-green-700">
                                    Mark Complete
                                </button>
                            @elseif ($isCompletionPending)
                                <span class="inline-flex items-center rounded-lg bg-amber-100 px-3 py-2 text-xs font-medium text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">Waiting for approval</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 p-10 text-center dark:border-gray-700">
                        <p class="text-base font-medium text-gray-700 dark:text-gray-200">No trips assigned yet.</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Assigned trips will appear here once the manager schedules them.</p>
                    </div>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800 md:block">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-white/[0.03]">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Trip</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Route</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Load Date</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Truck</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-transparent">
                        @forelse ($trips as $trip)
                            @php
                                $tripStatus = strtolower((string) $trip->status?->name);
                                $isCompletionPending = $tripStatus === 'in_progress' && $trip->completion_requested_at !== null;
                                $displayStatus = $isCompletionPending ? 'completion_pending' : $tripStatus;
                            @endphp
                            <tr>
                                <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">{{ $trip->trip_code }}</td>
                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $trip->pickup_point }} &rarr; {{ $trip->delivery_point }}</td>
                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ optional($trip->load_date)->format('d M Y, h:i A') }}</td>
                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $trip->truck?->truck_number ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm">
                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusStyles[$displayStatus] ?? $statusStyles['created'] }}">
                                        {{ $isCompletionPending ? 'Completion Pending' : ($trip->status?->name ? str_replace('_', ' ', ucfirst($trip->status->name)) : 'Created') }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('driver.trips.show', $trip) }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">View Details</a>

                                        @if ($tripStatus === 'created')
                                            <button type="button"
                                                @click="startTrip('{{ route('driver.trips.update-status', $trip) }}', '{{ $trip->trip_code }}')"
                                                class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-blue-700">
                                                Start Trip
                                            </button>
                                        @elseif ($tripStatus === 'in_progress' && ! $isCompletionPending)
                                            <a href="{{ route('driver.trips.show', ['trip' => $trip, 'modal' => 'expense']) }}"
                                                class="inline-flex items-center rounded-lg bg-amber-500 px-3 py-2 text-xs font-medium text-white transition hover:bg-amber-600">
                                                Add Expense
                                            </a>
                                            <a href="{{ route('driver.trips.show', ['trip' => $trip, 'modal' => 'reload']) }}"
                                                class="inline-flex items-center rounded-lg bg-slate-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-slate-700">
                                                Add Reload
                                            </a>
                                            <button type="button"
                                                @click="completeTrip('{{ route('driver.trips.update-status', $trip) }}', '{{ $trip->trip_code }}')"
                                                class="inline-flex items-center rounded-lg bg-green-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-green-700">
                                                Mark Complete
                                            </button>
                                        @elseif ($isCompletionPending)
                                            <span class="inline-flex items-center rounded-lg bg-amber-100 px-3 py-2 text-xs font-medium text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">Waiting for approval</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No trips assigned yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $trips->links() }}
            </div>
        </x-common.component-card>
    </div>

    @push('scripts')
        <script>
            function driverTripIndex() {
                return {
                    async startTrip(url, tripCode) {
                        const result = await Swal.fire({
                            title: 'Start Trip?',
                            text: 'Are you sure you want to start trip ' + tripCode + '? This cannot be undone.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#2563eb',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Yes, start it',
                        });

                        if (! result.isConfirmed) {
                            return;
                        }

                        const response = await fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ status: 'in_progress' }),
                        });

                        const data = await response.json();

                        if (! response.ok) {
                            Toastify({
                                text: data.message ?? 'Failed to start trip.',
                                duration: 4000,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: '#ef4444',
                                stopOnFocus: true,
                            }).showToast();

                            return;
                        }

                        Toastify({
                            text: data.message ?? 'Trip started successfully.',
                            duration: 2500,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: '#22c55e',
                            stopOnFocus: true,
                        }).showToast();

                        window.location.reload();
                    },

                    async completeTrip(url, tripCode) {
                        const result = await Swal.fire({
                            title: 'Mark Trip Complete?',
                            text: 'Mark trip ' + tripCode + ' as complete? This sends a completion request to manager/admin for approval.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#16a34a',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Yes, mark complete',
                        });

                        if (! result.isConfirmed) {
                            return;
                        }

                        const response = await fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ status: 'completed' }),
                        });

                        const data = await response.json();

                        if (! response.ok) {
                            Toastify({
                                text: data.message ?? 'Failed to complete trip.',
                                duration: 4000,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: '#ef4444',
                                stopOnFocus: true,
                            }).showToast();

                            return;
                        }

                        Toastify({
                            text: data.message ?? 'Completion request sent.',
                            duration: 2500,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: '#22c55e',
                            stopOnFocus: true,
                        }).showToast();

                        window.location.reload();
                    },
                };
            }
        </script>
    @endpush
@endsection