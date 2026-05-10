<div class="grid grid-cols-1 gap-2 rounded-lg border border-gray-200 p-3 md:grid-cols-12 dark:border-gray-700">
    <input :name="`goods[${index}][item_name]`" x-model="item.item_name" placeholder="Item name" class="md:col-span-4 h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    <input :name="`goods[${index}][unit]`" x-model="item.unit" placeholder="Unit" class="md:col-span-2 h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    <input :name="`goods[${index}][quantity]`" x-model="item.quantity" type="number" step="0.01" min="0" placeholder="Qty" class="md:col-span-2 h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    <input :name="`goods[${index}][unit_price]`" x-model="item.unit_price" type="number" step="0.01" min="0" placeholder="Unit price" class="md:col-span-3 h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    <button type="button" @click="removeRow(index)" class="md:col-span-1 h-10 rounded-lg border border-red-200 text-xs text-red-600">Remove</button>
</div>
