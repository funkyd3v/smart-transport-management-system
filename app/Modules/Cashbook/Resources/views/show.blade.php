@php
    $layout = auth()->user()?->role === 'admin' ? 'admin::layouts.app' : 'manager::layouts.app';
@endphp
@extends($layout)

@section('title', 'Cashbook Entry Detail')

@section('content')
    <x-common.page-breadcrumb pageTitle="Cashbook Entry" />

    <div class="mx-auto max-w-2xl">
        <x-common.component-card title="Entry Detail" desc="Full details for this cashbook transaction.">

            @php
                $isCredit = ($entry->type?->value ?? $entry->type) === 'credit';
            @endphp

            <dl class="divide-y divide-gray-200 dark:divide-gray-700">

                <div class="grid grid-cols-3 py-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Entry ID</dt>
                    <dd class="col-span-2 text-sm font-mono text-gray-900 dark:text-white">{{ $entry->id }}</dd>
                </div>

                <div class="grid grid-cols-3 py-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</dt>
                    <dd class="col-span-2 text-sm text-gray-900 dark:text-white">{{ $entry->entry_date?->format('d M Y') ?? '—' }}</dd>
                </div>

                <div class="grid grid-cols-3 py-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                    <dd class="col-span-2">
                        @if ($isCredit)
                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-800">
                                Credit
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700 ring-1 ring-rose-100 dark:bg-rose-900/30 dark:text-rose-400 dark:ring-rose-800">
                                Debit
                            </span>
                        @endif
                    </dd>
                </div>

                <div class="grid grid-cols-3 py-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Amount</dt>
                    <dd class="col-span-2 text-sm font-semibold {{ $isCredit ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $isCredit ? '+' : '-' }} BDT {{ number_format((float) $entry->amount, 2) }}
                    </dd>
                </div>

                <div class="grid grid-cols-3 py-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Running Balance</dt>
                    <dd class="col-span-2 text-sm text-gray-900 dark:text-white">BDT {{ number_format((float) $entry->balance, 2) }}</dd>
                </div>

                <div class="grid grid-cols-3 py-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                    <dd class="col-span-2 text-sm text-gray-900 dark:text-white">{{ $entry->description }}</dd>
                </div>

                @if ($entry->note)
                    <div class="grid grid-cols-3 py-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Note</dt>
                        <dd class="col-span-2 text-sm text-gray-900 dark:text-white">{{ $entry->note }}</dd>
                    </div>
                @endif

                <div class="grid grid-cols-3 py-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Source</dt>
                    <dd class="col-span-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ $entry->reference_type ? ucwords(str_replace('_', ' ', $entry->reference_type)) : 'Manual' }}
                        @if ($entry->reference_id)
                            <span class="ml-2 font-mono text-xs text-gray-400">{{ $entry->reference_id }}</span>
                        @endif
                    </dd>
                </div>

                <div class="grid grid-cols-3 py-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Recorded By</dt>
                    <dd class="col-span-2 text-sm text-gray-900 dark:text-white">{{ $entry->recordedBy?->name ?? '—' }}</dd>
                </div>

                <div class="grid grid-cols-3 py-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="col-span-2">
                        @if ($entry->is_void)
                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:ring-gray-600">
                                Voided
                            </span>
                            <span class="ml-2 text-xs text-gray-400">
                                on {{ $entry->voided_at?->format('d M Y H:i') }}
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-800">
                                Active
                            </span>
                        @endif
                    </dd>
                </div>

                <div class="grid grid-cols-3 py-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                    <dd class="col-span-2 text-sm text-gray-600 dark:text-gray-400">{{ $entry->created_at?->format('d M Y H:i') }}</dd>
                </div>
            </dl>

            <div class="mt-6 flex items-center gap-3">
                <a href="{{ route('cashbooks.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                    ← Back to Cashbook
                </a>
            </div>
        </x-common.component-card>
    </div>
@endsection
