@extends('admin::layouts.app')

@section('title', 'Admin - Reports')

@section('content')
<x-common.page-breadcrumb pageTitle="Reports" />

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Trips</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['trips']) }}</p>
        </article>
        <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-5 shadow-sm">
            <p class="text-sm text-sky-700">Trip Income</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">BDT {{ number_format((float) $stats['payments'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm">
            <p class="text-sm text-emerald-700">Spare Sales Revenue</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">BDT {{ number_format((float) ($stats['spare_sales_revenue'] ?? 0), 2) }}</p>
        </article>
        <article class="rounded-2xl border border-rose-100 bg-rose-50/60 p-5 shadow-sm">
            <p class="text-sm text-rose-700">Trip Expenses</p>
            <p class="mt-2 text-2xl font-semibold text-rose-700">BDT {{ number_format((float) $stats['expenses'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-5 shadow-sm">
            <p class="text-sm text-indigo-700">Total Profit (Trip + Spare)</p>
            <p class="mt-2 text-2xl font-semibold text-indigo-700">BDT {{ number_format((float) ($stats['total_profit'] ?? 0), 2) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm sm:col-span-2 xl:col-span-4">
            <p class="text-sm text-amber-700">Outstanding Dues</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">BDT {{ number_format((float) $stats['dues'], 2) }}</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
            <h2 class="text-lg font-semibold text-slate-900">Daily Profit Breakdown</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Trip Income</td>
                            <td class="py-2 text-right font-medium text-slate-900">BDT {{ number_format((float) ($stats['daily_trip_income'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Spare Sales Revenue</td>
                            <td class="py-2 text-right font-medium text-slate-900">BDT {{ number_format((float) ($stats['daily_spare_sales_revenue'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Total Income</td>
                            <td class="py-2 text-right font-medium text-slate-900">BDT {{ number_format((float) ($stats['daily_total_income'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Trip Expenses</td>
                            <td class="py-2 text-right font-medium text-rose-700">BDT {{ number_format((float) ($stats['daily_trip_expenses'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Spare-related Expenses</td>
                            <td class="py-2 text-right font-medium text-rose-700">BDT {{ number_format((float) ($stats['daily_spare_related_expenses'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Trip Profit</td>
                            <td class="py-2 text-right font-medium text-emerald-700">BDT {{ number_format((float) ($stats['daily_trip_profit'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Spare Profit</td>
                            <td class="py-2 text-right font-medium text-emerald-700">BDT {{ number_format((float) ($stats['daily_spare_profit'] ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-semibold text-slate-900">Total Profit (Trip + Spare)</td>
                            <td class="py-2 text-right font-semibold text-indigo-700">BDT {{ number_format((float) ($stats['daily_total_profit'] ?? 0), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
            <h2 class="text-lg font-semibold text-slate-900">Monthly Profit Breakdown</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Trip Income</td>
                            <td class="py-2 text-right font-medium text-slate-900">BDT {{ number_format((float) ($stats['monthly_trip_income'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Spare Sales Revenue</td>
                            <td class="py-2 text-right font-medium text-slate-900">BDT {{ number_format((float) ($stats['monthly_spare_sales_revenue'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Total Income</td>
                            <td class="py-2 text-right font-medium text-slate-900">BDT {{ number_format((float) ($stats['monthly_total_income'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Trip Expenses</td>
                            <td class="py-2 text-right font-medium text-rose-700">BDT {{ number_format((float) ($stats['monthly_trip_expenses'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Spare-related Expenses</td>
                            <td class="py-2 text-right font-medium text-rose-700">BDT {{ number_format((float) ($stats['monthly_spare_related_expenses'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Trip Profit</td>
                            <td class="py-2 text-right font-medium text-emerald-700">BDT {{ number_format((float) ($stats['monthly_trip_profit'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-2 text-slate-600">Spare Profit</td>
                            <td class="py-2 text-right font-medium text-emerald-700">BDT {{ number_format((float) ($stats['monthly_spare_profit'] ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-semibold text-slate-900">Total Profit (Trip + Spare)</td>
                            <td class="py-2 text-right font-semibold text-indigo-700">BDT {{ number_format((float) ($stats['monthly_total_profit'] ?? 0), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <h2 class="mb-4 text-lg font-semibold text-slate-900">Generate Report</h2>

        <form id="report-generate-form" action="{{ route('admin.reports.generate') }}" method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @csrf
            <div class="md:col-span-2">
                <label for="report_type" class="mb-1 block text-sm font-medium text-slate-700">Report Type</label>
                <select id="report_type" name="report_type" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none">
                    @foreach ($reportTypes as $type)
                        <option value="{{ $type }}" @selected(($selectedReportType ?? '') === $type)>{{ str_replace('-', ' ', ucwords($type, '-')) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button id="report-generate-button" type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Generate Preview</button>
            </div>
        </form>

        <div class="mt-6 border-t border-slate-200 pt-4">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Quick Downloads</h3>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($reportTypes as $type)
                    <a href="{{ route('admin.reports.download', $type) }}" data-report-type="{{ $type }}" class="js-report-download rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 transition hover:border-slate-400 hover:text-slate-900">{{ str_replace('-', ' ', ucwords($type, '-')) }}</a>
                @endforeach
            </div>
        </div>

        <div id="report-preview-wrapper" class="mt-6 border-t border-slate-200 pt-4 {{ empty($previewReport) ? 'hidden' : '' }}">
            <h3 id="report-preview-title" class="text-base font-semibold text-slate-900">
                @if (!empty($previewReport))
                    Preview: {{ $previewReport['title'] }}
                @else
                    Preview
                @endif
            </h3>
            <div class="mt-3 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-3 py-2">Metric</th>
                            <th class="px-3 py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="report-preview-body">
                        @if (!empty($previewReport))
                            @foreach ($previewReport['rows'] as $row)
                                <tr class="border-b border-slate-200">
                                    <td class="px-3 py-2 text-slate-700">{{ $row['label'] }}</td>
                                    <td class="px-3 py-2 text-right font-medium text-slate-900">BDT {{ number_format((float) $row['value'], 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <script>
        (function() {
            const form = document.getElementById('report-generate-form');
            const submitButton = document.getElementById('report-generate-button');
            const typeSelect = document.getElementById('report_type');
            const previewWrapper = document.getElementById('report-preview-wrapper');
            const previewTitle = document.getElementById('report-preview-title');
            const previewBody = document.getElementById('report-preview-body');
            const downloadLinks = document.querySelectorAll('.js-report-download');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

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

            function escapeHtml(value) {
                return String(value)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function renderPreview(payload) {
                if (!payload || !Array.isArray(payload.rows)) {
                    return;
                }

                previewTitle.textContent = `Preview: ${payload.title}`;
                previewBody.innerHTML = payload.rows
                    .map((row) => {
                        const amount = Number(row.value || 0).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        });

                        return `<tr class="border-b border-slate-200">
                            <td class="px-3 py-2 text-slate-700">${escapeHtml(row.label)}</td>
                            <td class="px-3 py-2 text-right font-medium text-slate-900">BDT ${amount}</td>
                        </tr>`;
                    })
                    .join('');

                previewWrapper.classList.remove('hidden');
            }

            if (form && submitButton && typeSelect) {
                form.addEventListener('submit', async function(event) {
                    event.preventDefault();

                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-70', 'cursor-not-allowed');

                    const originalLabel = submitButton.textContent;
                    submitButton.textContent = 'Generating...';

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'application/json',
                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: new URLSearchParams(new FormData(form)),
                        });

                        const payload = await response.json();

                        if (!response.ok) {
                            const firstError = payload?.errors ? Object.values(payload.errors)[0]?.[0] : null;
                            throw new Error(firstError || 'Failed to generate report preview.');
                        }

                        renderPreview(payload?.data?.preview_report);

                        const selectedType = payload?.data?.selected_report_type || typeSelect.value;
                        const nextUrl = `{{ route('admin.reports.index') }}?preview=1&report_type=${encodeURIComponent(selectedType)}`;
                        window.history.replaceState({}, '', nextUrl);

                        showToast(payload?.message || 'Report preview generated.');
                    } catch (error) {
                        showToast(error.message || 'Failed to generate report preview.', true);
                    } finally {
                        submitButton.disabled = false;
                        submitButton.classList.remove('opacity-70', 'cursor-not-allowed');
                        submitButton.textContent = originalLabel;
                    }
                });
            }

            downloadLinks.forEach((link) => {
                link.addEventListener('click', async function(event) {
                    event.preventDefault();

                    const originalText = link.textContent;
                    link.classList.add('opacity-70', 'pointer-events-none');
                    link.textContent = 'Preparing...';

                    try {
                        const response = await fetch(link.href, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Could not download report.');
                        }

                        const blob = await response.blob();
                        const objectUrl = window.URL.createObjectURL(blob);
                        const downloadLink = document.createElement('a');
                        const contentDisposition = response.headers.get('Content-Disposition') || '';
                        const filenameMatch = contentDisposition.match(/filename="([^"]+)"/);
                        const fallback = `report-${link.dataset.reportType || 'download'}.csv`;

                        downloadLink.href = objectUrl;
                        downloadLink.download = filenameMatch ? filenameMatch[1] : fallback;
                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        downloadLink.remove();
                        window.URL.revokeObjectURL(objectUrl);

                        showToast('Report download started.');
                    } catch (error) {
                        showToast(error.message || 'Could not download report.', true);
                    } finally {
                        link.classList.remove('opacity-70', 'pointer-events-none');
                        link.textContent = originalText;
                    }
                });
            });
        })();
    </script>
@endpush
