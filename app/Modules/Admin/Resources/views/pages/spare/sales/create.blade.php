@extends('admin::layouts.app')

@section('title', 'Admin - Record Spare Sale')

@section('content')
<x-common.page-breadcrumb pageTitle="Spare Sales / Create" />

<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
    x-data="saleForm({
        saleTypes: @js($saleTypes->map(fn ($type) => ['id' => $type->id, 'name' => $type->name])->values()),
        priceEndpoint: @js(route('admin.spare.inventory.price', ['part' => '__PART__'])),
        selectedSaleTypeId: @js(old('sale_type_id')),
        selectedPartId: @js(old('spare_part_id')),
        salePrice: @js(old('sale_price', 0)),
        quantity: @js(old('quantity')),
    })"
    x-init="init()">
    <h2 class="text-xl font-semibold text-slate-900">Record New Sale</h2>
    <p class="mt-1 text-sm text-slate-500">Capture spare part, security solution, or monthly maintenance sales.</p>

    <form method="POST" action="{{ route('admin.spare.sales.store') }}" class="mt-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="sale_type_id" class="mb-1 block text-sm font-medium text-slate-700">Sale Type <span class="text-red-500">*</span></label>
                <select id="sale_type_id" name="sale_type_id" x-model="selectedSaleTypeId" @change="handleSaleTypeChange" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none">
                    <option value="">Select sale type</option>
                    @foreach ($saleTypes as $saleType)
                        <option value="{{ $saleType->id }}">{{ ucwords(str_replace('_', ' ', $saleType->name)) }}</option>
                    @endforeach
                </select>
                @error('sale_type_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="buyer_name" class="mb-1 block text-sm font-medium text-slate-700">Buyer Name <span class="text-red-500">*</span></label>
                <input id="buyer_name" name="buyer_name" type="text" value="{{ old('buyer_name') }}" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none" />
                @error('buyer_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2" x-show="isSparePartSale" x-cloak>
            <div>
                <label for="spare_part_id" class="mb-1 block text-sm font-medium text-slate-700">Spare Part <span class="text-red-500">*</span></label>
                <select id="spare_part_id" name="spare_part_id" x-model="selectedPartId" @change="fetchPartPrice" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none">
                    <option value="">Select part</option>
                    @foreach ($spareParts as $part)
                        <option value="{{ $part->id }}">{{ $part->name }} (Stock: {{ $part->quantity }})</option>
                    @endforeach
                </select>
                @error('spare_part_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="quantity" class="mb-1 block text-sm font-medium text-slate-700">Quantity <span class="text-red-500">*</span></label>
                <input id="quantity" name="quantity" type="number" min="1" x-model.number="quantity" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none" />
                <p class="mt-1 text-xs text-slate-500">Available stock: <span x-text="availableStock"></span></p>
                @error('quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Purchase Price Snapshot</label>
                <input type="text" :value="purchasePrice.toFixed(2)" class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700" readonly />
            </div>

            <div>
                <label for="sale_price" class="mb-1 block text-sm font-medium text-slate-700">Sale Price <span class="text-red-500">*</span></label>
                <input id="sale_price" name="sale_price" type="number" min="0" step="0.01" x-model.number="salePrice" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none" />
                @error('sale_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Profit Preview</label>
                <div
                    :class="isNegativeProfit
                        ? 'text-red-500 bg-red-500/10 border border-red-500/30'
                        : 'text-green-500 bg-green-500/10 border border-green-500/30'"
                    class="rounded-lg px-4 py-2 text-sm font-semibold transition-all duration-200"
                >
                    <span x-text="profitDisplay"></span>
                    <span x-show="isNegativeProfit" class="ml-2 text-xs font-normal opacity-80">
                        Selling below purchase price
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="sold_at" class="mb-1 block text-sm font-medium text-slate-700">Sold At <span class="text-red-500">*</span></label>
                <x-form.date-picker id="sold_at" name="sold_at" :defaultDate="old('sold_at')" placeholder="Select sold date" />
                @error('sold_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="note" class="mb-1 block text-sm font-medium text-slate-700" x-text="dynamicNoteLabel"></label>
                <textarea id="note" name="note" rows="3" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none">{{ old('note') }}</textarea>
                @error('note') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">Record Sale</button>
            <a href="{{ route('admin.spare.sales.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
</section>
@endsection

@push('scripts')
    <script>
        function saleForm(config) {
            return {
                saleTypes: config.saleTypes || [],
                priceEndpoint: config.priceEndpoint || '',
                selectedSaleTypeId: config.selectedSaleTypeId || '',
                selectedPartId: config.selectedPartId || '',
                salePrice: Number(config.salePrice || 0),
                quantity: config.quantity || '',
                purchasePrice: 0,
                availableStock: 0,
                get selectedSaleTypeName() {
                    const type = this.saleTypes.find((item) => String(item.id) === String(this.selectedSaleTypeId));
                    return type ? type.name : '';
                },
                get isSparePartSale() {
                    return this.selectedSaleTypeName === 'spare_part';
                },
                get dynamicNoteLabel() {
                    if (this.selectedSaleTypeName === 'security_solution') {
                        return 'Security Solution Description';
                    }

                    if (this.selectedSaleTypeName === 'monthly_maintenance') {
                        return 'Contract Note';
                    }

                    return 'Note';
                },
                get profitAmount() {
                    return Number(this.salePrice || 0) - Number(this.purchasePrice || 0);
                },
                get isNegativeProfit() {
                    return this.profitAmount < 0;
                },
                get profitDisplay() {
                    return 'BDT ' + this.profitAmount.toFixed(2);
                },
                handleSaleTypeChange() {
                    if (!this.isSparePartSale) {
                        this.selectedPartId = '';
                        this.quantity = '';
                        this.purchasePrice = 0;
                        this.availableStock = 0;
                    }
                },
                async fetchPartPrice() {
                    if (!this.selectedPartId) {
                        this.purchasePrice = 0;
                        this.availableStock = 0;
                        return;
                    }

                    const endpoint = this.priceEndpoint.replace('__PART__', this.selectedPartId);

                    try {
                        const response = await fetch(endpoint, { headers: { Accept: 'application/json' } });
                        const payload = await response.json();
                        this.purchasePrice = Number(payload?.data?.purchase_price || 0);
                        this.availableStock = Number(payload?.data?.available_stock || 0);
                    } catch (error) {
                        this.purchasePrice = 0;
                        this.availableStock = 0;
                        Toastify({
                            text: 'Could not load selected part price.',
                            duration: 3000,
                            gravity: 'top',
                            position: 'right',
                            style: { background: '#ef4444' },
                        }).showToast();
                    }
                },
                init() {
                    if (this.selectedPartId) {
                        this.fetchPartPrice();
                    }
                },
            };
        }
    </script>
@endpush
