@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Manager Profile" />

    <div class="space-y-6" x-data="managerProfilePage()">
        <section class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-50/60 via-transparent to-emerald-50/30 dark:from-brand-500/5 dark:to-emerald-500/5"></div>

            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    <img
                        id="managerAvatarPreview"
                        src="{{ $manager->avatar_url }}"
                        alt="{{ $manager->name }}"
                        class="h-20 w-20 rounded-2xl border border-gray-200 object-cover dark:border-gray-700"
                    />

                    <div>
                        <h2 id="managerNameHeading" class="text-2xl font-bold text-gray-900 dark:text-white">{{ $manager->name }}</h2>
                        <p id="managerEmailHeading" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $manager->email }}</p>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">
                                Operations Manager
                            </span>
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                                {{ $manager->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid w-full grid-cols-2 gap-3 sm:grid-cols-3 lg:w-auto lg:min-w-[420px]">
                    <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-900/40">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Clients</p>
                        <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ number_format((int) $stats['managed_clients']) }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-900/40">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Drivers</p>
                        <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ number_format((int) $stats['managed_drivers']) }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-900/40">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Fleet</p>
                        <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ number_format((int) $stats['managed_trucks']) }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-slate-50 via-transparent to-brand-50/40 dark:from-white/[0.02] dark:to-brand-500/5"></div>

                    <div class="relative">
                        <div class="mb-5 flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Account Details</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Maintain your core manager identity and communication details.</p>
                            </div>
                        </div>

                        <form id="managerProfileForm" class="space-y-5" @submit.prevent="submitProfileUpdate">
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name <span class="text-red-500">*</span></label>
                                    <input id="name" name="name" type="text" value="{{ $manager->name }}" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                                </div>

                                <div>
                                    <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone <span class="text-red-500">*</span></label>
                                    <input id="phone" name="phone" type="tel" inputmode="numeric" pattern="[0-9]*" value="{{ $manager->phone }}" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Work Email <span class="text-red-500">*</span></label>
                                    <input id="email" name="email" type="email" value="{{ $manager->email }}" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Changing email will require verification again for security compliance.</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="avatar" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Profile Photo</label>
                                    <input id="avatar" name="avatar" type="file" accept="image/png,image/jpeg,image/webp" class="block w-full rounded-lg border border-gray-300 bg-transparent p-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-brand-600 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-brand-700 dark:border-gray-700" />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Allowed: JPG, PNG, WEBP. Max 10MB.</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                                <button type="submit" class="inline-flex items-center rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-700">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Change Password</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Use a strong password to protect operational data and access.</p>

                    <form id="managerPasswordForm" class="mt-4 space-y-4" @submit.prevent="submitPasswordUpdate">
                        <div>
                            <label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password <span class="text-red-500">*</span></label>
                            <input id="current_password" name="current_password" type="password" autocomplete="current-password" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                        </div>

                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">New Password <span class="text-red-500">*</span></label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                Must be at least 8 characters and include uppercase and lowercase letters, a number, and a symbol.
                            </p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password <span class="text-red-500">*</span></label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                        </div>

                        <div class="flex items-center justify-end border-t border-gray-100 pt-4 dark:border-gray-800">
                            <button type="submit" class="inline-flex items-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6 xl:col-span-1">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Operational Snapshot</h3>
                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between"><span class="text-gray-500 dark:text-gray-400">Active Trips</span><span class="font-semibold text-gray-900 dark:text-white">{{ number_format((int) $stats['active_trips']) }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500 dark:text-gray-400">Completed This Month</span><span class="font-semibold text-gray-900 dark:text-white">{{ number_format((int) $stats['completed_this_month']) }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500 dark:text-gray-400">Month Revenue</span><span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format((float) $stats['month_revenue'], 2) }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500 dark:text-gray-400">Outstanding Due</span><span class="font-semibold text-amber-600 dark:text-amber-400">{{ number_format((float) $stats['outstanding_due'], 2) }}</span></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Account Integrity</h3>
                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between"><span class="text-gray-500 dark:text-gray-400">Role</span><span class="font-semibold text-gray-900 dark:text-white">Manager</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500 dark:text-gray-400">Email Verified</span><span class="font-semibold {{ $manager->email_verified_at ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">{{ $manager->email_verified_at ? 'Yes' : 'No' }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500 dark:text-gray-400">Last Login</span><span class="font-semibold text-gray-900 dark:text-white">{{ $manager->last_login_at?->diffForHumans() ?? 'Not recorded' }}</span></div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(249,115,22,0.10),transparent_45%)] dark:bg-[radial-gradient(circle_at_top_right,rgba(249,115,22,0.12),transparent_45%)]"></div>

                    <div class="relative">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Manager Focus</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">A quick operational readout for daily decision making.</p>

                        <div class="mt-4 space-y-4">
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/40">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Receivables Pressure</p>
                                <p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">{{ (float) $stats['outstanding_due'] > 0 ? 'Follow Up Required' : 'Healthy' }}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ (float) $stats['outstanding_due'] > 0 ? 'Outstanding collections need active follow-up from accounts and client operations.' : 'No critical due pressure detected in your managed trips.' }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Coverage</p>
                                    <p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">{{ number_format((int) $stats['managed_drivers']) }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Drivers under your supervision</p>
                                </div>
                                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Throughput</p>
                                    <p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">{{ number_format((int) $stats['completed_this_month']) }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Trips closed this month</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Trips Managed</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest operational records linked to your manager account.</p>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800">
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Trip</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Client</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Truck</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Load Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTrips as $trip)
                            <tr class="border-b border-gray-100 dark:border-gray-800/60">
                                <td class="px-3 py-3 text-sm font-medium text-gray-800 dark:text-gray-200">{{ $trip->trip_code }}</td>
                                <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $trip->client?->company_name ?? 'N/A' }}</td>
                                <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $trip->truck?->truck_number ?? 'N/A' }}</td>
                                <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', (string) ($trip->status?->name ?? 'unknown'))) }}</td>
                                <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $trip->load_date?->format('d M Y, h:i A') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No recent trips found for this manager account.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            function managerProfilePage() {
                return {
                    async submitProfileUpdate() {
                        const form = document.getElementById('managerProfileForm');
                        const formData = new FormData(form);
                        formData.append('_method', 'PATCH');

                        try {
                            const response = await fetch('{{ route('manager.profile.update') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: formData,
                            });

                            const payload = await response.json();

                            if (!response.ok) {
                                const firstError = payload.errors ? Object.values(payload.errors)[0]?.[0] : null;

                                Toastify({
                                    text: firstError ?? payload.message ?? 'Failed to update profile.',
                                    duration: 3500,
                                    gravity: 'top',
                                    position: 'right',
                                    backgroundColor: '#ef4444',
                                    stopOnFocus: true,
                                }).showToast();

                                return;
                            }

                            if (payload.user?.name) {
                                document.getElementById('managerNameHeading').textContent = payload.user.name;
                            }

                            if (payload.user?.email) {
                                document.getElementById('managerEmailHeading').textContent = payload.user.email;
                            }

                            if (payload.user?.avatar_url) {
                                const cacheBustedAvatarUrl = `${payload.user.avatar_url}${payload.user.avatar_url.includes('?') ? '&' : '?'}v=${Date.now()}`;
                                document.getElementById('managerAvatarPreview').src = cacheBustedAvatarUrl;
                            }

                            Toastify({
                                text: payload.message ?? 'Profile updated successfully.',
                                duration: 2500,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: '#22c55e',
                                stopOnFocus: true,
                            }).showToast();
                        } catch (_) {
                            Toastify({
                                text: 'Network error while updating profile.',
                                duration: 3500,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: '#ef4444',
                                stopOnFocus: true,
                            }).showToast();
                        }
                    },
                    async submitPasswordUpdate() {
                        const form = document.getElementById('managerPasswordForm');
                        const formData = new FormData(form);
                        formData.append('_method', 'PUT');

                        try {
                            const response = await fetch('{{ route('password.update') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: formData,
                            });

                            const payload = await response.json();

                            if (!response.ok) {
                                const firstError = payload.errors ? Object.values(payload.errors)[0]?.[0] : null;

                                Toastify({
                                    text: firstError ?? payload.message ?? 'Failed to update password.',
                                    duration: 3500,
                                    gravity: 'top',
                                    position: 'right',
                                    backgroundColor: '#ef4444',
                                    stopOnFocus: true,
                                }).showToast();

                                return;
                            }

                            form.reset();

                            Toastify({
                                text: payload.message ?? 'Password updated successfully.',
                                duration: 2500,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: '#22c55e',
                                stopOnFocus: true,
                            }).showToast();
                        } catch (_) {
                            Toastify({
                                text: 'Network error while updating password.',
                                duration: 3500,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: '#ef4444',
                                stopOnFocus: true,
                            }).showToast();
                        }
                    },
                };
            }
        </script>
    @endpush
@endsection
