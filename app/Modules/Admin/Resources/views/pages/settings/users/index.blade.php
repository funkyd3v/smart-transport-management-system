@extends('admin::layouts.app')

@section('title', 'Admin - Settings - Users')

@section('content')
<x-common.page-breadcrumb pageTitle="Settings / Users" />

<div id="settings-page-region" class="grid grid-cols-1 gap-6 lg:grid-cols-4">
    <div class="lg:col-span-1">
        @include('admin::pages.settings._sidebar')
    </div>

    <section id="settings-users-ajax-region" class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <form id="settings-users-filter-form" method="GET" action="{{ route('admin.settings.users.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-5">
            <div class="md:col-span-2">
                <label class="mb-1 block text-xs font-medium text-slate-500">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name or email" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Role</label>
                <select name="role" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                    <option value="">All</option>
                    @foreach (['admin', 'manager', 'driver'] as $role)
                        <option value="{{ $role }}" @selected(($filters['role'] ?? '') === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Status</label>
                <select name="status" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                    <option value="">All</option>
                    <option value="1" @selected(($filters['status'] ?? '') === '1')>Active</option>
                    <option value="0" @selected(($filters['status'] ?? '') === '0')>Inactive</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="h-11 flex-1 rounded-lg bg-sky-600 px-4 text-sm font-medium text-white hover:bg-sky-700">Filter</button>
                <a href="{{ route('admin.settings.users.index') }}" class="js-users-reset h-11 rounded-lg border border-slate-300 px-4 text-sm font-medium leading-[44px] text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>

        <div class="mt-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">User Management</h2>
            <a href="{{ route('admin.settings.users.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Add New User</a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[960px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Name</th>
                        <th class="px-3 py-3">Email</th>
                        <th class="px-3 py-3">Role</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Created</th>
                        <th class="px-3 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $user->name }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $user->email }}</td>
                            <td class="px-3 py-3"><span class="rounded-full bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-700">{{ ucfirst($user->role) }}</span></td>
                            <td class="px-3 py-3">
                                @if ($user->is_active)
                                    <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700">Active</span>
                                @else
                                    <span class="rounded-full bg-rose-100 px-2 py-1 text-xs font-medium text-rose-700">Inactive</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-slate-700">{{ $user->created_at?->format('d M Y') }}</td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.settings.users.edit', $user) }}" class="rounded border border-slate-300 px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50">Edit</a>
                                    <form method="POST" action="{{ route('admin.settings.users.toggle-status', $user) }}" class="js-toggle-user-status inline-block" data-self="{{ (int) $currentUserId === (int) $user->id ? '1' : '0' }}" data-name="{{ $user->name }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded border border-amber-200 px-3 py-1 text-xs font-medium text-amber-700 hover:bg-amber-50" {{ (int) $currentUserId === (int) $user->id ? 'disabled' : '' }}>
                                            Toggle Status
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-slate-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="settings-users-pagination" class="mt-4">{{ $users->links() }}</div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
        function bindUserStatusToggle() {
            document.querySelectorAll('#settings-users-ajax-region .js-toggle-user-status').forEach((form) => {
                if (form.dataset.boundToggle === '1') {
                    return;
                }

                form.dataset.boundToggle = '1';
                form.addEventListener('submit', function(event) {
                    if (form.dataset.self === '1') {
                        event.preventDefault();
                        Toastify({ text: 'You cannot deactivate your own account.', duration: 3000, gravity: 'top', position: 'right', style: { background: '#ef4444' } }).showToast();
                        return;
                    }

                    event.preventDefault();
                    Swal.fire({
                        title: 'Update user status?',
                        text: `This will toggle status for ${form.dataset.name}.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, continue',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#d97706',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        }

        async function refreshUsersRegion(url, pushState = true) {
            const region = document.getElementById('settings-users-ajax-region');

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
                    throw new Error('Failed to load users.');
                }

                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const incomingRegion = doc.getElementById('settings-users-ajax-region');

                if (!incomingRegion) {
                    throw new Error('Users region not found in response.');
                }

                region.innerHTML = incomingRegion.innerHTML;

                if (pushState) {
                    window.history.pushState({}, '', url);
                }

                bindUsersFilters();
                bindUserStatusToggle();
            } catch (error) {
                Toastify({ text: 'Could not load users list.', duration: 3000, gravity: 'top', position: 'right', style: { background: '#ef4444' } }).showToast();
            } finally {
                region.classList.remove('opacity-60', 'pointer-events-none');
            }
        }

        function bindUsersFilters() {
            const form = document.getElementById('settings-users-filter-form');
            const region = document.getElementById('settings-users-ajax-region');

            if (!form || !region || form.dataset.boundFilter === '1') {
                return;
            }

            form.dataset.boundFilter = '1';

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const params = new URLSearchParams(new FormData(form));
                refreshUsersRegion(`${form.action}?${params.toString()}`);
            });

            const resetLink = region.querySelector('.js-users-reset');
            if (resetLink && resetLink.dataset.boundReset !== '1') {
                resetLink.dataset.boundReset = '1';
                resetLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    refreshUsersRegion(resetLink.href);
                });
            }

            const paginationLinks = region.querySelectorAll('#settings-users-pagination a[href]');
            paginationLinks.forEach((link) => {
                if (link.dataset.boundPage === '1') {
                    return;
                }

                link.dataset.boundPage = '1';
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    refreshUsersRegion(link.href);
                });
            });
        }

        window.addEventListener('popstate', function() {
            refreshUsersRegion(window.location.href, false);
        });

        bindUsersFilters();
        bindUserStatusToggle();
</script>
@endsection
