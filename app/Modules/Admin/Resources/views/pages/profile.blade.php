@extends('admin::layouts.app')

@section('content')
	<x-common.page-breadcrumb pageTitle="Admin Profile" />

	<div class="space-y-6">
		<x-profile.profile-header :user="$user" />
		<x-profile.stats-strip />

		<section id="profile-tabs-section" class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
			<div class="mb-5 overflow-x-auto">
				<nav class="flex min-w-max gap-2" id="profile-tabs-nav">
					<button class="profile-tab-btn rounded-lg px-4 py-2 text-sm font-medium" data-tab="personal" onclick="switchTab('personal', true)">Personal Information</button>
					<button class="profile-tab-btn rounded-lg px-4 py-2 text-sm font-medium" data-tab="security" onclick="switchTab('security')">Security</button>
					<button class="profile-tab-btn rounded-lg px-4 py-2 text-sm font-medium" data-tab="activity" onclick="switchTab('activity')">Activity Log</button>
					<button class="profile-tab-btn rounded-lg px-4 py-2 text-sm font-medium" data-tab="company" onclick="switchTab('company')">Company Information</button>
				</nav>
			</div>

			<x-profile.tab-personal :user="$user" />
			<x-profile.tab-security :loginHistory="$loginHistory" />
			<x-profile.tab-activity />
			<x-profile.tab-company :user="$user" />
		</section>
	</div>
@endsection

