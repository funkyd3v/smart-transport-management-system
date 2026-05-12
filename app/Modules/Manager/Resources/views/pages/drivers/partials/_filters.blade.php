<div x-data="driverFilters()">
    <form x-ref="form" data-driver-filters-form method="GET" action="{{ route('manager.drivers.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-4 xl:w-5/6">
        <input
            x-ref="search"
            name="search"
            type="text"
            value="{{ $filters['search'] ?? '' }}"
            @input.debounce.400ms="submit()"
            placeholder="Search by name or mobile"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
        />

        <select
            name="driving_type"
            @change="submit()"
            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
        >
            <option value="">All Types</option>
            <option value="permanent" @selected(($filters['driving_type'] ?? '') === 'permanent')>Permanent</option>
            <option value="backup" @selected(($filters['driving_type'] ?? '') === 'backup')>Backup</option>
        </select>

        <select
            name="status"
            @change="submit()"
            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
        >
            <option value="">All Status</option>
            <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
            <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
        </select>

        <select
            name="is_approved"
            @change="submit()"
            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
        >
            <option value="">All</option>
            <option value="1" @selected(($filters['is_approved'] ?? '') === '1')>Approved</option>
            <option value="0" @selected(($filters['is_approved'] ?? '') === '0')>Unapproved</option>
        </select>
    </form>
</div>
