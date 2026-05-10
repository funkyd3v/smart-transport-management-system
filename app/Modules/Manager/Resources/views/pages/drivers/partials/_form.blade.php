@php
    $isEdit = isset($driver) && $driver !== null;
    $initialData = [
        'name' => old('name', $driver->name ?? ''),
        'mobile_number' => old('mobile_number', $driver->mobile_number ?? ''),
        'license_number' => old('license_number', $driver->license_number ?? ''),
        'nid_number' => old('nid_number', $driver->nid_number ?? ''),
        'driving_type' => old('driving_type', $driver->driving_type ?? 'permanent'),
        'joining_date' => old('joining_date', isset($driver) ? optional($driver->joining_date)->format('Y-m-d') : ''),
        'status' => old('status', $driver->status ?? 'active'),
        'avatar' => isset($driver) ? ($driver->getFirstMediaUrl('avatar') ?: asset('images/user/user-01.jpg')) : null,
    ];
@endphp

<div class="space-y-6" x-data="driverFormComponent(@js($initialData))" x-init="initDatePicker()">
    <x-common.component-card :title="$isEdit ? 'Edit Driver' : 'Driver Registration Form'" :desc="$isEdit ? 'Update driver profile, license info, and avatar.' : 'Register a new driver and upload an avatar.'">
        <form x-ref="driverForm" class="space-y-6" enctype="multipart/form-data" @submit.prevent="submitDriverForm('{{ $formAction }}', '{{ $formMethod }}')">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Driver Name</label>
                    <input name="name" x-model="form.name" type="text" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Enter driver name" />
                    <span x-show="errors.name" x-text="errors.name ? errors.name[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Mobile Number</label>
                    <input name="mobile_number" x-model="form.mobile_number" type="text" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="01XXXXXXXXX" />
                    <span x-show="errors.mobile_number" x-text="errors.mobile_number ? errors.mobile_number[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">License Number</label>
                    <input name="license_number" x-model="form.license_number" type="text" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Enter license number" />
                    <span x-show="errors.license_number" x-text="errors.license_number ? errors.license_number[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">NID Number</label>
                    <input name="nid_number" x-model="form.nid_number" type="text" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Enter NID number" />
                    <span x-show="errors.nid_number" x-text="errors.nid_number ? errors.nid_number[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Driving Type</label>
                    <select name="driving_type" x-model="form.driving_type" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="permanent">Permanent</option>
                        <option value="backup">Backup</option>
                    </select>
                    <span x-show="errors.driving_type" x-text="errors.driving_type ? errors.driving_type[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Joining Date</label>
                    <div class="relative custom-datepicker">
                        <input
                            x-ref="joining_date"
                            name="joining_date"
                            x-model="form.joining_date"
                            type="text"
                            placeholder="Select joining date"
                            autocomplete="off"
                            readonly
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                        />
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" class="size-5">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 2C8.41421 2 8.75 2.33579 8.75 2.75V3.75H15.25V2.75C15.25 2.33579 15.5858 2 16 2C16.4142 2 16.75 2.33579 16.75 2.75V3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V9V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V9V6C3.25 4.75736 4.25736 3.75 5.5 3.75H7.25V2.75C7.25 2.33579 7.58579 2 8 2ZM8 5.25H5.5C5.08579 5.25 4.75 5.58579 4.75 6V8.25H19.25V6C19.25 5.58579 18.9142 5.25 18.5 5.25H16H8ZM19.25 9.75H4.75V19C4.75 19.4142 5.08579 19.75 5.5 19.75H18.5C18.9142 19.75 19.25 19.4142 19.25 19V9.75Z" fill="currentColor"></path>
                            </svg>
                        </span>
                    </div>
                    <span x-show="errors.joining_date" x-text="errors.joining_date ? errors.joining_date[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                    <select name="status" x-model="form.status" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <span x-show="errors.status" x-text="errors.status ? errors.status[0] : ''" class="mt-1 block text-sm text-red-500"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Driver Avatar</label>
                    <input
                        x-ref="imageInput"
                        name="image"
                        type="file"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="hidden"
                        @change="onFileChange($event)"
                    />

                    <div
                        @click="openFileDialog()"
                        @dragover.prevent
                        @drop.prevent="handleDrop($event)"
                        class="group relative flex min-h-28 cursor-pointer items-center justify-between gap-4 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/70 px-4 py-4 transition hover:border-brand-300 hover:bg-brand-50/40 dark:border-gray-700 dark:bg-gray-900/60 dark:hover:border-brand-700"
                    >
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-white text-brand-500 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 16V7M12 7L8.5 10.5M12 7L15.5 10.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M20 16.5C20.621 16.109 21 15.447 21 14.714C21 13.394 19.768 12.317 18.288 12.184C17.726 10.27 15.922 8.875 13.772 8.875C11.196 8.875 9.086 10.885 9.01 13.406C7.593 13.514 6.5 14.53 6.5 15.786C6.5 17.114 7.716 18.214 9.27 18.214H15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-800 dark:text-white/90" x-text="imageName || 'Drop image here or click to browse'"></p>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400" x-text="imageName ? imageSize : 'JPG, PNG, WEBP up to 2MB'"></p>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <button type="button" @click.stop="openFileDialog()" class="rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white shadow-theme-xs transition hover:bg-brand-600">Choose</button>
                            <button x-show="imageName" type="button" @click.stop="clearSelectedImage()" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Remove</button>
                        </div>
                    </div>

                    <span x-show="errors.image" x-text="errors.image ? errors.image[0] : ''" class="mt-1 block text-sm text-red-500"></span>

                    <div class="mt-3 flex items-center gap-3" x-show="avatarPreview">
                        <img :src="avatarPreview" alt="Avatar Preview" class="size-20 rounded-full border border-gray-200 object-cover dark:border-gray-700">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">Preview</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">This image will be used as the driver's profile photo.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    {{ $isEdit ? 'Update Driver' : 'Save Driver' }}
                </button>
                <a href="{{ route('manager.drivers.index') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-3.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                    Cancel
                </a>
            </div>
        </form>
    </x-common.component-card>
