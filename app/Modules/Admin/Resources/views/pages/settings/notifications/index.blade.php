@extends('admin::layouts.app')

@section('title', 'Admin - Settings - Notifications')

@section('content')
<x-common.page-breadcrumb pageTitle="Settings / Notifications" />

<div id="settings-page-region" class="grid grid-cols-1 gap-6 lg:grid-cols-4">
    <div class="lg:col-span-1">
        @include('admin::pages.settings._sidebar')
    </div>

    <section class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6"
             x-data="{ loading: false, smsEnabled: @js((bool) old('sms_enabled', $settings['sms_enabled'])), whatsappEnabled: @js((bool) old('whatsapp_enabled', $settings['whatsapp_enabled'])) }">
        <h2 class="text-lg font-semibold text-slate-900">Notification Settings</h2>

        <form method="POST" action="{{ route('admin.settings.notifications.update') }}" class="mt-4 space-y-5" data-settings-ajax-form @submit="loading = true">
            @csrf

            <div class="rounded-lg border border-slate-200 p-4">
                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                    <input type="checkbox" name="sms_enabled" value="1" x-model="smsEnabled" @checked(old('sms_enabled', $settings['sms_enabled'])) />
                    <span>Enable SMS Notifications</span>
                </label>

                <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3" x-show="smsEnabled" x-transition>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">SMS Provider <span class="text-red-500">*</span></label>
                        <select name="sms_provider" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                            <option value="">Select provider</option>
                            @foreach (['twilio', 'nexmo', 'custom'] as $provider)
                                <option value="{{ $provider }}" @selected(old('sms_provider', $settings['sms_provider']) === $provider)>{{ ucfirst($provider) }}</option>
                            @endforeach
                        </select>
                        @error('sms_provider') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">SMS API Key <span class="text-red-500">*</span></label>
                        <input type="text" name="sms_api_key" placeholder="{{ $settings['sms_api_key_masked'] ?? 'Enter new API key' }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                        @error('sms_api_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">SMS Sender Name <span class="text-red-500">*</span></label>
                        <input type="text" name="sms_sender_name" value="{{ old('sms_sender_name', $settings['sms_sender_name']) }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                        @error('sms_sender_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 p-4">
                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                    <input type="checkbox" name="whatsapp_enabled" value="1" x-model="whatsappEnabled" @checked(old('whatsapp_enabled', $settings['whatsapp_enabled'])) />
                    <span>Enable WhatsApp Notifications</span>
                </label>

                <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2" x-show="whatsappEnabled" x-transition>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">WhatsApp API Key <span class="text-red-500">*</span></label>
                        <input type="text" name="whatsapp_api_key" placeholder="{{ $settings['whatsapp_api_key_masked'] ?? 'Enter new API key' }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                        @error('whatsapp_api_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">WhatsApp Sender Number <span class="text-red-500">*</span></label>
                        <input type="text" name="whatsapp_sender_number" value="{{ old('whatsapp_sender_number', $settings['whatsapp_sender_number']) }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                        @error('whatsapp_sender_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <input type="checkbox" name="low_stock_alert_enabled" value="1" @checked(old('low_stock_alert_enabled', $settings['low_stock_alert_enabled'])) />
                    <span>Low Stock Alert</span>
                </label>
                <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <input type="checkbox" name="due_payment_alert_enabled" value="1" @checked(old('due_payment_alert_enabled', $settings['due_payment_alert_enabled'])) />
                    <span>Due Payment Alert</span>
                </label>
                <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <input type="checkbox" name="trip_status_alert_enabled" value="1" @checked(old('trip_status_alert_enabled', $settings['trip_status_alert_enabled'])) />
                    <span>Trip Status Alert</span>
                </label>
            </div>

            <button type="submit" :disabled="loading" class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700 disabled:opacity-70">
                <svg x-show="loading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" /><path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="3" class="opacity-75" /></svg>
                <span x-text="loading ? 'Saving...' : 'Save Notification Settings'"></span>
            </button>
        </form>
    </section>
</div>
@endsection
