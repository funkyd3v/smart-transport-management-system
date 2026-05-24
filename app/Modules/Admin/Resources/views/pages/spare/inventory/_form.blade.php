@php
    $isEdit = isset($part);
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.spare.inventory.update', $part) : route('admin.spare.inventory.store') }}" class="space-y-6" x-data="{ condition: @js(old('condition', $part->condition ?? 'new')) }">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Part Name <span class="text-red-500">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name', $part->name ?? '') }}" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none" />
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="category_id" class="mb-1 block text-sm font-medium text-slate-700">Category <span class="text-red-500">*</span></label>
            <select id="category_id" name="category_id" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none">
                <option value="">Select category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $part->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="condition" class="mb-1 block text-sm font-medium text-slate-700">Condition <span class="text-red-500">*</span></label>
            <select id="condition" name="condition" x-model="condition" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none">
                <option value="new">New</option>
                <option value="old">Old</option>
            </select>
            @error('condition') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="quantity" class="mb-1 block text-sm font-medium text-slate-700">Stock Quantity <span class="text-red-500">*</span></label>
            <input id="quantity" name="quantity" type="number" min="0" value="{{ old('quantity', $part->quantity ?? 0) }}" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none" />
            @error('quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="purchase_price" class="mb-1 block text-sm font-medium text-slate-700">Purchase Price <span class="text-red-500">*</span></label>
            <input id="purchase_price" name="purchase_price" type="number" min="0" step="0.01" value="{{ old('purchase_price', $part->purchase_price ?? 0) }}" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none" />
            @error('purchase_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2" x-show="condition === 'new'" x-cloak>
        <div>
            <label for="source_memo_number" class="mb-1 block text-sm font-medium text-slate-700">Vendor Memo Number <span class="text-red-500">*</span></label>
            <input id="source_memo_number" name="source_memo_number" type="text" value="{{ old('source_memo_number', $part->source_memo_number ?? '') }}" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none" />
            @error('source_memo_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2" x-show="condition === 'old'" x-cloak>
        <div>
            <label for="source_truck_id" class="mb-1 block text-sm font-medium text-slate-700">Source Truck Number <span class="text-red-500">*</span></label>
            <select id="source_truck_id" name="source_truck_id" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none">
                <option value="">Select truck</option>
                @foreach ($trucks as $truck)
                    <option value="{{ $truck->id }}" @selected((string) old('source_truck_id', $part->source_truck_id ?? '') === (string) $truck->id)>{{ $truck->truck_number }}</option>
                @endforeach
            </select>
            @error('source_truck_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">{{ $isEdit ? 'Update Part' : 'Save Part' }}</button>
        <a href="{{ route('admin.spare.inventory.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
    </div>
</form>
