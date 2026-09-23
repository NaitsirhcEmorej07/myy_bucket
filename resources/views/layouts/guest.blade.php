<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Myy Bucket: Your Alternative to iCloud Cloud Storage') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-[#f5f5f7] dark:bg-[#000000] text-[#1d1d1f] dark:text-[#f5f5f7] selection:bg-[#0071e3]/20 selection:text-[#0071e3]">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 sm:py-12">
            <div class="mb-4">
                <a href="/" class="flex flex-col items-center gap-1.5 group transition-transform active:scale-95 text-center">
                    <x-application-logo class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl shadow-apple-card" />
                    <span class="font-bold text-sm tracking-tight text-gray-900 dark:text-white">
                        Myy Bucket
                    </span>
                    <span class="text-[11px] text-[#86868b] font-medium">
                        Your Alternative to iCloud Cloud Storage
                    </span>
                </a>
            </div>

            <div class="w-full sm:max-w-md p-5 sm:p-7 bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-2xl border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card rounded-[24px] sm:rounded-3xl">
                {{ $slot }}
            </div>
        </div>

        <x-theme-toggle />
    </body>
</html>
