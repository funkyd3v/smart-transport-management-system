@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Create Trip" />

    <div class="space-y-6" x-data="tripGoodsForm()">
        <x-common.component-card title="Trip Creation Form" desc="Create a trip and initialize goods details.">
            <form class="space-y-6" @submit.prevent="submitForm()">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Client
                            <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <select x-model="form.client_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700">
                            <option value="">Select client</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->company_name ?? $client->user?->name ?? ('Client #'.$client->id) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Truck
                            <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <select x-model="form.truck_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700">
                            <option value="">Select truck</option>
                            @foreach ($trucks as $truck)
                                <option value="{{ $truck->id }}">{{ $truck->truck_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Driver
                            <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <select x-model="form.driver_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700">
                            <option value="">Select driver</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name ?? $driver->user?->name ?? ('Driver #'.$driver->id) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Pickup Point / Port
                            <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input x-model="form.pickup_point" required placeholder="Pickup point / Port" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Delivery Point / Project
                            <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input x-model="form.delivery_point" required placeholder="Delivery point / Project" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Load Date & Time
                            <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input x-model="form.load_datetime" required type="datetime-local" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Trip Rate
                            <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input x-model="form.trip_rate" required type="number" step="0.01" min="0" placeholder="Trip rate" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                    </div>
                    <input x-model="form.advance_payment" type="number" step="0.01" min="0" placeholder="Advance payment" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
                </div>

                <textarea x-model="form.route_description" rows="2" placeholder="Route description" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-700"></textarea>
                <textarea x-model="form.goods_description" rows="2" placeholder="Goods summary" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-700"></textarea>
                <textarea x-model="form.notes" rows="2" placeholder="Internal notes" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-700"></textarea>
                <textarea x-model="form.sms_note" rows="2" placeholder="SMS note" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-700"></textarea>

                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-800 dark:text-white/90">
                            Goods Items
                            <span class="text-red-500" aria-hidden="true">*</span>
                        </h2>
                        <button type="button" @click="addRow" class="rounded bg-brand-500 px-3 py-1 text-sm text-white">Add Row</button>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(item, index) in goods" :key="index">
                            @include('manager::trips.partials._goods_row')
                        </template>
                    </div>
                    <div class="mt-3 text-sm font-medium text-gray-600 dark:text-gray-300">Grand Total: <span x-text="subtotal.toFixed(2)"></span></div>
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
                    form: {
                        client_id: '',
                        truck_id: '',
                        driver_id: '',
                        pickup_point: '',
                        delivery_point: '',
                        load_datetime: '',
                        trip_rate: '',
                        advance_payment: '',
                        route_description: '',
                        goods_description: '',
                        notes: '',
                        sms_note: '',
                    },
                    goods: [{ item_name: '', unit: '', quantity: 1, unit_price: 0 }],
                    get subtotal() {
                        return this.goods.reduce((sum, item) => sum + ((Number(item.quantity) || 0) * (Number(item.unit_price) || 0)), 0);
                    },
                    rowSubtotal(index) {
                        const item = this.goods[index] ?? { quantity: 0, unit_price: 0 };

                        return (Number(item.quantity) || 0) * (Number(item.unit_price) || 0);
                    },
                    addRow() {
                        this.goods.push({ item_name: '', unit: '', quantity: 1, unit_price: 0 });
                    },
                    removeRow(index) {
                        if (this.goods.length > 1) {
                            this.goods.splice(index, 1);
                        }
                    },
                    async submitForm() {
                        const payload = {
                            ...this.form,
                            goods: this.goods,
                        };

                        const response = await fetch('{{ route('manager.trips.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            Toastify({
                                text: data.message ?? 'Failed to create trip.',
                                duration: 4000,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: '#ef4444',
                                stopOnFocus: true,
                            }).showToast();

                            return;
                        }

                        Toastify({
                            text: data.message ?? 'Trip created successfully.',
                            duration: 2500,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: '#22c55e',
                            stopOnFocus: true,
                        }).showToast();

                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    },
                }
            }
        </script>
    @endpush
@endsection
