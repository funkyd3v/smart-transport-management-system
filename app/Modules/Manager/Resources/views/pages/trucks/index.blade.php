@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Trucks" />

    <div class="space-y-6">
        <x-common.component-card title="Truck List" desc="Manage fleet trucks, status, and assignment readiness.">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                @include('manager::pages.trucks.partials._filters')

                <a href="{{ route('manager.trucks.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    + Add Truck
                </a>
            </div>

            <div id="trucks-results" class="relative">
                @include('manager::pages.trucks.partials._table')
            </div>
        </x-common.component-card>
    </div>
@endsection

@push('scripts')
    <script>
        function truckFilters() {
            return {
                submit(page = 1) {
                    const form = this.$refs.form;

                    if (!form) {
                        return;
                    }

                    window.fetchTrucksFromFilters(form, page);
                }
            };
        }

        function showSuccessToast(message) {
            Toastify({ text: message, duration: 3000, gravity: 'top', position: 'right', backgroundColor: '#22c55e', stopOnFocus: true }).showToast();
        }

        function showErrorToast(message = 'Something went wrong.') {
            Toastify({ text: message, duration: 4000, gravity: 'top', position: 'right', backgroundColor: '#ef4444', stopOnFocus: true }).showToast();
        }

        window.fetchTrucksFromFilters = async function(form, page = 1) {
            const params = new URLSearchParams(new FormData(form));

            if (page > 1) {
                params.set('page', String(page));
            } else {
                params.delete('page');
            }

            const url = `${form.action}?${params.toString()}`;
            const resultsContainer = document.getElementById('trucks-results');

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
                    throw new Error('Failed to load trucks.');
                }

                const data = await response.json();
                resultsContainer.innerHTML = data.html ?? '';
                window.history.replaceState({}, '', url);

                if (window.Alpine) {
                    window.Alpine.initTree(resultsContainer);
                }
            } catch (error) {
                showErrorToast('Failed to load trucks. Reloading page.');
                window.location.href = url;
            } finally {
                resultsContainer.classList.remove('opacity-60', 'pointer-events-none');
            }
        };

        window.updateTruckStatus = async function(truckId, status, isOnTrip, onSuccess) {
            if (isOnTrip) {
                showErrorToast('Truck is currently on a trip.');
                return;
            }

            const url = `{{ url('manager/trucks') }}/${truckId}/status`;

            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ status }),
            });

            const data = await response.json();

            if (!response.ok) {
                showErrorToast(data.message ?? 'Something went wrong.');
                return;
            }

            if (typeof onSuccess === 'function') {
                onSuccess(data.status || status);
            }

            showSuccessToast(data.message ?? 'Truck status updated successfully.');
        };

        window.deleteTruck = function(truckId, isOnTrip) {
            if (isOnTrip) {
                showErrorToast('Truck is currently on a trip.');
                return;
            }

            Swal.fire({
                title: 'Delete Truck?',
                text: 'This cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then(async (result) => {
                if (!result.isConfirmed) {
                    return;
                }

                const url = `{{ url('manager/trucks') }}/${truckId}`;
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                const data = await response.json();

                if (!response.ok) {
                    showErrorToast(data.message ?? 'Something went wrong.');
                    return;
                }

                const row = document.getElementById(`truck-row-${truckId}`);
                if (row) {
                    row.remove();
                }

                const tableBody = document.getElementById('truck-table-body');
                if (tableBody && tableBody.querySelectorAll('tr[id^="truck-row-"]').length === 0) {
                    tableBody.innerHTML = '<tr id="truck-empty-row"><td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No trucks found.</td></tr>';
                }

                showSuccessToast(data.message ?? 'Truck deleted successfully.');
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            const msg = sessionStorage.getItem('toast_success');
            if (msg) {
                Toastify({ text: msg, duration: 3000, gravity: 'top', position: 'right', backgroundColor: '#22c55e' }).showToast();
                sessionStorage.removeItem('toast_success');
            }

            document.addEventListener('click', function(event) {
                const link = event.target.closest('#trucks-results .pagination a');

                if (!link) {
                    return;
                }

                event.preventDefault();

                const form = document.querySelector('[data-truck-filters-form]');
                if (!form) {
                    window.location.href = link.href;
                    return;
                }

                const linkUrl = new URL(link.href);
                const page = Number(linkUrl.searchParams.get('page') ?? '1');
                window.fetchTrucksFromFilters(form, page);
            });
        });
    </script>
@endpush
