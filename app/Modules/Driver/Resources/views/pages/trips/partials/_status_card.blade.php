<x-common.component-card title="Status Action" desc="Move the trip forward from the current status.">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="space-y-2">
            <p class="text-sm text-gray-500 dark:text-gray-400">Current Status</p>
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium" :class="badgeClass()" x-text="statusLabel()"></span>
            <p x-show="completionRequestedAt" class="text-xs text-amber-700 dark:text-amber-300">Completion requested. Waiting for manager approval.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="button" x-show="status === 'created'" :disabled="statusActionSubmitting" @click="updateStatus('in_progress')" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-70">
                <svg x-show="statusActionSubmitting" class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                Start Trip
            </button>

            <button type="button" x-show="status === 'in_progress' && ! completionRequestedAt" :disabled="statusActionSubmitting" @click="updateStatus('completed')" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-70">
                <svg x-show="statusActionSubmitting" class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                Mark Complete
            </button>

            <span x-show="completionRequestedAt" class="inline-flex items-center rounded-lg bg-amber-100 px-4 py-2 text-sm font-medium text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">Waiting for approval</span>
            <span x-show="status === 'completed'" class="inline-flex items-center rounded-lg bg-green-100 px-4 py-2 text-sm font-medium text-green-700 dark:bg-green-500/15 dark:text-green-300">Trip completed</span>
        </div>
    </div>
</x-common.component-card>