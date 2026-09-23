<div x-data="{
    isDark: document.documentElement.classList.contains('dark'),
    toggle() {
        this.isDark = !this.isDark;
        if (this.isDark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }
}" class="fixed bottom-16 sm:bottom-6 right-3.5 sm:right-6 z-50">
    <button
        @click="toggle()"
        type="button"
        aria-label="Toggle light and dark mode"
        title="Toggle appearance"
        class="w-9 h-9 sm:w-11 sm:h-11 rounded-full flex items-center justify-center shadow-apple-float border border-black/[0.08] dark:border-white/[0.12] bg-white/85 dark:bg-[#1c1c1e] text-[#1d1d1f] dark:text-amber-400 hover:scale-105 active:scale-95 transition-all duration-200 backdrop-blur-xl focus:outline-none cursor-pointer"
    >
        <i x-show="!isDark" class="pi pi-moon text-sm sm:text-base transition-transform duration-200"></i>
        <i x-show="isDark" x-cloak class="pi pi-sun text-sm sm:text-base transition-transform duration-200"></i>
    </button>
</div>
