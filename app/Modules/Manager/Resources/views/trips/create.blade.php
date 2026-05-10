@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Create Trip" />

    <div class="space-y-6" x-data="tripGoodsForm()">
        <x-common.component-card title="Trip Creation Form" desc="Create a trip and initialize goods details.">
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('manager.trips.store') }}" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <select name="client_id" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700">
                        <option value="">Select client</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" @selected((string) old('client_id') === (string) $client->id)>{{ $client->company_name ?? $client->user?->name ?? ('Client #'.$client->id) }}</option>
                        @endforeach
                    </select>
                    <select name="truck_id" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700">
                        <option value="">Select truck</option>
                        @foreach ($trucks as $truck)
                            <option value="{{ $truck->id }}" @selected((string) old('truck_id') === (string) $truck->id)>{{ $truck->truck_number }}</option>
                        @endforeach
                    </select>
                    <select name="driver_id" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700">
                        <option value="">Select driver</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}" @selected((string) old('driver_id') === (string) $driver->id)>{{ $driver->user?->name ?? ('Driver #'.$driver->id) }}</option>
                        @endforeach
                    </select>
                    <select name="status_id" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700">
                        <option value="">Select initial status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}" @selected((string) old('status_id') === (string) $status->id)>{{ ucfirst(str_replace('_', ' ', $status->name)) }}</option>
                        @endforeach
                    </select>
                    <input name="pickup_point" value="{{ old('pickup_point') }}" placeholder="Pickup point" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                    <input name="delivery_point" value="{{ old('delivery_point') }}" placeholder="Delivery point" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                    <input name="load_date" value="{{ old('load_date') }}" type="date" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                    <input name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" type="date" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                    <input name="trip_rate" value="{{ old('trip_rate') }}" type="number" step="0.01" min="0" placeholder="Trip rate" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                    <input name="advance_payment" value="{{ old('advance_payment') }}" type="number" step="0.01" min="0" placeholder="Advance payment" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                </div>

                <textarea name="route_description" rows="2" placeholder="Route description" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-700">{{ old('route_description') }}</textarea>
                <textarea name="goods_description" rows="2" placeholder="Goods summary" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-700">{{ old('goods_description') }}</textarea>
                <textarea name="notes" rows="2" placeholder="Internal notes" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-700">{{ old('notes') }}</textarea>
                <textarea name="sms_note" rows="2" placeholder="SMS note" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-700">{{ old('sms_note') }}</textarea>

                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-800 dark:text-white/90">Goods Items</h2>
                        <button type="button" @click="addRow" class="rounded bg-brand-500 px-3 py-1 text-sm text-white">Add Row</button>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(item, index) in goods" :key="index">
                            @include('manager::trips.partials._goods_row')
                        </template>
                    </div>
                    <div class="mt-3 text-sm font-medium text-gray-600 dark:text-gray-300">Subtotal: <span x-text="subtotal.toFixed(2)"></span></div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                    <a href="{{ route('manager.trips.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">Cancel</a>
                    <button class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-medium text-white">Save Trip</button>
                </div>
            </form>
        </x-common.component-card>
    </div>

    @push('scripts')
        <script>
            function tripGoodsForm() {
                return {
                    goods: [{ item_name: '', unit: '', quantity: 1, unit_price: 0 }],
                    get subtotal() {
                        return this.goods.reduce((sum, item) => sum + ((Number(item.quantity) || 0) * (Number(item.unit_price) || 0)), 0);
                    },
                    addRow() {
                        this.goods.push({ item_name: '', unit: '', quantity: 1, unit_price: 0 });
                    },
                    removeRow(index) {
                        if (this.goods.length > 1) {
                            this.goods.splice(index, 1);
                        }
                    },
                }
            }
        </script>
    @endpush
@endsection
