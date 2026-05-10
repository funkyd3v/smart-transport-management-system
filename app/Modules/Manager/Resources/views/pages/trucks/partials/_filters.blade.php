<div x-data="truckFilters()">
    <form x-ref="form" data-truck-filters-form method="GET" action="{{ route('manager.trucks.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-3 xl:w-3/4">
        <input
            name="search"
            type="text"
            value="{{ $filters['search'] ?? '' }}"
            @input.debounce.400ms="submit()"
            placeholder="Search by truck number"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
        />

        <input
            name="truck_type"
            type="text"
            value="{{ $filters['truck_type'] ?? '' }}"
            @input.debounce.400ms="submit()"
            placeholder="Filter by truck type"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
        />

        <select
            name="status"
            @change="submit()"
            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
        >
            <option value="">All Status</option>
            <option value="idle" @selected(($filters['status'] ?? '') === 'idle')>Idle</option>
            <option value="on_trip" @selected(($filters['status'] ?? '') === 'on_trip')>On Trip</option>
            <option value="under_workshop" @selected(($filters['status'] ?? '') === 'under_workshop')>Under Workshop</option>
        </select>
    </form>
</div>
