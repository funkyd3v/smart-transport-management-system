@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Clients" />

    <div class="space-y-6">
        <x-common.component-card title="Client List" desc="Manage all transport business clients from one place.">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                @include('manager::pages.clients.partials._filters')

                <a href="{{ route('manager.clients.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    + Add Client
                </a>
            </div>

            <div id="clients-results" class="relative">
                @include('manager::pages.clients.partials._table_with_pagination')
            </div>
        </x-common.component-card>
    </div>
@endsection

@push('scripts')
    <script>
        function clientFilters() {
            return {
                isLoading: false,
                submit(page = 1) {
                    const form = this.$refs.form;

                    if (!form) {
                        return;
                    }

                    window.fetchClientsFromFilters(form, page);
                }
            };
        }

        window.fetchClientsFromFilters = async function(form, page = 1) {
            const params = new URLSearchParams(new FormData(form));

            if (page > 1) {
                params.set('page', String(page));
            } else {
                params.delete('page');
            }

            const url = `${form.action}?${params.toString()}`;
            const resultsContainer = document.getElementById('clients-results');

            if (!resultsContainer) {
                window.location.href = url;
                return;
            }

            resultsContainer.classList.add('opacity-60', 'pointer-events-none');

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to load clients.');
                }

                const data = await response.json();
                resultsContainer.innerHTML = data.html ?? '';
                window.history.replaceState({}, '', url);

                if (window.Alpine) {
                    window.Alpine.initTree(resultsContainer);
                }
            } catch (error) {
                showErrorToast('Failed to load clients. Reloading page.');
                window.location.href = url;
            } finally {
                resultsContainer.classList.remove('opacity-60', 'pointer-events-none');
            }
        };

        function showSuccessToast(message) {
            Toastify({ text: message, duration: 3000, gravity: 'top', position: 'right', backgroundColor: '#22c55e', stopOnFocus: true }).showToast();
        }

        function showErrorToast(message = 'Something went wrong.') {
            Toastify({ text: message, duration: 4000, gravity: 'top', position: 'right', backgroundColor: '#ef4444', stopOnFocus: true }).showToast();
        }

        window.toggleClientStatus = async function(clientId, onSuccess) {
            const url = `{{ url('manager/clients') }}/${clientId}/toggle-status`;
            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            });
            const data = await response.json();
            if (!response.ok) {
                showErrorToast(data.message ?? 'Something went wrong.');
                return;
            }

            if (typeof onSuccess === 'function') {
                onSuccess(data.status);
            }
            showSuccessToast(data.message ?? 'Client status updated successfully.');
        };

        window.deleteClient = function(clientId) {
            Swal.fire({
                title: 'Delete Client?',
                text: 'This cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const url = `{{ url('manager/clients') }}/${clientId}`;
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({})
                    });
                    const data = await response.json();
                    if (!response.ok) {
                        showErrorToast(data.message ?? 'Something went wrong.');
                        return;
                    }

                    const row = document.getElementById(`client-row-${clientId}`);
                    if (row) {
                        row.remove();
                    }

                    const tableBody = document.getElementById('client-table-body');
                    if (tableBody && tableBody.querySelectorAll('tr[id^="client-row-"]').length === 0) {
                        tableBody.innerHTML = '<tr id="client-empty-row"><td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No clients found.</td></tr>';
                    }

                    showSuccessToast(data.message ?? 'Client deleted successfully.');
                }
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            const msg = sessionStorage.getItem('toast_success');
            if (msg) {
                Toastify({ text: msg, duration: 3000, gravity: 'top', position: 'right', backgroundColor: '#22c55e' }).showToast();
                sessionStorage.removeItem('toast_success');
            }

            document.addEventListener('click', function(event) {
                const link = event.target.closest('#clients-results .pagination a');

                if (!link) {
                    return;
                }

                event.preventDefault();

                const form = document.querySelector('[data-client-filters-form]');

                if (!form) {
                    window.location.href = link.href;
                    return;
                }

                const linkUrl = new URL(link.href);
                const page = Number(linkUrl.searchParams.get('page') ?? '1');
                window.fetchClientsFromFilters(form, page);
            });
        });
    </script>
@endpush
