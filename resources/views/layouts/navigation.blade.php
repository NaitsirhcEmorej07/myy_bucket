<nav x-data="{ open: false }" class="sticky top-0 z-40 backdrop-blur-2xl apple-glass-nav bg-white/80 dark:bg-[#161618]/90 border-b border-black/[0.06] dark:border-white/[0.1] transition-colors duration-200">
    <!-- Primary Top Navigation Bar -->
    <div class="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14 sm:h-16">
            <div class="flex items-center gap-3 sm:gap-6">
                <!-- Apple-style Logo Squircle -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group transition-transform active:scale-95">
                        <img
                            src="{{ asset(file_exists(public_path('logo/logo.png')) ? 'logo/logo.png' : 'logo.png') }}"
                            alt="{{ config('app.name', 'Myy Bucket: Your Alternative to iCloud Cloud Storage') }}"
                            class="w-8 h-8 sm:w-9 sm:h-9 object-contain rounded-xl shadow-xs shrink-0"
                        />
                        <div class="flex items-baseline gap-1.5">
                            <span class="font-bold text-xs sm:text-sm tracking-tight text-gray-900 dark:text-white">
                                Myy Bucket
                            </span>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 hidden md:inline font-normal">
                                • Your Alternative to iCloud Cloud Storage
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Segmented Navigation Pills -->
                <div class="hidden sm:flex items-center p-1 bg-black/[0.04] dark:bg-white/[0.06] rounded-full text-xs font-medium">
                    <a
                        href="{{ route('dashboard') }}"
                        class="px-3.5 py-1.5 rounded-full transition-all duration-150 flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-white dark:bg-[#2c2c2e] text-gray-900 dark:text-white shadow-xs font-semibold' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }}"
                    >
                        <i class="pi pi-compass text-xs"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                    <a
                        href="{{ route('bucket') }}"
                        class="px-3.5 py-1.5 rounded-full transition-all duration-150 flex items-center gap-1.5 {{ request()->routeIs('bucket') ? 'bg-white dark:bg-[#2c2c2e] text-gray-900 dark:text-white shadow-xs font-semibold' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }}"
                    >
                        <i class="pi pi-box text-xs"></i>
                        <span>{{ __('Bucket') }}</span>
                    </a>
                </div>
            </div>

            <!-- Desktop User Dropdown & Controls -->
            <div class="hidden sm:flex sm:items-center sm:gap-2">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium text-gray-700 dark:text-gray-200 bg-black/[0.03] dark:bg-white/[0.06] hover:bg-black/[0.06] dark:hover:bg-white/[0.1] transition-all cursor-pointer">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 text-white flex items-center justify-center font-bold text-[10px]">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                            <i class="pi pi-chevron-down text-[9px] text-gray-400"></i>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 text-xs">
                            <p class="font-semibold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">
                            <i class="pi pi-user text-xs me-2"></i> {{ __('Profile Settings') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-red-600 dark:text-red-400">
                                <i class="pi pi-sign-out text-xs me-2"></i> {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Top Right: Profile / Quick Menu -->
            <div class="flex items-center gap-1.5 sm:hidden">
                <a
                    href="{{ route('profile.edit') }}"
                    class="p-1 rounded-full text-gray-600 dark:text-gray-300 hover:bg-black/[0.05] dark:hover:bg-white/[0.1] transition-colors"
                    title="Profile"
                >
                    <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 text-white flex items-center justify-center font-bold text-[11px] shadow-2xs">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </a>

                <!-- Mobile Hamburger for secondary actions -->
                <button
                    @click="open = !open"
                    class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-black/[0.05] dark:hover:bg-white/[0.1] transition-colors focus:outline-none"
                    aria-label="Toggle menu"
                >
                    <i :class="open ? 'pi pi-times' : 'pi pi-ellipsis-v'" class="text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Slide-down Sheet / Menu -->
    <div
        x-show="open"
        x-cloak
        @click.outside="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="sm:hidden border-t border-black/[0.06] dark:border-white/[0.1] bg-white/95 dark:bg-[#1c1c1e] backdrop-blur-2xl px-4 py-3 space-y-2 shadow-xl transition-colors"
    >
        <div class="flex items-center gap-2.5 pb-2.5 border-b border-gray-100 dark:border-white/[0.08]">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 text-white flex items-center justify-center font-bold text-xs">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="truncate text-xs">
                <div class="font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</div>
                <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</div>
            </div>
        </div>

        <div class="pt-1 space-y-1 text-xs">
            <a
                href="{{ route('profile.edit') }}"
                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-black/[0.04] dark:hover:bg-white/[0.08] transition-colors"
            >
                <i class="pi pi-user text-xs text-indigo-500"></i>
                <span>Profile Settings</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors text-xs text-left"
                >
                    <i class="pi pi-sign-out text-xs"></i>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </div>
</nav>
