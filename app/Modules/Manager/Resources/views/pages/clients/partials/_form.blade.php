@php
    $isEdit = isset($client);
    $initialFormState = [
        'clientType' => old('client_type', $client->client_type ?? ''),
        'email' => old('email', ''),
        'password' => old('password', ''),
        'isEdit' => $isEdit,
    ];
@endphp

<div class="space-y-6" x-data="clientFormComponent(@js($initialFormState))" x-init="initDatePicker()">
    <x-common.component-card :title="$isEdit ? 'Edit Client' : 'Client Registration Form'" :desc="$isEdit ? 'Update client information and contact details.' : 'Register a new transport business client.'">
        <form x-ref="clientForm" class="space-y-6" @submit.prevent="submitClientForm('{{ $formAction }}', '{{ $formMethod }}')">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Client Name <span class="text-red-500">*</span></label>
                    <input
                        x-ref="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $client->name ?? '') }}"
                        maxlength="255"
                        required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        placeholder="Enter client name"
                    />
                    <span x-show="errors.name" x-text="errors.name ? errors.name[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Phone Number <span class="text-red-500">*</span></label>
                    <input
                        x-ref="phone_number"
                        name="phone_number"
                        type="text"
                        value="{{ old('phone_number', old('contact_number', $client->contact_number ?? '')) }}"
                        inputmode="numeric"
                        pattern="^01[3-9]\d{8}$"
                        minlength="11"
                        maxlength="11"
                        required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        placeholder="01XXXXXXXXX"
                    />
                    <span x-show="errors.phone_number" x-text="errors.phone_number ? errors.phone_number[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                @if (! $isEdit)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email (Optional)</label>
                        <input
                            x-ref="email"
                            name="email"
                            x-model="email"
                            type="email"
                            value="{{ old('email', '') }}"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            placeholder="client@example.com"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">If left empty, the system will assign an internal email for account integrity.</p>
                        <span x-show="errors.email" x-text="errors.email ? errors.email[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password</label>
                        <div class="relative flex gap-2">
                            <div class="relative flex-1">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    x-ref="password"
                                    name="password"
                                    x-model="password"
                                    x-on:input="checkStrength()"
                                    value="{{ old('password', '') }}"
                                    minlength="8"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="Minimum 8 characters"
                                />
                                <button type="button" x-on:click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <template x-if="!showPassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </template>
                                    <template x-if="showPassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/></svg>
                                    </template>
                                </button>
                            </div>
                            <button type="button" x-on:click="generatePassword()" class="whitespace-nowrap rounded-lg border border-gray-300 px-3 py-2.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Auto Generate</button>
                            <button type="button" x-show="password.length > 0" x-on:click="copyToClipboard(password)" class="rounded-lg border border-gray-300 px-3 py-2.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Copy</button>
                        </div>

                        <div x-show="passwordStrength !== ''" class="mt-2 flex items-center gap-2">
                            <div class="flex gap-1">
                                <span class="h-1.5 w-8 rounded-full" :class="['weak','fair','good','strong'].includes(passwordStrength) ? 'bg-red-400' : 'bg-gray-200'"></span>
                                <span class="h-1.5 w-8 rounded-full" :class="['fair','good','strong'].includes(passwordStrength) ? 'bg-yellow-400' : 'bg-gray-200'"></span>
                                <span class="h-1.5 w-8 rounded-full" :class="['good','strong'].includes(passwordStrength) ? 'bg-blue-400' : 'bg-gray-200'"></span>
                                <span class="h-1.5 w-8 rounded-full" :class="passwordStrength === 'strong' ? 'bg-green-400' : 'bg-gray-200'"></span>
                            </div>
                            <span class="text-xs capitalize" :class="{
                                'text-red-500': passwordStrength === 'weak',
                                'text-yellow-500': passwordStrength === 'fair',
                                'text-blue-500': passwordStrength === 'good',
                                'text-green-500': passwordStrength === 'strong'
                            }" x-text="passwordStrength"></span>
                        </div>

                        <span x-show="errors.password" x-text="errors.password ? errors.password[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                    </div>
                @endif

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Client Type <span class="text-red-500">*</span></label>
                    <select
                        x-model="clientType"
                        name="client_type"
                        required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                    >
                        <option value="port">Port</option>
                        <option value="contractual">Contractual</option>
                        <option value="mega_project">Mega Project</option>
                    </select>
                    <span x-show="errors.client_type" x-text="errors.client_type ? errors.client_type[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div x-show="clientType === 'contractual' || clientType === 'mega_project'" x-cloak>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Project <span class="text-red-500">*</span></label>
                    <input
                        x-ref="project"
                        name="project"
                        type="text"
                        value="{{ old('project', $client->project ?? '') }}"
                        maxlength="255"
                        :required="clientType === 'contractual' || clientType === 'mega_project'"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        placeholder="Enter project name"
                    />
                    <span x-show="errors.project" x-text="errors.project ? errors.project[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div x-show="clientType === 'contractual' || clientType === 'mega_project'" x-cloak>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Project Agreement Number <span class="text-red-500">*</span></label>
                    <input
                        x-ref="project_agreement_number"
                        name="project_agreement_number"
                        type="text"
                        value="{{ old('project_agreement_number', $client->project_agreement_number ?? '') }}"
                        maxlength="100"
                        :required="clientType === 'contractual' || clientType === 'mega_project'"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        placeholder="Enter agreement number"
                    />
                    <span x-show="errors.project_agreement_number" x-text="errors.project_agreement_number ? errors.project_agreement_number[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div x-show="clientType === 'contractual' || clientType === 'mega_project'" x-cloak>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Project Value in BDT <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400">BDT</span>
                        <input
                            x-ref="project_value"
                            name="project_value"
                            type="number"
                            value="{{ old('project_value', $client->project_value ?? '') }}"
                            step="0.01"
                            min="0"
                            :required="clientType === 'contractual' || clientType === 'mega_project'"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-14 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            placeholder="0"
                        />
                    </div>
                    <span x-show="errors.project_value" x-text="errors.project_value ? errors.project_value[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div x-show="clientType === 'contractual' || clientType === 'mega_project'" x-cloak>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Target Finishing Date <span class="text-red-500">*</span></label>
                    <div class="relative custom-datepicker">
                        <input
                            x-ref="target_finishing_date"
                            name="target_finishing_date"
                            type="text"
                            value="{{ old('target_finishing_date', $client->target_finishing_date ?? '') }}"
                            placeholder="Select date"
                            autocomplete="off"
                            readonly
                            :required="clientType === 'contractual' || clientType === 'mega_project'"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                        />
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" class="size-5">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 2C8.41421 2 8.75 2.33579 8.75 2.75V3.75H15.25V2.75C15.25 2.33579 15.5858 2 16 2C16.4142 2 16.75 2.33579 16.75 2.75V3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V9V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V9V6C3.25 4.75736 4.25736 3.75 5.5 3.75H7.25V2.75C7.25 2.33579 7.58579 2 8 2ZM8 5.25H5.5C5.08579 5.25 4.75 5.58579 4.75 6V8.25H19.25V6C19.25 5.58579 18.9142 5.25 18.5 5.25H16H8ZM19.25 9.75H4.75V19C4.75 19.4142 5.08579 19.75 5.5 19.75H18.5C18.9142 19.75 19.25 19.4142 19.25 19V9.75Z" fill="currentColor"></path>
                            </svg>
                        </span>
                    </div>
                    <span x-show="errors.target_finishing_date" x-text="errors.target_finishing_date ? errors.target_finishing_date[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                @isset($client)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status <span class="text-red-500">*</span></label>
                        <select
                            x-ref="status"
                            name="status"
                            required
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
        function clientFormComponent(initialData) {
            return {
                clientType: initialData.clientType || 'port',
                isEdit: !!initialData.isEdit,
                email: initialData.email || '',
                password: initialData.password || '',
                showPassword: false,
                passwordStrength: '',
                errors: {},
                datePicker: null,
                initDatePicker() {
                    this.$nextTick(() => {
                        if (typeof flatpickr === 'undefined' || !this.$refs.target_finishing_date) {
                            return;
                        }

                        this.datePicker = flatpickr(this.$refs.target_finishing_date, {
                            mode: 'single',
                            static: true,
                            monthSelectorType: 'static',
                            dateFormat: 'Y-m-d',
                            allowInput: false,
                            defaultDate: this.$refs.target_finishing_date.value || null,
                        });

                        this.checkStrength();
                    });
                },
                generatePassword() {
                    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$!';
                    let generated = '';

                    for (let i = 0; i < 12; i++) {
                        generated += chars.charAt(Math.floor(Math.random() * chars.length));
                    }

                    this.password = generated;
                    this.showPassword = true;
                    this.checkStrength();
                },
                checkStrength() {
                    if (this.password.length === 0) {
                        this.passwordStrength = '';
                        return;
                    }

                    if (this.password.length < 6) {
                        this.passwordStrength = 'weak';
                        return;
                    }

                    const checks = [/[A-Z]/.test(this.password), /[a-z]/.test(this.password), /[0-9]/.test(this.password), /[@#$!]/.test(this.password)].filter(Boolean).length;
                    this.passwordStrength = checks <= 2 ? 'fair' : checks === 3 ? 'good' : 'strong';
                },
                copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(() => {
                        Toastify({ text: 'Copied to clipboard.', duration: 2000, gravity: 'top', position: 'right', backgroundColor: '#6366f1' }).showToast();
                    });
                },
                escapeHtml(value) {
                    if (!value) {
                        return '';
                    }

                    return value
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                },
                async submitClientForm(url, method) {
                    if (!this.$refs.clientForm.reportValidity()) {
                        return;
                    }

                    const payload = {
                        name: this.$refs.name.value,
                        phone_number: this.$refs.phone_number.value,
                        email: this.isEdit ? undefined : this.email,
                        password: this.isEdit ? undefined : this.password,
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

                    if (response.status === 201 && method === 'POST' && data.credentials) {
                        const email = data.credentials.email ? this.escapeHtml(data.credentials.email) : '';
                        const phone = this.escapeHtml(data.credentials.phone || '');
                        const password = this.escapeHtml(data.credentials.password || '');
                        const emailBlock = email !== ''
                            ? `<div><p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Email</p><p class="text-sm font-medium text-gray-800">${email}</p></div>`
                            : '';

                        await Swal.fire({
                            title: 'Client Account Created',
                            icon: 'success',
                            html: `
                                <p class="text-sm text-gray-500 mb-4">Share these credentials with the client. The password will not be shown again.</p>
                                <div class="text-left bg-gray-50 rounded-lg p-4 space-y-3">
                                    ${emailBlock}
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Phone</p>
                                        <p class="text-sm font-medium text-gray-800">${phone}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Password</p>
                                        <p class="text-sm font-medium text-gray-800 font-mono">${password}</p>
                                    </div>
                                </div>
                            `,
                            confirmButtonText: 'Copy & Continue',
                            confirmButtonColor: '#3b82f6',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        }).then((result) => {
                            if (!result.isConfirmed) {
                                return;
                            }

                            const parts = [];
                            if (data.credentials.email) {
                                parts.push(`Email: ${data.credentials.email}`);
                            }
                            parts.push(`Phone: ${data.credentials.phone}`);
                            parts.push(`Password: ${data.credentials.password}`);
                            navigator.clipboard.writeText(parts.join('\n'));
                            sessionStorage.setItem('toast_success', data.message ?? 'Client created successfully.');
                            window.location.href = '{{ route('manager.clients.index') }}';
                        });

                        return;
                    }

                    sessionStorage.setItem('toast_success', data.message ?? 'Client saved successfully.');
                    window.location.href = '{{ route('manager.clients.index') }}';
                }
            };
        }
    </script>
@endpush
