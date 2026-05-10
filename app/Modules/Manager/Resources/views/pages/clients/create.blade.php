@extends('manager::layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Add New Client" />

    @include('manager::pages.clients.partials._form', [
        'formAction' => route('manager.clients.store'),
        'formMethod' => 'POST',
    ])
@endsection
