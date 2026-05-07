@extends('admin::layouts.app')

@section('content')
<div class="space-y-6" x-data="tripGoodsForm()">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Create Trip</h1>
    <form method="POST" action="{{ route('admin.trips.store') }}" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <input name="client_id" placeholder="Client ID" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <input name="truck_id" placeholder="Truck ID" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <input name="driver_id" placeholder="Driver ID" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <input name="status_id" placeholder="Status ID (pending)" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <input name="pickup_point" placeholder="Pickup" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <input name="delivery_point" placeholder="Delivery" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <input name="load_date" type="datetime-local" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <input name="expected_delivery_date" type="datetime-local" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <input name="trip_rate" type="number" step="0.01" placeholder="Trip Rate" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <input name="advance_payment" type="number" step="0.01" placeholder="Advance" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
        </div>

        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-semibold">Goods</h2>
                <button type="button" @click="addRow" class="rounded bg-brand-500 px-3 py-1 text-sm text-white">Add Row</button>
            </div>
            <div class="space-y-2">
                <template x-for="(item, index) in goods" :key="index">
                    @include('trip::admin.trips.partials._goods_row')
                </template>
            </div>
            <div class="mt-3 text-sm font-medium">Subtotal: <span x-text="subtotal.toFixed(2)"></span></div>
        </div>

        <button class="rounded bg-brand-500 px-5 py-2.5 text-white">Save Trip</button>
    </form>
</div>

@push('scripts')
<script>
function tripGoodsForm() {
    return {
        goods: [{ item_name: '', unit: '', quantity: 1, unit_price: 0 }],
        get subtotal() { return this.goods.reduce((sum, item) => sum + ((Number(item.quantity) || 0) * (Number(item.unit_price) || 0)), 0); },
        addRow() { this.goods.push({ item_name: '', unit: '', quantity: 1, unit_price: 0 }); },
        removeRow(index) { if (this.goods.length > 1) { this.goods.splice(index, 1); } },
    }
}
</script>
@endpush
@endsection
