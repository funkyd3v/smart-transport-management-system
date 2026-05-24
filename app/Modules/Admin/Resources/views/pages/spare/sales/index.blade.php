@extends('admin::layouts.app')

@section('title', 'Admin - Spare Sales')

@section('content')
<x-common.page-breadcrumb pageTitle="Spare Sales" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Sales</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format((int) ($stats['total_sales'] ?? 0)) }}</p>
        </article>
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">Total Revenue</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">BDT {{ number_format((float) ($stats['total_revenue'] ?? 0), 2) }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm">
            <p class="text-sm text-emerald-700">Total Profit</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">BDT {{ number_format((float) ($stats['total_profit'] ?? 0), 2) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm">
            <p class="text-sm text-amber-700">This Month Sales</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format((int) ($stats['this_month_sales'] ?? 0)) }}</p>
        </article>
    </section>

    <section id="sales-ajax-region" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <form id="sales-filter-form" method="GET" action="{{ route('admin.spare.sales.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-6">
            <div class="md:col-span-2">
                <label for="search" class="mb-1 block text-xs font-medium text-slate-500">Search</label>
                <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Search buyer or part name" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none" />
            </div>
            <div>
                <label for="sale_type_id" class="mb-1 block text-xs font-medium text-slate-500">Sale Type</label>
                <select id="sale_type_id" name="sale_type_id" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-sky-500 focus:outline-none">
                    <option value="">All</option>
                    @foreach ($saleTypes as $saleType)
                        <option value="{{ $saleType->id }}" @selected((string) request('sale_type_id') === (string) $saleType->id)>{{ ucwords(str_replace('_', ' ', $saleType->name)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from" class="mb-1 block text-xs font-medium text-slate-500">From</label>
                <x-form.date-picker id="from" name="from" :defaultDate="request('from')" placeholder="From date" />
            </div>
            <div>
                <label for="to" class="mb-1 block text-xs font-medium text-slate-500">To</label>
                <x-form.date-picker id="to" name="to" :defaultDate="request('to')" placeholder="To date" />
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="h-11 flex-1 rounded-lg bg-sky-600 px-4 text-sm font-medium text-white hover:bg-sky-700">Filter</button>
                <a href="{{ route('admin.spare.sales.index') }}" class="js-sales-reset h-11 rounded-lg border border-slate-300 px-4 text-sm font-medium leading-[44px] text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>

        <div class="mt-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Sales Register</h2>
            <a href="{{ route('admin.spare.sales.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Record New Sale</a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[1120px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Buyer Name</th>
                        <th class="px-3 py-3">Sale Type</th>
                        <th class="px-3 py-3">Part Name</th>
                        <th class="px-3 py-3">Qty</th>
                        <th class="px-3 py-3">Sale Price</th>
                        <th class="px-3 py-3">Profit</th>
                        <th class="px-3 py-3">Date</th>
                        <th class="px-3 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        @php $profit = (float) $sale->profit; @endphp
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $sale->buyer_name }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ ucwords(str_replace('_', ' ', (string) $sale->saleType?->name)) }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $sale->sparePart?->name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $sale->quantity ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">BDT {{ number_format((float) $sale->sale_price, 2) }}</td>
                            <td class="px-3 py-3 font-medium {{ $profit > 0 ? 'text-emerald-700' : 'text-red-700' }}">BDT {{ number_format($profit, 2) }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $sale->sold_at?->format('d M Y') ?? '-' }}</td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.spare.sales.show', $sale) }}" class="rounded border border-slate-300 px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50">View</a>
                                    <form method="POST" action="{{ route('admin.spare.sales.destroy', $sale) }}" class="js-delete-sale inline-block" data-name="{{ $sale->buyer_name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded border border-red-200 px-3 py-1 text-xs font-medium text-red-700 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-slate-500">No spare sales found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="sales-pagination" class="mt-4">{{ $sales->links() }}</div>
    </section>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function bindSaleDeleteActions() {
            document.querySelectorAll('#sales-ajax-region .js-delete-sale').forEach((form) => {
                if (form.dataset.boundDelete === '1') {
                    return;
                }

                form.dataset.boundDelete = '1';
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    Swal.fire({
                        title: 'Delete sale record?',
                        text: `This will archive sale for ${form.dataset.name}.`,
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

        async function refreshSalesRegion(url, pushState = true) {
            const region = document.getElementById('sales-ajax-region');

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
                    throw new Error('Failed to load sales.');
                }

                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const incomingRegion = doc.getElementById('sales-ajax-region');

                if (!incomingRegion) {
                    throw new Error('Sales region not found in response.');
                }

                region.innerHTML = incomingRegion.innerHTML;

                if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                    window.Alpine.initTree(region);
                }

                if (pushState) {
                    window.history.pushState({}, '', url);
                }

                bindSaleDeleteActions();
                bindSalesFilters();
            } catch (error) {
                Toastify({
                    text: 'Could not load filtered sales.',
                    duration: 3000,
                    gravity: 'top',
                    position: 'right',
                    style: { background: '#ef4444' },
                }).showToast();
            } finally {
                region.classList.remove('opacity-60', 'pointer-events-none');
            }
        }

        function bindSalesFilters() {
            const form = document.getElementById('sales-filter-form');
            const region = document.getElementById('sales-ajax-region');

            if (!form || !region || form.dataset.boundFilter === '1') {
                return;
            }

            form.dataset.boundFilter = '1';

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const params = new URLSearchParams(new FormData(form));
                refreshSalesRegion(`${form.action}?${params.toString()}`);
            });

            const resetLink = region.querySelector('.js-sales-reset');
            if (resetLink && resetLink.dataset.boundReset !== '1') {
                resetLink.dataset.boundReset = '1';
                resetLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    refreshSalesRegion(resetLink.href);
                });
            }

            const paginationLinks = region.querySelectorAll('#sales-pagination a[href]');
            paginationLinks.forEach((link) => {
                if (link.dataset.boundPage === '1') {
                    return;
                }

                link.dataset.boundPage = '1';
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    refreshSalesRegion(link.href);
                });
            });
        }

        window.addEventListener('popstate', function() {
            refreshSalesRegion(window.location.href, false);
        });

        bindSaleDeleteActions();
        bindSalesFilters();
    </script>
@endpush
