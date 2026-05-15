@extends('driver::layouts.app')

@section('content')
    @php
        $statusValue = strtolower((string) $trip->status?->name);
        $statusStyles = [
            'created' => 'bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-300',
            'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
            'completed' => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300',
            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
        ];

        $loadDateTime = optional($trip->load_date)?->format('d M Y, h:i A');
    @endphp

    <x-common.page-breadcrumb pageTitle="Trip {{ $trip->trip_code }}" />

    <div class="space-y-6" x-data="driverTripShow({
        status: @js($statusValue ?: 'created'),
        completionRequestedAt: @js(optional($trip->completion_requested_at)->toIso8601String()),
        tripCode: @js($trip->trip_code),
        tripUlid: @js($trip->ulid),
        updateStatusUrl: @js(route('driver.trips.update-status', $trip)),
        expenseUrl: @js(route('driver.trips.expenses.store', $trip)),
        reloadUrl: @js(route('driver.trips.reloads.store', $trip)),
        trackingUrl: @js(route('driver.api.trips.location.store', $trip, false)),
        initialSummary: @js($summary),
        openModal: @js(request('modal')),
    })" x-init="init()">
        <x-common.component-card title="Trip Info" desc="Trip details assigned to you.">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl bg-gray-50 p-4 dark:bg-white/[0.03]">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Trip Code</p>
                    <p class="mt-2 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $trip->trip_code }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 dark:bg-white/[0.03] xl:col-span-2">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Route</p>
                    <p class="mt-2 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $trip->pickup_point }} &rarr; {{ $trip->delivery_point }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $loadDateTime }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 dark:bg-white/[0.03]">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Truck</p>
                    <p class="mt-2 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $trip->truck?->truck_number ?? '-' }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $trip->truck?->model ?? 'Truck assigned by manager' }}</p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 dark:bg-white/[0.03]">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</p>
                    <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-medium" :class="badgeClass()" x-text="statusLabel()"></span>
                </div>
            </div>
        </x-common.component-card>

        @include('driver::pages.trips.partials._status_card', ['trip' => $trip])
        @include('driver::pages.trips.partials._goods_table', ['trip' => $trip])

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            @include('driver::pages.trips.partials._expenses_table', ['trip' => $trip])
            @include('driver::pages.trips.partials._reload_history', ['trip' => $trip])
        </div>

        @include('driver::pages.trips.partials._financial_summary', ['trip' => $trip, 'summary' => $summary])

        <div x-cloak x-show="showExpenseModal" class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6">
            <div class="absolute inset-0 bg-gray-900/50" @click="closeExpenseModal()"></div>
            <div class="relative w-full max-w-2xl rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Add Expense</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Log a trip expense you paid during the trip.</p>
                    </div>
                    <button type="button" @click="closeExpenseModal()" class="rounded-full p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5">&times;</button>
                </div>

                <form class="mt-6 space-y-4" @submit.prevent="submitExpense()">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Category</label>
                        <select x-model="expenseForm.category" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="fuel">Fuel</option>
                            <option value="toll">Toll</option>
                            <option value="driver_expense">Driver Expense</option>
                            <option value="other">Other</option>
                        </select>
                        <p class="mt-1 text-xs text-red-500" x-show="expenseErrors.category" x-text="expenseErrors.category ? expenseErrors.category[0] : ''"></p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Amount</label>
                            <input x-model="expenseForm.amount" type="number" min="0.01" step="0.01" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            <p class="mt-1 text-xs text-red-500" x-show="expenseErrors.amount" x-text="expenseErrors.amount ? expenseErrors.amount[0] : ''"></p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Expense Date</label>
                            <input x-model="expenseForm.expense_date" type="date" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            <p class="mt-1 text-xs text-red-500" x-show="expenseErrors.expense_date" x-text="expenseErrors.expense_date ? expenseErrors.expense_date[0] : ''"></p>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
                        <textarea x-model="expenseForm.description" rows="3" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
                        <p class="mt-1 text-xs text-red-500" x-show="expenseErrors.description" x-text="expenseErrors.description ? expenseErrors.description[0] : ''"></p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="closeExpenseModal()" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:text-gray-300">Cancel</button>
                        <button type="submit" :disabled="expenseSubmitting" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-70">
                            <svg x-show="expenseSubmitting" class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span>Add Expense</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-cloak x-show="showReloadModal" class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6">
            <div class="absolute inset-0 bg-gray-900/50" @click="closeReloadModal()"></div>
            <div class="relative w-full max-w-2xl rounded-3xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Add Reload</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Record a reload stop with location, amount, and time.</p>
                    </div>
                    <button type="button" @click="closeReloadModal()" class="rounded-full p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5">&times;</button>
                </div>

                <form class="mt-6 space-y-4" @submit.prevent="submitReload()">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Location</label>
                        <input x-model="reloadForm.location" type="text" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                        <p class="mt-1 text-xs text-red-500" x-show="reloadErrors.location" x-text="reloadErrors.location ? reloadErrors.location[0] : ''"></p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Reload Amount</label>
                            <input x-model="reloadForm.reload_amount" type="number" min="0" step="0.01" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            <p class="mt-1 text-xs text-red-500" x-show="reloadErrors.reload_amount" x-text="reloadErrors.reload_amount ? reloadErrors.reload_amount[0] : ''"></p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Reloaded At</label>
                            <input x-model="reloadForm.reloaded_at" type="datetime-local" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            <p class="mt-1 text-xs text-red-500" x-show="reloadErrors.reloaded_at" x-text="reloadErrors.reloaded_at ? reloadErrors.reloaded_at[0] : ''"></p>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Note</label>
                        <textarea x-model="reloadForm.note" rows="3" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
                        <p class="mt-1 text-xs text-red-500" x-show="reloadErrors.note" x-text="reloadErrors.note ? reloadErrors.note[0] : ''"></p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="closeReloadModal()" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:text-gray-300">Cancel</button>
                        <button type="submit" :disabled="reloadSubmitting" class="inline-flex items-center gap-2 rounded-lg bg-slate-700 px-5 py-2.5 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-70">
                            <svg x-show="reloadSubmitting" class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span>Add Reload</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function driverTripShow(config) {
                const today = new Date();
                const currentDate = new Date(today.getTime() - (today.getTimezoneOffset() * 60000)).toISOString().slice(0, 10);
                const currentDateTime = new Date(today.getTime() - (today.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);

                return {
                    status: config.status,
                    completionRequestedAt: config.completionRequestedAt,
                    tripCode: config.tripCode,
                    updateStatusUrl: config.updateStatusUrl,
                    expenseUrl: config.expenseUrl,
                    reloadUrl: config.reloadUrl,
                    trackingUrl: config.trackingUrl,
                    tripUlid: config.tripUlid,
                    financialSummary: config.initialSummary,
                    showExpenseModal: false,
                    showReloadModal: false,
                    statusActionSubmitting: false,
                    expenseSubmitting: false,
                    reloadSubmitting: false,
                    expenseErrors: {},
                    reloadErrors: {},
                    trackingWatcherId: null,
                    trackingIntervalId: null,
                    lastSentAt: 0,
                    lastSentKey: '',
                    lastTrackingErrorAt: 0,
                    expenseForm: {
                        category: 'fuel',
                        amount: '',
                        description: '',
                        expense_date: currentDate,
                    },
                    reloadForm: {
                        location: '',
                        reload_amount: '',
                        note: '',
                        reloaded_at: currentDateTime,
                    },
                    init() {
                        if (config.openModal === 'expense') {
                            this.openExpenseModal();
                        }

                        if (config.openModal === 'reload') {
                            this.openReloadModal();
                        }

                        this.updateTrackingLifecycle();

                        window.addEventListener('beforeunload', () => {
                            this.stopTracking();
                        });
                    },
                    statusLabel() {
                        if (this.completionRequestedAt) {
                            return 'Completion Pending';
                        }

                        const labels = {
                            created: 'Created',
                            in_progress: 'In Progress',
                            completed: 'Completed',
                            cancelled: 'Cancelled',
                        };

                        return labels[this.status] ?? this.status;
                    },
                    badgeClass() {
                        if (this.completionRequestedAt) {
                            return 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300';
                        }

                        const classes = {
                            created: 'bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-300',
                            in_progress: 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
                            completed: 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300',
                            cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
                        };

                        return classes[this.status] ?? classes.created;
                    },
                    formatCurrency(value) {
                        return `BDT ${Number(value || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                    },
                    async updateStatus(targetStatus) {
                        const isCompleting = targetStatus === 'completed';
                        const result = await Swal.fire({
                            title: isCompleting ? 'Mark Trip Complete?' : 'Start Trip?',
                            text: isCompleting
                                ? 'This will send a completion request to manager/admin for approval before final completion.'
                                : 'Are you sure you want to start this trip? This cannot be undone.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: isCompleting ? '#16a34a' : '#2563eb',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: isCompleting ? 'Yes, mark complete' : 'Yes, start it',
                        });

                        if (! result.isConfirmed) {
                            return;
                        }

                        this.statusActionSubmitting = true;

                        const response = await fetch(this.updateStatusUrl, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ status: targetStatus }),
                        });

                        const data = await response.json();

                        if (! response.ok) {
                            Toastify({
                                text: data.message ?? 'Failed to update trip status.',
                                duration: 4000,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: '#ef4444',
                                stopOnFocus: true,
                            }).showToast();

                            this.statusActionSubmitting = false;
                            return;
                        }

                        this.status = data.trip?.status ?? this.status;
                        this.completionRequestedAt = data.trip?.completion_requested_at ?? this.completionRequestedAt;
                        this.updateTrackingLifecycle();

                        Toastify({
                            text: data.message ?? 'Trip status updated successfully.',
                            duration: 2500,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: '#22c55e',
                            stopOnFocus: true,
                        }).showToast();
                        this.statusActionSubmitting = false;
                    },
                    isTrackingEnabledStatus() {
                        return this.status === 'in_progress' && ! this.completionRequestedAt;
                    },
                    getDeviceId() {
                        const key = 'driver_tracking_device_id';
                        let value = localStorage.getItem(key);

                        if (value) {
                            return value;
                        }

                        if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                            value = window.crypto.randomUUID();
                        } else {
                            value = `${Date.now()}-${Math.random().toString(36).slice(2, 12)}`;
                        }

                        localStorage.setItem(key, value);

                        return value;
                    },
                    updateTrackingLifecycle() {
                        if (this.isTrackingEnabledStatus()) {
                            this.startTracking();
                            return;
                        }

                        this.stopTracking();
                    },
                    startTracking() {
                        if (!navigator.geolocation || this.trackingWatcherId !== null) {
                            return;
                        }

                        this.trackingWatcherId = navigator.geolocation.watchPosition(
                            (position) => {
                                this.handlePosition(position);
                            },
                            () => {
                                const now = Date.now();

                                if ((now - this.lastTrackingErrorAt) < 20000) {
                                    return;
                                }

                                this.lastTrackingErrorAt = now;

                                Toastify({
                                    text: 'Unable to fetch GPS location. Please check location permission.',
                                    duration: 3500,
                                    gravity: 'top',
                                    position: 'right',
                                    backgroundColor: '#ef4444',
                                    stopOnFocus: true,
                                }).showToast();
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 3000,
                            },
                        );

                        this.trackingIntervalId = window.setInterval(() => {
                            if (!this.isTrackingEnabledStatus()) {
                                return;
                            }

                            navigator.geolocation.getCurrentPosition(
                                (position) => this.handlePosition(position),
                                () => {
                                    // Ignore transient failures and continue trying.
                                },
                                {
                                    enableHighAccuracy: true,
                                    timeout: 10000,
                                    maximumAge: 5000,
                                },
                            );
                        }, 10000);
                    },
                    stopTracking() {
                        if (this.trackingWatcherId !== null) {
                            navigator.geolocation.clearWatch(this.trackingWatcherId);
                            this.trackingWatcherId = null;
                        }

                        if (this.trackingIntervalId !== null) {
                            clearInterval(this.trackingIntervalId);
                            this.trackingIntervalId = null;
                        }
                    },
                    handlePosition(position) {
                        if (!this.isTrackingEnabledStatus()) {
                            return;
                        }

                        const coords = position.coords;
                        const now = Date.now();
                        const key = `${Number(coords.latitude).toFixed(5)}:${Number(coords.longitude).toFixed(5)}`;

                        if (key === this.lastSentKey && (now - this.lastSentAt) < 5000) {
                            return;
                        }

                        this.lastSentAt = now;
                        this.lastSentKey = key;

                        this.sendLocation({
                            latitude: Number(coords.latitude),
                            longitude: Number(coords.longitude),
                            accuracy_meters: Number.isFinite(coords.accuracy) ? Number(coords.accuracy) : null,
                            speed_kph: Number.isFinite(coords.speed) && coords.speed !== null ? Number(coords.speed) * 3.6 : null,
                            heading_degrees: Number.isFinite(coords.heading) && coords.heading !== null
                                ? Math.max(0, Math.min(359, Math.round(coords.heading)))
                                : null,
                            captured_at: new Date(position.timestamp || Date.now()).toISOString(),
                            device_id: this.getDeviceId(),
                            source: 'driver_web',
                        });
                    },
                    async sendLocation(payload) {
                        try {
                            const response = await fetch(this.trackingUrl, {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(payload),
                            });

                            if (response.status === 401 || response.status === 403) {
                                this.stopTracking();
                                return;
                            }

                            if (response.status === 202) {
                                const data = await response.json();

                                if ((data.message || '').toLowerCase().includes('not active')) {
                                    this.stopTracking();
                                }

                                if ((data.message || '').toLowerCase().includes('completion request')) {
                                    this.stopTracking();
                                }
                            }
                        } catch (_error) {
                            // Ignore temporary network errors to keep the tracker resilient.
                        }
                    },
                    openExpenseModal() {
                        this.expenseErrors = {};
                        this.expenseSubmitting = false;
                        this.showExpenseModal = true;
                        this.expenseForm.expense_date = new Date(new Date().getTime() - (new Date().getTimezoneOffset() * 60000)).toISOString().slice(0, 10);
                    },
                    closeExpenseModal() {
                        this.showExpenseModal = false;
                    },
                    openReloadModal() {
                        this.reloadErrors = {};
                        this.reloadSubmitting = false;
                        this.showReloadModal = true;
                        this.reloadForm.reloaded_at = new Date(new Date().getTime() - (new Date().getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
                    },
                    closeReloadModal() {
                        this.showReloadModal = false;
                    },
                    escapeHtml(value) {
                        const map = {
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#039;',
                        };

                        return String(value ?? '').replace(/[&<>"']/g, (char) => map[char] ?? char);
                    },
                    expenseRowHtml(expense) {
                        const isApproved = Boolean(expense.is_approved);
                        const isRejected = Boolean(expense.is_rejected);
                        const statusBadge = isApproved
                            ? '<span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Approved</span>'
                            : isRejected
                                ? '<span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-xs font-medium text-rose-700">Rejected</span>'
                                : '<span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">Pending Approval</span>';

                        return `
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${this.escapeHtml(expense.expense_date)}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${this.escapeHtml(expense.category)}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">BDT ${Number(expense.amount || 0).toFixed(2)}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">${this.escapeHtml(expense.description || '-')}</td>
                                <td class="px-4 py-3 text-sm">${statusBadge}</td>
                            </tr>
                        `;
                    },
                    reloadRowHtml(reload) {
                        return `
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${this.escapeHtml(reload.reloaded_at)}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${this.escapeHtml(reload.location)}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">BDT ${Number(reload.reload_amount || 0).toFixed(2)}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">${this.escapeHtml(reload.note || '-')}</td>
                            </tr>
                        `;
                    },
                    updateFinancialSummary(summary) {
                        this.financialSummary = summary;
                    },
                    async submitExpense() {
                        this.expenseSubmitting = true;
                        this.expenseErrors = {};

                        const response = await fetch(this.expenseUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(this.expenseForm),
                        });

                        const data = await response.json();
                        this.expenseSubmitting = false;

                        if (! response.ok) {
                            if (response.status === 422) {
                                this.expenseErrors = data.errors ?? {};
                                return;
                            }

                            Toastify({
                                text: data.message ?? 'Failed to record expense.',
                                duration: 4000,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: '#ef4444',
                                stopOnFocus: true,
                            }).showToast();

                            return;
                        }

                        const tbody = document.getElementById('driver-expenses-body');
                        const emptyState = document.getElementById('driver-expenses-empty');

                        if (tbody) {
                            tbody.insertAdjacentHTML('afterbegin', this.expenseRowHtml(data.expense));
                        }

                        if (emptyState) {
                            emptyState.remove();
                        }

                        if (data.financial_summary) {
                            this.updateFinancialSummary(data.financial_summary);
                        }

                        this.closeExpenseModal();

                        Toastify({
                            text: data.message ?? 'Expense recorded successfully.',
                            duration: 2500,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: '#22c55e',
                            stopOnFocus: true,
                        }).showToast();
                    },
                    async submitReload() {
                        this.reloadSubmitting = true;
                        this.reloadErrors = {};

                        const response = await fetch(this.reloadUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(this.reloadForm),
                        });

                        const data = await response.json();
                        this.reloadSubmitting = false;

                        if (! response.ok) {
                            if (response.status === 422) {
                                this.reloadErrors = data.errors ?? {};
                                return;
                            }

                            Toastify({
                                text: data.message ?? 'Failed to add reload history.',
                                duration: 4000,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: '#ef4444',
                                stopOnFocus: true,
                            }).showToast();

                            return;
                        }

                        const tbody = document.getElementById('driver-reload-body');
                        const emptyState = document.getElementById('driver-reload-empty');

                        if (tbody) {
                            tbody.insertAdjacentHTML('afterbegin', this.reloadRowHtml(data.reload));
                        }

                        if (emptyState) {
                            emptyState.remove();
                        }

                        this.closeReloadModal();

                        Toastify({
                            text: data.message ?? 'Reload history added successfully.',
                            duration: 2500,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: '#22c55e',
                            stopOnFocus: true,
                        }).showToast();
                    },
                };
            }
        </script>
    @endpush
@endsection