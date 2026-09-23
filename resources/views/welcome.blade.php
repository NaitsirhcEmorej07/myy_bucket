<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="description" content="Myy Bucket: Your Alternative to iCloud Cloud Storage. Enjoy unlimited cloud storage with our early access free trial. Fast, secure, and mobile-first.">

    <title>{{ config('app.name', 'Myy Bucket: Your Alternative to iCloud Cloud Storage') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">

    <!-- Apple Web App Meta -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#f5f5f7" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=SF+Pro+Display:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="bg-[#f5f5f7] dark:bg-[#000000] text-[#1d1d1f] dark:text-[#f5f5f7] font-sans antialiased selection:bg-[#0071e3] selection:text-white transition-colors duration-300 min-h-screen flex flex-col overflow-x-hidden"
      x-data="{
          isDark: document.documentElement.classList.contains('dark'),
          mobileMenuOpen: false,
          activeFaq: null,
          toggleTheme() {
              this.isDark = !this.isDark;
              if (this.isDark) {
                  document.documentElement.classList.add('dark');
                  localStorage.setItem('theme', 'dark');
              } else {
                  document.documentElement.classList.remove('dark');
                  localStorage.setItem('theme', 'light');
              }
          }
      }">

    <!-- Top Ambient Glow (Apple-style subtle radial backdrop) -->
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[640px] overflow-hidden -z-10">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[720px] sm:w-[980px] h-[520px] bg-gradient-to-tr from-blue-500/20 via-sky-400/20 to-purple-500/20 dark:from-blue-600/15 dark:via-cyan-500/10 dark:to-purple-800/20 blur-3xl opacity-70"></div>
    </div>

    <!-- Sticky Glass Navigation -->
    <header class="sticky top-0 z-50 backdrop-blur-2xl bg-white/75 dark:bg-[#161618]/80 border-b border-black/[0.06] dark:border-white/[0.08] transition-colors">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-14 sm:h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-2xl bg-white dark:bg-[#1c1c1e] p-1.5 shadow-apple-card border border-black/[0.05] dark:border-white/[0.08] flex items-center justify-center group-hover:scale-105 transition-transform">
                    <img src="{{ asset('logo/logo.png') }}" alt="Myy Bucket Logo" class="w-full h-full object-contain">
                </div>
                <div class="flex flex-col">
                    <span class="text-xs sm:text-sm font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-1.5">
                        Myy Bucket
                        <span class="text-[9px] font-semibold uppercase tracking-wider px-1.5 py-0.2 rounded-full bg-blue-100 dark:bg-blue-950/70 text-[#0071e3] dark:text-[#0a84ff]">Trial</span>
                    </span>
                    <span class="text-[9px] text-[#86868b] hidden sm:inline-block leading-none">Your Alternative to iCloud</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-7 text-xs font-medium text-gray-600 dark:text-gray-300">
                <a href="#features" class="hover:text-[#0071e3] dark:hover:text-white transition-colors">Features</a>
                <a href="#unlimited" class="hover:text-[#0071e3] dark:hover:text-white transition-colors">Unlimited Storage</a>
                <a href="#comparison" class="hover:text-[#0071e3] dark:hover:text-white transition-colors">Why Myy Bucket</a>
                <a href="#pricing" class="hover:text-[#0071e3] dark:hover:text-white transition-colors">Free Trial</a>
                <a href="#faq" class="hover:text-[#0071e3] dark:hover:text-white transition-colors">FAQ</a>
            </nav>

            <!-- Right Actions & Theme Toggle -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Dark Mode Toggle Button -->
                <button
                    @click="toggleTheme()"
                    type="button"
                    aria-label="Toggle Appearance"
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center border border-black/[0.06] dark:border-white/[0.1] bg-black/[0.03] dark:bg-white/[0.06] text-gray-700 dark:text-amber-400 hover:scale-105 active:scale-95 transition-all"
                >
                    <i x-show="!isDark" class="pi pi-moon text-xs sm:text-sm"></i>
                    <i x-show="isDark" x-cloak class="pi pi-sun text-xs sm:text-sm"></i>
                </button>

                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-1.5 rounded-full bg-[#0071e3] hover:bg-[#0077ed] text-white text-xs font-semibold shadow-apple-card transition-all active:scale-95"
                    >
                        <span>Open Drive</span>
                        <i class="pi pi-arrow-right text-[10px]"></i>
                    </a>
                @endauth

                <!-- Mobile Hamburger Toggle -->
                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    type="button"
                    class="md:hidden p-1.5 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-black/[0.05] dark:hover:bg-white/[0.08]"
                    aria-label="Toggle Menu"
                >
                    <i :class="mobileMenuOpen ? 'pi pi-times' : 'pi pi-bars'" class="text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div
            x-show="mobileMenuOpen"
            x-cloak
            x-collapse
            class="md:hidden border-t border-black/[0.06] dark:border-white/[0.08] bg-white/95 dark:bg-[#161618]/95 px-5 py-4 space-y-3"
        >
            <a @click="mobileMenuOpen = false" href="#features" class="block text-xs font-medium py-1.5 text-gray-700 dark:text-gray-200">Features</a>
            <a @click="mobileMenuOpen = false" href="#unlimited" class="block text-xs font-medium py-1.5 text-gray-700 dark:text-gray-200">Unlimited Storage</a>
            <a @click="mobileMenuOpen = false" href="#comparison" class="block text-xs font-medium py-1.5 text-gray-700 dark:text-gray-200">Comparison</a>
            <a @click="mobileMenuOpen = false" href="#pricing" class="block text-xs font-medium py-1.5 text-gray-700 dark:text-gray-200">Pricing & Free Trial</a>
            <a @click="mobileMenuOpen = false" href="#faq" class="block text-xs font-medium py-1.5 text-gray-700 dark:text-gray-200">FAQ</a>

            @guest
                <div class="pt-3 border-t border-black/[0.06] dark:border-white/[0.08] flex items-center justify-between">
                    <a @click="mobileMenuOpen = false" href="{{ route('login') }}" class="text-xs font-medium text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-white">
                        Sign In
                    </a>
                    <a @click="mobileMenuOpen = false" href="{{ route('register') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#0071e3] text-white text-xs font-semibold shadow-xs">
                        <span>Start Free Trial</span>
                        <i class="pi pi-arrow-right text-[9px]"></i>
                    </a>
                </div>
            @else
                <div class="pt-3 border-t border-black/[0.06] dark:border-white/[0.08]">
                    <a @click="mobileMenuOpen = false" href="{{ route('dashboard') }}" class="inline-flex items-center justify-center w-full gap-1.5 px-4 py-2 rounded-full bg-[#0071e3] text-white text-xs font-semibold shadow-xs">
                        <span>Go to Myy Bucket Drive</span>
                        <i class="pi pi-arrow-right text-[10px]"></i>
                    </a>
                </div>
            @endguest

            <div class="pt-2 border-t border-black/[0.06] dark:border-white/[0.08] flex items-center justify-between">
                <span class="text-[11px] text-[#86868b]">Storage Tier:</span>
                <span class="text-xs font-bold text-[#0071e3] dark:text-[#0a84ff] flex items-center gap-1">
                    <i class="pi pi-infinity text-[11px]"></i> Unlimited
                </span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="grow">

        <!-- Hero Section (Mobile 1st, Apple-inspired Typography & Badges) -->
        <section class="pt-10 sm:pt-16 md:pt-24 pb-12 sm:pb-20 px-4 sm:px-6 max-w-5xl mx-auto text-center relative">
            <!-- Free Trial Announcement Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50/90 dark:bg-blue-950/50 border border-blue-200/60 dark:border-blue-800/50 text-[#0071e3] dark:text-[#0a84ff] text-[11px] sm:text-xs font-semibold mb-6 shadow-xs animate-fade-in backdrop-blur-sm">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0071e3]"></span>
                </span>
                <span>Early Access Free Trial • Unlimited Cloud Storage</span>
                <i class="pi pi-sparkles text-[10px]"></i>
            </div>

            <!-- Main Headline -->
            <h1 class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-gray-950 dark:text-white leading-[1.12]">
                Your Alternative to iCloud. <br class="hidden sm:inline">
                <span class="bg-gradient-to-r from-[#0071e3] via-[#5856d6] to-[#bf5af2] bg-clip-text text-transparent">
                    Without Storage Limits.
                </span>
            </h1>

            <!-- Subtitle -->
            <p class="mt-4 sm:mt-6 text-sm sm:text-lg md:text-xl text-[#86868b] dark:text-gray-400 max-w-2xl mx-auto font-normal leading-relaxed">
                Meet Myy Bucket — high-speed, secure, and privacy-first cloud storage for all your photos, 4K videos, documents, and creative archives. No upgrade prompts. No arbitrary limits.
            </p>

            <!-- Call to Actions -->
            <div class="mt-7 sm:mt-9 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 max-w-sm sm:max-w-md mx-auto">
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-[#0071e3] hover:bg-[#0077ed] text-white text-sm font-semibold shadow-apple-float transition-all hover:scale-[1.02] active:scale-95"
                    >
                        <i class="pi pi-box text-sm"></i>
                        <span>Go to Myy Bucket</span>
                    </a>
                @else
                    <a
                        href="{{ route('register') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-[#0071e3] hover:bg-[#0077ed] text-white text-sm font-semibold shadow-apple-float transition-all hover:scale-[1.02] active:scale-95"
                    >
                        <i class="pi pi-sparkles text-sm"></i>
                        <span>Start Free Trial Now</span>
                        <i class="pi pi-arrow-right text-xs"></i>
                    </a>
                    <a
                        href="{{ route('login') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-white/80 dark:bg-white/10 hover:bg-white dark:hover:bg-white/15 text-gray-800 dark:text-white text-sm font-semibold border border-black/[0.08] dark:border-white/[0.1] backdrop-blur-xl transition-all hover:scale-[1.02] active:scale-95"
                    >
                        <i class="pi pi-sign-in text-xs"></i>
                        <span>Sign In</span>
                    </a>
                @endauth
            </div>

            <!-- Guarantee Pills -->
            <div class="mt-5 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-[11px] text-[#86868b] dark:text-gray-400">
                <span class="flex items-center gap-1.5">
                    <i class="pi pi-check-circle text-emerald-500 text-xs"></i>
                    100% Free Trial
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="pi pi-check-circle text-emerald-500 text-xs"></i>
                    No Credit Card Required
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="pi pi-check-circle text-emerald-500 text-xs"></i>
                    Instant 30-Sec Setup
                </span>
            </div>

            <!-- Hero Interactive Glass Mockup (Apple Dashboard Preview) -->
            <div class="mt-10 sm:mt-14 relative mx-auto max-w-4xl">
                <div class="relative rounded-2xl sm:rounded-3xl p-2 sm:p-3 bg-gradient-to-b from-white/90 to-white/40 dark:from-white/15 dark:to-white/5 border border-black/[0.08] dark:border-white/[0.12] shadow-apple-float backdrop-blur-2xl">
                    <div class="rounded-xl sm:rounded-2xl overflow-hidden bg-[#fafafc] dark:bg-[#121214] border border-black/[0.05] dark:border-white/[0.06] p-4 sm:p-6 text-left">

                        <!-- Mockup Top Bar -->
                        <div class="flex items-center justify-between pb-4 border-b border-black/[0.05] dark:border-white/[0.08]">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-[#ff5f56]"></div>
                                <div class="w-3 h-3 rounded-full bg-[#ffbd2e]"></div>
                                <div class="w-3 h-3 rounded-full bg-[#27c93f]"></div>
                                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 ml-2">Myy Bucket Drive</span>
                            </div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/50 text-[#0071e3] dark:text-[#0a84ff] text-[10px] sm:text-xs font-semibold">
                                <i class="pi pi-infinity text-[10px]"></i>
                                <span>Unlimited Storage Active</span>
                            </div>
                        </div>

                        <!-- Mockup Storage Meter -->
                        <div class="mt-4 p-4 rounded-xl sm:rounded-2xl bg-white/90 dark:bg-[#1c1c1e]/90 border border-black/[0.04] dark:border-white/[0.06] shadow-xs">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <p class="text-[11px] text-[#86868b] font-medium">Drive Capacity</p>
                                    <p class="text-lg sm:text-2xl font-bold text-gray-950 dark:text-white flex items-center gap-2">
                                        <span>Unlimited Storage</span>
                                        <span class="text-xs font-normal text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full">0 B of ∞ Used</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 text-xs font-semibold text-[#0071e3] dark:text-[#0a84ff]">
                                    <i class="pi pi-bolt text-xs"></i>
                                    <span>Cloudflare R2 Edge Network</span>
                                </div>
                            </div>

                            <!-- Colorful Multi-category bar -->
                            <div class="w-full h-2.5 rounded-full bg-black/[0.04] dark:bg-white/[0.08] overflow-hidden flex mt-3">
                                <div class="w-[30%] bg-[#0071e3] transition-all" title="Photos"></div>
                                <div class="w-[25%] bg-[#bf5af2] transition-all" title="4K Videos"></div>
                                <div class="w-[15%] bg-[#ff9f0a] transition-all" title="Lossless Audio"></div>
                                <div class="w-[20%] bg-[#30d158] transition-all" title="Documents"></div>
                                <div class="w-[10%] bg-gray-400 transition-all" title="Archives"></div>
                            </div>
                        </div>

                        <!-- Mockup File Grid (Sample rich cards) -->
                        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3.5">
                            <div class="p-3 rounded-xl bg-white/80 dark:bg-[#1c1c1e]/80 border border-black/[0.04] dark:border-white/[0.06] flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-[#0071e3] flex items-center justify-center shrink-0">
                                    <i class="pi pi-image text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-semibold text-gray-900 dark:text-white truncate">Kyoto_RAW_01.heic</p>
                                    <p class="text-[9px] text-[#86868b]">48.2 MB • Photo</p>
                                </div>
                            </div>

                            <div class="p-3 rounded-xl bg-white/80 dark:bg-[#1c1c1e]/80 border border-black/[0.04] dark:border-white/[0.06] flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/50 text-[#bf5af2] flex items-center justify-center shrink-0">
                                    <i class="pi pi-video text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-semibold text-gray-900 dark:text-white truncate">Drone_4K_Reel.mp4</p>
                                    <p class="text-[9px] text-[#86868b]">1.8 GB • Video</p>
                                </div>
                            </div>

                            <div class="p-3 rounded-xl bg-white/80 dark:bg-[#1c1c1e]/80 border border-black/[0.04] dark:border-white/[0.06] flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-[#30d158] flex items-center justify-center shrink-0">
                                    <i class="pi pi-file-pdf text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-semibold text-gray-900 dark:text-white truncate">Financial_2026.pdf</p>
                                    <p class="text-[9px] text-[#86868b]">4.1 MB • PDF</p>
                                </div>
                            </div>

                            <div class="p-3 rounded-xl bg-white/80 dark:bg-[#1c1c1e]/80 border border-black/[0.04] dark:border-white/[0.06] flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-[#ff9f0a] flex items-center justify-center shrink-0">
                                    <i class="pi pi-volume-up text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-semibold text-gray-900 dark:text-white truncate">Podcast_Ep14.flac</p>
                                    <p class="text-[9px] text-[#86868b]">230 MB • Audio</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- Bento Grid Feature Section -->
        <section id="features" class="py-12 sm:py-20 px-4 sm:px-6 max-w-6xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-[#0071e3] dark:text-[#0a84ff]">Features</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-950 dark:text-white tracking-tight mt-1">
                    Everything you love about cloud storage, minus the storage panic.
                </h2>
                <p class="mt-3 text-xs sm:text-sm text-[#86868b]">
                    Built from the ground up for speed, visual beauty, and mobile usability.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                <!-- Bento 1: Unlimited Storage (Spans 2 cols on tablet/desktop) -->
                <div id="unlimited" class="md:col-span-2 rounded-2xl sm:rounded-3xl p-6 sm:p-8 bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-xl border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card flex flex-col justify-between group hover:border-[#0071e3]/40 transition-all">
                    <div>
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-[#0071e3] flex items-center justify-center mb-4">
                            <i class="pi pi-infinity text-lg"></i>
                        </div>
                        <h3 class="text-lg sm:text-2xl font-bold text-gray-950 dark:text-white">Unlimited Storage for Everyone</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#86868b] leading-relaxed max-w-lg">
                            Forget checking how many megabytes you have left or deleting memories to make room for system updates. Myy Bucket gives you unlimited storage space during our trial so you can backup your entire digital life without hesitation.
                        </p>
                    </div>
                    <div class="mt-6 pt-5 border-t border-black/[0.04] dark:border-white/[0.06] flex items-center gap-4 text-xs font-semibold text-[#0071e3] dark:text-[#0a84ff]">
                        <span class="flex items-center gap-1.5"><i class="pi pi-check"></i> High-res RAW Photos</span>
                        <span class="flex items-center gap-1.5"><i class="pi pi-check"></i> Full 4K Video Masters</span>
                        <span class="flex items-center gap-1.5"><i class="pi pi-check"></i> Huge ZIP Archives</span>
                    </div>
                </div>

                <!-- Bento 2: Mobile 1st Design -->
                <div class="rounded-2xl sm:rounded-3xl p-6 sm:p-8 bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-xl border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card flex flex-col justify-between group hover:border-[#0071e3]/40 transition-all">
                    <div>
                        <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-[#bf5af2] flex items-center justify-center mb-4">
                            <i class="pi pi-mobile text-lg"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-950 dark:text-white">Designed for Mobile 1st</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#86868b] leading-relaxed">
                            Compact touch navigation dock, seamless gestures, and responsive layouts tailored for your iPhone and Android screens.
                        </p>
                    </div>
                    <div class="mt-6 inline-flex items-center gap-2 text-xs font-semibold text-[#bf5af2]">
                        <span>Apple iOS Aesthetic</span>
                        <i class="pi pi-arrow-right text-[10px]"></i>
                    </div>
                </div>

                <!-- Bento 3: Inline Media Streaming -->
                <div class="rounded-2xl sm:rounded-3xl p-6 sm:p-8 bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-xl border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card flex flex-col justify-between group hover:border-[#0071e3]/40 transition-all">
                    <div>
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-[#ff9f0a] flex items-center justify-center mb-4">
                            <i class="pi pi-play text-lg"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-950 dark:text-white">Instant Media Player</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#86868b] leading-relaxed">
                            Stream videos, listen to loss-less audio tracks, and preview PDFs directly in your browser without waiting for large downloads.
                        </p>
                    </div>
                    <div class="mt-6 text-xs text-amber-600 dark:text-amber-400 font-semibold">
                        HTML5 Native Streaming Player
                    </div>
                </div>

                <!-- Bento 4: Smart Tagging & Folders (Spans 2 cols) -->
                <div class="md:col-span-2 rounded-2xl sm:rounded-3xl p-6 sm:p-8 bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-xl border border-black/[0.06] dark:border-white/[0.08] shadow-apple-card flex flex-col justify-between group hover:border-[#0071e3]/40 transition-all">
                    <div>
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-[#30d158] flex items-center justify-center mb-4">
                            <i class="pi pi-folder text-lg"></i>
                        </div>
                        <h3 class="text-lg sm:text-2xl font-bold text-gray-950 dark:text-white">Smart Color-Coded Organization</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#86868b] leading-relaxed max-w-lg">
                            Categorize your work, personal photos, and client files using customizable Apple color tags. Star your critical files for single-tap access anytime.
                        </p>
                    </div>
                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Work Projects
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span> 4K Footage
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Important Docs
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Comparison Table (Myy Bucket vs iCloud & Competitors) -->
        <section id="comparison" class="py-12 sm:py-20 px-4 sm:px-6 max-w-5xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-widest text-[#0071e3] dark:text-[#0a84ff]">Comparison</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-950 dark:text-white tracking-tight mt-1">
                    Why users are switching to Myy Bucket.
                </h2>
                <p class="mt-3 text-xs sm:text-sm text-[#86868b]">
                    Traditional providers lock you into expensive monthly tiers. We believe storage should be limitless.
                </p>
            </div>

            <div class="overflow-x-auto rounded-2xl sm:rounded-3xl border border-black/[0.08] dark:border-white/[0.1] bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-xl shadow-apple-card">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-black/[0.06] dark:border-white/[0.08] bg-black/[0.02] dark:bg-white/[0.02]">
                            <th class="py-4 px-4 sm:px-6 font-semibold text-gray-600 dark:text-gray-300">Feature</th>
                            <th class="py-4 px-4 sm:px-6 font-bold text-[#0071e3] dark:text-[#0a84ff] bg-blue-50/50 dark:bg-blue-950/20">Myy Bucket</th>
                            <th class="py-4 px-4 sm:px-6 font-semibold text-gray-500 dark:text-gray-400">Apple iCloud</th>
                            <th class="py-4 px-4 sm:px-6 font-semibold text-gray-500 dark:text-gray-400">Google Drive</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/[0.04] dark:divide-white/[0.06]">
                        <tr>
                            <td class="py-3.5 px-4 sm:px-6 font-medium text-gray-900 dark:text-white">Free Trial Storage</td>
                            <td class="py-3.5 px-4 sm:px-6 font-bold text-emerald-600 dark:text-emerald-400 bg-blue-50/50 dark:bg-blue-950/20 flex items-center gap-1.5">
                                <i class="pi pi-infinity"></i> Unlimited
                            </td>
                            <td class="py-3.5 px-4 sm:px-6 text-gray-500">Only 5 GB</td>
                            <td class="py-3.5 px-4 sm:px-6 text-gray-500">15 GB (Shared with Gmail)</td>
                        </tr>
                        <tr>
                            <td class="py-3.5 px-4 sm:px-6 font-medium text-gray-900 dark:text-white">Cloud Architecture</td>
                            <td class="py-3.5 px-4 sm:px-6 font-bold text-[#0071e3] dark:text-[#0a84ff] bg-blue-50/50 dark:bg-blue-950/20">
                                Cloudflare R2 / S3 Edge
                            </td>
                            <td class="py-3.5 px-4 sm:px-6 text-gray-500">Proprietary iCloud</td>
                            <td class="py-3.5 px-4 sm:px-6 text-gray-500">Google Cloud</td>
                        </tr>
                        <tr>
                            <td class="py-3.5 px-4 sm:px-6 font-medium text-gray-900 dark:text-white">Mobile UX</td>
                            <td class="py-3.5 px-4 sm:px-6 font-bold text-emerald-600 dark:text-emerald-400 bg-blue-50/50 dark:bg-blue-950/20">
                                Dedicated Apple Glass Dock
                            </td>
                            <td class="py-3.5 px-4 sm:px-6 text-gray-500">Native iOS app only</td>
                            <td class="py-3.5 px-4 sm:px-6 text-gray-500">Generic mobile web</td>
                        </tr>
                        <tr>
                            <td class="py-3.5 px-4 sm:px-6 font-medium text-gray-900 dark:text-white">Inline Media Streaming</td>
                            <td class="py-3.5 px-4 sm:px-6 font-bold text-emerald-600 dark:text-emerald-400 bg-blue-50/50 dark:bg-blue-950/20">
                                Video, Audio, Image & PDF
                            </td>
                            <td class="py-3.5 px-4 sm:px-6 text-gray-500">Limited formats</td>
                            <td class="py-3.5 px-4 sm:px-6 text-gray-500">Compressed previews</td>
                        </tr>
                        <tr>
                            <td class="py-3.5 px-4 sm:px-6 font-medium text-gray-900 dark:text-white">Credit Card Required</td>
                            <td class="py-3.5 px-4 sm:px-6 font-bold text-emerald-600 dark:text-emerald-400 bg-blue-50/50 dark:bg-blue-950/20">
                                None (Instant Activation)
                            </td>
                            <td class="py-3.5 px-4 sm:px-6 text-gray-500">Required for upgrades</td>
                            <td class="py-3.5 px-4 sm:px-6 text-gray-500">Required for upgrades</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Pricing / Free Trial SaaS Section -->
        <section id="pricing" class="py-12 sm:py-20 px-4 sm:px-6 max-w-4xl mx-auto text-center">
            <div class="max-w-xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-widest text-[#0071e3] dark:text-[#0a84ff]">Transparent Pricing</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-950 dark:text-white tracking-tight mt-1">
                    Free Trial. Unlimited Power.
                </h2>
                <p class="mt-2 text-xs sm:text-sm text-[#86868b]">
                    We are currently in public early-access. Enjoy completely unlimited cloud storage with zero charges.
                </p>
            </div>

            <!-- Single Hero SaaS Pricing Card -->
            <div class="relative rounded-3xl p-6 sm:p-10 bg-white/90 dark:bg-[#1c1c1e]/90 border-2 border-[#0071e3]/40 dark:border-[#0a84ff]/40 shadow-apple-float backdrop-blur-2xl text-left max-w-lg mx-auto">
                <div class="absolute -top-3.5 right-6 inline-flex items-center gap-1 px-3 py-0.5 rounded-full bg-[#0071e3] text-white text-[11px] font-bold shadow-xs">
                    <i class="pi pi-star-fill text-[9px]"></i>
                    <span>EARLY ACCESS FREE TRIAL</span>
                </div>

                <div class="flex items-baseline gap-2">
                    <span class="text-4xl sm:text-5xl font-extrabold text-gray-950 dark:text-white tracking-tight">$0</span>
                    <span class="text-xs text-[#86868b]">/ free during public trial</span>
                </div>
                <p class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                    Full unlocked access to all Myy Bucket features with unlimited storage bandwidth.
                </p>

                <div class="my-6 border-t border-black/[0.05] dark:border-white/[0.08]"></div>

                <ul class="space-y-3 text-xs sm:text-sm text-gray-700 dark:text-gray-200">
                    <li class="flex items-center gap-2.5">
                        <i class="pi pi-check text-emerald-500 font-bold"></i>
                        <span><strong>Unlimited Cloud Storage</strong> (No artificial caps)</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="pi pi-check text-emerald-500 font-bold"></i>
                        <span><strong>Multi-file Drag & Drop</strong> uploader</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="pi pi-check text-emerald-500 font-bold"></i>
                        <span><strong>Native Media Streaming</strong> for 4K video & audio</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="pi pi-check text-emerald-500 font-bold"></i>
                        <span><strong>Smart Color-Tagged Folders</strong> & Starred favorites</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="pi pi-check text-emerald-500 font-bold"></i>
                        <span><strong>Apple Glass Mobile Interface</strong> with Dark Mode</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="pi pi-check text-emerald-500 font-bold"></i>
                        <span><strong>Zero Ads & Zero Tracking</strong></span>
                    </li>
                </ul>

                <div class="mt-8">
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-full bg-[#0071e3] hover:bg-[#0077ed] text-white text-sm font-semibold shadow-apple-card transition-all active:scale-95"
                        >
                            <span>Enter Your Bucket Drive</span>
                            <i class="pi pi-arrow-right text-xs"></i>
                        </a>
                    @else
                        <a
                            href="{{ route('register') }}"
                            class="w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-full bg-[#0071e3] hover:bg-[#0077ed] text-white text-sm font-semibold shadow-apple-card transition-all active:scale-95"
                        >
                            <span>Claim Your Free Trial</span>
                            <i class="pi pi-arrow-right text-xs"></i>
                        </a>
                    @endauth
                    <p class="mt-2 text-center text-[10px] text-[#86868b]">
                        No credit card required. Instant account setup in 30 seconds.
                    </p>
                </div>
            </div>
        </section>

        <!-- FAQ Section (Accordion) -->
        <section id="faq" class="py-12 sm:py-20 px-4 sm:px-6 max-w-3xl mx-auto">
            <div class="text-center mb-10">
                <span class="text-xs font-bold uppercase tracking-widest text-[#0071e3] dark:text-[#0a84ff]">FAQ</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-950 dark:text-white tracking-tight mt-1">
                    Frequently Asked Questions
                </h2>
            </div>

            <div class="space-y-3">
                <!-- FAQ 1 -->
                <div class="rounded-2xl bg-white/80 dark:bg-[#1c1c1e]/80 border border-black/[0.06] dark:border-white/[0.08] p-4 transition-all">
                    <button
                        @click="activeFaq = (activeFaq === 1 ? null : 1)"
                        type="button"
                        class="w-full flex items-center justify-between text-left text-xs sm:text-sm font-bold text-gray-900 dark:text-white focus:outline-none"
                    >
                        <span>Totoo bang Unlimited ang storage sa Free Trial?</span>
                        <i :class="activeFaq === 1 ? 'pi pi-chevron-up' : 'pi pi-chevron-down'" class="text-xs text-gray-400"></i>
                    </button>
                    <div x-show="activeFaq === 1" x-collapse x-cloak class="mt-2.5 text-xs text-[#86868b] dark:text-gray-400 leading-relaxed">
                        Opo! Sa ating public trial, walang fixed storage ceiling tulad ng 5 GB o 15 GB limits. Pwede kang mag-upload ng mga high-res photos, videos, audio tracks, at documents gamit ang high-speed cloud storage engine.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="rounded-2xl bg-white/80 dark:bg-[#1c1c1e]/80 border border-black/[0.06] dark:border-white/[0.08] p-4 transition-all">
                    <button
                        @click="activeFaq = (activeFaq === 2 ? null : 2)"
                        type="button"
                        class="w-full flex items-center justify-between text-left text-xs sm:text-sm font-bold text-gray-900 dark:text-white focus:outline-none"
                    >
                        <span>Kailangan ba ng Credit Card para makapag-register?</span>
                        <i :class="activeFaq === 2 ? 'pi pi-chevron-up' : 'pi pi-chevron-down'" class="text-xs text-gray-400"></i>
                    </button>
                    <div x-show="activeFaq === 2" x-collapse x-cloak class="mt-2.5 text-xs text-[#86868b] dark:text-gray-400 leading-relaxed">
                        Hindi po. Mag-sign up lang gamit ang iyong email at password, at automatic nang magiging active ang iyong unlimited cloud bucket.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="rounded-2xl bg-white/80 dark:bg-[#1c1c1e]/80 border border-black/[0.06] dark:border-white/[0.08] p-4 transition-all">
                    <button
                        @click="activeFaq = (activeFaq === 3 ? null : 3)"
                        type="button"
                        class="w-full flex items-center justify-between text-left text-xs sm:text-sm font-bold text-gray-900 dark:text-white focus:outline-none"
                    >
                        <span>Pano ito gamitin sa Mobile o iPhone ko?</span>
                        <i :class="activeFaq === 3 ? 'pi pi-chevron-up' : 'pi pi-chevron-down'" class="text-xs text-gray-400"></i>
                    </button>
                    <div x-show="activeFaq === 3" x-collapse x-cloak class="mt-2.5 text-xs text-[#86868b] dark:text-gray-400 leading-relaxed">
                        Naka-disenyo ang Myy Bucket bilang mobile 1st web app. Buksan lang ang website sa Safari o Chrome sa iyong mobile phone, at may lalabas na iOS-style dock menu sa ilalim kung saan pwede kang mag-upload at mag-stream ng media kahit saan.
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Banner -->
        <section class="py-12 sm:py-20 px-4 sm:px-6 max-w-5xl mx-auto">
            <div class="rounded-3xl p-8 sm:p-12 bg-gradient-to-tr from-[#0071e3] to-[#5856d6] text-white text-center shadow-apple-float relative overflow-hidden">
                <div class="relative z-10 max-w-xl mx-auto">
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                        Ready to experience unlimited cloud storage?
                    </h2>
                    <p class="mt-3 text-xs sm:text-sm text-blue-100 leading-relaxed">
                        Simulan na ang iyong free trial ngayon. I-upload ang mga larawan at dokumento mo nang walang inaalalang storage limit.
                    </p>
                    <div class="mt-6 flex justify-center">
                        @auth
                            <a
                                href="{{ route('dashboard') }}"
                                class="px-6 py-3 rounded-full bg-white text-[#0071e3] hover:bg-gray-100 font-bold text-xs sm:text-sm shadow-apple-card transition-all active:scale-95"
                            >
                                Open Myy Bucket Drive
                            </a>
                        @else
                            <a
                                href="{{ route('register') }}"
                                class="px-6 py-3 rounded-full bg-white text-[#0071e3] hover:bg-gray-100 font-bold text-xs sm:text-sm shadow-apple-card transition-all active:scale-95"
                            >
                                Create Free Account
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="border-t border-black/[0.06] dark:border-white/[0.08] bg-white/60 dark:bg-[#121214]/60 backdrop-blur-xl py-8 px-4 sm:px-6 text-center text-xs text-[#86868b]">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <img src="{{ asset('logo/logo.png') }}" alt="Logo" class="w-5 h-5 object-contain">
                <span class="font-bold text-gray-900 dark:text-white">Myy Bucket</span>
                <span>• Your Alternative to iCloud Cloud Storage</span>
            </div>
            <div class="flex items-center gap-4 text-[11px]">
                <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Systems Operational
                </span>
                <span>Unlimited Storage Engine</span>
                <span>© {{ date('Y') }} Myy Bucket</span>
            </div>
        </div>
    </footer>

</body>
</html>
