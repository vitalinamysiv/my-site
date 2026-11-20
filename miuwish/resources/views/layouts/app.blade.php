<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'MiuWish' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-cream text-ink">
        <div class="min-h-screen">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white border-b border-line">
                    <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            @if (session('status') && session('status') !== 'profile-updated')
                <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4">
                    <p class="rounded-xl bg-petal px-4 py-3 text-sm">{{ session('status') }}</p>
                </div>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
