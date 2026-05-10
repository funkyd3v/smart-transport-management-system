@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Add Truck" />

    @include('manager::pages.trucks.partials._form', [
        'truck' => null,
        'formAction' => route('manager.trucks.store'),
        'formMethod' => 'POST',
    ])
@endsection
