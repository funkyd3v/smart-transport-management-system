@extends('driver::layouts.app')

@section('title', 'Driver Dashboard')

@section('content')
    @php
        $currency = static fn (float|int|null $amount): string => '৳ ' . number_format((float) ($amount ?? 0), 2);

        $statusStyles = [
            'created'     => 'bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-300',
            'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
            'completed'   => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300',
            'cancelled'   => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
        ];

        $activeTripStatus = $activeTrip ? strtolower((string) $activeTrip->status?->name) : null;
    @endphp

    {{-- Page Header --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                Welcome back{{ $driver ? ', ' . ($driver->user?->name ?? 'Driver') : '' }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('l, d M Y') }}</p>
        </div>
        @if ($driver)
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium {{ $driver->is_available ? 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                <span class="h-1.5 w-1.5 rounded-full {{ $driver->is_available ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                {{ $driver->is_available ? 'Available' : 'Unavailable' }}
            </span>
        @endif
    </div>

    {{-- KPI Cards --}}
    <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <article class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/15">
                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none">
                    <path d="M5 19L19 5M19 5h-8m8 0v8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Active Trip</p>
            <h3 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">
                {{ $activeTrip ? $activeTrip->trip_code : '—' }}
            </h3>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ $activeTrip ? ($activeTrip->pickup_point . ' → ' . $activeTrip->delivery_point) : 'No active trip' }}
            </p>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 dark:bg-green-500/15">
                <svg class="h-5 w-5 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="none">
                    <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Completed Trips</p>
            <h3 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $stats['completed'] }}</h3>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">All time</p>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-500/15">
                <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Trips This Month</p>
            <h3 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $stats['this_month'] }}</h3>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ now()->format('F Y') }}</p>
        </article>
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Active Trip Card --}}
        <div class="xl:col-span-2">
            <x-common.component-card title="Active Trip" desc="Your current assignment requiring attention.">
                @if ($activeTrip)
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Trip Code</p>
                                <p class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $activeTrip->trip_code }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusStyles[$activeTripStatus] ?? $statusStyles['created'] }}">
                                {{ str_replace('_', ' ', ucfirst((string) $activeTripStatus)) }}
                            </span>
                        </div>

                        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-500/15">
                                    <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="currentColor" stroke-width="1.8"/>
                                        <circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Route</p>
                                    <p class="truncate font-medium text-gray-800 dark:text-white/90">
                                        {{ $activeTrip->pickup_point }} &rarr; {{ $activeTrip->delivery_point }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/[0.03]">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Truck</p>
                                <p class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $activeTrip->truck?->truck_number ?? '—' }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/[0.03]">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Client</p>
                                <p class="mt-1 truncate font-medium text-gray-800 dark:text-white/90">
                                    {{ $activeTrip->client?->company_name ?? $activeTrip->client?->user?->name ?? '—' }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/[0.03]">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Load Date</p>
                                <p class="mt-1 font-medium text-gray-800 dark:text-white/90">
                                    {{ optional($activeTrip->load_date)->format('d M Y') ?? '—' }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/[0.03]">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Goods</p>
                                <p class="mt-1 font-medium text-gray-800 dark:text-white/90">
                                    {{ $activeTrip->goods?->count() ?? 0 }} item(s)
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('driver.trips.show', $activeTrip) }}"
                                class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-medium text-white transition hover:bg-blue-700 sm:flex-none">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                                View Details
                            </a>
                            @if ($activeTripStatus === 'created')
                                <a href="{{ route('driver.trips.show', $activeTrip) }}"
                                    class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-gray-200 px-5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 sm:flex-none">
                                    Start Trip
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">
                            <svg class="h-8 w-8 text-gray-400" viewBox="0 0 24 24" fill="none">
                                <path d="M3 7h18v10H3z" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M7 7V5m10 2V5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <p class="font-medium text-gray-700 dark:text-gray-300">No Active Trip</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You have no ongoing trip right now.</p>
                    </div>
                @endif
            </x-common.component-card>
        </div>

        {{-- Quick Actions --}}
        <div>
            <x-common.component-card title="Quick Actions" desc="Jump to a common task fast.">
                <div class="flex flex-col gap-3">
                    <a href="{{ route('driver.trips.index') }}"
                        class="flex items-center gap-4 rounded-xl border border-gray-100 p-4 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/15">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none">
                                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="1.8"/>
                                <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white/90">My Trips</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">View all assigned trips</p>
                        </div>
                    </a>

                    @if ($activeTrip)
                        <a href="{{ route('driver.trips.show', ['trip' => $activeTrip, 'modal' => 'expense']) }}"
                            class="flex items-center gap-4 rounded-xl border border-gray-100 p-4 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-500/15">
                                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" viewBox="0 0 24 24" fill="none">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M14 2v6h6M9 13h6M9 17h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white/90">Log Expense</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Record trip expense</p>
                            </div>
                        </a>

                        <a href="{{ route('driver.trips.show', ['trip' => $activeTrip, 'modal' => 'reload']) }}"
                            class="flex items-center gap-4 rounded-xl border border-gray-100 p-4 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-500/15">
                                <svg class="h-5 w-5 text-slate-600 dark:text-slate-400" viewBox="0 0 24 24" fill="none">
                                    <path d="M1 4v6h6M23 20v-6h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10M23 14l-4.64 4.36A9 9 0 0 1 3.51 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white/90">Add Reload</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Log a reload event</p>
                            </div>
                        </a>
                    @endif

                    <a href="{{ route('driver.profile') }}"
                        class="flex items-center gap-4 rounded-xl border border-gray-100 p-4 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-500/15">
                            <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M5.5 21a6.5 6.5 0 0 1 13 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white/90">My Profile</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">View and update profile</p>
                        </div>
                    </a>
                </div>
            </x-common.component-card>
        </div>
    </div>

    {{-- Recent Trips --}}
    <div class="mt-6">
        <x-common.component-card title="Recent Trips" desc="Your last 5 assigned trips.">
            @if ($recentTrips->isNotEmpty())
                {{-- Desktop Table --}}
                <div class="hidden overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800 md:block">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Trip Code</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Route</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Truck</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Load Date</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($recentTrips as $trip)
                                @php $ts = strtolower((string) $trip->status?->name); @endphp
                                <tr class="transition hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td class="px-5 py-4 font-medium text-gray-800 dark:text-white/90">{{ $trip->trip_code }}</td>
                                    <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $trip->pickup_point }} &rarr; {{ $trip->delivery_point }}</td>
                                    <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $trip->truck?->truck_number ?? '—' }}</td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400">{{ optional($trip->load_date)->format('d M Y') ?? '—' }}</td>
                                    <td class="px-5 py-4">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles[$ts] ?? $statusStyles['created'] }}">
                                            {{ str_replace('_', ' ', ucfirst($ts ?: 'Created')) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <a href="{{ route('driver.trips.show', $trip) }}" class="text-xs font-medium text-blue-600 hover:underline dark:text-blue-400">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="grid gap-3 md:hidden">
                    @foreach ($recentTrips as $trip)
                        @php $ts = strtolower((string) $trip->status?->name); @endphp
                        <a href="{{ route('driver.trips.show', $trip) }}" class="block rounded-xl border border-gray-200 p-4 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-white/90">{{ $trip->trip_code }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $trip->pickup_point }} &rarr; {{ $trip->delivery_point }}</p>
                                </div>
                                <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles[$ts] ?? $statusStyles['created'] }}">
                                    {{ str_replace('_', ' ', ucfirst($ts ?: 'Created')) }}
                                </span>
                            </div>
                            <div class="mt-3 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $trip->truck?->truck_number ?? '—' }}</span>
                                <span>&bull;</span>
                                <span>{{ optional($trip->load_date)->format('d M Y') ?? '—' }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="pt-2 text-center">
                    <a href="{{ route('driver.trips.index') }}" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">
                        View all trips &rarr;
                    </a>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">
                        <svg class="h-7 w-7 text-gray-400" viewBox="0 0 24 24" fill="none">
                            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <p class="font-medium text-gray-600 dark:text-gray-400">No trips yet</p>
                    <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">Your assigned trips will appear here.</p>
                </div>
            @endif
        </x-common.component-card>
    </div>
@endsection
