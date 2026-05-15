@extends('manager::layouts.app')

@section('content')
    @php
        $canUpdateStatus = auth()->user()?->can('updateStatus', $trip) ?? false;
        $canRecordPayment = auth()->user()?->can('recordPayment', $trip) ?? false;
        $canRecordExpense = auth()->user()?->can('recordExpense', $trip) ?? false;
        $currentLocation = $trip->currentVehicleLocation;
        $isTripInProgress = strtolower(trim((string) $trip->status?->name)) === 'in_progress';
        $trackingChannelName = 'trips.'.$trip->ulid.'.tracking';
            $isCompletionRequested = $trip->completion_requested_at !== null;

        $initialMapLocation = [
            'latitude' => $currentLocation ? (float) $currentLocation->latitude : null,
            'longitude' => $currentLocation ? (float) $currentLocation->longitude : null,
            'captured_at' => optional($currentLocation?->captured_at)->toIso8601String(),
            'speed_kph' => $currentLocation && $currentLocation->speed_kph !== null ? (float) $currentLocation->speed_kph : null,
            'heading_degrees' => $currentLocation?->heading_degrees,
            'is_online' => $currentLocation?->is_online ?? false,
        ];
    @endphp

    <x-common.page-breadcrumb pageTitle="Trip Details" />

    <div class="space-y-6">
        <x-common.component-card title="{{ $trip->trip_code }}" desc="Monitor and control this trip lifecycle.">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500">Route</p>
                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $trip->pickup_point }} -> {{ $trip->delivery_point }}</p>
                    <p class="mt-3 text-sm text-gray-500">Client</p>
                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $trip->client?->company_name ?? $trip->client?->user?->name ?? '-' }}</p>
                    <p class="mt-3 text-sm text-gray-500">Driver</p>
                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $trip->driver?->name ?? $trip->driver?->user?->name ?? '-' }}</p>
                    <p class="mt-3 text-sm text-gray-500">Truck</p>
                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $trip->truck?->truck_number ?? '-' }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500">Financials</p>
                    <p class="mt-1 text-sm">Trip Rate: <strong>{{ number_format((float) $summary['trip_rate'], 2) }}</strong></p>
                    <p class="text-sm">Advance Paid: <strong>{{ number_format((float) $summary['advance_paid'], 2) }}</strong></p>
                    <p class="text-sm">Payments Total: <strong>{{ number_format((float) $summary['payments_total'], 2) }}</strong></p>
                    <p class="text-sm">Expense Total: <strong>{{ number_format((float) $summary['total_expense'], 2) }}</strong></p>
                    <p class="text-sm">Due: <strong>{{ number_format((float) $summary['due_balance'], 2) }}</strong></p>
                    <p class="text-sm">Profit: <strong>{{ number_format((float) $summary['profit'], 2) }}</strong></p>
                    <p class="mt-3 text-sm">Current Status: <strong>{{ ucfirst(str_replace('_', ' ', (string) $trip->status?->name)) }}</strong></p>
                    <p class="text-sm">Invoiced: <strong>{{ $trip->isInvoiced() ? 'Yes' : 'No' }}</strong></p>
                        @if ($isCompletionRequested)
                            <p class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">
                                Driver requested completion {{ optional($trip->completion_requested_at)->diffForHumans() ?? 'recently' }}.
                            </p>
                        @endif
                </div>
            </div>

            <div id="trip-tracking-map-card" class="mt-6 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-3 dark:border-gray-800">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Live Vehicle Tracking</h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Realtime GPS stream from the assigned driver during active trips.</p>
                    </div>
                    <span id="tracking-status-badge" class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">Waiting for live updates</span>
                </div>

                <div class="grid grid-cols-1 gap-0 xl:grid-cols-[2fr_1fr]">
                    <div>
                        <div id="trip-live-map" class="h-[360px] w-full"></div>
                    </div>
                    <div class="space-y-2 border-t border-gray-100 p-4 text-sm text-gray-700 dark:border-gray-800 dark:text-gray-300 xl:border-l xl:border-t-0">
                        <p>Trip: <strong>{{ $trip->trip_code }}</strong></p>
                        <p>Driver: <strong>{{ $trip->driver?->name ?? $trip->driver?->user?->name ?? '-' }}</strong></p>
                        <p>Truck: <strong>{{ $trip->truck?->truck_number ?? '-' }}</strong></p>
                        <p>Current Status: <strong>{{ ucfirst(str_replace('_', ' ', (string) $trip->status?->name)) }}</strong></p>
                        <p>Last GPS Time: <strong id="tracking-last-time">{{ optional($currentLocation?->captured_at)->format('Y-m-d H:i:s') ?? '-' }}</strong></p>
                        <p>Speed: <strong id="tracking-speed">{{ $currentLocation && $currentLocation->speed_kph !== null ? number_format((float) $currentLocation->speed_kph, 1).' km/h' : '-' }}</strong></p>
                            @if ($isCompletionRequested)
                                <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">
                                    Completion request pending approval.
                                </p>
                            @endif
                        <p>Coordinates: <strong id="tracking-coordinates">{{ $currentLocation ? number_format((float) $currentLocation->latitude, 6).', '.number_format((float) $currentLocation->longitude, 6) : '-' }}</strong></p>
                        @if (! $isTripInProgress)
                            <p class="mt-3 rounded-lg bg-blue-50 px-3 py-2 text-xs text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                                Live tracking starts automatically when the trip status becomes In Progress.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-3" x-data="tripMutations()">
                <form @submit.prevent="submitStatus" class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white/90">Update Status</h3>
                    <select x-model="statusForm.status" @disabled(! $canUpdateStatus) class="mb-3 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-700 dark:disabled:bg-gray-800 dark:disabled:text-gray-500">
                        @foreach (\App\Modules\Trip\Enums\TripStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <textarea x-model="statusForm.note" rows="2" placeholder="Optional note" @disabled(! $canUpdateStatus) class="mb-3 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-700 dark:disabled:bg-gray-800 dark:disabled:text-gray-500"></textarea>
                    <button @disabled(! $canUpdateStatus) title="{{ $canUpdateStatus ? 'Save status' : 'You are not allowed to update status for this trip' }}" class="rounded px-4 py-2 text-sm text-white disabled:cursor-not-allowed disabled:bg-gray-400 {{ $canUpdateStatus ? 'bg-brand-500' : 'bg-gray-400' }}">Save</button>
                </form>

                <form @submit.prevent="submitPayment" class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white/90">Record Payment</h3>
                    <select x-model="paymentForm.payment_method_id" @disabled(! $canRecordPayment) class="mb-2 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-700 dark:disabled:bg-gray-800 dark:disabled:text-gray-500">
                        <option value="">Payment method</option>
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                        @endforeach
                    </select>
                    <input x-model="paymentForm.amount" type="number" step="0.01" min="0" placeholder="Amount" @disabled(! $canRecordPayment) class="mb-2 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-700 dark:disabled:bg-gray-800 dark:disabled:text-gray-500" />
                    <input x-model="paymentForm.payment_date" type="date" @disabled(! $canRecordPayment) class="mb-2 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-700 dark:disabled:bg-gray-800 dark:disabled:text-gray-500" />
                    <input x-model="paymentForm.transaction_reference" placeholder="Reference (optional)" @disabled(! $canRecordPayment) class="mb-2 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-700 dark:disabled:bg-gray-800 dark:disabled:text-gray-500" />
                    <textarea x-model="paymentForm.note" rows="2" placeholder="Note" @disabled(! $canRecordPayment) class="mb-3 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-700 dark:disabled:bg-gray-800 dark:disabled:text-gray-500"></textarea>
                    <button @disabled(! $canRecordPayment) title="{{ $canRecordPayment ? 'Save payment' : 'You are not allowed to record payment for this trip' }}" class="rounded px-4 py-2 text-sm text-white disabled:cursor-not-allowed disabled:bg-gray-400 {{ $canRecordPayment ? 'bg-indigo-600' : 'bg-gray-400' }}">Save Payment</button>
                </form>

                <form @submit.prevent="submitExpense" class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white/90">Record Expense</h3>
                    <select x-model="expenseForm.category_id" @disabled(! $canRecordExpense) class="mb-2 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-700 dark:disabled:bg-gray-800 dark:disabled:text-gray-500">
                        <option value="">Expense category</option>
                        @foreach ($expenseCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <input x-model="expenseForm.amount" type="number" step="0.01" min="0" placeholder="Amount" @disabled(! $canRecordExpense) class="mb-2 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-700 dark:disabled:bg-gray-800 dark:disabled:text-gray-500" />
                    <input x-model="expenseForm.expense_date" type="date" @disabled(! $canRecordExpense) class="mb-2 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-700 dark:disabled:bg-gray-800 dark:disabled:text-gray-500" />
                    <textarea x-model="expenseForm.description" rows="2" placeholder="Description" @disabled(! $canRecordExpense) class="mb-3 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-700 dark:disabled:bg-gray-800 dark:disabled:text-gray-500"></textarea>
                    <button @disabled(! $canRecordExpense) title="{{ $canRecordExpense ? 'Save expense' : 'You are not allowed to record expense for this trip' }}" class="rounded px-4 py-2 text-sm text-white disabled:cursor-not-allowed disabled:bg-gray-400 {{ $canRecordExpense ? 'bg-orange-600' : 'bg-gray-400' }}">Save Expense</button>
                </form>
            </div>

            <div class="mt-6 rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Invoice</h3>
                    <a href="{{ route('manager.trips.invoice.show', $trip) }}" class="rounded bg-green-600 px-4 py-2 text-sm text-white">View / Print Invoice</a>
                </div>
                <p class="mt-2 text-xs text-gray-500">Invoice is generated automatically on trip creation and synced with due records.</p>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2">
                <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800">
                    <div class="border-b border-gray-100 px-4 py-3 text-sm font-semibold dark:border-gray-800">Payment History</div>

                    <div id="payments-mobile-list" class="space-y-3 p-4 md:hidden">
                        @forelse ($trip->payments as $payment)
                            <div class="rounded-xl border border-gray-200 p-3 dark:border-gray-800">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $payment->paymentMethod?->name ?? '-' }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format((float) $payment->amount, 2) }}</p>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Date: {{ $payment->payment_date }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Reference: {{ $payment->transaction_reference ?? '-' }}</p>
                            </div>
                        @empty
                            <p id="payments-mobile-empty" class="py-6 text-center text-sm text-gray-500">No payments recorded.</p>
                        @endforelse
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full min-w-[540px]">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Date</th>
                                    <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Method</th>
                                    <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Amount</th>
                                    <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Reference</th>
                                </tr>
                            </thead>
                            <tbody id="payments-table-body">
                                @forelse ($trip->payments as $payment)
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $payment->payment_date }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $payment->paymentMethod?->name ?? '-' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ number_format((float) $payment->amount, 2) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $payment->transaction_reference ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr id="payments-empty-row"><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No payments recorded.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800">
                    <div class="border-b border-gray-100 px-4 py-3 text-sm font-semibold dark:border-gray-800">Expense History</div>

                    <div id="expenses-mobile-list" class="space-y-3 p-4 md:hidden">
                        @forelse ($trip->expenses as $expense)
                            <div data-expense-card-id="{{ $expense->id }}" class="rounded-xl border border-gray-200 p-3 dark:border-gray-800 {{ $expense->isPending() ? 'bg-amber-50 dark:bg-amber-900/10' : '' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $expense->category?->name ?? '-' }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format((float) $expense->amount, 2) }}</p>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Date: {{ $expense->expense_date }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $expense->description ?? '-' }}</p>
                                <div class="js-expense-mobile-status mt-2 text-xs">
                                    @if ($expense->is_approved)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">Approved</span>
                                    @elseif ($expense->isRejected())
                                        <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-xs font-medium text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">Rejected</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                                    @endif
                                </div>
                                @if ($expense->isPending())
                                    <div class="js-expense-mobile-actions mt-3 flex items-center gap-2">
                                        <form method="POST" action="{{ route('manager.trips.expenses.approve', [$trip, $expense]) }}" class="js-expense-action-form inline" data-action="approve" data-expense-id="{{ $expense->id }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center rounded bg-emerald-600 px-2 py-1 text-xs font-medium text-white hover:bg-emerald-700">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('manager.trips.expenses.reject', [$trip, $expense]) }}" class="js-expense-action-form inline" data-action="reject" data-expense-id="{{ $expense->id }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center rounded bg-rose-600 px-2 py-1 text-xs font-medium text-white hover:bg-rose-700">Reject</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p id="expenses-mobile-empty" class="py-6 text-center text-sm text-gray-500">No expenses recorded.</p>
                        @endforelse
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full min-w-[540px]">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Date</th>
                                    <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Category</th>
                                    <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Amount</th>
                                    <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Description</th>
                                    <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="expenses-table-body">
                                @forelse ($trip->expenses as $expense)
                                    <tr data-expense-row-id="{{ $expense->id }}" class="border-b border-gray-100 dark:border-gray-800 {{ $expense->isPending() ? 'bg-amber-50 dark:bg-amber-900/10' : '' }}">
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $expense->expense_date }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $expense->category?->name ?? '-' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ number_format((float) $expense->amount, 2) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $expense->description ?? '-' }}</td>
                                        <td class="js-expense-status-cell px-4 py-2 text-sm">
                                            @if ($expense->is_approved)
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">Approved</span>
                                            @elseif ($expense->isRejected())
                                                <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-xs font-medium text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">Rejected</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                                            @endif
                                        </td>
                                        <td class="js-expense-actions-cell px-4 py-2 text-sm">
                                            @if ($expense->isPending())
                                                <div class="flex items-center gap-2">
                                                    <form method="POST" action="{{ route('manager.trips.expenses.approve', [$trip, $expense]) }}" class="js-expense-action-form inline" data-action="approve" data-expense-id="{{ $expense->id }}">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center rounded bg-emerald-600 px-2 py-1 text-xs font-medium text-white hover:bg-emerald-700">Approve</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('manager.trips.expenses.reject', [$trip, $expense]) }}" class="js-expense-action-form inline" data-action="reject" data-expense-id="{{ $expense->id }}">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center rounded bg-rose-600 px-2 py-1 text-xs font-medium text-white hover:bg-rose-700">Reject</button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="expenses-empty-row"><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No expenses recorded.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800">
                <div class="border-b border-gray-100 px-4 py-3 text-sm font-semibold dark:border-gray-800">Trip Timeline</div>
                <div class="space-y-2 p-4 text-sm text-gray-700 dark:text-gray-300">
                    <p>Created at: {{ optional($trip->created_at)->format('Y-m-d H:i') }}</p>
                    <p>Last updated: {{ optional($trip->updated_at)->format('Y-m-d H:i') }}</p>
                    <p>Completed at: {{ optional($trip->completed_at)->format('Y-m-d H:i') ?? '-' }}</p>
                    <p>Cancelled at: {{ optional($trip->cancelled_at)->format('Y-m-d H:i') ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800">
                <div class="border-b border-gray-100 px-4 py-3 text-sm font-semibold dark:border-gray-800">Goods Details</div>
                <table class="w-full min-w-[700px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Item</th>
                            <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Unit</th>
                            <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Qty</th>
                            <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Unit Price</th>
                            <th class="px-4 py-2 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trip->goods as $item)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->item_name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->unit }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->quantity }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ number_format((float) $item->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No goods items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-common.component-card>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            (() => {
                const styleId = 'leaflet-css-trip-live-map';

                if (!document.getElementById(styleId)) {
                    const styleLink = document.createElement('link');
                    styleLink.id = styleId;
                    styleLink.rel = 'stylesheet';
                    styleLink.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    styleLink.crossOrigin = '';
                    styleLink.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
                    document.head.appendChild(styleLink);
                }
            })();

            function tripMutations() {
                return {
                    statusForm: { status: '{{ (string) ($trip->status?->name ?? 'created') }}', note: '' },
                    paymentForm: {
                        trip_ulid: '{{ $trip->ulid }}',
                        client_id: '{{ $trip->client_id }}',
                        payment_method_id: '',
                        amount: '',
                        payment_date: '{{ now()->toDateString() }}',
                        transaction_reference: '',
                        is_advance: false,
                        note: '',
                    },
                    expenseForm: {
                        trip_ulid: '{{ $trip->ulid }}',
                        category_id: '',
                        amount: '',
                        expense_date: '{{ now()->toDateString() }}',
                        description: '',
                    },
                    async submitStatus() {
                        await this.sendJson('{{ route('manager.trips.update-status', $trip) }}', this.statusForm, 'PATCH', () => {
                            window.location.reload();
                        });
                    },
                    async submitPayment() {
                        await this.sendJson('{{ route('manager.trips.payments.store', $trip) }}', this.paymentForm, 'POST', (data) => {
                            const empty = document.getElementById('payments-empty-row');
                            if (empty) {
                                empty.remove();
                            }
                            const mobileEmpty = document.getElementById('payments-mobile-empty');
                            if (mobileEmpty) {
                                mobileEmpty.remove();
                            }

                            const row = document.createElement('tr');
                            row.className = 'border-b border-gray-100 dark:border-gray-800';
                            row.innerHTML = `<td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">${data.payment.date}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">${data.payment.method || '-'}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">${Number(data.payment.amount).toFixed(2)}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">${data.payment.reference || '-'}</td>`;
                            document.getElementById('payments-table-body').prepend(row);

                            const card = document.createElement('div');
                            card.className = 'rounded-xl border border-gray-200 p-3 dark:border-gray-800';
                            card.innerHTML = `<div class="flex items-start justify-between gap-3">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">${data.payment.method || '-'}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">${Number(data.payment.amount).toFixed(2)}</p>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Date: ${data.payment.date}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Reference: ${data.payment.reference || '-'}</p>`;
                            document.getElementById('payments-mobile-list').prepend(card);
                        });
                    },
                    async submitExpense() {
                        await this.sendJson('{{ route('manager.trips.expenses.store', $trip) }}', this.expenseForm, 'POST', (data) => {
                            const empty = document.getElementById('expenses-empty-row');
                            if (empty) {
                                empty.remove();
                            }
                            const mobileEmpty = document.getElementById('expenses-mobile-empty');
                            if (mobileEmpty) {
                                mobileEmpty.remove();
                            }

                            const row = document.createElement('tr');
                            row.className = 'border-b border-gray-100 dark:border-gray-800';
                            row.innerHTML = `<td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">${data.expense.date}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">${data.expense.category || '-'}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">${Number(data.expense.amount).toFixed(2)}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">${data.expense.description || '-'}</td>
                                <td class="px-4 py-2 text-sm"><span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">Approved</span></td>
                                <td class="px-4 py-2 text-sm"><span class="text-xs text-gray-400">-</span></td>`;
                            document.getElementById('expenses-table-body').prepend(row);

                            const card = document.createElement('div');
                            card.className = 'rounded-xl border border-gray-200 p-3 dark:border-gray-800';
                            card.innerHTML = `<div class="flex items-start justify-between gap-3">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">${data.expense.category || '-'}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">${Number(data.expense.amount).toFixed(2)}</p>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Date: ${data.expense.date}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">${data.expense.description || '-'}</p>
                                <div class="mt-2 text-xs"><span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">Approved</span></div>`;
                            document.getElementById('expenses-mobile-list').prepend(card);
                        });
                    },
                    async sendJson(url, payload, method, onSuccess) {
                        const response = await fetch(url, {
                            method,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            Toastify({ text: data.message ?? 'Action failed.', duration: 4000, gravity: 'top', position: 'right', backgroundColor: '#ef4444', stopOnFocus: true }).showToast();
                            return;
                        }

                        Toastify({ text: data.message ?? 'Action completed.', duration: 2500, gravity: 'top', position: 'right', backgroundColor: '#22c55e', stopOnFocus: true }).showToast();

                        if (typeof onSuccess === 'function') {
                            onSuccess(data);
                        }
                    },
                }
            }

            (() => {
                document.querySelectorAll('.js-expense-action-form').forEach((form) => {
                    form.addEventListener('submit', async (event) => {
                        event.preventDefault();

                        const action = form.dataset.action === 'reject' ? 'reject' : 'approve';
                        const isReject = action === 'reject';
                        const expenseId = form.dataset.expenseId;

                        const result = await Swal.fire({
                            title: isReject ? 'Reject expense?' : 'Approve expense?',
                            text: isReject
                                ? 'This expense will be marked as rejected and excluded from totals.'
                                : 'This expense will be approved and included in trip totals.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: isReject ? '#e11d48' : '#059669',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: isReject ? 'Yes, reject' : 'Yes, approve',
                        });

                        if (!result.isConfirmed) {
                            return;
                        }

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                credentials: 'same-origin',
                            });

                            const data = await response.json().catch(() => ({}));

                            if (!response.ok) {
                                Toastify({
                                    text: data.message ?? 'Action failed.',
                                    duration: 4000,
                                    gravity: 'top',
                                    position: 'right',
                                    backgroundColor: '#ef4444',
                                    stopOnFocus: true,
                                }).showToast();

                                return;
                            }

                            const statusHtml = isReject
                                ? '<span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-xs font-medium text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">Rejected</span>'
                                : '<span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">Approved</span>';

                            if (expenseId) {
                                const row = document.querySelector(`[data-expense-row-id="${expenseId}"]`);
                                if (row) {
                                    row.classList.remove('bg-amber-50', 'dark:bg-amber-900/10');

                                    const statusCell = row.querySelector('.js-expense-status-cell');
                                    if (statusCell) {
                                        statusCell.innerHTML = statusHtml;
                                    }

                                    const actionsCell = row.querySelector('.js-expense-actions-cell');
                                    if (actionsCell) {
                                        actionsCell.innerHTML = '<span class="text-xs text-gray-400">-</span>';
                                    }
                                }

                                const mobileCard = document.querySelector(`[data-expense-card-id="${expenseId}"]`);
                                if (mobileCard) {
                                    mobileCard.classList.remove('bg-amber-50', 'dark:bg-amber-900/10');

                                    const mobileStatus = mobileCard.querySelector('.js-expense-mobile-status');
                                    if (mobileStatus) {
                                        mobileStatus.innerHTML = statusHtml;
                                    }

                                    const mobileActions = mobileCard.querySelector('.js-expense-mobile-actions');
                                    if (mobileActions) {
                                        mobileActions.remove();
                                    }
                                }
                            }

                            Toastify({
                                text: data.message ?? (isReject ? 'Expense rejected successfully.' : 'Expense approved successfully.'),
                                duration: 2500,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: '#22c55e',
                                stopOnFocus: true,
                            }).showToast();
                        } catch (_error) {
                            Toastify({
                                text: 'Network error. Please try again.',
                                duration: 4000,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: '#ef4444',
                                stopOnFocus: true,
                            }).showToast();
                        }
                    });
                });

                const config = {
                    tripUlid: @js($trip->ulid),
                    channel: @js($trackingChannelName),
                    initialLocation: @js($initialMapLocation),
                    isTripInProgress: @js($isTripInProgress),
                };

                const mapContainer = document.getElementById('trip-live-map');
                const statusBadge = document.getElementById('tracking-status-badge');
                const lastTime = document.getElementById('tracking-last-time');
                const speed = document.getElementById('tracking-speed');
                const coordinates = document.getElementById('tracking-coordinates');

                if (!mapContainer || typeof L === 'undefined') {
                    return;
                }

                const hasInitial = config.initialLocation.latitude !== null && config.initialLocation.longitude !== null;
                const defaultCenter = hasInitial
                    ? [config.initialLocation.latitude, config.initialLocation.longitude]
                    : [23.8103, 90.4125];

                const map = L.map(mapContainer, {
                    zoomControl: true,
                    scrollWheelZoom: true,
                }).setView(defaultCenter, hasInitial ? 14 : 6);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(map);

                const marker = L.circleMarker(defaultCenter, {
                    radius: 8,
                    color: '#15803d',
                    fillColor: '#22c55e',
                    fillOpacity: 0.95,
                    weight: 2,
                }).addTo(map);

                function setStatus(text, color) {
                    if (!statusBadge) {
                        return;
                    }

                    statusBadge.textContent = text;
                    statusBadge.className = `inline-flex rounded-full px-3 py-1 text-xs font-medium ${color}`;
                }

                function applyLocation(location, shouldPan = true) {
                    if (!location || location.latitude === null || location.longitude === null) {
                        return;
                    }

                    const point = [location.latitude, location.longitude];
                    marker.setLatLng(point);

                    const isOnline = Boolean(location.is_online);
                    marker.setStyle({
                        color: isOnline ? '#15803d' : '#b91c1c',
                        fillColor: isOnline ? '#22c55e' : '#ef4444',
                    });

                    if (shouldPan) {
                        map.panTo(point, {
                            animate: true,
                            duration: 0.8,
                        });
                    }

                    const popupLines = [
                        `<strong>Trip ${config.tripUlid}</strong>`,
                        `Lat/Lng: ${Number(location.latitude).toFixed(6)}, ${Number(location.longitude).toFixed(6)}`,
                        `Speed: ${location.speed_kph !== null ? Number(location.speed_kph).toFixed(1) + ' km/h' : '-'}`,
                        `Heading: ${location.heading_degrees ?? '-'}`,
                        `Captured: ${location.captured_at ?? '-'}`,
                    ];

                    marker.bindPopup(popupLines.join('<br>'));

                    if (coordinates) {
                        coordinates.textContent = `${Number(location.latitude).toFixed(6)}, ${Number(location.longitude).toFixed(6)}`;
                    }

                    if (speed) {
                        speed.textContent = location.speed_kph !== null ? `${Number(location.speed_kph).toFixed(1)} km/h` : '-';
                    }

                    if (lastTime) {
                        lastTime.textContent = location.captured_at ?? '-';
                    }

                    setStatus(
                        isOnline ? 'Live' : 'Offline',
                        isOnline
                            ? 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300'
                            : 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300'
                    );
                }

                if (hasInitial) {
                    applyLocation(config.initialLocation, false);
                } else {
                    setStatus(
                        config.isTripInProgress ? 'Waiting for first GPS fix' : 'Tracking not started',
                        'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300'
                    );
                }

                if (!window.Echo) {
                    return;
                }

                const channel = window.Echo.private(config.channel);

                channel.listen('.trip.location.updated', (event) => {
                    applyLocation(event.location, true);
                });

                channel.listen('.trip.tracking.stopped', (event) => {
                    setStatus('Tracking stopped', 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300');

                    if (event && event.message) {
                        Toastify({
                            text: event.message,
                            duration: 3500,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: '#f59e0b',
                            stopOnFocus: true,
                        }).showToast();
                    }
                });
            })();
        </script>
    @endpush
@endsection
