@props(['title' => config('app.name')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ $title }} — {{ config('app.name') }}</title>

        @fluxAppearance
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-zinc-100 dark:bg-zinc-900 antialiased [--sidebar-width:16rem] lg:grid lg:[grid-template-areas:'sidebar_main'] lg:[grid-template-columns:var(--sidebar-width)_1fr]">
        {{ $slot }}

        @fluxScripts
        @livewireScripts
    </body>
</html>
