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
    <div class="min-h-screen">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-xl border-b border-black/[0.05] dark:border-white/[0.08]">
                <div class="max-w-7xl mx-auto py-3.5 sm:py-5 px-3.5 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content with mobile bottom bar padding -->
        <main class="pb-24 sm:pb-12">
            {{ $slot }}
        </main>
    </div>

    <!-- Apple-style iOS Mobile Bottom Navigation Dock (Icon-only on mobile, dark-mode reactive) -->
    <nav aria-label="Mobile Bottom Navigation" class="fixed bottom-0 inset-x-0 z-40 sm:hidden backdrop-blur-2xl apple-glass-dock bg-white/85 dark:bg-[#161618]/90 border-t border-black/[0.08] dark:border-white/[0.12] px-6 py-2 shadow-apple-float">
        <div class="flex items-center justify-around max-w-xs mx-auto">
            <!-- Dashboard Tab (Icon Only) -->
            <a
                href="{{ route('dashboard') }}"
                aria-label="Dashboard"
                title="Dashboard"
                class="relative flex items-center justify-center w-12 h-11 rounded-2xl transition-all duration-200 active:scale-90 {{ request()->routeIs('dashboard') ? 'bg-[#0071e3]/12 dark:bg-[#0a84ff]/20 text-[#0071e3] dark:text-[#0a84ff] shadow-xs' : 'text-gray-400 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-black/[0.04] dark:hover:bg-white/[0.06]' }}"
            >
                <i class="pi pi-compass text-xl"></i>
                @if(request()->routeIs('dashboard'))
                    <span class="absolute bottom-1 w-1 h-1 rounded-full bg-[#0071e3] dark:bg-[#0a84ff]"></span>
                @endif
            </a>

            <!-- Bucket Tab (Icon Only) -->
            <a
                href="{{ route('bucket') }}"
                aria-label="Bucket"
                title="Bucket"
                class="relative flex items-center justify-center w-12 h-11 rounded-2xl transition-all duration-200 active:scale-90 {{ request()->routeIs('bucket') ? 'bg-[#0071e3]/12 dark:bg-[#0a84ff]/20 text-[#0071e3] dark:text-[#0a84ff] shadow-xs' : 'text-gray-400 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-black/[0.04] dark:hover:bg-white/[0.06]' }}"
            >
                <i class="pi pi-box text-xl"></i>
                @if(request()->routeIs('bucket'))
                    <span class="absolute bottom-1 w-1 h-1 rounded-full bg-[#0071e3] dark:bg-[#0a84ff]"></span>
                @endif
            </a>

            <!-- Profile Tab (Icon Only) -->
            <a
                href="{{ route('profile.edit') }}"
                aria-label="Profile"
                title="Profile"
                class="relative flex items-center justify-center w-12 h-11 rounded-2xl transition-all duration-200 active:scale-90 {{ request()->routeIs('profile.edit') ? 'bg-[#0071e3]/12 dark:bg-[#0a84ff]/20 text-[#0071e3] dark:text-[#0a84ff] shadow-xs' : 'text-gray-400 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-black/[0.04] dark:hover:bg-white/[0.06]' }}"
            >
                <i class="pi pi-user text-xl"></i>
                @if(request()->routeIs('profile.edit'))
                    <span class="absolute bottom-1 w-1 h-1 rounded-full bg-[#0071e3] dark:bg-[#0a84ff]"></span>
                @endif
            </a>
        </div>
    </nav>

    <x-theme-toggle />
</body>

</html>