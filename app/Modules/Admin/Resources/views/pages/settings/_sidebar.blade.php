<aside class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">Settings</h2>
    <nav class="space-y-1">
        <a href="{{ route('admin.settings.general.index') }}"
              class="js-settings-nav flex items-center gap-2 rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.settings.general.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-slate-50' }}">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3l8 4v6c0 5-3.5 7.5-8 8-4.5-.5-8-3-8-8V7l8-4z" stroke-width="1.8"/></svg>
            <span>General</span>
        </a>
        <a href="{{ route('admin.settings.financial.index') }}"
              class="js-settings-nav flex items-center gap-2 rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.settings.financial.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-slate-50' }}">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M3 12h18M3 18h18" stroke-width="1.8"/></svg>
            <span>Financial</span>
        </a>
        <a href="{{ route('admin.settings.notifications.index') }}"
              class="js-settings-nav flex items-center gap-2 rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.settings.notifications.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-slate-50' }}">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0" stroke-width="1.8"/></svg>
            <span>Notifications</span>
        </a>
    </nav>
</aside>

@once
    @push('scripts')
        <script>
            (function() {
                if (window.AdminSettingsAjax) {
                    return;
                }

                function isSettingsLink(link) {
                    if (!link) {
                        return false;
                    }

                    try {
                        const url = new URL(link.href, window.location.origin);
                        return url.pathname.startsWith('/admin/settings/');
                    } catch (error) {
                        return false;
                    }
                }

                function executeScripts(rootElement) {
                    const scripts = rootElement.querySelectorAll('script');

                    scripts.forEach((script) => {
                        const replacement = document.createElement('script');

                        Array.from(script.attributes).forEach((attribute) => {
                            replacement.setAttribute(attribute.name, attribute.value);
                        });

                        replacement.text = script.text;
                        script.replaceWith(replacement);
                    });
                }

                function showToast(text, isError = false) {
                    if (typeof window.Toastify !== 'function') {
                        return;
                    }

                    Toastify({
                        text,
                        duration: 3000,
                        gravity: 'top',
                        position: 'right',
                        stopOnFocus: true,
                        style: { background: isError ? '#ef4444' : '#22c55e' },
                    }).showToast();
                }

                function renderSettingsSkeleton(region) {
                    region.innerHTML = `
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                            <div class="lg:col-span-1">
                                <aside class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                    <div class="h-4 w-24 animate-pulse rounded bg-slate-200"></div>
                                    <div class="mt-4 space-y-2">
                                        <div class="h-9 w-full animate-pulse rounded-lg bg-slate-100"></div>
                                        <div class="h-9 w-full animate-pulse rounded-lg bg-slate-100"></div>
                                        <div class="h-9 w-full animate-pulse rounded-lg bg-slate-100"></div>
                                    </div>
                                </aside>
                            </div>
                            <section class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
                                <div class="h-6 w-52 animate-pulse rounded bg-slate-200"></div>
                                <div class="mt-4 space-y-3">
                                    <div class="h-11 w-full animate-pulse rounded-lg bg-slate-100"></div>
                                    <div class="h-11 w-full animate-pulse rounded-lg bg-slate-100"></div>
                                    <div class="h-11 w-2/3 animate-pulse rounded-lg bg-slate-100"></div>
                                    <div class="h-28 w-full animate-pulse rounded-lg bg-slate-100"></div>
                                </div>
                            </section>
                        </div>
                    `;
                }

                async function loadSettingsRegion(url, pushState = true) {
                    const currentRegion = document.getElementById('settings-page-region');

                    if (!currentRegion) {
                        window.location.href = url;
                        return;
                    }

                    currentRegion.classList.add('opacity-60', 'pointer-events-none');
                    renderSettingsSkeleton(currentRegion);

                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'text/html',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Failed to load settings page.');
                        }

                        const html = await response.text();
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const incomingRegion = doc.getElementById('settings-page-region');

                        if (!incomingRegion) {
                            throw new Error('Settings content not found.');
                        }

                        currentRegion.replaceWith(incomingRegion);
                        executeScripts(incomingRegion);

                        if (pushState) {
                            window.history.pushState({}, '', url);
                        }

                        const title = doc.querySelector('title');
                        if (title) {
                            document.title = title.textContent || document.title;
                        }
                    } catch (error) {
                        window.location.href = url;
                    }
                }

                async function submitSettingsForm(form) {
                    const currentRegion = document.getElementById('settings-page-region');

                    if (!currentRegion) {
                        form.submit();
                        return;
                    }

                    const submitButton = form.querySelector('button[type="submit"]');
                    const buttonLabel = submitButton ? submitButton.querySelector('span') : null;
                    const buttonText = buttonLabel ? buttonLabel.textContent : null;

                    try {
                        if (submitButton) {
                            submitButton.disabled = true;
                        }

                        if (buttonLabel) {
                            buttonLabel.textContent = 'Saving...';
                        }

                        const response = await fetch(form.action, {
                            method: form.method || 'POST',
                            body: new FormData(form),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'text/html',
                            },
                        });

                        const html = await response.text();
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const incomingRegion = doc.getElementById('settings-page-region');

                        if (!incomingRegion) {
                            throw new Error('Settings content not found.');
                        }

                        currentRegion.replaceWith(incomingRegion);
                        executeScripts(incomingRegion);

                        const firstError = incomingRegion.querySelector('.text-red-600');

                        if (firstError) {
                            showToast(firstError.textContent.trim(), true);
                            return;
                        }

                        showToast('Settings updated successfully.');
                    } catch (error) {
                        console.error(error);
                        showToast('Unable to save settings. Please try again.', true);
                    } finally {
                        if (submitButton) {
                            submitButton.disabled = false;
                        }

                        if (buttonLabel && buttonText !== null) {
                            buttonLabel.textContent = buttonText;
                        }
                    }
                }

                document.addEventListener('click', function(event) {
                    const link = event.target.closest('.js-settings-nav');

                    if (!isSettingsLink(link)) {
                        return;
                    }

                    event.preventDefault();
                    loadSettingsRegion(link.href);
                });

                document.addEventListener('submit', function(event) {
                    const form = event.target.closest('[data-settings-ajax-form]');

                    if (!form) {
                        return;
                    }

                    event.preventDefault();
                    submitSettingsForm(form);
                });

                window.addEventListener('popstate', function() {
                    if (!window.location.pathname.startsWith('/admin/settings/')) {
                        return;
                    }

                    loadSettingsRegion(window.location.href, false);
                });

                window.AdminSettingsAjax = {
                    loadRegion: loadSettingsRegion,
                    submitForm: submitSettingsForm,
                    showToast,
                };
            })();
        </script>
    @endpush
@endonce
