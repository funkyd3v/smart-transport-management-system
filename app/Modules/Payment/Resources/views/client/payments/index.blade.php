@extends('client::layouts.app')

@section('content')
    <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/95 p-6 shadow-sm sm:p-8">
        <div class="pointer-events-none absolute -right-20 -top-16 h-56 w-56 rounded-full bg-rose-200/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 left-1/3 h-64 w-64 rounded-full bg-cyan-200/50 blur-3xl"></div>

        <div class="relative">
            <p class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                Client Payment Hub
            </p>
            <h1 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">Pay Outstanding Trips with bKash</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-600 sm:text-base">
                This page is dedicated to online bKash checkout. Offline or manual collection is intentionally disabled here for security and payment traceability.
            </p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Pending Trips</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $summary['trip_count'] }}</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Total Remaining Due</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">BDT {{ number_format((float) $summary['total_due'], 2) }}</p>
                </article>
                <article class="rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-cyan-50 p-4">
                    <p class="text-xs uppercase tracking-[0.16em] text-sky-700">Gateway</p>
                    <p class="mt-2 text-2xl font-semibold text-sky-950">bKash Sandbox</p>
                    <p class="mt-2 text-xs text-sky-800">Test PIN 12121 | OTP 123456</p>
                </article>
            </div>
        </div>
    </section>

    @if ($paymentResult)
        <section class="mt-5 rounded-2xl border px-4 py-3 text-sm {{ $paymentResult['status'] === 'succeeded' ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-amber-200 bg-amber-50 text-amber-900' }}">
            <p class="font-semibold">{{ $paymentResult['message'] }}</p>
            <p class="mt-1 text-xs">Payment: {{ $paymentResult['payment_ulid'] }} | Provider Ref: {{ $paymentResult['reference'] }}</p>
        </section>
    @endif

    @if ($errors->any())
        <section class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            @foreach ($errors->all() as $message)
                <p>{{ $message }}</p>
            @endforeach
        </section>
    @endif

    <section class="mt-6 grid gap-5 lg:grid-cols-2">
        @forelse ($dueRecords as $due)
            <article class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-[40px] bg-gradient-to-br from-sky-100/80 to-cyan-100/70"></div>

                <div class="relative">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Trip</p>
                            <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ $due->trip?->trip_code ?? $due->trip?->ulid ?? 'Trip #'.$due->trip_id }}</h2>
                        </div>
                        <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">bKash</span>
                    </div>

                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                            <dt class="text-xs uppercase tracking-[0.14em] text-slate-500">Remaining Due</dt>
                            <dd class="mt-1 text-base font-semibold text-slate-900">BDT {{ number_format((float) $due->remaining_due, 2) }}</dd>
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                            <dt class="text-xs uppercase tracking-[0.14em] text-slate-500">Due Date</dt>
                            <dd class="mt-1 text-base font-semibold text-slate-900">{{ $due->due_date?->format('d M Y') ?? 'Not set' }}</dd>
                        </div>
                    </dl>

                    <p class="mt-3 text-xs text-slate-500">
                        Route: {{ $due->trip?->pickup_point ?? 'N/A' }} to {{ $due->trip?->delivery_point ?? 'N/A' }}
                    </p>

                    <form action="{{ route('client.payments.bkash.initiate') }}" method="POST" class="mt-4 space-y-3">
                        @csrf
                        <input type="hidden" name="trip_ulid" value="{{ $due->trip?->ulid }}">

                        <label class="block text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Amount To Pay</label>
                        <input
                            type="number"
                            name="amount"
                            min="1"
                            max="{{ number_format((float) $due->remaining_due, 2, '.', '') }}"
                            step="0.01"
                            value="{{ number_format((float) $due->remaining_due, 2, '.', '') }}"
                            class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none ring-sky-200 transition focus:border-sky-500 focus:ring"
                            required
                        >

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-fuchsia-600 via-rose-600 to-orange-500 px-4 py-3 text-sm font-semibold text-white shadow-md transition hover:brightness-105"
                        >
                            <span>Pay with bKash</span>
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <article class="col-span-full rounded-2xl border border-emerald-200 bg-emerald-50 p-6 text-center">
                <h2 class="text-lg font-semibold text-emerald-900">All dues are already settled</h2>
                <p class="mt-2 text-sm text-emerald-800">No pending trip payment is available right now.</p>
            </article>
        @endforelse
    </section>
@endsection
