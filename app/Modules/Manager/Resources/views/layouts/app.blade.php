<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('manager::layouts.partials.head')
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-900">
        <div class="flex min-h-screen">
            @include('manager::layouts.partials.sidebar')

            <div class="flex min-h-screen flex-1 flex-col">
                @include('manager::layouts.partials.header')

                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
