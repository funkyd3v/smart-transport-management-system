@extends('driver::layouts.app')

@section('title', 'My Profile')

@section('content')
    <x-common.page-breadcrumb pageTitle="My Profile" />

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-500/30 dark:bg-green-500/10 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        {{-- Profile Overview --}}
        <x-common.component-card title="Driver Profile" desc="Your personal details and account information.">
            <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-center">
                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-3xl font-bold text-blue-700 dark:bg-blue-500/15 dark:text-blue-400">
                    {{ strtoupper(substr($user->name ?? 'D', 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">{{ $user->name ?? '—' }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->email ?? '—' }}</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if ($driver)
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-500/15 dark:text-blue-400">
                                {{ ucfirst((string) ($driver->driving_type ?? 'driver')) }}
                            </span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $driver->is_available ? 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $driver->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </x-common.component-card>

        {{-- Trip Stats --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Trips</p>
                <h3 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $stats['total'] }}</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">All time</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Completed</p>
                <h3 class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-400">{{ $stats['completed'] }}</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Successfully delivered</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">This Month</p>
                <h3 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $stats['this_month'] }}</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ now()->format('F Y') }}</p>
            </div>
        </div>

        {{-- Driver Details (Read-only) --}}
        @if ($driver)
            <x-common.component-card title="Driver Information" desc="License, NID, and employment details managed by admin.">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">License Number</p>
                        <p class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $driver->license_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">NID Number</p>
                        <p class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $driver->nid_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Driver Type</p>
                        <p class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ ucfirst((string) ($driver->driving_type ?? '—')) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Joining Date</p>
                        <p class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ optional($driver->joining_date)->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Rating</p>
                        <p class="mt-1 font-medium text-gray-800 dark:text-white/90">
                            @if ($driver->rating)
                                {{ number_format((float) $driver->rating, 1) }} / 5.0
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Mobile</p>
                        <p class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $driver->mobile_number ?? $user->email }}</p>
                    </div>
                </div>
            </x-common.component-card>
        @endif

        {{-- Edit Profile Form --}}
        <x-common.component-card title="Edit Profile" desc="Update your name and contact number.">
            <form action="{{ route('driver.profile.update') }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="h-11 w-full rounded-lg border px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs transition focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 {{ $errors->has('name') ? 'border-red-400 dark:border-red-500' : 'border-gray-300' }}"
                        />
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Mobile Number</label>
                        <input
                            id="phone"
                            name="phone"
                            type="text"
                            value="{{ old('phone', $driver?->mobile_number ?? '') }}"
                            class="h-11 w-full rounded-lg border px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs transition focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 {{ $errors->has('phone') ? 'border-red-400 dark:border-red-500' : 'border-gray-300' }}"
                        />
                        @error('phone')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="inline-flex h-11 items-center gap-2 rounded-xl bg-blue-600 px-6 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        <svg x-show="submitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Save Changes
                    </button>
                    <a href="{{ route('driver.dashboard') }}" class="inline-flex h-11 items-center rounded-xl border border-gray-200 px-6 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                        Cancel
                    </a>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection
