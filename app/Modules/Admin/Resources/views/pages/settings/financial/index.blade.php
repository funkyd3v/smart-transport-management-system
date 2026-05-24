@extends('admin::layouts.app')

@section('title', 'Admin - Settings - Financial')

@section('content')
<x-common.page-breadcrumb pageTitle="Settings / Financial" />

<div id="settings-page-region" class="grid grid-cols-1 gap-6 lg:grid-cols-4">
    <div class="lg:col-span-1">
        @include('admin::pages.settings._sidebar')
    </div>

    <section class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6" x-data="{ loading: false }">
        <h2 class="text-lg font-semibold text-slate-900">Financial Settings</h2>

        <form method="POST" action="{{ route('admin.settings.financial.update') }}" class="mt-4 space-y-4" @submit="loading = true">
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Invoice Prefix <span class="text-red-500">*</span></label>
                    <input name="invoice_prefix" value="{{ old('invoice_prefix', $settings['invoice_prefix']) }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                    @error('invoice_prefix') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Invoice Format <span class="text-red-500">*</span></label>
                    <select name="invoice_number_format" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                        @foreach (['PREFIX-YEAR-SEQ', 'PREFIX-SEQ'] as $format)
                            <option value="{{ $format }}" @selected(old('invoice_number_format', $settings['invoice_number_format']) === $format)>{{ $format }}</option>
                        @endforeach
                    </select>
                    @error('invoice_number_format') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Due Reminder Days <span class="text-red-500">*</span></label>
                    <input type="number" min="1" max="30" name="due_reminder_days" value="{{ old('due_reminder_days', $settings['due_reminder_days']) }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                    @error('due_reminder_days') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Tax Rate (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate']) }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                    @error('tax_rate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Fiscal Year Start <span class="text-red-500">*</span></label>
                    <select name="fiscal_year_start" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                        @foreach (['01-01', '07-01'] as $start)
                            <option value="{{ $start }}" @selected(old('fiscal_year_start', $settings['fiscal_year_start']) === $start)>{{ $start }}</option>
                        @endforeach
                    </select>
                    @error('fiscal_year_start') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Default Payment Methods <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @php $selectedMethods = old('default_payment_methods', $settings['default_payment_methods']); @endphp
                    @foreach (['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'mobile_banking' => 'Mobile Banking', 'cheque' => 'Cheque'] as $value => $label)
                        <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700">
                            <input type="checkbox" name="default_payment_methods[]" value="{{ $value }}" @checked(in_array($value, (array) $selectedMethods, true)) />
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('default_payment_methods') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                @error('default_payment_methods.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <button type="submit" :disabled="loading" class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700 disabled:opacity-70">
                <svg x-show="loading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" /><path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="3" class="opacity-75" /></svg>
                <span x-text="loading ? 'Saving...' : 'Save Financial Settings'"></span>
            </button>
        </form>
    </section>
</div>
@endsection
