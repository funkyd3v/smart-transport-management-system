@php
    $isEdit = isset($truck) && $truck !== null;
    $statusValue = old('status', 'idle');

    if ($isEdit) {
        $rawStatus = $truck->status ?? null;
        $statusValue = is_object($rawStatus)
            ? strtolower(str_replace(' ', '_', (string) ($rawStatus->name ?? 'idle')))
            : (string) $rawStatus;

        if (! in_array($statusValue, ['idle', 'on_trip', 'under_workshop'], true)) {
            $statusValue = 'idle';
        }

        $statusValue = old('status', $statusValue);
    }

    $initialData = [
        'truck_number' => old('truck_number', $truck->truck_number ?? ''),
        'truck_type' => old('truck_type', $truck->truck_type ?? $truck->model ?? ''),
        'capacity' => old('capacity', $truck->capacity ?? $truck->capacity_tons ?? ''),
        'status' => $statusValue,
    ];
@endphp

<div class="space-y-6" x-data="truckFormComponent(@js($initialData))">
    <x-common.component-card :title="$isEdit ? 'Edit Truck' : 'Truck Registration Form'" :desc="$isEdit ? 'Update truck details and current status.' : 'Register a new truck for fleet operations.'">
        <form x-ref="truckForm" class="space-y-6" @submit.prevent="submitTruckForm('{{ $formAction }}', '{{ $formMethod }}')">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Truck Number</label>
                    <input name="truck_number" x-model="form.truck_number" type="text" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm uppercase text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Enter truck registration" />
                    <span x-show="errors.truck_number" x-text="errors.truck_number ? errors.truck_number[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Truck Type</label>
                    <input name="truck_type" x-model="form.truck_type" type="text" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Covered Van, Flatbed, Tank" />
                    <span x-show="errors.truck_type" x-text="errors.truck_type ? errors.truck_type[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Capacity (Tons)</label>
                    <input name="capacity" x-model="form.capacity" type="number" step="0.01" min="0" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="0.00" />
                    <span x-show="errors.capacity" x-text="errors.capacity ? errors.capacity[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                    <select name="status" x-model="form.status" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="idle">Idle</option>
                        <option value="under_workshop">Under Workshop</option>
                    </select>
                    <span x-show="errors.status" x-text="errors.status ? errors.status[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    {{ $isEdit ? 'Update Truck' : 'Save Truck' }}
                </button>
                <a href="{{ route('manager.trucks.index') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-3.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                    Cancel
                </a>
            </div>
        </form>
    </x-common.component-card>
</div>

@push('scripts')
    <script>
        function truckFormComponent(initialData) {
            return {
                form: {
                    truck_number: initialData.truck_number || '',
                    truck_type: initialData.truck_type || '',
                    capacity: initialData.capacity || '',
                    status: initialData.status || 'idle',
                },
                errors: {},
                async submitTruckForm(url, method) {
                    const payload = {
                        truck_number: (this.form.truck_number || '').toUpperCase().trim(),
                        truck_type: (this.form.truck_type || '').trim(),
                        capacity: this.form.capacity,
                        status: this.form.status,
                    };

                    if (method === 'PUT') {
                        payload._method = 'PUT';
                    }

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const data = await response.json();

                    if (response.status === 422) {
                        this.errors = data.errors || {};
                        return;
                    }

                    if (!response.ok) {
                        Toastify({ text: data.message ?? 'Something went wrong.', duration: 4000, gravity: 'top', position: 'right', backgroundColor: '#ef4444', stopOnFocus: true }).showToast();
                        return;
                    }

                    this.errors = {};
                    sessionStorage.setItem('toast_success', data.message ?? 'Truck saved successfully.');
                    window.location.href = '{{ route('manager.trucks.index') }}';
                },
            };
        }
    </script>
@endpush
