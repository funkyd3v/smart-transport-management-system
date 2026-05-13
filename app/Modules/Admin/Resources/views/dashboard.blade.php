@extends('admin::layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
 @php
 $formatCurrency = static fn (float|int|null $value): string => '৳ '.number_format((float) ($value ?? 0), 2);

 $topCards = [
 ['label' => 'Total Trucks', 'value' => $kpisTop['total_trucks'] ?? 0, 'accent' => 'blue', 'trend' => '+4.1%'],
 ['label' => 'Running Trips', 'value' => $kpisTop['running_trips'] ?? 0, 'accent' => 'emerald', 'trend' => '+2.3%'],
 ['label' => 'Under Workshop', 'value' => $kpisTop['workshop_trucks'] ?? 0, 'accent' => 'yellow', 'trend' => '-1.2%'],
 ['label' => "Today's Income", 'value' => $formatCurrency($kpisTop['today_income'] ?? 0), 'accent' => 'emerald', 'trend' => '+6.2%'],
 ['label' => "Today's Expense", 'value' => $formatCurrency($kpisTop['today_expense'] ?? 0), 'accent' => 'red', 'trend' => '+1.9%'],
 ['label' => "Today's Due", 'value' => $formatCurrency($kpisTop['today_due'] ?? 0), 'accent' => 'orange', 'trend' => '-3.7%'],
 ];

 $secondCards = [
 ['label' => 'Monthly Profit', 'value' => $formatCurrency($kpisSecond['monthly_profit'] ?? 0), 'accent' => 'sky'],
 ['label' => 'Total Clients', 'value' => $kpisSecond['total_clients'] ?? 0, 'accent' => 'blue'],
 ['label' => 'Active Drivers', 'value' => $kpisSecond['active_drivers'] ?? 0, 'accent' => 'emerald'],
 ['label' => 'Spare Parts Value', 'value' => $formatCurrency($kpisSecond['spare_parts_value'] ?? 0), 'accent' => 'yellow'],
 ];

 $accentMap = [
 'blue' => 'bg-blue-50 text-blue-600 ring-1 ring-blue-100',
 'emerald' => 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100',
 'yellow' => 'bg-amber-50 text-amber-600 ring-1 ring-amber-100',
 'red' => 'bg-rose-50 text-rose-600 ring-1 ring-rose-100',
 'orange' => 'bg-orange-50 text-orange-600 ring-1 ring-orange-100',
 'sky' => 'bg-cyan-50 text-cyan-600 ring-1 ring-cyan-100',
 ];
 @endphp

 <div class="relative space-y-7 overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-b from-slate-50 to-white p-5 shadow-[0_18px_40px_-24px_rgba(15,23,42,0.25)] md:p-7">
 <div class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 rounded-full bg-sky-100/70 blur-3xl"></div>
 <div class="pointer-events-none absolute -bottom-28 -left-28 h-64 w-64 rounded-full bg-amber-100/60 blur-3xl"></div>

 <div class="relative flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/70 bg-white/85 p-5 backdrop-blur-sm">
 <div>
 <h1 class="text-2xl font-semibold tracking-tight text-slate-900 md:text-[28px]">Admin Command Center</h1>
 <p class="mt-1 text-sm text-slate-500">Full platform oversight across operations, finance, and governance.</p>
 </div>
 <a href="{{ route('admin.reports.index') }}" class="inline-flex h-11 items-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 px-4 text-sm font-medium text-white shadow-lg shadow-sky-600/20 transition hover:from-sky-500 hover:to-blue-500">
 Generate Report
 </a>
 </div>

 <section class="relative grid grid-cols-1 gap-4 md:grid-cols-3 xl:grid-cols-6">
 @foreach ($topCards as $card)
 <article class="rounded-2xl border border-slate-200 bg-white/95 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
 <div class="mb-4 flex items-center justify-between">
 <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl {{ $accentMap[$card['accent']] ?? 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' }}">
 <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
 <path d="M4 12h16M12 4v16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
 </svg>
 </span>
 <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-500">{{ $card['trend'] }}</span>
 </div>
 <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
 <h3 class="mt-2 text-xl font-semibold text-slate-900">{{ $card['value'] }}</h3>
 </article>
 @endforeach
 </section>

 <section class="relative grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
 @foreach ($secondCards as $card)
 <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
 <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
 <h3 class="mt-2 text-xl font-semibold text-slate-900">{{ $card['value'] }}</h3>
 </article>
 @endforeach
 </section>

 <section class="relative grid grid-cols-1 gap-6 lg:grid-cols-5">
 <div class="space-y-6 lg:col-span-3">
 <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
 <div class="mb-4 flex items-center justify-between">
 <h3 class="text-base font-semibold text-slate-900">Revenue vs Expense (Last 6 Months)</h3>
 <span class="text-xs text-slate-500">Chart-ready data</span>
 </div>
 <div class="grid grid-cols-6 gap-3">
 @foreach ($monthlyFinancials as $row)
 <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-center">
 <p class="text-xs text-slate-500">{{ $row['month'] }}</p>
 <p class="mt-2 text-xs font-medium text-emerald-600">{{ number_format((float) $row['income']) }}</p>
 <p class="mt-1 text-xs font-medium text-rose-600">{{ number_format((float) $row['expense']) }}</p>
 </div>
 @endforeach
 </div>
 </div>

 <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
 <h3 class="mb-4 text-base font-semibold text-slate-900">Recent Trips</h3>
 <div class="overflow-x-auto">
 <table class="w-full min-w-[760px] text-sm">
 <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
 <tr>
 <th class="px-3 py-3">Trip</th>
 <th class="px-3 py-3">Client</th>
 <th class="px-3 py-3">Driver</th>
 <th class="px-3 py-3">Truck</th>
 <th class="px-3 py-3">Route</th>
 <th class="px-3 py-3">Amount</th>
 </tr>
 </thead>
 <tbody>
 @forelse ($recentTrips as $trip)
 <tr class="border-b border-slate-200 transition hover:bg-slate-50">
 <td class="px-3 py-3 font-medium text-slate-900">{{ $trip->trip_code }}</td>
 <td class="px-3 py-3 text-slate-700">{{ $trip->client?->company_name ?? $trip->client?->user?->name ?? '-' }}</td>
 <td class="px-3 py-3 text-slate-700">{{ $trip->driver?->user?->name ?? '-' }}</td>
 <td class="px-3 py-3 text-slate-700">{{ $trip->truck?->truck_number ?? '-' }}</td>
 <td class="px-3 py-3 text-slate-700">{{ $trip->pickup_point }} &rarr; {{ $trip->delivery_point }}</td>
 <td class="px-3 py-3 font-medium text-slate-900">{{ $formatCurrency($trip->trip_rate) }}</td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-3 py-6 text-center text-slate-500">No trips available.</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>

 <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
 <h3 class="mb-4 text-base font-semibold text-slate-900">Due Summary</h3>
 <div class="overflow-x-auto">
 <table class="w-full min-w-[640px] text-sm">
 <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
 <tr>
 <th class="px-3 py-3">Client</th>
 <th class="px-3 py-3">Total Due</th>
 <th class="px-3 py-3">Last Payment</th>
 </tr>
 </thead>
 <tbody>
 @forelse ($topOverdueClients as $row)
 <tr class="border-b border-slate-200 transition hover:bg-slate-50">
 <td class="px-3 py-3 text-slate-800">{{ $row->client?->company_name ?? $row->client?->user?->name ?? '-' }}</td>
 <td class="px-3 py-3 font-medium text-orange-600">{{ $formatCurrency($row->due_amount) }}</td>
 <td class="px-3 py-3 text-slate-500">{{ optional($row->last_payment_date)->format('d M Y') ?? '-' }}</td>
 </tr>
 @empty
 <tr>
 <td colspan="3" class="px-3 py-6 text-center text-slate-500">No due data found.</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 </div>

 <div class="space-y-6 lg:col-span-2">
 <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-sm">
 <p class="text-sm text-emerald-700">Monthly Profit</p>
 <h3 class="mt-2 text-3xl font-bold text-emerald-700">{{ $formatCurrency($kpisSecond['monthly_profit'] ?? 0) }}</h3>
 <p class="mt-2 text-sm text-emerald-600">Business health trend is stable this month.</p>
 </div>

 <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
 <h3 class="mb-3 text-base font-semibold text-slate-900">Trip Status Breakdown</h3>
 <div class="space-y-2">
 <p class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700"><span>Pending</span><span class="font-medium">{{ $tripStatusBreakdown['pending'] ?? 0 }}</span></p>
 <p class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700"><span>In Progress</span><span class="font-medium">{{ $tripStatusBreakdown['in_progress'] ?? 0 }}</span></p>
 <p class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700"><span>Completed</span><span class="font-medium">{{ $tripStatusBreakdown['completed'] ?? 0 }}</span></p>
 <p class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700"><span>Cancelled</span><span class="font-medium">{{ $tripStatusBreakdown['cancelled'] ?? 0 }}</span></p>
 </div>
 </div>

 <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
 <h3 class="mb-3 text-base font-semibold text-slate-900">Top Drivers</h3>
 <div class="space-y-3">
 @forelse ($topDrivers as $driver)
 <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
 <p class="text-sm text-slate-800">{{ $driver->user?->name ?? 'Driver' }}</p>
 <span class="text-xs text-slate-500">{{ $driver->trips_count }} trips</span>
 </div>
 @empty
 <p class="text-sm text-slate-500">No performance data yet.</p>
 @endforelse
 </div>
 </div>

 <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
 <h3 class="mb-3 text-base font-semibold text-slate-900">Quick Actions</h3>
 <div class="grid grid-cols-2 gap-3 text-sm">
 <a href="{{ route('admin.trips.create') }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-800 transition hover:bg-sky-50 hover:text-sky-700">New Trip</a>
 <a href="{{ route('admin.drivers.create') }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-800 transition hover:bg-sky-50 hover:text-sky-700">Add Driver</a>
 <a href="{{ route('admin.trucks.create') }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-800 transition hover:bg-sky-50 hover:text-sky-700">Add Truck</a>
 <a href="{{ route('admin.clients.create') }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-800 transition hover:bg-sky-50 hover:text-sky-700">Add Client</a>
 <a href="{{ route('admin.reports.index') }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-800 transition hover:bg-sky-50 hover:text-sky-700">Reports</a>
 <a href="{{ route('admin.spare.create') }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-800 transition hover:bg-sky-50 hover:text-sky-700">Add Spare</a>
 </div>
 </div>

 <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
 <div class="mb-3 flex items-center justify-between">
 <h3 class="text-base font-semibold text-slate-900">Recent Audit Logs</h3>
 <a href="{{ route('admin.audit.index') }}" class="text-xs text-sky-600 hover:text-sky-700">View all</a>
 </div>
 <div class="space-y-2">
 @forelse ($recentAuditLogs as $log)
 <p class="rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-700">{{ $log->user?->name ?? 'System' }} - {{ $log->action }} - {{ $log->table_name }}</p>
 @empty
 <p class="text-sm text-slate-500">No audit activity found.</p>
 @endforelse
 </div>
 </div>

 <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-6 shadow-sm">
 <p class="text-sm text-amber-700">Pending Approvals</p>
 <div class="mt-2 flex items-center justify-between">
 <h3 class="text-2xl font-bold text-amber-700">{{ $pendingApprovals }}</h3>
 <a href="{{ route('admin.users.index') }}" class="rounded-lg bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700 hover:bg-amber-200">Review</a>
 </div>
 </div>
 </div>
 </section>
 </div>
@endsection
