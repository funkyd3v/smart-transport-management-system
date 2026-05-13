@extends('admin::layouts.app')

@section('title', 'Admin - Users')

@section('content')
<x-common.page-breadcrumb pageTitle="Users" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Users</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['total']) }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm">
            <p class="text-sm text-emerald-700">Active</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ number_format($stats['active']) }}</p>
        </article>
        <article class="rounded-2xl border border-rose-100 bg-rose-50/60 p-5 shadow-sm">
            <p class="text-sm text-rose-700">Inactive</p>
            <p class="mt-2 text-2xl font-semibold text-rose-700">{{ number_format($stats['inactive']) }}</p>
        </article>
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">Admins</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">{{ number_format($stats['admins']) }}</p>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">User Directory</h2>
            <span class="text-xs text-slate-500">{{ $users->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[840px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Name</th>
                        <th class="px-3 py-3">Email</th>
                        <th class="px-3 py-3">Role</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Last Login</th>
                        <th class="px-3 py-3">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $user->name }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $user->email }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ ucfirst((string) $user->role) }}</td>
                            <td class="px-3 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-rose-50 text-rose-700 ring-1 ring-rose-100' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-slate-600">{{ $user->last_login_at?->format('d M Y, h:i A') ?? 'Never' }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $user->created_at?->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-slate-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </section>
</div>
@endsection
