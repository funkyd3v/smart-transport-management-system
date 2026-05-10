@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Driver" />

    @include('manager::pages.drivers.partials._form', [
        'driver' => $driver,
        'formAction' => route('manager.drivers.update', $driver),
        'formMethod' => 'PUT',
    ])
@endsection
