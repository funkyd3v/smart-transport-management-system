@extends('admin::layouts.app')

@section('title', 'Admin - Edit Spare Part')

@section('content')
<x-common.page-breadcrumb pageTitle="Spare Inventory / Edit" />

<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-xl font-semibold text-slate-900">Edit Spare Part</h2>
    <p class="mt-1 text-sm text-slate-500">Update inventory details and current stock information.</p>

    <div class="mt-6">
        @include('admin::pages.spare.inventory._form')
    </div>
</section>
@endsection
