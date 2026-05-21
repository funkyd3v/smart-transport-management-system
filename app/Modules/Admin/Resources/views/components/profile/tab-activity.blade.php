<section id="tab-panel-activity" class="tab-panel hidden">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-4 grid grid-cols-1 gap-3 lg:grid-cols-5">
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-500">From</label>
                <x-form.date-picker id="activity-from" name="activity-from" placeholder="From date" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-500">To</label>
                <x-form.date-picker id="activity-to" name="activity-to" placeholder="To date" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-500">Action Type</label>
                <select id="activity-type" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-600">
                    <option value="all">All</option>
                    <option value="trip">Trip actions</option>
                    <option value="payment">Payment actions</option>
                    <option value="profile">Profile actions</option>
                    <option value="auth">Auth actions</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="button" onclick="loadActivityLog(1)" class="w-full rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Filter</button>
            </div>
            <div class="flex items-end">
                <button type="button" onclick="exportActivity()" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Export CSV</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-gray-200 text-left text-xs uppercase text-gray-500 dark:border-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-3 py-2">Date & Time</th>
                        <th class="px-3 py-2">Action</th>
                        <th class="px-3 py-2">Description</th>
                        <th class="px-3 py-2">IP</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Details</th>
                    </tr>
                </thead>
                <tbody id="activity-table-body"></tbody>
            </table>
        </div>

        <div id="activity-pagination" class="mt-4 flex flex-wrap gap-2"></div>
    </div>
</section>
