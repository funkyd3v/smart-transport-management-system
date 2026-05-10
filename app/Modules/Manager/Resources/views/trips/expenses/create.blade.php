@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Record Trip Expense" />

    <x-common.component-card title="Expense Entry" desc="Register operational expense for this trip.">
        <form method="POST" action="{{ route('manager.trips.expenses.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="trip_ulid" value="{{ $trip->ulid }}" />

            <select name="category_id" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700">
                <option value="">Select expense category</option>
                @foreach ($expenseCategories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <input name="amount" type="number" step="0.01" min="0" placeholder="Amount" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <input name="expense_date" type="date" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />
            <textarea name="description" rows="3" placeholder="Description (optional)" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm dark:border-gray-700"></textarea>
            <input name="receipt" type="file" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700" />

            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('manager.trips.show', $trip->ulid) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm">Cancel</a>
                <button class="rounded-lg bg-brand-500 px-4 py-2 text-sm text-white">Save Expense</button>
            </div>
        </form>
    </x-common.component-card>
@endsection
