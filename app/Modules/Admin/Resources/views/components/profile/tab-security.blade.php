<section id="tab-panel-security" class="tab-panel hidden">
    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Change Password</h3>
            <form id="password-form" class="mt-4 space-y-4" onsubmit="event.preventDefault(); submitPasswordChange();">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input id="current-password-input" name="current_password" type="password" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 pr-10 text-sm dark:border-gray-600">
                        <button type="button" data-toggle-password="current-password-input" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"><i class="ti ti-eye"></i></button>
                    </div>
                    <span class="text-sm text-red-500" id="current_password-error"></span>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">New Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input id="new-password-input" name="password" type="password" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 pr-10 text-sm dark:border-gray-600">
                        <button type="button" data-toggle-password="new-password-input" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"><i class="ti ti-eye"></i></button>
                    </div>
                    <span class="text-sm text-red-500" id="password-error"></span>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input id="confirm-password-input" name="password_confirmation" type="password" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 pr-10 text-sm dark:border-gray-600">
                        <button type="button" data-toggle-password="confirm-password-input" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"><i class="ti ti-eye"></i></button>
                    </div>
                    <span class="text-sm text-red-500" id="confirm-password-error"></span>
                </div>

                <div>
                    <div class="mb-2 flex gap-2">
                        <span id="pwd-seg-1" class="h-2 flex-1 rounded bg-gray-200 dark:bg-gray-700"></span>
                        <span id="pwd-seg-2" class="h-2 flex-1 rounded bg-gray-200 dark:bg-gray-700"></span>
                        <span id="pwd-seg-3" class="h-2 flex-1 rounded bg-gray-200 dark:bg-gray-700"></span>
                        <span id="pwd-seg-4" class="h-2 flex-1 rounded bg-gray-200 dark:bg-gray-700"></span>
                    </div>
                    <p id="password-strength-text" class="text-xs text-gray-500 dark:text-gray-400"></p>
                </div>

                <button id="password-save-btn" type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                    <span id="password-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                    Update Password
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Active Sessions</h3>
                <button type="button" onclick="terminateOtherSessions()" class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:border-red-500/30 dark:text-red-300 dark:hover:bg-red-500/10">Sign out all other devices</button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-gray-200 text-left text-xs uppercase text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-3 py-2">Device</th>
                            <th class="px-3 py-2">Location</th>
                            <th class="px-3 py-2">Last Active</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody id="sessions-table-body"></tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Login History</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-gray-200 text-left text-xs uppercase text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-3 py-2">Date/Time</th>
                            <th class="px-3 py-2">IP Address</th>
                            <th class="px-3 py-2">Browser</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginHistory as $history)
                            <tr class="border-b border-gray-100 dark:border-gray-700/60">
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ optional($history->created_at)->format('d M Y, h:i A') }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $history->ip_address }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $history->parsed_user_agent['browser'] }} · {{ $history->parsed_user_agent['os'] }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $history->status === 'success' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300' }}">{{ ucfirst($history->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-4 text-center text-gray-500">No login history available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
