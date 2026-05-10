@php
    $isEdit = isset($client);
@endphp

<div class="space-y-6" x-data="clientFormComponent('{{ old('client_type', $client->client_type ?? '') }}')">
    <x-common.component-card :title="$isEdit ? 'Edit Client' : 'Client Registration Form'" :desc="$isEdit ? 'Update client information and contact details.' : 'Register a new transport business client.'">
        <form class="space-y-6" @submit.prevent="submitClientForm('{{ $formAction }}', '{{ $formMethod }}')">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Client Name</label>
                    <input
                        x-ref="name"
                        type="text"
                        value="{{ old('name', $client->name ?? '') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        placeholder="Enter client name"
                    />
                    <span x-show="errors.name" x-text="errors.name ? errors.name[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Contact Number</label>
                    <input
                        x-ref="contact_number"
                        type="text"
                        value="{{ old('contact_number', $client->contact_number ?? '') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        placeholder="01XXXXXXXXX"
                    />
                    <span x-show="errors.contact_number" x-text="errors.contact_number ? errors.contact_number[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Client Type</label>
                    <select
                        x-model="clientType"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                    >
                        <option value="port">Port</option>
                        <option value="contractual">Contractual</option>
                        <option value="mega_project">Mega Project</option>
                    </select>
                    <span x-show="errors.client_type" x-text="errors.client_type ? errors.client_type[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div x-show="clientType === 'contractual' || clientType === 'mega_project'" x-cloak>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Project</label>
                    <input
                        x-ref="project"
                        type="text"
                        value="{{ old('project', $client->project ?? '') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        placeholder="Enter project name"
                    />
                    <span x-show="errors.project" x-text="errors.project ? errors.project[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div x-show="clientType === 'contractual' || clientType === 'mega_project'" x-cloak>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Project Agreement Number</label>
                    <input
                        x-ref="project_agreement_number"
                        type="text"
                        value="{{ old('project_agreement_number', $client->project_agreement_number ?? '') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        placeholder="Enter agreement number"
                    />
                    <span x-show="errors.project_agreement_number" x-text="errors.project_agreement_number ? errors.project_agreement_number[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div x-show="clientType === 'contractual' || clientType === 'mega_project'" x-cloak>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Project Value in BDT</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400">BDT</span>
                        <input
                            x-ref="project_value"
                            type="number"
                            value="{{ old('project_value', $client->project_value ?? '') }}"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-14 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            placeholder="0"
                        />
                    </div>
                    <span x-show="errors.project_value" x-text="errors.project_value ? errors.project_value[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div x-show="clientType === 'contractual' || clientType === 'mega_project'" x-cloak>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Target Finishing Date</label>
                    <input
                        x-ref="target_finishing_date"
                        type="date"
                        value="{{ old('target_finishing_date', $client->target_finishing_date ?? '') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                    />
                    <span x-show="errors.target_finishing_date" x-text="errors.target_finishing_date ? errors.target_finishing_date[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                @isset($client)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                        <select
                            x-ref="status"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                        >
                            <option value="active" @selected(old('status', $client->status) === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $client->status) === 'inactive')>Inactive</option>
                        </select>
                        <span x-show="errors.status" x-text="errors.status ? errors.status[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                    </div>
                @endisset
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    {{ $isEdit ? 'Update Client' : 'Save Client' }}
                </button>
                <a href="{{ route('manager.clients.index') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-3.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                    Cancel
                </a>
            </div>
        </form>
    </x-common.component-card>
</div>

@push('scripts')
    <script>
        function clientFormComponent(initialType) {
            return {
                clientType: initialType || 'port',
                errors: {},
                async submitClientForm(url, method) {
                    const payload = {
                        name: this.$refs.name.value,
                        contact_number: this.$refs.contact_number.value,
                        client_type: this.clientType,
                        project: this.$refs.project?.value,
                        project_agreement_number: this.$refs.project_agreement_number?.value,
                        project_value: this.$refs.project_value?.value,
                        target_finishing_date: this.$refs.target_finishing_date?.value,
                        status: this.$refs.status?.value,
                        _method: method === 'PUT' ? 'PUT' : undefined,
                    };

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await response.json();
                    if (response.status === 422) {
                        this.errors = data.errors;
                        return;
                    }
                    if (!response.ok) {
                        Toastify({ text: 'Something went wrong.', duration: 4000, gravity: 'top', position: 'right', backgroundColor: '#ef4444', stopOnFocus: true }).showToast();
                        return;
                    }

                    this.errors = {};
                    sessionStorage.setItem('toast_success', data.message ?? 'Client saved successfully.');
                    window.location.href = '{{ route('manager.clients.index') }}';
                }
            };
        }
    </script>
@endpush
