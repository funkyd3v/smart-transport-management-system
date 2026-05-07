<div class="grid grid-cols-1 gap-2 md:grid-cols-6">
    <input :name="`goods[${index}][item_name]`" x-model="item.item_name" placeholder="Item name" class="h-10 rounded border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    <input :name="`goods[${index}][unit]`" x-model="item.unit" placeholder="Unit" class="h-10 rounded border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    <input :name="`goods[${index}][quantity]`" x-model="item.quantity" type="number" step="0.001" placeholder="Qty" class="h-10 rounded border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    <input :name="`goods[${index}][unit_price]`" x-model="item.unit_price" type="number" step="0.01" placeholder="Unit price" class="h-10 rounded border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
    <input :value="((Number(item.quantity) || 0) * (Number(item.unit_price) || 0)).toFixed(2)" readonly class="h-10 rounded border border-gray-300 bg-gray-50 px-3 text-sm dark:border-gray-700 dark:bg-gray-800" />
    <button type="button" @click="removeRow(index)" class="h-10 rounded bg-red-500 px-3 text-sm text-white">Remove</button>
</div>
