<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', config('app.name', 'Book Store'))</title>

        @include('partials.assets')
    </head>
    <body>

        {{-- Sign-in and register pages bring their own branding, so no site header or footer. --}}
        @yield('content')

    </body>
</html>
