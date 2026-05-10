<div class="grid grid-cols-1 gap-2 rounded-lg border border-gray-200 p-3 md:grid-cols-12 dark:border-gray-700">
    <div class="md:col-span-4">
        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-400">
            Item Name
            <span class="text-red-500" aria-hidden="true">*</span>
        </label>
        <input :name="`goods[${index}][item_name]`" x-model="item.item_name" required placeholder="Item name" class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-400">
            Unit
            <span class="text-red-500" aria-hidden="true">*</span>
        </label>
        <input :name="`goods[${index}][unit]`" x-model="item.unit" required placeholder="Unit" class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-400">
            Qty
            <span class="text-red-500" aria-hidden="true">*</span>
        </label>
        <input :name="`goods[${index}][quantity]`" x-model="item.quantity" required type="number" step="0.01" min="0" placeholder="Qty" class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    </div>
    <div class="md:col-span-3">
        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-400">
            Unit Price
            <span class="text-red-500" aria-hidden="true">*</span>
        </label>
        <input :name="`goods[${index}][unit_price]`" x-model="item.unit_price" required type="number" step="0.01" min="0" placeholder="Unit price" class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    </div>
    <div class="md:col-span-1 self-end">
        <button type="button" @click="removeRow(index)" class="h-10 w-full rounded-lg border border-red-200 text-xs text-red-600">Remove</button>
    </div>
    <div class="md:col-span-12 text-right text-xs font-medium text-gray-600 dark:text-gray-300">Row total: <span x-text="rowSubtotal(index).toFixed(2)"></span></div>
</div>