</div>

@push('scripts')
    <script>
        function driverFormComponent(initialData) {
            return {
                form: {
                    name: initialData.name || '',
                    mobile_number: initialData.mobile_number || '',
                    license_number: initialData.license_number || '',
                    nid_number: initialData.nid_number || '',
                    driving_type: initialData.driving_type || 'permanent',
                    joining_date: initialData.joining_date || '',
                    status: initialData.status || 'active',
                },
                initialAvatar: initialData.avatar || null,
                avatarPreview: initialData.avatar || null,
                imageName: '',
                imageSize: '',
                errors: {},
                datePicker: null,
                initDatePicker() {
                    this.$nextTick(() => {
                        if (typeof flatpickr === 'undefined' || !this.$refs.joining_date) {
                            return;
                        }

                        this.datePicker = flatpickr(this.$refs.joining_date, {
                            mode: 'single',
                            static: true,
                            monthSelectorType: 'static',
                            dateFormat: 'Y-m-d',
                            maxDate: 'today',
                            allowInput: false,
                            defaultDate: this.form.joining_date || null,
                            onChange: (selectedDates, dateStr) => {
                                this.form.joining_date = dateStr;
                            },
                        });
                    });
                },
                openFileDialog() {
                    this.$refs.imageInput?.click();
                },
                onFileChange(event) {
                    const file = event.target.files[0] ?? null;
                    this.setImageFile(file);
                },
                handleDrop(event) {
                    const file = event.dataTransfer?.files?.[0] ?? null;
                    this.setImageFile(file);
                },
                setImageFile(file) {
                    if (!file) {
                        return;
                    }

                    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

                    if (!allowedTypes.includes(file.type)) {
                        Toastify({ text: 'Please upload JPG, PNG, or WEBP image.', duration: 3500, gravity: 'top', position: 'right', backgroundColor: '#ef4444', stopOnFocus: true }).showToast();
                        return;
                    }

                    if (file.size > 2 * 1024 * 1024) {
                        Toastify({ text: 'Image must be 2MB or smaller.', duration: 3500, gravity: 'top', position: 'right', backgroundColor: '#ef4444', stopOnFocus: true }).showToast();
                        return;
                    }

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    this.$refs.imageInput.files = dataTransfer.files;

                    this.imageName = file.name;
                    this.imageSize = this.formatFileSize(file.size);

                    if (this.avatarPreview && this.avatarPreview.startsWith('blob:')) {
                        URL.revokeObjectURL(this.avatarPreview);
                    }

                    this.avatarPreview = URL.createObjectURL(file);
                },
                clearSelectedImage() {
                    this.$refs.imageInput.value = '';
                    this.imageName = '';
                    this.imageSize = '';

                    if (this.avatarPreview && this.avatarPreview.startsWith('blob:')) {
                        URL.revokeObjectURL(this.avatarPreview);
                    }

                    this.avatarPreview = this.initialAvatar;
                },
                formatFileSize(bytes) {
                    if (!bytes) {
                        return '0 B';
                    }

                    const units = ['B', 'KB', 'MB', 'GB'];
                    const unitIndex = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
                    const value = bytes / Math.pow(1024, unitIndex);

                    return `${value.toFixed(value >= 10 || unitIndex === 0 ? 0 : 1)} ${units[unitIndex]}`;
                },
                async submitDriverForm(url, method) {
                    const formData = new FormData(this.$refs.driverForm);

                    if (method === 'PUT') {
                        formData.append('_method', 'PUT');
                    }

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const data = await response.json();

                    if (response.status === 422) {
                        this.errors = data.errors;
                        return;
                    }

                    if (!response.ok) {
                        Toastify({ text: data.message ?? 'Something went wrong.', duration: 4000, gravity: 'top', position: 'right', backgroundColor: '#ef4444', stopOnFocus: true }).showToast();
                        return;
                    }

                    this.errors = {};
                    sessionStorage.setItem('toast_success', data.message ?? 'Driver saved successfully.');
                    window.location.href = '{{ route('manager.drivers.index') }}';
                }
            };
        }
    </script>
@endpush