@push('scripts')
	<script>
		let activityLoaded = false;
		let currentSessionId = null;

		if (typeof window.Toastify !== 'function') {
			window.Toastify = function(options = {}) {
				return {
					showToast() {
						const background = options?.style?.background || '';
						const isError = background.includes('EF4444');
						const timer = options.duration || 3000;

						if (typeof Swal !== 'undefined') {
							Swal.fire({
								toast: true,
								position: 'top-end',
								showConfirmButton: false,
								timer,
								icon: isError ? 'error' : 'success',
								title: options.text || '',
							});
							return;
						}
					},
				};
			};
		}

		function scrollToProfileTabsSection() {
			const section = document.getElementById('profile-tabs-section');
			if (!section) {
				return;
			}

			const stickyHeader = document.querySelector('header.sticky');
			const offset = (stickyHeader ? stickyHeader.offsetHeight : 0) + 16;
			const top = section.getBoundingClientRect().top + window.scrollY - offset;

			window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
		}

		function switchTab(tabName, shouldScroll = false, updateHash = true) {
			const validTabs = ['personal', 'security', 'activity', 'company'];
			if (!validTabs.includes(tabName)) {
				tabName = 'personal';
			}

			document.querySelectorAll('.tab-panel').forEach((panel) => panel.classList.add('hidden'));
			document.querySelectorAll('.profile-tab-btn').forEach((btn) => {
				btn.classList.remove('bg-brand-500', 'text-white');
				btn.classList.add('text-gray-600', 'dark:text-gray-300');
			});

			const panel = document.getElementById(`tab-panel-${tabName}`);
			if (panel) {
				panel.classList.remove('hidden');
			}

			const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
			if (activeButton) {
				activeButton.classList.add('bg-brand-500', 'text-white');
				activeButton.classList.remove('text-gray-600', 'dark:text-gray-300');
			}

			localStorage.setItem('adminProfileTab', tabName);
			if (updateHash) {
				history.replaceState(null, null, '#' + tabName);
			}

			if (tabName === 'activity' && !activityLoaded) {
				loadActivityLog();
			}

			if (tabName === 'security') {
				loadSessions();
			}

			if (shouldScroll) {
				scrollToProfileTabsSection();
			}
		}

		function ajaxRequest(options) {
			const method = (options.method || 'GET').toUpperCase();
			const headers = {
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
				'Accept': 'application/json',
				'X-Requested-With': 'XMLHttpRequest',
				...(options.headers || {}),
			};

			const isFormData = options.data instanceof FormData;
			let url = options.url;
			let body;

			if (method === 'GET' && options.data) {
				const queryString = typeof options.data === 'string'
					? options.data
					: new URLSearchParams(options.data).toString();

				if (queryString) {
					url += (url.includes('?') ? '&' : '?') + queryString;
				}
			} else if (options.data !== undefined) {
				if (isFormData) {
					body = options.data;
				} else if (typeof options.data === 'string') {
					body = options.data;
					headers['Content-Type'] = 'application/json';
				} else {
					body = JSON.stringify(options.data);
					headers['Content-Type'] = 'application/json';
				}
			}

			if (typeof options.beforeSend === 'function') {
				options.beforeSend();
			}

			const requestHeaders = { ...headers };
			if (isFormData) {
				delete requestHeaders['Content-Type'];
			}

			fetch(url, {
				method,
				headers: requestHeaders,
				body,
				credentials: 'same-origin',
			})
				.then(async (response) => {
					if (response.redirected && response.url.includes('/login')) {
						window.location.href = '{{ route('login') }}';
						return;
					}

					const text = await response.text();
					const contentType = response.headers.get('content-type') || '';
					const isJson = contentType.includes('application/json');
					let responseJSON = {};
					if (isJson) {
						try {
							responseJSON = text ? JSON.parse(text) : {};
						} catch {
							responseJSON = {};
						}
					}

					const xhr = { status: response.status, responseJSON };

					if (response.ok) {
						if (typeof options.success === 'function') {
							options.success(responseJSON);
						}
						return;
					}

					if (!isJson && xhr.status >= 400) {
						Swal.fire({
							icon: 'error',
							title: 'Request Failed',
							text: 'Unexpected server response. Please refresh and try again.',
						});
						return;
					}

					if (xhr.status === 419) {
						Toastify({ text: 'Session expired. Redirecting to login.', style: { background: '#EF4444' } }).showToast();
						window.location.href = '{{ route('login') }}';
						return;
					}

					if (xhr.status === 401) {
						window.location.href = '{{ route('login') }}';
						return;
					}

					if (xhr.status === 422) {
						if (typeof options.validationError === 'function') {
							options.validationError(xhr.responseJSON?.errors || {});
						}

						if (typeof options.error === 'function') {
							options.error(xhr.responseJSON || {});
						}

						return;
					}

					if (xhr.status >= 500) {
						Swal.fire({
							icon: 'error',
							title: 'Server Error',
							text: 'Server error. Please try again or contact support.',
						});
					}

					if (typeof options.error === 'function') {
						options.error(xhr.responseJSON || {});
					}
				})
				.catch(() => {
					Swal.fire({
						icon: 'error',
						title: 'Network Error',
						text: 'Could not connect to server. Please try again.',
					});
				})
				.finally(() => {
					if (typeof options.complete === 'function') {
						options.complete();
					}
				});
		}

		function serializeForm(formId) {
			const form = document.getElementById(formId);
			if (!form) {
				return {};
			}

			return Object.fromEntries(new FormData(form).entries());
		}

		function showSpinner(id, show) {
			const spinner = document.getElementById(id);
			if (!spinner) {
				return;
			}

			spinner.classList.toggle('hidden', !show);
		}

		document.getElementById('avatar-overlay-trigger').addEventListener('click', function() {
			document.getElementById('avatar-input').click();
		});

		document.getElementById('avatar-input').addEventListener('change', function(e) {
			const file = e.target.files[0];
			if (!file) {
				return;
			}

			if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > (2 * 1024 * 1024)) {
				Toastify({ text: 'Invalid file. Use JPG/PNG/WEBP up to 2MB.', style: { background: '#EF4444' } }).showToast();
				this.value = '';
				return;
			}

			const reader = new FileReader();
			reader.onload = function(event) {
				document.getElementById('avatar-preview').src = event.target.result;
				document.getElementById('save-avatar-btn').classList.remove('hidden');
				document.getElementById('save-avatar-btn').classList.add('inline-flex');
			};
			reader.readAsDataURL(file);
		});

		function saveAvatar() {
			const file = document.getElementById('avatar-input').files[0];
			if (!file) {
				return;
			}

			const formData = new FormData();
			formData.append('avatar', file);

			ajaxRequest({
				method: 'POST',
				url: '{{ route('admin.profile.avatar.update') }}',
				data: formData,
				processData: false,
				contentType: false,
				beforeSend: () => {
					showSpinner('save-avatar-spinner', true);
					document.getElementById('save-avatar-btn').setAttribute('disabled', 'disabled');
				},
				success: (response) => {
					const avatarUrl = response?.data?.avatar_url;
					if (avatarUrl) {
						document.querySelectorAll('#avatar-preview').forEach((img) => {
							img.src = avatarUrl + (avatarUrl.includes('?') ? '&' : '?') + 'v=' + Date.now();
						});
					}

					Toastify({ text: 'Profile photo updated', style: { background: '#10B981' } }).showToast();
					document.getElementById('save-avatar-btn').classList.add('hidden');
					document.getElementById('avatar-input').value = '';
				},
				complete: () => {
					showSpinner('save-avatar-spinner', false);
					document.getElementById('save-avatar-btn').removeAttribute('disabled');
				},
			});
		}

		function clearErrors(formId) {
			document.querySelectorAll(`#${formId} span[id$="-error"]`).forEach((el) => el.textContent = '');
			document.querySelectorAll(`#${formId} input, #${formId} textarea, #${formId} select`).forEach((input) => {
				input.classList.remove('border-red-500');
			});
		}

		function submitPersonalInfo() {
			clearErrors('personal-info-form');

			ajaxRequest({
				method: 'PUT',
				url: '{{ route('admin.profile.personal.update') }}',
				data: serializeForm('personal-info-form'),
				beforeSend: () => showSpinner('personal-spinner', true),
				success: () => {
					Toastify({ text: 'Profile updated successfully', duration: 3000, style: { background: '#10B981' } }).showToast();
					document.getElementById('profile-name-heading').textContent = document.getElementById('name').value;
					document.getElementById('profile-email-heading').textContent = document.getElementById('email').value;
				},
				validationError: (errors) => {
					Object.keys(errors).forEach((field) => {
						const input = document.getElementById(field);
						const errorEl = document.getElementById(`${field}-error`);
						if (input) {
							input.classList.add('border-red-500');
						}
						if (errorEl) {
							errorEl.textContent = errors[field][0];
						}
					});

					Toastify({ text: 'Please fix the errors below', style: { background: '#EF4444' } }).showToast();
				},
				complete: () => showSpinner('personal-spinner', false),
			});
		}

		function updateStrengthBar(score) {
			const colors = {
				1: 'bg-red-500',
				2: 'bg-amber-500',
				3: 'bg-blue-500',
				4: 'bg-emerald-500',
			};

			const labels = {
				0: '',
				1: 'Weak',
				2: 'Fair',
				3: 'Strong',
				4: 'Very strong',
			};

			[1, 2, 3, 4].forEach((index) => {
				const segment = document.getElementById(`pwd-seg-${index}`);
				segment.className = 'h-2 flex-1 rounded bg-gray-200 dark:bg-gray-700';
				if (index <= score) {
					segment.classList.add(colors[score]);
				}
			});

			document.getElementById('password-strength-text').textContent = labels[score] || '';
		}

		document.getElementById('new-password-input').addEventListener('input', function() {
			const val = this.value;
			let score = 0;
			if (val.length >= 8) score++;
			if (/[A-Z]/.test(val)) score++;
			if (/[0-9]/.test(val)) score++;
			if (/[^A-Za-z0-9]/.test(val)) score++;
			updateStrengthBar(score);
		});

		document.getElementById('confirm-password-input').addEventListener('input', function() {
			const match = this.value === document.getElementById('new-password-input').value;
			document.getElementById('confirm-password-error').textContent = match ? '' : 'Passwords do not match';
		});

		function submitPasswordChange() {
			clearErrors('password-form');

			if (document.getElementById('new-password-input').value !== document.getElementById('confirm-password-input').value) {
				document.getElementById('confirm-password-error').textContent = 'Passwords do not match';
				return;
			}

			ajaxRequest({
				method: 'PUT',
				url: '{{ route('admin.profile.password.update') }}',
				data: serializeForm('password-form'),
				beforeSend: () => showSpinner('password-spinner', true),
				success: () => {
					Swal.fire({
						icon: 'success',
						title: 'Password Changed',
						text: 'Password changed. You will remain logged in on this device. Other sessions have been invalidated.',
					});
					document.getElementById('password-form').reset();
					updateStrengthBar(0);
				},
				validationError: (errors) => {
					Object.keys(errors).forEach((field) => {
						const inputId = field === 'password' ? 'new-password-input' : (field === 'password_confirmation' ? 'confirm-password-input' : 'current-password-input');
						const input = document.getElementById(inputId);
						const errorEl = document.getElementById(`${field}-error`);
						if (input) {
							input.classList.add('border-red-500');
						}
						if (errorEl) {
							errorEl.textContent = errors[field][0];
						}
					});

					Toastify({ text: 'Please fix the errors below', style: { background: '#EF4444' } }).showToast();
				},
				error: (response) => {
					if (response?.errors?.field === 'current_password') {
						document.getElementById('current_password-error').textContent = response.message;
						Toastify({ text: response.message, style: { background: '#EF4444' } }).showToast();
					}
				},
				complete: () => showSpinner('password-spinner', false),
			});
		}

		document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
			btn.addEventListener('click', function() {
				const input = document.getElementById(this.dataset.togglePassword);
				input.type = input.type === 'password' ? 'text' : 'password';
				const icon = this.querySelector('i');
				icon.classList.toggle('ti-eye');
				icon.classList.toggle('ti-eye-off');
			});
		});

		function parseDevice(userAgent) {
			const ua = (userAgent || '').toLowerCase();
			let browser = 'Unknown';
			if (ua.includes('edg/')) browser = 'Edge';
			else if (ua.includes('chrome/')) browser = 'Chrome';
			else if (ua.includes('firefox/')) browser = 'Firefox';
			else if (ua.includes('safari/') && !ua.includes('chrome/')) browser = 'Safari';

			let os = 'Unknown';
			if (ua.includes('windows')) os = 'Windows';
			else if (ua.includes('android')) os = 'Android';
			else if (ua.includes('iphone') || ua.includes('ipad')) os = 'iOS';
			else if (ua.includes('mac')) os = 'macOS';
			else if (ua.includes('linux')) os = 'Linux';

			let device = 'desktop';
			if (ua.includes('ipad') || ua.includes('tablet')) device = 'tablet';
			else if (ua.includes('mobile') || ua.includes('iphone') || ua.includes('android')) device = 'mobile';

			let icon = 'ti-device-desktop';
			if (device === 'mobile') icon = 'ti-device-mobile';
			if (device === 'tablet') icon = 'ti-device-tablet';

			return { browser, os, device, icon };
		}

		function loadSessions() {
			ajaxRequest({
				method: 'GET',
				url: '{{ route('admin.profile.sessions') }}',
				success: (response) => {
					const payload = response.data || {};
					const sessions = payload.sessions || [];
					currentSessionId = payload.current_session_id || null;

					const tbody = document.getElementById('sessions-table-body');
					tbody.innerHTML = '';

					if (sessions.length === 0) {
						tbody.innerHTML = '<tr><td colspan="4" class="px-3 py-4 text-center text-gray-500">No active sessions found.</td></tr>';
						return;
					}

					sessions.forEach((session) => {
						const device = parseDevice(session.user_agent);
						const isCurrent = currentSessionId && session.id === currentSessionId;

						tbody.insertAdjacentHTML('beforeend', `
							<tr class="border-b border-gray-100 dark:border-gray-700/60 ${isCurrent ? 'bg-emerald-50/60 dark:bg-emerald-500/5' : ''}">
								<td class="px-3 py-2 text-gray-700 dark:text-gray-300">
									<span class="inline-flex items-center gap-2"><i class="ti ${device.icon}"></i> ${device.browser} · ${device.os}</span>
								</td>
								<td class="px-3 py-2 text-gray-700 dark:text-gray-300">${session.ip_address || '-'}</td>
								<td class="px-3 py-2 text-gray-700 dark:text-gray-300">${session.last_activity_formatted}</td>
								<td class="px-3 py-2">${isCurrent ? '<span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">Current session</span>' : '<span class="text-xs text-gray-500">Active</span>'}</td>
							</tr>
						`);
					});
				},
			});
		}

		function terminateOtherSessions() {
			Swal.fire({
				title: 'Sign out other devices?',
				html: `
					<p class="text-sm text-gray-600 mb-4">This will sign out your account on all other devices. You will need to enter your current password.</p>
					<input type="password" id="swal-password" class="swal2-input" placeholder="Current password">
				`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Sign out all others',
				confirmButtonColor: '#EF4444',
				cancelButtonText: 'Cancel',
				preConfirm: () => {
					const password = document.getElementById('swal-password').value;
					if (!password) {
						Swal.showValidationMessage('Password is required');
						return false;
					}
					return password;
				}
			}).then(result => {
				if (!result.isConfirmed) {
					return;
				}

				ajaxRequest({
					method: 'DELETE',
					url: '{{ route('admin.profile.sessions.destroy') }}',
					data: { password: result.value },
					success: () => {
						Toastify({ text: 'All other sessions terminated', style: { background: '#10B981' } }).showToast();
						loadSessions();
					},
					error: (response) => {
						Swal.fire({ icon: 'error', title: 'Incorrect password', text: response.message || 'Password is incorrect.' });
					}
				});
			});
		}

		document.querySelectorAll('[data-notification-toggle]').forEach((toggle) => {
			toggle.addEventListener('change', function() {
				const event = this.dataset.event;
				const channel = this.dataset.channel;
				const enabled = this.checked;
				const spinner = document.getElementById(`spinner-${event}-${channel}`);

				spinner.classList.remove('hidden');

				ajaxRequest({
					method: 'PATCH',
					url: '{{ route('admin.profile.notifications.update') }}',
					data: { event, channel, enabled: enabled ? 1 : 0 },
					success: () => {
						Toastify({ text: 'Preference saved', duration: 1500, style: { background: '#3B82F6' } }).showToast();
					},
					error: () => {
						this.checked = !enabled;
						Toastify({ text: 'Failed to save preference', style: { background: '#EF4444' } }).showToast();
					},
					complete: () => spinner.classList.add('hidden')
				});
			});
		});

		function showActivitySkeleton() {
			const tbody = document.getElementById('activity-table-body');
			tbody.innerHTML = '';
			for (let i = 0; i < 5; i++) {
				tbody.insertAdjacentHTML('beforeend', '<tr><td colspan="5" class="px-3 py-3"><div class="h-5 w-full animate-pulse rounded bg-gray-200 dark:bg-gray-700"></div></td></tr>');
			}
		}

		function renderActivityTable(rows, meta) {
			const tbody = document.getElementById('activity-table-body');
			tbody.innerHTML = '';

			if (!rows.length) {
				tbody.innerHTML = '<tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">No activity found for selected filters.</td></tr>';
			}

			rows.forEach((row) => {
				const status = (row.action || '').includes('failed') ? 'Failed' : 'Success';

				tbody.insertAdjacentHTML('beforeend', `
					<tr class="border-b border-gray-100 dark:border-gray-700/60">
						<td class="px-3 py-2 text-gray-700 dark:text-gray-300">${new Date(row.created_at).toLocaleString()}</td>
						<td class="px-3 py-2 text-gray-700 dark:text-gray-300">${row.action || '-'}</td>
						<td class="px-3 py-2 text-gray-700 dark:text-gray-300">${row.table_name || '-'}</td>
						<td class="px-3 py-2 text-gray-700 dark:text-gray-300">${row.ip_address || '-'}</td>
						<td class="px-3 py-2"><span class="inline-flex rounded-full px-2 py-1 text-xs font-medium ${status === 'Success' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'}">${status}</span></td>
					</tr>
				`);
			});

			const pag = document.getElementById('activity-pagination');
			pag.innerHTML = '';

			for (let i = 1; i <= (meta.last_page || 1); i++) {
				pag.insertAdjacentHTML('beforeend', `<button class="rounded border px-3 py-1 text-sm ${i === meta.current_page ? 'bg-brand-500 text-white border-brand-500' : 'border-gray-300 text-gray-700'}" onclick="loadActivityLog(${i})">${i}</button>`);
			}
		}

		function loadActivityLog(page = 1) {
			const from = document.getElementById('activity-from').value;
			const to = document.getElementById('activity-to').value;
			const type = document.getElementById('activity-type').value;

			showActivitySkeleton();

			ajaxRequest({
				method: 'GET',
				url: '{{ route('admin.profile.activity') }}',
				data: { page, from, to, type },
				success: (response) => {
					renderActivityTable(response.data || [], response.meta || {});
					activityLoaded = true;
				},
			});
		}

		function exportActivity() {
			const from = document.getElementById('activity-from').value;
			const to = document.getElementById('activity-to').value;
			const type = document.getElementById('activity-type').value;
			window.location.href = `{{ route('admin.profile.activity.export') }}?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&type=${encodeURIComponent(type)}`;
		}

		function initActivityDateRangeConstraint(retryCount = 0) {
			const fromInput = document.getElementById('activity-from');
			const toInput = document.getElementById('activity-to');

			if (!fromInput || !toInput) {
				return;
			}

			const syncToMinDate = () => {
				const fromDate = fromInput.value || null;
				const toPicker = toInput._flatpickr || null;

				if (toPicker) {
					toPicker.set('minDate', fromDate);
				}

				if (fromDate && toInput.value && toInput.value < fromDate) {
					if (toPicker) {
						toPicker.clear();
					} else {
						toInput.value = '';
					}

					Toastify({ text: 'To date must be on or after From date', style: { background: '#EF4444' } }).showToast();
				}
			};

			if (!fromInput.dataset.rangeConstraintBound) {
				fromInput.addEventListener('change', syncToMinDate);
				fromInput.addEventListener('input', syncToMinDate);
				toInput.addEventListener('change', syncToMinDate);
				toInput.addEventListener('input', syncToMinDate);
				fromInput.dataset.rangeConstraintBound = '1';
			}

			syncToMinDate();

			if ((!fromInput._flatpickr || !toInput._flatpickr) && retryCount < 10) {
				setTimeout(() => initActivityDateRangeConstraint(retryCount + 1), 100);
			}
		}

		function loadProfileStats() {
			ajaxRequest({
				method: 'GET',
				url: '{{ route('admin.profile.stats') }}',
				success: (response) => {
					const data = response.data || {};
					document.getElementById('stat-trips').textContent = data.trips_created ?? 0;
					document.getElementById('stat-payments').textContent = data.payments_recorded ?? 0;
					document.getElementById('stat-invoices').textContent = data.invoices_generated ?? 0;
					document.getElementById('stat-actions').textContent = data.total_actions ?? 0;

					document.querySelectorAll('.stat-skeleton').forEach(el => el.classList.add('hidden'));
					document.querySelectorAll('.stat-value').forEach(el => el.classList.remove('hidden'));
				},
			});
		}

		function submitCompanyInfo() {
			clearErrors('company-form');

			ajaxRequest({
				method: 'PUT',
				url: '{{ route('admin.profile.company.update') }}',
				data: serializeForm('company-form'),
				beforeSend: () => showSpinner('company-spinner', true),
				success: () => {
					Toastify({ text: 'Company information updated', style: { background: '#10B981' } }).showToast();
				},
				validationError: (errors) => {
					Object.keys(errors).forEach((field) => {
						const errorEl = document.getElementById(`${field}-error`);
						if (errorEl) {
							errorEl.textContent = errors[field][0];
						}
					});
					Toastify({ text: 'Please fix the errors below', style: { background: '#EF4444' } }).showToast();
				},
				complete: () => showSpinner('company-spinner', false),
			});
		}

		function setupPreview(inputId, previewId, saveBtnId, allowed, maxBytes) {
			document.getElementById(inputId).addEventListener('change', function(e) {
				const file = e.target.files[0];
				if (!file) {
					return;
				}

				if (!allowed.includes(file.type) || file.size > maxBytes) {
					Toastify({ text: 'Invalid file selected', style: { background: '#EF4444' } }).showToast();
					this.value = '';
					return;
				}

				const reader = new FileReader();
				reader.onload = function(event) {
					const preview = document.getElementById(previewId);
					preview.src = event.target.result;
					preview.classList.remove('hidden');
					document.getElementById(saveBtnId).classList.remove('hidden');
				};
				reader.readAsDataURL(file);
			});
		}

		function saveCompanyLogo() {
			const file = document.getElementById('logo-input').files[0];
			if (!file) return;

			const fd = new FormData();
			fd.append('logo', file);

			ajaxRequest({
				method: 'POST',
				url: '{{ route('admin.profile.company.logo') }}',
				data: fd,
				processData: false,
				contentType: false,
				success: () => {
					Toastify({ text: 'Company logo updated', style: { background: '#10B981' } }).showToast();
					document.getElementById('save-logo-btn').classList.add('hidden');
				},
			});
		}

		function saveCompanySignature() {
			const file = document.getElementById('signature-input').files[0];
			if (!file) return;

			const fd = new FormData();
			fd.append('signature', file);

			ajaxRequest({
				method: 'POST',
				url: '{{ route('admin.profile.company.signature') }}',
				data: fd,
				processData: false,
				contentType: false,
				success: () => {
					Toastify({ text: 'Company signature updated', style: { background: '#10B981' } }).showToast();
					document.getElementById('save-signature-btn').classList.add('hidden');
				},
			});
		}

		document.addEventListener('DOMContentLoaded', function() {
			loadProfileStats();
			initActivityDateRangeConstraint();

			setupPreview('logo-input', 'logo-preview', 'save-logo-btn', ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'], 1024 * 1024);
			setupPreview('signature-input', 'signature-preview', 'save-signature-btn', ['image/jpeg', 'image/png'], 1024 * 1024);

			const hash = window.location.hash.replace('#', '');
			const saved = localStorage.getItem('adminProfileTab');
			const initial = ['personal', 'security', 'activity', 'company'].includes(hash)
				? hash
				: (saved || 'personal');
			switchTab(initial, Boolean(hash), Boolean(hash));
		});
	</script>
@endpush
