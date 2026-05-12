@extends('manager::layouts.app')

@section('title', 'Manager Dashboard')

@section('content')
    @php
        $currency = static fn (float|int|null $amount): string => '৳ '.number_format((float) ($amount ?? 0), 2);

        $activeTripsData = collect($activeTrips ?? []);
        $topDueClientsData = collect($topDueClients ?? []);
        $monthlyFinancialsData = collect($monthlyFinancials ?? []);
        $paymentMethodBreakdownData = collect($paymentMethodBreakdown ?? []);
        $driverPerformanceData = collect($driverPerformance ?? []);
        $spareInventoryData = collect($spareInventorySummary ?? []);
        $recentCashbookData = collect($recentCashbook ?? []);

        $routeOrNull = static function (array $routeCandidates): ?string {
            foreach ($routeCandidates as $routeName) {
                if (\Illuminate\Support\Facades\Route::has($routeName)) {
                    return route($routeName);
                }
            }

            return null;
        };

        $tripStatusMeta = static function (?string $statusName): array {
            $normalized = strtolower(trim((string) $statusName));

            return match ($normalized) {
                'running', 'in_progress', 'in progress', 'in_transit', 'in transit', 'active' => ['Running', 'primary'],
                'reload', 'reloading' => ['Reload', 'warning'],
                'completed' => ['Completed', 'success'],
                default => [ucfirst(str_replace('_', ' ', $normalized ?: 'Unknown')), 'light'],
            };
        };

        $profitPositive = (float) ($monthlyProfit ?? 0) >= 0;

        $quickActions = [
            [
                'label' => 'Create Trip',
                'route' => $routeOrNull(['manager.trips.create']),
                'icon' => 'truck-plus',
            ],
            [
                'label' => 'Add Client',
                'route' => $routeOrNull(['manager.clients.create']),
                'icon' => 'user-plus',
            ],
            [
                'label' => 'Add Driver',
                'route' => $routeOrNull(['manager.drivers.create']),
                'icon' => 'id-card',
            ],
            [
                'label' => 'Record Expense',
                'route' => $routeOrNull(['manager.trips.index', 'expenses.index']),
                'icon' => 'receipt',
            ],
            [
                'label' => 'View Reports',
                'route' => $routeOrNull(['manager.reports.index', 'reports.index']),
                'icon' => 'chart',
            ],
            [
                'label' => 'Collect Due',
                'route' => $routeOrNull(['manager.dues.index', 'dues.index']),
                'icon' => 'wallet',
            ],
        ];
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Dashboard</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Welcome back. Here's what's happening today.</p>
        </div>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('l, d M Y') }}</span>
    </div>

    <section class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4 xl:grid-cols-7">
        <article class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
                <svg class="h-5 w-5 text-gray-700 dark:text-white/90" viewBox="0 0 24 24" fill="none"><path d="M3 7h18v10H3z" stroke="currentColor" stroke-width="1.8"/><path d="M7 7V5m10 2V5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Trucks</p>
            <h3 class="mt-1 text-xl font-semibold text-gray-800 dark:text-white/90">{{ (int) ($totalTrucks ?? 0) }}</h3>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Fleet registered</p>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/15">
                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none"><path d="M5 19L19 5M19 5h-8m8 0v8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Running Trips</p>
            <h3 class="mt-1 text-xl font-semibold text-gray-800 dark:text-white/90">{{ (int) ($runningTrips ?? 0) }}</h3>
            <x-ui.badge size="sm" color="primary" class="mt-2">Active</x-ui.badge>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-yellow-50 dark:bg-yellow-500/15">
                <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" viewBox="0 0 24 24" fill="none"><path d="M14 4H6a2 2 0 0 0-2 2v12h16V8l-6-4z" stroke="currentColor" stroke-width="1.8"/><path d="M14 4v4h4" stroke="currentColor" stroke-width="1.8"/></svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Under Workshop</p>
            <h3 class="mt-1 text-xl font-semibold text-gray-800 dark:text-white/90">{{ (int) ($workshopTrucks ?? 0) }}</h3>
            <x-ui.badge size="sm" color="warning" class="mt-2">Attention</x-ui.badge>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 dark:bg-green-500/15">
                <svg class="h-5 w-5 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Today's Income</p>
            <h3 class="mt-1 text-xl font-semibold text-gray-800 dark:text-white/90">{{ $currency($todayIncome ?? 0) }}</h3>
            <x-ui.badge size="sm" color="success" class="mt-2">Inflow</x-ui.badge>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 dark:bg-red-500/15">
                <svg class="h-5 w-5 text-red-600 dark:text-red-400" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h10M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Today's Expense</p>
            <h3 class="mt-1 text-xl font-semibold text-gray-800 dark:text-white/90">{{ $currency($todayExpense ?? 0) }}</h3>
            <x-ui.badge size="sm" color="error" class="mt-2">Outflow</x-ui.badge>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-500/15">
                <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" viewBox="0 0 24 24" fill="none"><path d="M12 8v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/></svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Today's Due</p>
            <h3 class="mt-1 text-xl font-semibold text-gray-800 dark:text-white/90">{{ $currency($todayDue ?? 0) }}</h3>
            <x-ui.badge size="sm" color="warning" class="mt-2">Pending</x-ui.badge>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl {{ $profitPositive ? 'bg-green-50 dark:bg-green-500/15' : 'bg-red-50 dark:bg-red-500/15' }}">
                <svg class="h-5 w-5 {{ $profitPositive ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" viewBox="0 0 24 24" fill="none"><path d="M4 16l4-4 3 3 6-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 8h3v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Monthly Profit</p>
            <h3 class="mt-1 text-xl font-semibold text-gray-800 dark:text-white/90">{{ $currency($monthlyProfit ?? 0) }}</h3>
            <x-ui.badge size="sm" :color="$profitPositive ? 'success' : 'error'" class="mt-2">{{ $profitPositive ? 'Positive' : 'Negative' }}</x-ui.badge>
        </article>
    </section>

    <section class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <x-common.component-card title="Active Trips" desc="Running and reload trips requiring close operational monitoring." class="xl:col-span-2">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[980px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Trip Code</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Client</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Truck</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Driver</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Route</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activeTripsData as $trip)
                                @php
                                    [$statusLabel, $statusColor] = $tripStatusMeta($trip->status?->name ?? null);
                                @endphp
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                        @if (\Illuminate\Support\Facades\Route::has('manager.trips.show'))
                                            <a href="{{ route('manager.trips.show', $trip) }}" class="text-brand-600 hover:underline dark:text-brand-400">{{ $trip->trip_code }}</a>
                                        @else
                                            {{ $trip->trip_code }}
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $trip->client?->company_name ?? $trip->client?->user?->name ?? 'N/A' }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $trip->truck?->truck_number ?? 'N/A' }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $trip->driver?->name ?? $trip->driver?->user?->name ?? 'N/A' }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Str::limit(($trip->pickup_point ?? 'N/A').' → '.($trip->delivery_point ?? 'N/A'), 38) }}</td>
                                    <td class="px-5 py-4">
                                        <x-ui.badge size="sm" :color="$statusColor">{{ $statusLabel }}</x-ui.badge>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if (\Illuminate\Support\Facades\Route::has('manager.trips.show'))
                                            <a href="{{ route('manager.trips.show', $trip) }}" class="inline-flex rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">View</a>
                                        @else
                                            <span class="inline-flex cursor-not-allowed rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500">View</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end">
                @if (\Illuminate\Support\Facades\Route::has('manager.trips.index'))
                    <a href="{{ route('manager.trips.index') }}" class="text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">View All Trips →</a>
                @else
                    <span class="text-sm font-medium text-gray-400">View All Trips →</span>
                @endif
            </div>
        </x-common.component-card>

        <x-common.component-card title="Outstanding Dues" desc="Top clients with the highest pending receivables.">
            <div class="max-h-[316px] space-y-3 overflow-y-auto pr-1 custom-scrollbar">
                @forelse ($topDueClientsData as $client)
                    <div class="rounded-xl border border-gray-200 p-3 dark:border-gray-800">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $client['name'] ?? 'N/A' }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Last payment: {{ !empty($client['last_payment_date']) ? \Carbon\Carbon::parse($client['last_payment_date'])->format('d M Y') : 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-red-600 dark:text-red-400">{{ $currency((float) ($client['due_amount'] ?? 0)) }}</p>
                                @php
                                    $collectRoute = \Illuminate\Support\Facades\Route::has('dues.index') ? route('dues.index', [], [], ['client_id' => (int) ($client['client_id'] ?? 0)]) : null;
                                @endphp
                                @if ($collectRoute)
                                    <a href="{{ $collectRoute }}" class="mt-2 inline-flex rounded-lg border border-red-200 px-2.5 py-1 text-xs font-medium text-red-500 hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10">Collect</a>
                                @else
                                    <span class="mt-2 inline-flex cursor-not-allowed rounded-lg border border-gray-200 px-2.5 py-1 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500">Collect</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-gray-200 px-5 py-10 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">No data available</div>
                @endforelse
            </div>

            <div class="mt-2 border-t border-gray-100 pt-4 dark:border-gray-800">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Outstanding</p>
                <p class="mt-1 text-2xl font-semibold text-red-600 dark:text-red-400">{{ $currency($totalOutstandingDue ?? 0) }}</p>
            </div>
        </x-common.component-card>
    </section>

    <section class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <x-common.component-card title="Monthly Financial Overview" desc="Income vs expense trend over the last six months.">
            <div class="h-[280px]">
                <canvas id="monthlyFinancialOverviewChart" class="h-full w-full"></canvas>
            </div>
        </x-common.component-card>

        <x-common.component-card title="Collection by Payment Method" desc="Current month collection split by method.">
            <div class="h-[280px]">
                <canvas id="paymentMethodBreakdownChart" class="h-full w-full"></canvas>
            </div>

            <div class="mt-4 space-y-2">
                @forelse ($paymentMethodBreakdownData as $method)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-800">
                        <span class="text-gray-700 dark:text-gray-300">{{ $method['method'] ?? 'Unknown' }}</span>
                        <span class="font-medium text-gray-800 dark:text-white/90">{{ $currency((float) ($method['amount'] ?? 0)) }} ({{ number_format((float) ($method['percentage'] ?? 0), 2) }}%)</span>
                    </div>
                @empty
                    <div class="rounded-xl border border-gray-200 px-5 py-10 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">No data available</div>
                @endforelse
            </div>
        </x-common.component-card>
    </section>

    <section class="mb-6" x-data="{ tab: 'all' }">
        <x-common.component-card title="Driver Performance — This Month" desc="Track driver contribution, profitability, and utilization status.">
            <div class="mb-4 flex items-center gap-2">
                <button type="button" @click="tab = 'all'" :class="tab === 'all' ? 'bg-brand-500 text-white' : 'border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-300'" class="rounded-lg px-3 py-1.5 text-xs font-medium">All Drivers</button>
                <button type="button" @click="tab = 'active'" :class="tab === 'active' ? 'bg-brand-500 text-white' : 'border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-300'" class="rounded-lg px-3 py-1.5 text-xs font-medium">Active Only</button>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1100px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Driver</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Trips Completed</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Total Income Generated</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Total Expenses</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Net Profit Contribution</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Rating</th>
                                <th class="px-5 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($driverPerformanceData as $driver)
                                @php
                                    $driverStatus = strtolower((string) ($driver['status'] ?? 'inactive'));
                                    $driverName = (string) ($driver['name'] ?? 'N/A');
                                    $driverInitial = strtoupper((string) \Illuminate\Support\Str::substr($driverName, 0, 1));
                                    $driverRating = (int) round((float) ($driver['rating'] ?? 0));
                                    $driverProfit = (float) ($driver['profit'] ?? 0);
                                @endphp
                                <tr x-show="tab === 'all' || '{{ $driverStatus }}' === 'active'" class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500/15 text-sm font-semibold text-brand-600 dark:text-brand-300">{{ $driverInitial }}</div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $driverName }}</p>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ (int) ($driver['trips'] ?? 0) }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $currency((float) ($driver['income'] ?? 0)) }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $currency((float) ($driver['expenses'] ?? 0)) }}</td>
                                    <td class="px-5 py-4 text-sm font-medium {{ $driverProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ $currency($driverProfit) }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-1">
                                            @for ($star = 1; $star <= 5; $star++)
                                                <svg class="h-4 w-4 {{ $star <= $driverRating ? 'text-yellow-500' : 'text-gray-300 dark:text-gray-700' }}" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.922-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 0 0-1.176 0l-2.8 2.034c-.783.57-1.838-.196-1.539-1.118l1.07-3.292a1 1 0 0 0-.363-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 0 0 .95-.69l1.07-3.292Z" />
                                                </svg>
                                            @endfor
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <x-ui.badge size="sm" :color="$driverStatus === 'active' ? 'success' : 'error'">{{ ucfirst($driverStatus) }}</x-ui.badge>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end">
                @if (\Illuminate\Support\Facades\Route::has('manager.drivers.index'))
                    <a href="{{ route('manager.drivers.index') }}" class="text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">View All Drivers →</a>
                @else
                    <span class="text-sm font-medium text-gray-400">View All Drivers →</span>
                @endif
            </div>
        </x-common.component-card>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <x-common.component-card title="Spare Inventory Snapshot" desc="Category-wise stock position with low-stock alerts.">
            <div class="space-y-3">
                @forelse ($spareInventoryData as $spare)
                    <div class="flex items-center justify-between rounded-xl border border-gray-200 px-3 py-2 dark:border-gray-800">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $spare['category'] ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ (int) ($spare['stock_count'] ?? 0) }} parts in stock</p>
                        </div>
                        @if (($spare['is_low_stock'] ?? false) === true)
                            <x-ui.badge size="sm" color="error">Low</x-ui.badge>
                        @else
                            <x-ui.badge size="sm" color="success">OK</x-ui.badge>
                        @endif
                    </div>
                @empty
                    <div class="rounded-xl border border-gray-200 px-5 py-10 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">No data available</div>
                @endforelse
            </div>

            <div class="mt-2 border-t border-gray-100 pt-4 dark:border-gray-800">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Spare Parts: <span class="font-semibold text-gray-900 dark:text-white">{{ (int) ($totalSpareParts ?? 0) }}</span></p>
                @if (\Illuminate\Support\Facades\Route::has('spares.index'))
                    <a href="{{ route('spares.index') }}" class="mt-2 inline-flex text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">View Inventory →</a>
                @else
                    <span class="mt-2 inline-flex text-sm font-medium text-gray-400">View Inventory →</span>
                @endif
            </div>
        </x-common.component-card>

        <x-common.component-card title="Recent Transactions" desc="Latest income and expense records from operational flow.">
            <div class="space-y-3">
                @forelse ($recentCashbookData as $entry)
                    @php
                        $entryType = strtolower((string) ($entry['type'] ?? 'income'));
                    @endphp
                    <div class="rounded-xl border border-gray-200 px-3 py-2 dark:border-gray-800">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ !empty($entry['date']) ? \Carbon\Carbon::parse($entry['date'])->format('d M Y') : 'N/A' }}</p>
                                <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ \Illuminate\Support\Str::limit((string) ($entry['description'] ?? 'N/A'), 30) }}</p>
                            </div>
                            <div class="text-right">
                                <x-ui.badge size="sm" :color="$entryType === 'income' ? 'success' : 'error'">{{ ucfirst($entryType) }}</x-ui.badge>
                                <p class="mt-1 text-sm font-semibold {{ $entryType === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ $currency((float) ($entry['amount'] ?? 0)) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-gray-200 px-5 py-10 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">No data available</div>
                @endforelse
            </div>

            <div class="flex justify-end">
                @if (\Illuminate\Support\Facades\Route::has('cashbooks.index'))
                    <a href="{{ route('cashbooks.index') }}" class="text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">View Full Cashbook →</a>
                @else
                    <span class="text-sm font-medium text-gray-400">View Full Cashbook →</span>
                @endif
            </div>
        </x-common.component-card>

        <x-common.component-card title="Quick Actions" desc="Fast operational shortcuts for daily manager workflow.">
            <div class="grid grid-cols-2 gap-3">
                @foreach ($quickActions as $action)
                    @php
                        $isDisabled = empty($action['route']);
                        $onclick = ! $isDisabled ? "window.location.href='" . ($action['route'] ?? '') . "'" : null;
                    @endphp
                    <x-ui.button
                        variant="outline"
                        size="sm"
                        :disabled="$isDisabled"
                        className="w-full justify-start text-left"
                        :onclick="$onclick"
                    >
                        @if (($action['icon'] ?? '') === 'truck-plus')
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M3 7h13v8H3zM16 10h3l2 2v3h-5zM7 18h.01M18 18h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @elseif (($action['icon'] ?? '') === 'user-plus')
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M12 14a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 0c-4 0-7 2-7 5M19 8v6M16 11h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @elseif (($action['icon'] ?? '') === 'id-card')
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><rect x="3" y="6" width="18" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/><circle cx="9" cy="12" r="2" stroke="currentColor" stroke-width="1.8"/><path d="M13 10h5M13 14h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @elseif (($action['icon'] ?? '') === 'receipt')
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M7 3h10v18l-2-1-3 1-3-1-2 1V3Z" stroke="currentColor" stroke-width="1.8"/><path d="M9 8h6M9 12h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @elseif (($action['icon'] ?? '') === 'chart')
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M5 19V9M12 19V5M19 19v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @else
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M3 7h18v10H3z" stroke="currentColor" stroke-width="1.8"/><path d="M7 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @endif
                        {{ $action['label'] }}
                    </x-ui.button>
                @endforeach
            </div>
        </x-common.component-card>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const monthlyData = @json($monthlyFinancials ?? []);
            const paymentData = @json($paymentMethodBreakdown ?? []);

            const initializeCharts = () => {
                if (typeof Chart === 'undefined') {
                    return;
                }

                const monthlyCanvas = document.getElementById('monthlyFinancialOverviewChart');
                if (monthlyCanvas) {
                    const labels = monthlyData.map((item) => item.month ?? '');
                    const income = monthlyData.map((item) => Number(item.income ?? 0));
                    const expense = monthlyData.map((item) => Number(item.expense ?? 0));

                    new Chart(monthlyCanvas, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [
                                {
                                    label: 'Income',
                                    data: income,
                                    backgroundColor: '#22c55e',
                                    borderRadius: 8,
                                },
                                {
                                    label: 'Expense',
                                    data: expense,
                                    backgroundColor: '#ef4444',
                                    borderRadius: 8,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value) {
                                            return '৳ ' + Number(value).toLocaleString();
                                        },
                                    },
                                },
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                            },
                        },
                    });
                }

                const paymentCanvas = document.getElementById('paymentMethodBreakdownChart');
                if (paymentCanvas) {
                    const labels = paymentData.map((item) => item.method ?? 'Unknown');
                    const values = paymentData.map((item) => Number(item.amount ?? 0));

                    new Chart(paymentCanvas, {
                        type: 'doughnut',
                        data: {
                            labels,
                            datasets: [
                                {
                                    data: values,
                                    backgroundColor: ['#22c55e', '#3b82f6', '#f59e0b', '#8b5cf6'],
                                    borderWidth: 0,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    display: false,
                                },
                            },
                        },
                    });
                }
            };

            if (typeof Chart === 'undefined') {
                const chartScript = document.createElement('script');
                chartScript.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                chartScript.onload = initializeCharts;
                document.head.appendChild(chartScript);
                return;
            }

            initializeCharts();
        });
    </script>
@endpush
