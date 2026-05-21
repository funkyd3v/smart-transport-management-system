<section class="overflow-hidden rounded-2xl border border-gray-200 border-t-4 border-t-brand-500 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[220px_1fr]">
        <div class="flex flex-col items-center">
            <div class="group relative h-40 w-40">
                <img
                    id="avatar-preview"
                    src="{{ $user->avatar_url }}"
                    alt="{{ $user->name }}"
                    class="h-40 w-40 rounded-full border-4 border-white object-cover shadow-lg dark:border-gray-700"
                >
                <button
                    type="button"
                    id="avatar-overlay-trigger"
                    class="absolute inset-0 flex flex-col items-center justify-center rounded-full bg-black/45 text-white opacity-0 transition-opacity group-hover:opacity-100"
                >
                    <i class="ti ti-camera text-xl"></i>
                    <span class="mt-1 text-xs font-medium">Change photo</span>
                </button>
                <input id="avatar-input" type="file" accept="image/jpeg,image/png,image/webp" class="hidden">
            </div>

            <button
                id="save-avatar-btn"
                type="button"
                onclick="saveAvatar()"
                class="mt-4 hidden items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600"
            >
                <span id="save-avatar-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                Save avatar
            </button>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">JPG, PNG, WEBP · Max 2MB</p>
        </div>

        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 id="profile-name-heading" class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                <span class="inline-flex items-center rounded-full bg-brand-100 px-3 py-1 text-xs font-medium text-brand-700 dark:bg-brand-500/20 dark:text-brand-200">
                    {{ ucfirst((string) $user->role) }}
                </span>
            </div>

            <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <span id="profile-email-heading">{{ $user->email }}</span>
                @if($user->email_verified_at)
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                        <i class="ti ti-check"></i> Verified
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">
                        <i class="ti ti-alert-triangle"></i> Unverified
                    </span>
                @endif
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 text-sm text-gray-600 sm:grid-cols-2 dark:text-gray-300">
                <p class="inline-flex items-center gap-2"><i class="ti ti-phone"></i> {{ $user->phone ?: 'Not set' }}</p>
                <p class="inline-flex items-center gap-2"><i class="ti ti-clock"></i> {{ $user->formatted_last_login ?: 'Not available' }}</p>
                <p class="inline-flex items-center gap-2"><i class="ti ti-calendar"></i> Member since {{ optional($user->created_at)->format('d M Y') }}</p>
                <p class="inline-flex items-center gap-2"><i class="ti ti-map-pin"></i> Last IP: {{ $user->last_login_ip ?: 'N/A' }}</p>
            </div>

            <a href="#personal" class="mt-5 inline-flex items-center text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300 dark:hover:text-brand-200" onclick="switchTab('personal', true); return false;">
                Edit profile
            </a>
        </div>
    </div>
</section>
