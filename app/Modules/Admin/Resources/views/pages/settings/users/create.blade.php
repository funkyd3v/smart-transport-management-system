@extends('admin::layouts.app')

@section('title', 'Admin - Settings - Create User')

@section('content')
<x-common.page-breadcrumb pageTitle="Settings / Users / Create" />

<div id="settings-page-region" class="grid grid-cols-1 gap-6 lg:grid-cols-4">
    <div class="lg:col-span-1">
        @include('admin::pages.settings._sidebar')
    </div>

    <section class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6" x-data="{ loading: false }">
        <h2 class="text-lg font-semibold text-slate-900">Create User</h2>

        <form method="POST" action="{{ route('admin.settings.users.store') }}" class="mt-4 space-y-4" @submit="loading = true">
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Name <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name') }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Role <span class="text-red-500">*</span></label>
                    <select name="role" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                        @foreach (['admin', 'manager', 'driver'] as $role)
                            <option value="{{ $role }}" @selected(old('role') === $role)>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center pt-7">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) />
                        <span>Active User</span>
                    </label>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
                    @error('password_confirmation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" :disabled="loading" class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700 disabled:opacity-70">
                    <span x-text="loading ? 'Saving...' : 'Create User'"></span>
                </button>
                <a href="{{ route('admin.settings.users.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
            </div>
        </form>
    </section>
</div>
@endsection
