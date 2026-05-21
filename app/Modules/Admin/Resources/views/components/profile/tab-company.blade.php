<section id="tab-panel-company" class="tab-panel hidden">
    <div class="space-y-5">
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200">
            <i class="ti ti-info-circle mr-1"></i>
            This information appears on all invoices, payment receipts, and downloadable reports.
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <form id="company-form" class="grid grid-cols-1 gap-5 md:grid-cols-2" onsubmit="event.preventDefault(); submitCompanyInfo();">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name <span class="text-red-500">*</span></label>
                    <input id="company_name" name="company_name" type="text" value="{{ $user->companySetting?->company_name }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-600">
                    <span class="text-sm text-red-500" id="company_name-error"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Trade License Number</label>
                    <input id="trade_license" name="trade_license" type="text" value="{{ $user->companySetting?->trade_license }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-600">
                    <span class="text-sm text-red-500" id="trade_license-error"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Company Phone <span class="text-red-500">*</span></label>
                    <input id="company_phone" name="phone" type="text" value="{{ $user->companySetting?->phone }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-600">
                    <span class="text-sm text-red-500" id="phone-error"></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Company Email <span class="text-red-500">*</span></label>
                    <input id="company_email" name="email" type="email" value="{{ $user->companySetting?->email }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-600">
                    <span class="text-sm text-red-500" id="email-error"></span>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Company Address <span class="text-red-500">*</span></label>
                    <textarea id="company_address" name="address" rows="4" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-600">{{ $user->companySetting?->address }}</textarea>
                    <span class="text-sm text-red-500" id="address-error"></span>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Website</label>
                    <input id="company_website" name="website" type="url" value="{{ $user->companySetting?->website }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-600">
                    <span class="text-sm text-red-500" id="website-error"></span>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50/60 p-4 dark:border-gray-700 dark:bg-gray-900/30">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Company Logo</p>
                            <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-medium text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">JPG, PNG, WEBP, SVG</span>
                        </div>

                        <div class="rounded-xl border-2 border-dashed border-gray-300 bg-white p-4 text-center transition hover:border-brand-400 dark:border-gray-600 dark:bg-gray-800/40">
                            <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
                                <i class="ti ti-photo text-xl"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Upload company logo</p>
                            <p class="mt-1 text-xs text-gray-500">Recommended square image, max 1MB</p>

                            <img id="logo-preview" src="{{ $user->companySetting?->logo_url }}" class="mx-auto mt-4 h-24 w-full rounded-lg border border-gray-200 bg-white object-contain dark:border-gray-700 {{ $user->companySetting?->logo_url ? '' : 'hidden' }}" alt="Logo preview">

                            <label for="logo-input" class="mt-4 inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                <i class="ti ti-upload"></i>
                                Choose logo
                            </label>
                            <input id="logo-input" type="file" accept="image/jpeg,image/png,image/webp,image/svg+xml" class="sr-only">
                        </div>

                        <button id="save-logo-btn" type="button" onclick="saveCompanyLogo()" class="mt-3 hidden w-full rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Save logo</button>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50/60 p-4 dark:border-gray-700 dark:bg-gray-900/30">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Authority Signature</p>
                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-medium text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">JPG, PNG</span>
                        </div>

                        <div class="rounded-xl border-2 border-dashed border-gray-300 bg-white p-4 text-center transition hover:border-brand-400 dark:border-gray-600 dark:bg-gray-800/40">
                            <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
                                <i class="ti ti-signature text-xl"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Upload signature image</p>
                            <p class="mt-1 text-xs text-gray-500">PNG with transparent background recommended</p>

                            <img id="signature-preview" src="{{ $user->companySetting?->signature_url }}" class="mx-auto mt-4 h-24 w-full rounded-lg border border-gray-200 bg-white object-contain dark:border-gray-700 {{ $user->companySetting?->signature_url ? '' : 'hidden' }}" alt="Signature preview">

                            <label for="signature-input" class="mt-4 inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                <i class="ti ti-upload"></i>
                                Choose signature
                            </label>
                            <input id="signature-input" type="file" accept="image/jpeg,image/png" class="sr-only">
                        </div>

                        <button id="save-signature-btn" type="button" onclick="saveCompanySignature()" class="mt-3 hidden w-full rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Save signature</button>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <button id="company-save-btn" type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                        <span id="company-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                        Save Company Information
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
