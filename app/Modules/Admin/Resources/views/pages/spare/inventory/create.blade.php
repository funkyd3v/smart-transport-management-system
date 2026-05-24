@extends('admin::layouts.app')

@section('title', 'Admin - Add Spare Part')

@section('content')
<x-common.page-breadcrumb pageTitle="Spare Inventory / Add" />

<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-xl font-semibold text-slate-900">Add New Spare Part</h2>
    <p class="mt-1 text-sm text-slate-500">Register a new inventory item with source and stock details.</p>

    <div class="mt-6">
        @include('admin::pages.spare.inventory._form')
    </div>
</section>
@endsection
