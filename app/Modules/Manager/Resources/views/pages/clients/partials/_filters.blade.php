<div x-data="clientFilters()">
    <form x-ref="form" data-client-filters-form method="GET" action="{{ route('manager.clients.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-3 xl:w-3/4">
        <input
            x-ref="search"
            name="search"
            type="text"
            value="{{ $filters['search'] ?? '' }}"
            @input.debounce.400ms="submit()"
            placeholder="Search by client name or contact"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
        />

        <select
            name="client_type"
            @change="submit()"
            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
        >
            <option value="">All Types</option>
            <option value="port" @selected(($filters['client_type'] ?? '') === 'port')>Port</option>
            <option value="contractual" @selected(($filters['client_type'] ?? '') === 'contractual')>Contractual</option>
            <option value="mega_project" @selected(($filters['client_type'] ?? '') === 'mega_project')>Mega Project</option>
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
    </form>
</div>
