@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Trip Management" />

    <div x-data="tripFilters()" x-init="init()" class="space-y-6">
        <x-common.component-card title="Trip List" desc="Track, filter, and control trip operations.">
            <form x-ref="form" data-trip-filters-form method="GET" action="{{ route('manager.trips.index') }}" @submit.prevent="submit()" class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-8">
                <select name="status_id" @change="submit()" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" @selected((string) ($filters['status_id'] ?? '') === (string) $status->id)>{{ ucfirst(str_replace('_', ' ', $status->name)) }}</option>
                    @endforeach
                </select>
                <select name="client_id" @change="submit()" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">All Clients</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected((string) ($filters['client_id'] ?? '') === (string) $client->id)>{{ $client->company_name ?? $client->user?->name ?? ('Client #'.$client->id) }}</option>
                    @endforeach
                </select>
                <select name="truck_id" @change="submit()" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">All Trucks</option>
                    @foreach ($trucks as $truck)
                        <option value="{{ $truck->id }}" @selected((string) ($filters['truck_id'] ?? '') === (string) $truck->id)>{{ $truck->truck_number }}</option>
                    @endforeach
                </select>
                <input name="search" value="{{ $filters['search'] ?? '' }}" @input.debounce.400ms="submit()" type="text" placeholder="Search code/client" class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                <div>
                    <div class="relative custom-datepicker">
                        <input
                            x-ref="dateFromInput"
                            id="trip-date-from"
                            name="date_from"
                            value="{{ $filters['date_from'] ?? '' }}"
                            type="text"
                            readonly
                            autocomplete="off"
                            placeholder="From Date"
                            aria-label="From Date"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                        />
                        <button type="button" @click="openFromCalendar()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" aria-label="Open from date calendar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" class="size-5">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 2C8.41421 2 8.75 2.33579 8.75 2.75V3.75H15.25V2.75C15.25 2.33579 15.5858 2 16 2C16.4142 2 16.75 2.33579 16.75 2.75V3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V9V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V9V6C3.25 4.75736 4.25736 3.75 5.5 3.75H7.25V2.75C7.25 2.33579 7.58579 2 8 2ZM8 5.25H5.5C5.08579 5.25 4.75 5.58579 4.75 6V8.25H19.25V6C19.25 5.58579 18.9142 5.25 18.5 5.25H16H8ZM19.25 9.75H4.75V19C4.75 19.4142 5.08579 19.75 5.5 19.75H18.5C18.9142 19.75 19.25 19.4142 19.25 19V9.75Z" fill="currentColor"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div>
                    <div class="relative custom-datepicker">
                        <input
                            x-ref="dateToInput"
                            id="trip-date-to"
                            name="date_to"
                            value="{{ $filters['date_to'] ?? '' }}"
                            type="text"
                            readonly
                            autocomplete="off"
                            placeholder="To Date"
                            aria-label="To Date"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                        />
                        <button type="button" @click="openToCalendar()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" aria-label="Open to date calendar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" class="size-5">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 2C8.41421 2 8.75 2.33579 8.75 2.75V3.75H15.25V2.75C15.25 2.33579 15.5858 2 16 2C16.4142 2 16.75 2.33579 16.75 2.75V3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V9V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V9V6C3.25 4.75736 4.25736 3.75 5.5 3.75H7.25V2.75C7.25 2.33579 7.58579 2 8 2ZM8 5.25H5.5C5.08579 5.25 4.75 5.58579 4.75 6V8.25H19.25V6C19.25 5.58579 18.9142 5.25 18.5 5.25H16H8ZM19.25 9.75H4.75V19C4.75 19.4142 5.08579 19.75 5.5 19.75H18.5C18.9142 19.75 19.25 19.4142 19.25 19V9.75Z" fill="currentColor"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white">Filter</button>
                    <a href="{{ route('manager.trips.create') }}" class="rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white">Create</a>
                </div>
            </form>
            <div id="trip-list-table">
                @include('manager::trips.partials._table', ['trips' => $trips])
            </div>
        </x-common.component-card>
    </div>
@endsection

@push('scripts')
    <script>
        function tripFilters() {
            return {
                fromDatePicker: null,
                toDatePicker: null,
                init() {
                    this.$nextTick(() => {
                        if (typeof flatpickr !== 'function') {
                            return;
                        }

                        this.fromDatePicker = flatpickr(this.$refs.dateFromInput, {
                            static: true,
                            monthSelectorType: 'static',
                            dateFormat: 'Y-m-d',
                            defaultDate: this.$refs.dateFromInput.value || null,
                            onChange: () => this.submit(),
                        });

                        this.toDatePicker = flatpickr(this.$refs.dateToInput, {
                            static: true,
                            monthSelectorType: 'static',
                            dateFormat: 'Y-m-d',
                            defaultDate: this.$refs.dateToInput.value || null,
                            onChange: () => this.submit(),
                        });
                    });
                },
                openFromCalendar() {
                    if (this.fromDatePicker) {
                        this.fromDatePicker.open();
                    }
                },
                openToCalendar() {
                    if (this.toDatePicker) {
                        this.toDatePicker.open();
                    }
                },
                submit(page = 1) {
                    const form = this.$refs.form;

                    if (!form) {
                        return;
                    }

                    window.fetchTripsFromFilters(form, page);
                }
            };
        }

        window.fetchTripsFromFilters = async function(form, page = 1) {
            const params = new URLSearchParams(new FormData(form));

            if (page > 1) {
                params.set('page', String(page));
            } else {
                params.delete('page');
            }

            const url = `${form.action}?${params.toString()}`;
            const resultsContainer = document.getElementById('trip-list-table');

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
                    throw new Error('Failed to load trips.');
                }

                const data = await response.json();
                resultsContainer.innerHTML = data.html ?? '';
                window.history.replaceState({}, '', url);

                if (window.Alpine) {
                    window.Alpine.initTree(resultsContainer);
                }
            } catch (error) {
                window.location.href = url;
            } finally {
                resultsContainer.classList.remove('opacity-60', 'pointer-events-none');
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(event) {
                const link = event.target.closest('#trip-list-table .pagination a');

                if (!link) {
                    return;
                }

                event.preventDefault();

                const form = document.querySelector('[data-trip-filters-form]');

                if (!form) {
                    window.location.href = link.href;
                    return;
                }

                const linkUrl = new URL(link.href);
                const page = Number(linkUrl.searchParams.get('page') ?? '1');
                window.fetchTripsFromFilters(form, page);
            });
        });
    </script>
@endpush
