<section id="tab-panel-personal" class="tab-panel">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <form id="personal-info-form" class="grid grid-cols-1 gap-5 md:grid-cols-2" onsubmit="event.preventDefault(); submitPersonalInfo();">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name <span class="text-red-500">*</span></label>
                <input id="name" name="name" type="text" value="{{ $user->name }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-600">
                <span class="text-sm text-red-500" id="name-error"></span>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address <span class="text-red-500">*</span></label>
                <input id="email" name="email" type="email" value="{{ $user->email }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-600">
                <span class="text-sm text-red-500" id="email-error"></span>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                <input id="phone" name="phone" type="text" value="{{ $user->phone }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-600">
                <span class="text-sm text-red-500" id="phone-error"></span>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">NID Number</label>
                <input id="nid" name="nid" type="text" value="{{ $user->nid }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-600">
                <span class="text-sm text-red-500" id="nid-error"></span>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Date of Birth</label>
                <input
                    id="date_of_birth"
                    name="date_of_birth"
                    type="date"
                    value="{{ $user->date_of_birth?->format('Y-m-d') }}"
                    max="{{ now()->format('Y-m-d') }}"
                    onclick="this.showPicker && this.showPicker()"
                    onfocus="this.showPicker && this.showPicker()"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-600"
                >
                <span class="text-sm text-red-500" id="date_of_birth-error"></span>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
                <select id="gender" name="gender" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-600">
                    <option value="">Select Gender</option>
                    <option value="male" @selected($user->gender === 'male')>Male</option>
                    <option value="female" @selected($user->gender === 'female')>Female</option>
                </select>
                <span class="text-sm text-red-500" id="gender-error"></span>
            </div>

            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                <textarea id="address" name="address" rows="4" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-600">{{ $user->address }}</textarea>
                <span class="text-sm text-red-500" id="address-error"></span>
            </div>

            <div class="md:col-span-2">
                <button id="personal-save-btn" type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                    <span id="personal-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                    Save Personal Information
                </button>
            </div>
        </form>
    </div>
</section>
