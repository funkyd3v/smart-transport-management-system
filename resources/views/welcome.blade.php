@extends('layouts.site')

@section('title', 'Home')

@push('styles')
    <x-landing.styles />
@endpush

@push('scripts')
    <x-landing.scripts />
@endpush

@section('content')
    <x-landing.hero />
    <x-landing.marquee />
    <x-landing.modules />
    <x-landing.workflow />
    <x-landing.benefits />
    <x-landing.cta-banner />
    <x-landing.contact />
@endsection
