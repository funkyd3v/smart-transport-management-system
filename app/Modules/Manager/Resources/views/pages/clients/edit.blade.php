@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Client" />

    @include('manager::pages.clients.partials._form', [
        'client' => $client,
        'formAction' => route('manager.clients.update', $client),
        'formMethod' => 'PUT',
    ])
@endsection
