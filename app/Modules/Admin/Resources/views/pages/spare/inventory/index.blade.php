@extends('admin::layouts.app')

@section('title', 'Admin - Spare Inventory')

@section('content')
<x-common.page-breadcrumb pageTitle="Spare Inventory" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Parts</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format((int) ($stats['total_parts'] ?? 0)) }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Categories</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format((int) ($stats['total_categories'] ?? 0)) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm">
            <p class="text-sm text-amber-700">Low Stock Items</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format((int) ($stats['low_stock_items'] ?? 0)) }}</p>
        </article>
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">Total Inventory Value</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">BDT {{ number_format((float) ($stats['total_inventory_value'] ?? 0), 2) }}</p>
        </article>
    </section>

    <section id="inventory-ajax-region" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <form id="inventory-filter-form" method="GET" action="{{ route('admin.spare.inventory.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-5">
            <div class="md:col-span-2">
                <label for="search" class="mb-1 block text-xs font-medium text-slate-500">Search</label>
                <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Search by part name" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none" />
            </div>
            <div>
                <label for="category_id" class="mb-1 block text-xs font-medium text-slate-500">Category</label>
                <select id="category_id" name="category_id" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="condition" class="mb-1 block text-xs font-medium text-slate-500">Condition</label>
                <select id="condition" name="condition" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none">
                    <option value="">All</option>
                    <option value="new" @selected(request('condition') === 'new')>New</option>
                    <option value="old" @selected(request('condition') === 'old')>Old</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="h-11 flex-1 rounded-lg bg-sky-600 px-4 text-sm font-medium text-white hover:bg-sky-700">Filter</button>
                <a href="{{ route('admin.spare.inventory.index') }}" class="js-inventory-reset h-11 rounded-lg border border-slate-300 px-4 text-sm font-medium leading-[44px] text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>

        <div class="mt-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Inventory Register</h2>
            <a href="{{ route('admin.spare.inventory.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Add New Part</a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[1080px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Part Name</th>
                        <th class="px-3 py-3">Category</th>
                        <th class="px-3 py-3">Condition</th>
                        <th class="px-3 py-3">Source Reference</th>
                        <th class="px-3 py-3">Stock Qty</th>
                        <th class="px-3 py-3">Purchase Price</th>
                        <th class="px-3 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($parts as $part)
                        @php
                            $stock = (int) $part->quantity;
                            $stockClass = $stock <= 3
                                ? 'bg-red-100 text-red-700'
                                : ($stock <= 10 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700');
                            $sourceReference = $part->condition === 'new'
                                ? ($part->source_memo_number ?: '-')
                                : ($part->sourceTruck?->truck_number ?: '-');
                        @endphp
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $part->name }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $part->category?->name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ ucfirst((string) $part->condition) }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $sourceReference }}</td>
                            <td class="px-3 py-3"><span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $stockClass }}">{{ number_format($stock) }}</span></td>
                            <td class="px-3 py-3 text-slate-700">BDT {{ number_format((float) $part->purchase_price, 2) }}</td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.spare.inventory.edit', $part) }}" class="rounded border border-slate-300 px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50">Edit</a>
                                    <form method="POST" action="{{ route('admin.spare.inventory.destroy', $part) }}" class="js-delete-part inline-block" data-name="{{ $part->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded border border-red-200 px-3 py-1 text-xs font-medium text-red-700 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-slate-500">No spare parts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="inventory-pagination" class="mt-4">{{ $parts->links() }}</div>
    </section>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function bindInventoryDeleteActions() {
            document.querySelectorAll('#inventory-ajax-region .js-delete-part').forEach((form) => {
                if (form.dataset.boundDelete === '1') {
                    return;
                }

                form.dataset.boundDelete = '1';
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    Swal.fire({
                        title: 'Delete spare part?',
                        text: `This will archive ${form.dataset.name}.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc2626',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        }

        async function refreshInventoryRegion(url, pushState = true) {
            const region = document.getElementById('inventory-ajax-region');

            if (!region) {
                return;
            }

            region.classList.add('opacity-60', 'pointer-events-none');

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'text/html',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to load inventory.');
                }

                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const incomingRegion = doc.getElementById('inventory-ajax-region');

                if (!incomingRegion) {
                    throw new Error('Inventory region not found in response.');
                }

                region.innerHTML = incomingRegion.innerHTML;

                if (pushState) {
                    window.history.pushState({}, '', url);
                }

                bindInventoryDeleteActions();
                bindInventoryFilters();
            } catch (error) {
                Toastify({
                    text: 'Could not load filtered inventory.',
                    duration: 3000,
                    gravity: 'top',
                    position: 'right',
                    style: { background: '#ef4444' },
                }).showToast();
            } finally {
                region.classList.remove('opacity-60', 'pointer-events-none');
            }
        }

        function bindInventoryFilters() {
            const form = document.getElementById('inventory-filter-form');
            const region = document.getElementById('inventory-ajax-region');

            if (!form || !region || form.dataset.boundFilter === '1') {
                return;
            }

            form.dataset.boundFilter = '1';

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const params = new URLSearchParams(new FormData(form));
                refreshInventoryRegion(`${form.action}?${params.toString()}`);
            });

            const resetLink = region.querySelector('.js-inventory-reset');
            if (resetLink && resetLink.dataset.boundReset !== '1') {
                resetLink.dataset.boundReset = '1';
                resetLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    refreshInventoryRegion(resetLink.href);
                });
            }

            const paginationLinks = region.querySelectorAll('#inventory-pagination a[href]');
            paginationLinks.forEach((link) => {
                if (link.dataset.boundPage === '1') {
                    return;
                }

                link.dataset.boundPage = '1';
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    refreshInventoryRegion(link.href);
                });
            });
        }

        window.addEventListener('popstate', function() {
            refreshInventoryRegion(window.location.href, false);
        });

        bindInventoryDeleteActions();
        bindInventoryFilters();
    </script>
@endpush
