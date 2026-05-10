@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Truck" />

    @include('manager::pages.trucks.partials._form', [
        'truck' => $truck,
        'formAction' => route('manager.trucks.update', $truck),
        'formMethod' => 'PUT',
    ])
@endsection
