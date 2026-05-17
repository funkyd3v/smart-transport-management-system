@php
    $layout = auth()->user()?->role === 'admin' ? 'admin::layouts.app' : 'manager::layouts.app';
@endphp
@extends($layout)

@section('title', 'Cashbook')

@section('content')
    <x-common.page-breadcrumb pageTitle="Cashbook" />

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-700 dark:bg-green-900/20 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-700 dark:bg-red-900/20 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <div x-data="cashbookPage()" class="space-y-6">

        {{-- Stats --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm dark:border-emerald-800 dark:bg-emerald-900/10">
                <p class="text-sm text-emerald-700 dark:text-emerald-400">Current Balance</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-400">
                    BDT {{ number_format((float) ($summary['current_balance'] ?? 0), 2) }}
                </p>
            </article>
            <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm dark:border-sky-800 dark:bg-sky-900/10">
                <p class="text-sm text-sky-700 dark:text-sky-400">Income — {{ $summary['month'] ?? '' }}</p>
                <p class="mt-2 text-2xl font-semibold text-sky-700 dark:text-sky-400">
                    BDT {{ number_format((float) ($summary['total_credits'] ?? 0), 2) }}
                </p>
            </article>
            <article class="rounded-2xl border border-rose-100 bg-rose-50/60 p-5 shadow-sm dark:border-rose-800 dark:bg-rose-900/10">
                <p class="text-sm text-rose-700 dark:text-rose-400">Expense — {{ $summary['month'] ?? '' }}</p>
                <p class="mt-2 text-2xl font-semibold text-rose-700 dark:text-rose-400">
                    BDT {{ number_format((float) ($summary['total_debits'] ?? 0), 2) }}
                </p>
            </article>
            <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm dark:border-amber-800 dark:bg-amber-900/10">
                @php $net = (float) ($summary['net'] ?? 0); @endphp
                <p class="text-sm text-amber-700 dark:text-amber-400">Net — {{ $summary['month'] ?? '' }}</p>
                <p class="mt-2 text-2xl font-semibold {{ $net >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400' }}">
                    BDT {{ number_format($net, 2) }}
                </p>
            </article>
        </section>

        {{-- Filter + table --}}
        <x-common.component-card title="Transaction Ledger" desc="All cashbook entries with running balance.">

            {{-- Filter row --}}
            <form method="GET" action="{{ route('cashbooks.index') }}" class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
                <input
                    name="date_from"
                    type="date"
                    value="{{ $filters['date_from'] ?? '' }}"
                    placeholder="From Date"
                    class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                />
                <input
                    name="date_to"
                    type="date"
                    value="{{ $filters['date_to'] ?? '' }}"
                    placeholder="To Date"
                    class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                />
                <select name="type" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">All Types</option>
                    <option value="credit" @selected(($filters['type'] ?? '') === 'credit')>Credit (Income)</option>
                    <option value="debit" @selected(($filters['type'] ?? '') === 'debit')>Debit (Expense)</option>
                </select>
                <div class="flex items-center gap-2 xl:col-span-2">
                    <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                        Filter
                    </button>
                    <a href="{{ route('cashbooks.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                        Clear
                    </a>
                    <button type="button" @click="openModal()" class="ml-auto rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700">
                        + Manual Entry
                    </button>
                </div>
            </form>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-gray-800 dark:text-gray-400">
                        <tr>
                            <th class="px-3 py-3">Date</th>
                            <th class="px-3 py-3">Type</th>
                            <th class="px-3 py-3">Description</th>
                            <th class="px-3 py-3">Source</th>
                            <th class="px-3 py-3 text-right">Amount</th>
                            <th class="px-3 py-3 text-right">Balance</th>
                            <th class="px-3 py-3">Recorded By</th>
                            <th class="px-3 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($entries as $entry)
                            @php
                                $isCredit = ($entry->type?->value ?? $entry->type) === 'credit';
                            @endphp
                            <tr class="border-b border-slate-200 hover:bg-slate-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                                <td class="px-3 py-3 font-medium text-slate-900 dark:text-white">
                                    {{ $entry->entry_date?->format('d M Y') ?? '—' }}
                                </td>
                                <td class="px-3 py-3">
                                    @if ($isCredit)
                                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-800">
                                            Credit
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700 ring-1 ring-rose-100 dark:bg-rose-900/30 dark:text-rose-400 dark:ring-rose-800">
                                            Debit
                                        </span>
                                    @endif
                                </td>
                                <td class="max-w-[200px] truncate px-3 py-3 text-slate-700 dark:text-gray-300" title="{{ $entry->description }}">
                                    {{ $entry->description }}
                                    @if ($entry->note)
                                        <span class="block text-xs text-gray-400">{{ \Illuminate\Support\Str::limit($entry->note, 40) }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-xs text-slate-500 dark:text-gray-400">
                                    {{ $entry->reference_type ? ucwords(str_replace('_', ' ', $entry->reference_type)) : 'Manual' }}
                                </td>
                                <td class="px-3 py-3 text-right font-semibold {{ $isCredit ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $isCredit ? '+' : '-' }} BDT {{ number_format((float) $entry->amount, 2) }}
                                </td>
                                <td class="px-3 py-3 text-right text-slate-700 dark:text-gray-300">
                                    BDT {{ number_format((float) $entry->balance, 2) }}
                                </td>
                                <td class="px-3 py-3 text-slate-600 dark:text-gray-400">
                                    {{ $entry->recordedBy?->name ?? '—' }}
                                </td>
                                <td class="px-3 py-3">
                                    @if ($entry->reference_type === 'manual' || $entry->reference_type === null)
                                        <form method="POST" action="{{ route('cashbooks.destroy', $entry->id) }}" onsubmit="return confirm('Void this entry? The running balance will not be recalculated.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-rose-500 hover:text-rose-700 dark:text-rose-400">
                                                Void
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">Auto</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-10 text-center text-slate-500 dark:text-gray-400">
                                    No cashbook entries found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $entries->appends($filters)->links() }}</div>
        </x-common.component-card>

        {{-- Manual Entry Modal --}}
        <div
            x-show="modalOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @keydown.escape.window="closeModal()"
        >
            <div
                @click.outside="closeModal()"
                class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900"
            >
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add Manual Cashbook Entry</h3>
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('cashbooks.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Type <span class="text-red-500">*</span></label>
                            <select name="type" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="credit">Credit (Income)</option>
                                <option value="debit">Debit (Expense)</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Amount <span class="text-red-500">*</span></label>
                            <input
                                name="amount"
                                type="number"
                                step="0.01"
                                min="0.01"
                                required
                                placeholder="0.00"
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Description <span class="text-red-500">*</span></label>
                        <input
                            name="description"
                            type="text"
                            required
                            maxlength="255"
                            placeholder="What is this entry for?"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Entry Date <span class="text-red-500">*</span></label>
                        <input
                            name="entry_date"
                            type="date"
                            required
                            value="{{ now()->toDateString() }}"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Note</label>
                        <textarea
                            name="note"
                            rows="2"
                            placeholder="Optional note..."
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        ></textarea>
                    </div>

                    @if ($errors->any())
                        <ul class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-700 dark:bg-red-900/20 dark:text-red-400">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="closeModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                            Record Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function cashbookPage() {
                return {
                    modalOpen: {{ $errors->any() ? 'true' : 'false' }},
                    openModal() { this.modalOpen = true; },
                    closeModal() { this.modalOpen = false; },
                };
            }
        </script>
    @endpush
@endsection
