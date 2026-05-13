@extends('admin::layouts.app')

@section('title', 'Admin - Settings')

@section('content')
<x-common.page-breadcrumb pageTitle="Settings" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Application</p>
            <h2 class="mt-2 text-lg font-semibold text-slate-900">{{ $settings['app_name'] }}</h2>
            <p class="mt-1 text-sm text-slate-600">Environment: {{ strtoupper($settings['app_env']) }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Localization</p>
            <p class="mt-2 text-sm text-slate-700">Timezone: <span class="font-medium">{{ $settings['timezone'] }}</span></p>
            <p class="mt-1 text-sm text-slate-700">Locale: <span class="font-medium">{{ $settings['locale'] }}</span></p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Infrastructure</p>
            <p class="mt-2 text-sm text-slate-700">Mail Driver: <span class="font-medium">{{ $settings['mail_driver'] }}</span></p>
            <p class="mt-1 text-sm text-slate-700">Queue Driver: <span class="font-medium">{{ $settings['queue_driver'] }}</span></p>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <h2 class="mb-4 text-lg font-semibold text-slate-900">Runtime Snapshot</h2>
        <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <dt class="text-slate-500">App Name</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ $settings['app_name'] }}</dd>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <dt class="text-slate-500">Environment</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ strtoupper($settings['app_env']) }}</dd>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <dt class="text-slate-500">App URL</dt>
                <dd class="mt-1 break-all font-medium text-slate-900">{{ $settings['app_url'] }}</dd>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <dt class="text-slate-500">Timezone</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ $settings['timezone'] }}</dd>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <dt class="text-slate-500">Locale</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ $settings['locale'] }}</dd>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <dt class="text-slate-500">Mail Driver</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ $settings['mail_driver'] }}</dd>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <dt class="text-slate-500">Queue Driver</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ $settings['queue_driver'] }}</dd>
            </div>
        </dl>
    </section>
</div>
@endsection
