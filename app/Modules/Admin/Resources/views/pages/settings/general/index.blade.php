@extends('admin::layouts.app')

@section('title', 'Admin - Settings - General')

@section('content')
<x-common.page-breadcrumb pageTitle="Settings / General" />

<div id="settings-page-region" class="grid grid-cols-1 gap-6 lg:grid-cols-4">
    <div class="lg:col-span-1">
        @include('admin::pages.settings._sidebar')
    </div>

    <section class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6" x-data="{ loading: false }">
        <h2 class="text-lg font-semibold text-slate-900">General Settings</h2>

        <form method="POST" action="{{ route('admin.settings.general.update') }}" enctype="multipart/form-data" class="mt-4 space-y-4" data-settings-ajax-form @submit="loading = true">
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Company Name <span class="text-red-500">*</span></label>
                    <input name="company_name" value="{{ old('company_name', $settings['company_name']) }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                    @error('company_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Contact Number <span class="text-red-500">*</span></label>
                    <input name="contact_number" value="{{ old('contact_number', $settings['contact_number']) }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                    @error('contact_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email_address" value="{{ old('email_address', $settings['email_address']) }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                    @error('email_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Currency Symbol <span class="text-red-500">*</span></label>
                    <input name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol']) }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                    @error('currency_symbol') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Timezone <span class="text-red-500">*</span></label>
                    <select name="timezone" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                        @foreach (timezone_identifiers_list() as $timezone)
                            <option value="{{ $timezone }}" @selected(old('timezone', $settings['timezone']) === $timezone)>{{ $timezone }}</option>
                        @endforeach
                    </select>
                    @error('timezone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Date Format <span class="text-red-500">*</span></label>
                    <select name="date_format" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                        @foreach (['d/m/Y', 'm/d/Y', 'Y-m-d', 'd-M-Y'] as $dateFormat)
                            <option value="{{ $dateFormat }}" @selected(old('date_format', $settings['date_format']) === $dateFormat)>{{ $dateFormat }}</option>
                        @endforeach
                    </select>
                    @error('date_format') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Company Address <span class="text-red-500">*</span></label>
                <textarea name="company_address" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('company_address', $settings['company_address']) }}</textarea>
                @error('company_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Company Logo</label>
                @if (!empty($settings['company_logo_url']))
                    <img src="{{ $settings['company_logo_url'] }}" alt="Company logo" class="mb-2 h-20 rounded border border-slate-200 bg-slate-50 p-1" />
                @endif
                <input type="file" name="company_logo" accept="image/jpeg,image/png,image/webp" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
                @error('company_logo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <button type="submit" :disabled="loading" class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700 disabled:opacity-70">
                <svg x-show="loading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" /><path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="3" class="opacity-75" /></svg>
                <span x-text="loading ? 'Saving...' : 'Save General Settings'"></span>
            </button>
        </form>
    </section>
</div>
@endsection
