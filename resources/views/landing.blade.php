<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Inshidento AI - Resolución Inteligente de Incidentes</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Scripts and Styles -->
        @viteReactRefresh
        @vite(['resources/js/landing-page/main.jsx', 'resources/js/landing-page/index.css'])
    </head>
    <body class="antialiased">
        <div id="root"></div>
    </body>
</html>
