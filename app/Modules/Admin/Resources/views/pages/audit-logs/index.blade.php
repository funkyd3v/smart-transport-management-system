@extends('admin::layouts.app')

@section('title', 'Admin - Audit Logs')

@section('content')
<x-common.page-breadcrumb pageTitle="Audit Logs" />

<div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Recent Activity Trail</h2>
            <span class="text-xs text-slate-500">{{ $logs->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1060px] text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Time</th>
                        <th class="px-3 py-3">User</th>
                        <th class="px-3 py-3">Role</th>
                        <th class="px-3 py-3">Action</th>
                        <th class="px-3 py-3">Table</th>
                        <th class="px-3 py-3">Record ID</th>
                        <th class="px-3 py-3">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $log->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ ucfirst((string) ($log->user?->role ?? 'system')) }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $log->action }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $log->table_name }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $log->record_id ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-slate-500">No audit logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </section>
</div>
@endsection
