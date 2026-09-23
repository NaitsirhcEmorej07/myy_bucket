<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 bg-red-600 hover:bg-red-700 active:scale-95 text-white rounded-full font-semibold text-xs sm:text-sm tracking-tight transition-all duration-150 shadow-xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-red-500/40']) }}>
    {{ $slot }}
</button>
