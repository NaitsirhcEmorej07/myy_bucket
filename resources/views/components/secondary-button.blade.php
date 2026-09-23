<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 bg-black/[0.04] dark:bg-white/[0.08] hover:bg-black/[0.08] dark:hover:bg-white/[0.12] active:scale-95 text-gray-700 dark:text-gray-200 border border-black/[0.06] dark:border-white/[0.1] rounded-full font-semibold text-xs sm:text-sm tracking-tight transition-all duration-150 cursor-pointer focus:outline-none']) }}>
    {{ $slot }}
</button>
