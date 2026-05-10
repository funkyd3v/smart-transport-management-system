@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Add Driver" />

    @include('manager::pages.drivers.partials._form', [
        'driver' => null,
        'formAction' => route('manager.drivers.store'),
        'formMethod' => 'POST',
    ])
@endsection
